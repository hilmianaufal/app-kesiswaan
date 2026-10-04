<?php

namespace Tests\Feature;

use App\Livewire\Attendances\Index as Attendances;
use App\Livewire\Classes\Index as Classes;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentViolation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class KesiswaanRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function schoolClass(string $name = '9C'): SchoolClass
    {
        return SchoolClass::create([
            'name' => $name, 'level' => '9', 'academic_year' => '2026/2027',
            'semester' => 'ganjil', 'capacity' => 36, 'is_active' => true,
        ]);
    }

    private function student(SchoolClass $class, string $nis = '001'): Student
    {
        return Student::create([
            'school_class_id' => $class->id, 'nis' => $nis, 'name' => 'Siswa '.$nis,
            'gender' => 'male', 'status' => 'active',
        ]);
    }

    public function test_admin_can_open_all_modules_and_pdf_reports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = $this->student($this->schoolClass());
        $this->actingAs($admin);

        foreach (['dashboard', 'classes.index', 'students.index', 'attendances.index',
            'development-notes.index', 'violations.index', 'achievements.index',
            'student-permissions.index', 'counseling.index', 'student-profiles.index',
            'reports.index', 'profile.edit'] as $route) {
            $this->get(route($route))->assertOk();
        }
        foreach (['attendance', 'violations', 'achievements', 'permissions', 'counseling'] as $report) {
            $this->get(route('reports.'.$report.'.pdf'))->assertOk()
                ->assertHeader('content-type', 'application/pdf');
        }
        $this->get(route('reports.student-profile.pdf', $student))->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_dashboard_counts_database_records_for_today_only(): void
    {
        $user = User::factory()->create();
        $student = $this->student($this->schoolClass());
        StudentAttendance::create([
            'student_id' => $student->id, 'school_class_id' => $student->school_class_id,
            'attendance_date' => now()->toDateString(), 'status' => 'present', 'is_late' => true,
        ]);
        StudentAttendance::create([
            'student_id' => $student->id, 'school_class_id' => $student->school_class_id,
            'attendance_date' => now()->subDay()->toDateString(), 'status' => 'present',
        ]);
        StudentViolation::create([
            'student_id' => $student->id, 'violation_name' => 'Terlambat', 'category' => 'light',
            'violation_date' => now()->toDateString(), 'chronology' => 'Datang terlambat.',
        ]);
        $this->actingAs($user)->get(route('dashboard'))->assertOk()
            ->assertViewHas('statistics', ['students' => 1, 'present' => 1, 'late' => 1, 'violations' => 1]);
    }

    public function test_dashboard_hides_menus_that_the_role_cannot_open(): void
    {
        foreach (['guru', 'wali_kelas', 'guru_bk', 'kepala_sekolah'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->get(route('dashboard'))->assertOk()
                ->assertDontSee('Kelola Data Kelas');
            $this->get(route('classes.index'))->assertForbidden();
            $this->get(route('student-profiles.index'))->assertOk();
            if ($role === 'kepala_sekolah') {
                $response->assertDontSee('href="'.route('development-notes.index').'"', false);
                $this->get(route('development-notes.index'))->assertForbidden();
            }
        }
    }

    public function test_inactive_account_cannot_open_dashboard_or_log_in(): void
    {
        $user = User::factory()->create(['is_active' => false]);
        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_class_can_be_saved_and_duplicate_is_validated(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        Livewire::test(Classes::class)->set('name', '9C')->set('level', '9')
            ->set('academicYear', '2026/2027')->call('save')->assertHasNoErrors()
            ->assertDispatched('swal:success');
        $this->assertDatabaseHas('school_classes', ['name' => '9C', 'level' => '9']);
        Livewire::test(Classes::class)->set('name', '9C')->set('level', '9')
            ->set('academicYear', '2026/2027')->call('save')->assertHasErrors(['name' => 'unique']);
    }

    public function test_attendance_can_be_saved_and_cannot_be_deleted_from_another_class(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $class = $this->schoolClass();
        $student = $this->student($class);
        $otherClass = $this->schoolClass('9D');
        $otherStudent = $this->student($otherClass, '002');
        $record = StudentAttendance::create([
            'student_id' => $otherStudent->id, 'school_class_id' => $otherClass->id,
            'attendance_date' => now()->toDateString(), 'status' => 'present',
        ]);
        Livewire::test(Attendances::class)->set('selectedClassId', (string) $class->id)
            ->set('statuses.'.$student->id, 'present')->call('saveAll')->assertHasNoErrors()
            ->assertDispatched('swal:success');
        $this->assertDatabaseHas('student_attendances', ['student_id' => $student->id, 'status' => 'present']);
        Livewire::test(Attendances::class)->set('selectedClassId', (string) $class->id)
            ->call('deleteAttendance', $otherStudent->id)->assertForbidden();
        $this->assertDatabaseHas('student_attendances', ['id' => $record->id]);
    }

    private function classSnapshot(User $user): string
    {
        $response = $this->actingAs($user)->get(route('classes.index'))->assertOk();
        preg_match('/wire:snapshot="([^"]+)"/', $response->getContent(), $matches);
        $this->assertNotEmpty($matches);
        return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public function test_livewire_rechecks_role_after_page_was_opened(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $snapshot = $this->classSnapshot($user);
        $user->update(['role' => 'guru']);
        $this->actingAs($user)->postJson(Livewire::getUpdateUri(), [
            'components' => [['snapshot' => $snapshot, 'updates' => [],
                'calls' => [['path' => '', 'method' => 'openCreateModal', 'params' => []]]]],
        ], ['X-Livewire' => 'true'])->assertForbidden();
    }

    public function test_livewire_rechecks_active_status_after_page_was_opened(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $snapshot = $this->classSnapshot($user);
        $user->update(['is_active' => false]);
        $this->actingAs($user)->postJson(Livewire::getUpdateUri(), [
            'components' => [['snapshot' => $snapshot, 'updates' => [],
                'calls' => [['path' => '', 'method' => 'openCreateModal', 'params' => []]]]],
        ], ['X-Livewire' => 'true'])->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
