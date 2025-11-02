<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desa;

class DesaSeeder extends Seeder
{
    public function run()
    {
        // Beberapa contoh desa di Makassar & Gowa (nama fiktif / representative)
        $desas = [
            ['nama' => 'Tamalanrea', 'kecamatan_id' => null, 'luas_m2' => 500000],
            ['nama' => 'Tallo', 'kecamatan_id' => null, 'luas_m2' => 420000],
            ['nama' => 'Rappocini', 'kecamatan_id' => null, 'luas_m2' => 380000],
            ['nama' => 'Sungguminasa', 'kecamatan_id' => null, 'luas_m2' => 620000],
            ['nama' => 'Pattalassang', 'kecamatan_id' => null, 'luas_m2' => 480000],
            // tambahkan lagi jika perlu
        ];

        foreach ($desas as $d) {
            Desa::create(array_merge($d, ['potensi' => null, 'jumlah_penduduk_cached' => 0]));
        }
    }
}
