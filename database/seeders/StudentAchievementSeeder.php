<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentAchievementSeeder extends Seeder
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

        $samples = [
            [
                'title' => 'Juara Olimpiade Matematika',
                'category' => StudentAchievement::CATEGORY_ACADEMIC,
                'level' => StudentAchievement::LEVEL_DISTRICT,
                'rank' => StudentAchievement::RANK_FIRST,
                'event_name' => 'Olimpiade Matematika Pelajar',
            ],
            [
                'title' => 'Juara Lomba Pidato',
                'category' => StudentAchievement::CATEGORY_RELIGIOUS,
                'level' => StudentAchievement::LEVEL_SUBDISTRICT,
                'rank' => StudentAchievement::RANK_SECOND,
                'event_name' => 'Lomba Pidato Keagamaan',
            ],
            [
                'title' => 'Juara Turnamen Futsal',
                'category' => StudentAchievement::CATEGORY_NON_ACADEMIC,
                'level' => StudentAchievement::LEVEL_DISTRICT,
                'rank' => StudentAchievement::RANK_THIRD,
                'event_name' => 'Turnamen Futsal Pelajar',
            ],
            [
                'title' => 'Finalis Lomba Kaligrafi',
                'category' => StudentAchievement::CATEGORY_RELIGIOUS,
                'level' => StudentAchievement::LEVEL_PROVINCE,
                'rank' => StudentAchievement::RANK_FINALIST,
                'event_name' => 'Festival Kaligrafi Pelajar',
            ],
            [
                'title' => 'Ketua OSIS Teladan',
                'category' => StudentAchievement::CATEGORY_ORGANIZATION,
                'level' => StudentAchievement::LEVEL_SCHOOL,
                'rank' => StudentAchievement::RANK_FIRST,
                'event_name' => 'Penghargaan Siswa Teladan',
            ],
        ];

        foreach ($students as $index => $student) {
            $sample = $samples[$index];

            StudentAchievement::query()->updateOrCreate(
                [
                    'student_id' => $student->id,
                    'title' => $sample['title'],
                ],
                [
                    'school_class_id' => $student->school_class_id,
                    'category' => $sample['category'],
                    'level' => $sample['level'],
                    'rank' => $sample['rank'],

                    'points' => StudentAchievement::calculatePoints(
                        $sample['level'],
                        $sample['rank']
                    ),

                    'event_name' => $sample['event_name'],
                    'organizer' => 'Dinas Pendidikan',
                    'mentor' => 'Guru Pembimbing',
                    'achievement_date' => now()->subDays($index + 1),
                    'description' => 'Data contoh prestasi siswa.',
                    'status' => StudentAchievement::STATUS_VERIFIED,
                    'recorded_by' => $admin?->id,
                    'verified_by' => $admin?->id,
                    'verified_at' => now(),
                ]
            );
        }
    }
}
