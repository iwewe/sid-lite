<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\ModuleQuestion;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Module 1: Jamban Septic
        $jamban = Module::create([
            'code' => 'jamban',
            'name' => 'Jamban Septic',
            'description' => 'Verifikasi kepemilikan dan penggunaan jamban septic',
            'min_verified' => 4,
            'is_active' => true,
            'order' => 1,
            'icon' => '🚽',
        ]);

        $jambanQuestions = [
            [
                'code' => 'b3r301a',
                'question' => 'Status kepemilikan bangunan tempat tinggal yang ditempati',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 0,
                'options' => [
                    ['value' => '1', 'label' => '1. Milik sendiri'],
                    ['value' => '2', 'label' => '2. Kontrak / Sewa'],
                    ['value' => '3', 'label' => '3. Bebas sewa'],
                    ['value' => '4', 'label' => '4. Dinas'],
                    ['value' => '5', 'label' => '5. Lainnya'],
                ],
            ],
            [
                'code' => 'b3r309a',
                'question' => 'Kepemilikan dan penggunaan fasilitas tempat buang air besar',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 1,
                'options' => [
                    ['value' => '1', 'label' => '1. Ada, digunakan hanya anggota keluarga sendiri'],
                    ['value' => '2', 'label' => '2. Ada, digunakan bersama anggota keluarga dari keluarga tertentu'],
                    ['value' => '3', 'label' => '3. Ada, di MCK komunal'],
                    ['value' => '4', 'label' => '4. Ada, di MCK umum / siapapun menggunakan'],
                    ['value' => '5', 'label' => '5. Ada, anggota keluarga tidak menggunakan'],
                    ['value' => '6', 'label' => '6. Tidak ada fasilitas'],
                ],
            ],
            [
                'code' => 'b3r309b',
                'question' => 'Jenis kloset',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 2,
                'options' => [
                    ['value' => '1', 'label' => '1. Kloset duduk leher angsa'],
                    ['value' => '2', 'label' => '2. Kloset jongkok leher angsa'],
                    ['value' => '3', 'label' => '3. Plengsengan dengan tutup'],
                    ['value' => '4', 'label' => '4. Plengsengan tanpa tutup'],
                    ['value' => '5', 'label' => '5. Cemplung / cubluk'],
                ],
            ],
            [
                'code' => 'b3r310',
                'question' => 'Tempat pembuangan akhir tinja',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 3,
                'options' => [
                    ['value' => '1', 'label' => '1. Tangki septik'],
                    ['value' => '2', 'label' => '2. IPAL'],
                    ['value' => '3', 'label' => '3. Kolam / sawah / sungai / danau / laut'],
                    ['value' => '4', 'label' => '4. Lubang tanah'],
                    ['value' => '5', 'label' => '5. Pantai / tanah lapang / kebun'],
                    ['value' => '6', 'label' => '6. Lainnya'],
                ],
            ],
        ];

        foreach ($jambanQuestions as $question) {
            ModuleQuestion::create(array_merge(['module_id' => $jamban->id], $question));
        }

        // Module 2: RTLH
        $rtlh = Module::create([
            'code' => 'rtlh',
            'name' => 'RTLH',
            'description' => 'Rumah Tidak Layak Huni',
            'min_verified' => 4,
            'is_active' => true,
            'order' => 2,
            'icon' => '🏠',
        ]);

        $rtlhQuestions = [
            [
                'code' => 'b3r301a',
                'question' => 'Status kepemilikan bangunan tempat tinggal yang ditempati',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 0,
                'options' => [
                    ['value' => '1', 'label' => '1. Milik sendiri'],
                    ['value' => '2', 'label' => '2. Kontrak / Sewa'],
                    ['value' => '3', 'label' => '3. Bebas sewa'],
                    ['value' => '4', 'label' => '4. Dinas'],
                    ['value' => '5', 'label' => '5. Lainnya'],
                ],
            ],
            [
                'code' => 'b3r303',
                'question' => 'Jenis lantai terluas',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 1,
                'options' => [
                    ['value' => '1', 'label' => '1. Marmer / granit'],
                    ['value' => '2', 'label' => '2. Keramik'],
                    ['value' => '3', 'label' => '3. Parket / vinil / karpet'],
                    ['value' => '4', 'label' => '4. Ubin / tegel / teraso'],
                    ['value' => '5', 'label' => '5. Kayu / papan'],
                    ['value' => '6', 'label' => '6. Semen / bata merah'],
                    ['value' => '7', 'label' => '7. Bambu'],
                    ['value' => '8', 'label' => '8. Tanah'],
                    ['value' => '9', 'label' => '9. Lainnya'],
                ],
            ],
            [
                'code' => 'b3r304',
                'question' => 'Jenis dinding terluas',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 2,
                'options' => [
                    ['value' => '1', 'label' => '1. Tembok'],
                    ['value' => '2', 'label' => '2. Plesteran anyaman bambu kawat'],
                    ['value' => '3', 'label' => '3. Kayu / papan / gypsum / GRC / calciboard'],
                    ['value' => '4', 'label' => '4. Anyaman bambu'],
                    ['value' => '5', 'label' => '5. Batang kayu'],
                    ['value' => '6', 'label' => '6. Bambu'],
                    ['value' => '7', 'label' => '7. Lainnya'],
                ],
            ],
            [
                'code' => 'b3r305',
                'question' => 'Jenis atap terluas',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 3,
                'options' => [
                    ['value' => '1', 'label' => '1. Beton'],
                    ['value' => '2', 'label' => '2. Genteng'],
                    ['value' => '3', 'label' => '3. Seng'],
                    ['value' => '4', 'label' => '4. Asbes'],
                    ['value' => '5', 'label' => '5. Bumbu'],
                    ['value' => '6', 'label' => '6. Kayu / sirap'],
                    ['value' => '7', 'label' => '7. Jerami / ijuk / daun-daunan / rumbia'],
                    ['value' => '8', 'label' => '8. Lainnya'],
                ],
            ],
        ];

        foreach ($rtlhQuestions as $question) {
            ModuleQuestion::create(array_merge(['module_id' => $rtlh->id], $question));
        }

        // Module 3: PAH (Air Minum)
        $pah = Module::create([
            'code' => 'pah',
            'name' => 'PAH (Air Minum)',
            'description' => 'Penyediaan Air Minum',
            'min_verified' => 2,
            'is_active' => true,
            'order' => 3,
            'icon' => '💧',
        ]);

        $pahQuestions = [
            [
                'code' => 'b3r301a',
                'question' => 'Status kepemilikan bangunan tempat tinggal yang ditempati',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 0,
                'options' => [
                    ['value' => '1', 'label' => '1. Milik sendiri'],
                    ['value' => '2', 'label' => '2. Kontrak / Sewa'],
                    ['value' => '3', 'label' => '3. Bebas sewa'],
                    ['value' => '4', 'label' => '4. Dinas'],
                    ['value' => '5', 'label' => '5. Lainnya'],
                ],
            ],
            [
                'code' => 'b3r306a',
                'question' => 'Sumber air minum utama',
                'field_type' => 'select',
                'is_required' => true,
                'order' => 1,
                'options' => [
                    ['value' => '1', 'label' => '1. Air kemasan bermerk'],
                    ['value' => '2', 'label' => '2. Air isi ulang'],
                    ['value' => '3', 'label' => '3. Leding'],
                    ['value' => '4', 'label' => '4. Sumur bor / pompa'],
                    ['value' => '5', 'label' => '5. Sumur terlindung'],
                    ['value' => '6', 'label' => '6. Sumur tak terlindung'],
                    ['value' => '7', 'label' => '7. Mata air terlindung'],
                    ['value' => '8', 'label' => '8. Mata air tak terlindung'],
                    ['value' => '9', 'label' => '9. Air permukaan (sungai / danau / waduk / kolam / irigasi)'],
                    ['value' => '10', 'label' => '10. Air hujan'],
                    ['value' => '11', 'label' => '11. Lainnya'],
                ],
            ],
        ];

        foreach ($pahQuestions as $question) {
            ModuleQuestion::create(array_merge(['module_id' => $pah->id], $question));
        }

        $this->command->info('✅ 3 modules seeded successfully (Jamban, RTLH, PAH)');
    }
}
