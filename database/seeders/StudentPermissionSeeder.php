<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentPermission;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::query()
            ->where('status', Student::STATUS_ACTIVE)
            ->take(5)
            ->get();

        $admin = User::query()
            ->where('email', 'admin@kesiswaan.test')
            ->first();

        $types = [
            StudentPermission::TYPE_ABSENCE,
            StudentPermission::TYPE_OUT,
            StudentPermission::TYPE_EARLY_LEAVE,
            StudentPermission::TYPE_ABSENCE,
            StudentPermission::TYPE_OUT,
        ];

        foreach ($students as $index => $student) {
            $type = $types[$index];

            StudentPermission::query()->updateOrCreate(
                [
                    'student_id' => $student->id,
                    'permission_date' => now()
                        ->subDays($index)
                        ->format('Y-m-d'),
                    'permission_type' => $type,
                ],
                [
                    'school_class_id' => $student->school_class_id,

                    'start_time' => $type === StudentPermission::TYPE_ABSENCE
                        ? null
                        : '09:00',

                    'expected_return_time' => $type === StudentPermission::TYPE_OUT
                        ? '11:00'
                        : null,

                    'actual_return_time' => null,
                    'reason' => 'Data contoh pengajuan perizinan siswa.',

                    'status' => $index < 2
                        ? StudentPermission::STATUS_APPROVED
                        : StudentPermission::STATUS_PENDING,

                    'recorded_by' => $admin?->id,

                    'approved_by' => $index < 2
                        ? $admin?->id
                        : null,

                    'approved_at' => $index < 2
                        ? now()
                        : null,
                ]
            );
        }
    }
}
