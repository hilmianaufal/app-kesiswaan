<?php

namespace Database\Seeders;

use App\Models\CounselingCase;
use App\Models\CounselingSession;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class CounselingSeeder extends Seeder
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

        $guruBk = User::query()
            ->where('role', User::ROLE_GURU_BK)
            ->where('is_active', true)
            ->first();

        $assignedTeacher = $guruBk ?? $admin;

        $samples = [
            [
                'title' => 'Kesulitan mengikuti pelajaran',
                'category' => CounselingCase::CATEGORY_ACADEMIC,
                'priority' => CounselingCase::PRIORITY_MEDIUM,
            ],
            [
                'title' => 'Sering terlambat masuk sekolah',
                'category' => CounselingCase::CATEGORY_DISCIPLINE,
                'priority' => CounselingCase::PRIORITY_HIGH,
            ],
            [
                'title' => 'Kurang percaya diri di kelas',
                'category' => CounselingCase::CATEGORY_PERSONAL,
                'priority' => CounselingCase::PRIORITY_MEDIUM,
            ],
            [
                'title' => 'Kesulitan bergaul dengan teman',
                'category' => CounselingCase::CATEGORY_SOCIAL,
                'priority' => CounselingCase::PRIORITY_HIGH,
            ],
            [
                'title' => 'Konsultasi rencana pendidikan',
                'category' => CounselingCase::CATEGORY_CAREER,
                'priority' => CounselingCase::PRIORITY_LOW,
            ],
        ];

        foreach ($students as $index => $student) {
            $sample = $samples[$index];

            $case = CounselingCase::query()
                ->firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'title' => $sample['title'],
                    ],
                    [
                        'school_class_id' =>
                            $student->school_class_id,

                        'case_number' =>
                            CounselingCase::generateCaseNumber(),

                        'problem_category' =>
                            $sample['category'],

                        'priority' =>
                            $sample['priority'],

                        'confidentiality_level' =>
                            CounselingCase::CONFIDENTIALITY_CONFIDENTIAL,

                        'summary' =>
                            'Data contoh kasus bimbingan konseling siswa.',

                        'status' =>
                            CounselingCase::STATUS_IN_PROGRESS,

                        'opened_at' =>
                            now()->subDays($index + 1),

                        'assigned_to' =>
                            $assignedTeacher?->id,

                        'created_by' =>
                            $admin?->id,
                    ]
                );

            CounselingSession::query()->firstOrCreate(
                [
                    'counseling_case_id' => $case->id,
                    'session_date' => now()
                        ->subDays($index)
                        ->format('Y-m-d'),
                ],
                [
                    'start_time' => '09:00',
                    'end_time' => '09:30',
                    'location' => 'Ruang BK',
                    'method' =>
                        CounselingSession::METHOD_INDIVIDUAL,

                    'attendance_status' =>
                        CounselingSession::ATTENDANCE_COMPLETED,

                    'agenda' =>
                        'Mengidentifikasi masalah dan kebutuhan siswa.',

                    'result' =>
                        'Siswa telah menyampaikan permasalahan yang dialami.',

                    'follow_up' =>
                        'Melakukan pemantauan dan pertemuan berikutnya.',

                    'next_session_date' =>
                        now()->addDays(7 + $index),

                    'is_parent_contacted' =>
                        $index === 1,

                    'parent_response' =>
                        $index === 1
                            ? 'Orang tua bersedia membantu proses pembinaan.'
                            : null,

                    'recorded_by' =>
                        $assignedTeacher?->id,
                ]
            );
        }
    }
}
