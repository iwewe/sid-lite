<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warga = [
            [
                'nik' => '3173010101010001',
                'nama' => 'Siti Aminah',
                'dusun' => 'Dusun I',
                'rw' => '02',
                'rt' => '01',
                'alamat' => 'Jl. Merdeka No. 12',
                'no_kk' => '3173010101000001',
            ],
            [
                'nik' => '3173010101010002',
                'nama' => 'Budi Santoso',
                'dusun' => 'Dusun I',
                'rw' => '02',
                'rt' => '02',
                'alamat' => 'Jl. Kemerdekaan No. 45',
                'no_kk' => '3173010101000002',
            ],
            [
                'nik' => '3173010101010003',
                'nama' => 'Rina Kurnia',
                'dusun' => 'Dusun II',
                'rw' => '03',
                'rt' => '01',
                'alamat' => 'Jl. Proklamasi No. 78',
                'no_kk' => '3173010101000003',
            ],
            [
                'nik' => '3173010101010004',
                'nama' => 'Ahmad Fauzi',
                'dusun' => 'Dusun III',
                'rw' => '01',
                'rt' => '03',
                'alamat' => 'Jl. Diponegoro No. 23',
                'no_kk' => '3173010101000004',
            ],
            [
                'nik' => '3173010101010005',
                'nama' => 'Dewi Lestari',
                'dusun' => 'Dusun II',
                'rw' => '03',
                'rt' => '02',
                'alamat' => 'Jl. Sudirman No. 56',
                'no_kk' => '3173010101000005',
            ],
        ];

        foreach ($warga as $data) {
            Warga::create($data);
        }

        $this->command->info('✅ 5 warga seeded successfully');
    }
}
