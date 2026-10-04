<?php

namespace Database\Seeders;

use App\Models\ViolationType;
use Illuminate\Database\Seeder;

class ViolationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Terlambat masuk sekolah',
                'category' => ViolationType::CATEGORY_LIGHT,
                'points' => 5,
                'default_action' => 'Teguran lisan dan pembinaan kedisiplinan.',
            ],
            [
                'name' => 'Tidak memakai atribut lengkap',
                'category' => ViolationType::CATEGORY_LIGHT,
                'points' => 5,
                'default_action' => 'Teguran dan melengkapi atribut sekolah.',
            ],
            [
                'name' => 'Tidak mengerjakan tugas',
                'category' => ViolationType::CATEGORY_LIGHT,
                'points' => 5,
                'default_action' => 'Mengerjakan tugas susulan.',
            ],
            [
                'name' => 'Membuat kegaduhan di kelas',
                'category' => ViolationType::CATEGORY_LIGHT,
                'points' => 10,
                'default_action' => 'Teguran dan pembinaan wali kelas.',
            ],
            [
                'name' => 'Bolos pelajaran',
                'category' => ViolationType::CATEGORY_MEDIUM,
                'points' => 20,
                'default_action' => 'Pembinaan wali kelas dan guru BK.',
            ],
            [
                'name' => 'Meninggalkan sekolah tanpa izin',
                'category' => ViolationType::CATEGORY_MEDIUM,
                'points' => 25,
                'default_action' => 'Pemanggilan orang tua.',
            ],
            [
                'name' => 'Merusak fasilitas sekolah',
                'category' => ViolationType::CATEGORY_MEDIUM,
                'points' => 30,
                'default_action' => 'Mengganti kerusakan dan pemanggilan orang tua.',
            ],
            [
                'name' => 'Melakukan perundungan',
                'category' => ViolationType::CATEGORY_HEAVY,
                'points' => 50,
                'default_action' => 'Konseling intensif dan pemanggilan orang tua.',
            ],
            [
                'name' => 'Berkelahi',
                'category' => ViolationType::CATEGORY_HEAVY,
                'points' => 60,
                'default_action' => 'Surat peringatan dan pemanggilan orang tua.',
            ],
            [
                'name' => 'Membawa barang terlarang',
                'category' => ViolationType::CATEGORY_HEAVY,
                'points' => 75,
                'default_action' => 'Penanganan kepala sekolah dan pemanggilan orang tua.',
            ],
        ];

        foreach ($types as $type) {
            ViolationType::query()->updateOrCreate(
                [
                    'name' => $type['name'],
                ],
                [
                    'category' => $type['category'],
                    'points' => $type['points'],
                    'default_action' => $type['default_action'],
                    'is_active' => true,
                ]
            );
        }
    }
}
