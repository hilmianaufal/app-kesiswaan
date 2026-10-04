<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('email', 'admin@kesiswaan.test')
            ->first();

        $classes = [
            [
                'name' => 'Kelas 7A',
                'level' => '7',
                'major' => null,
                'capacity' => 32,
            ],
            [
                'name' => 'Kelas 7B',
                'level' => '7',
                'major' => null,
                'capacity' => 32,
            ],
            [
                'name' => 'Kelas 8A',
                'level' => '8',
                'major' => null,
                'capacity' => 34,
            ],
            [
                'name' => 'Kelas 8B',
                'level' => '8',
                'major' => null,
                'capacity' => 34,
            ],
            [
                'name' => 'Kelas 9A',
                'level' => '9',
                'major' => null,
                'capacity' => 36,
            ],
        ];

        foreach ($classes as $class) {
            SchoolClass::query()->updateOrCreate(
                [
                    'name' => $class['name'],
                    'academic_year' => '2026/2027',
                    'semester' => 'ganjil',
                ],
                [
                    'level' => $class['level'],
                    'major' => $class['major'],
                    'homeroom_teacher_id' => $admin?->id,
                    'capacity' => $class['capacity'],
                    'description' => 'Data kelas tahun ajaran 2026/2027.',
                    'is_active' => true,
                ]
            );
        }
    }
}
