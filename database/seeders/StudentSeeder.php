<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = SchoolClass::query()
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        $admin = User::query()
            ->where('email', 'admin@kesiswaan.test')
            ->first();

        $students = [
            ['Ahmad Fauzan', 'male', '2012-01-10'],
            ['Muhammad Rizky', 'male', '2012-03-15'],
            ['Dimas Saputra', 'male', '2012-05-20'],
            ['Fajar Ramadhan', 'male', '2011-08-07'],
            ['Ilham Maulana', 'male', '2011-11-12'],
            ['Siti Aisyah', 'female', '2012-02-18'],
            ['Nabila Putri', 'female', '2012-04-23'],
            ['Aulia Rahma', 'female', '2011-06-14'],
            ['Zahra Safitri', 'female', '2011-09-28'],
            ['Nurul Hidayah', 'female', '2012-12-05'],
        ];

        foreach ($students as $index => $item) {
            $number = str_pad(
                (string) ($index + 1),
                3,
                '0',
                STR_PAD_LEFT
            );

            $class = $classes->count()
                ? $classes[$index % $classes->count()]
                : null;

            Student::query()->updateOrCreate(
                [
                    'nis' => '2026'.$number,
                ],
                [
                    'nisn' => '0012345'.$number,
                    'name' => $item[0],
                    'gender' => $item[1],
                    'birth_place' => 'Bandung',
                    'birth_date' => $item[2],
                    'school_class_id' => $class?->id,
                    'address' => 'Alamat siswa nomor '.($index + 1),
                    'father_name' => 'Ayah '.$item[0],
                    'mother_name' => 'Ibu '.$item[0],
                    'parent_phone' => '081234567'.$number,
                    'enrollment_year' => 2026,
                    'status' => Student::STATUS_ACTIVE,
                    'created_by' => $admin?->id,
                ]
            );
        }
    }
}
