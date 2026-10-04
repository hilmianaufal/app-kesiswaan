<?php

namespace App\Livewire\Reports;

use App\Models\CounselingCase;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentAttendance;
use App\Models\StudentPermission;
use App\Models\StudentViolation;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Index extends Component
{
    public string $dateFrom = '';

    public string $dateTo = '';

    public string $classId = '';

    public string $studentId = '';

    public function mount(): void
    {
        $this->dateFrom = now()
            ->startOfMonth()
            ->format('Y-m-d');

        $this->dateTo = now()
            ->endOfMonth()
            ->format('Y-m-d');
    }

    public function updatedClassId(): void
    {
        if (! filled($this->studentId)) {
            return;
        }

        $studentExists = Student::query()
            ->where('id', $this->studentId)
            ->when(
                filled($this->classId),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $this->classId
                )
            )
            ->exists();

        if (! $studentExists) {
            $this->studentId = '';
        }
    }

    public function resetFilters(): void
    {
        $this->dateFrom = now()
            ->startOfMonth()
            ->format('Y-m-d');

        $this->dateTo = now()
            ->endOfMonth()
            ->format('Y-m-d');

        $this->classId = '';
        $this->studentId = '';
    }

    public function render()
    {
        $schoolClasses = SchoolClass::query()
            ->where('is_active', true)
            ->orderByDesc('academic_year')
            ->orderBy('level')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'academic_year',
            ]);

        $students = Student::query()
            ->with('schoolClass:id,name')
            ->where(
                'status',
                Student::STATUS_ACTIVE
            )
            ->when(
                filled($this->classId),
                fn (Builder $query) => $query->where(
                    'school_class_id',
                    $this->classId
                )
            )
            ->orderBy('name')
            ->get([
                'id',
                'school_class_id',
                'name',
                'nis',
            ]);

        $statistics = [
            'attendance' => StudentAttendance::query()
                ->whereBetween('attendance_date', [
                    $this->dateFrom,
                    $this->dateTo,
                ])
                ->when(
                    filled($this->classId),
                    fn (Builder $query) => $query->where(
                        'school_class_id',
                        $this->classId
                    )
                )
                ->count(),

            'violations' => StudentViolation::query()
                ->whereBetween('violation_date', [
                    $this->dateFrom,
                    $this->dateTo,
                ])
                ->when(
                    filled($this->classId),
                    fn (Builder $query) => $query->where(
                        'school_class_id',
                        $this->classId
                    )
                )
                ->count(),

            'achievements' =>
                StudentAchievement::query()
                    ->whereBetween(
                        'achievement_date',
                        [
                            $this->dateFrom,
                            $this->dateTo,
                        ]
                    )
                    ->when(
                        filled($this->classId),
                        fn (Builder $query) => $query->where(
                            'school_class_id',
                            $this->classId
                        )
                    )
                    ->count(),

            'permissions' =>
                StudentPermission::query()
                    ->whereBetween(
                        'permission_date',
                        [
                            $this->dateFrom,
                            $this->dateTo,
                        ]
                    )
                    ->when(
                        filled($this->classId),
                        fn (Builder $query) => $query->where(
                            'school_class_id',
                            $this->classId
                        )
                    )
                    ->count(),

            'counseling' => CounselingCase::query()
                ->whereBetween('opened_at', [
                    $this->dateFrom,
                    $this->dateTo,
                ])
                ->when(
                    filled($this->classId),
                    fn (Builder $query) => $query->where(
                        'school_class_id',
                        $this->classId
                    )
                )
                ->count(),
        ];

        return view(
            'livewire.reports.index',
            [
                'schoolClasses' => $schoolClasses,
                'students' => $students,
                'statistics' => $statistics,
            ]
        );
    }
}
