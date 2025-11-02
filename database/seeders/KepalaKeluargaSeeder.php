<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KepalaKeluarga;
use App\Models\Anggota;
use App\Models\Desa;

class KepalaKeluargaSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');

        $TOTAL_KK = 500; // sesuai permintaan

        // area codes representative (Sulawesi Selatan / Makassar & Gowa)
        $areaCodes = [
            '737101','737102','737103','737104','737105', // Makassar-ish
            '737201','737202','737203','737204',          // Gowa-ish
            '730101','730102'                             // variasi lainnya
        ];

        // pastikan ada desa untuk assign, buat 3 desa contoh jika tidak ada
        if (Desa::count() === 0) {
            Desa::create(['nama' => 'Tamalanrea', 'potensi' => null, 'jumlah_penduduk_cached' => 0, 'luas_m2' => 500000]);
            Desa::create(['nama' => 'Tallo', 'potensi' => null, 'jumlah_penduduk_cached' => 0, 'luas_m2' => 420000]);
            Desa::create(['nama' => 'Sungguminasa', 'potensi' => null, 'jumlah_penduduk_cached' => 0, 'luas_m2' => 380000]);
        }

        $desas = Desa::all();

        // truncate tables with FK handling
        DB::statement('PRAGMA foreign_keys = OFF'); // sqlite-friendly
        DB::table('anggotas')->truncate();
        DB::table('kepala_keluargas')->truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        // cache existing NIKs in memory to ensure uniqueness quickly
        $existingNiks = [];

        // helper to generate NIK 16-digit
        $generateNik = function(string $area6, \DateTime $dob, int $serial4, string $gender) {
            $d = (int)$dob->format('d');
            if ($gender === 'P') $d += 40; // konvensi NIK: hari +40 untuk perempuan
            $day = str_pad((string)$d, 2, '0', STR_PAD_LEFT);
            $mm = $dob->format('m');
            $yy = $dob->format('y');
            $datePart = $day . $mm . $yy; // 6 chars
            $serial = str_pad((string)$serial4, 4, '0', STR_PAD_LEFT);
            return $area6 . $datePart . $serial; // total 6+6+4 = 16
        };

        // seeding loop
        for ($i = 0; $i < $TOTAL_KK; $i++) {
            // pick random desa and area code
            $desa = $desas->random();
            $area6 = $faker->randomElement($areaCodes);

            // kepala keluarga: must be male
            $dobKK = $faker->dateTimeBetween('-70 years', '-18 years');
            $genderKK = 'L';

            // generate unique nik for head
            $tries = 0;
            do {
                $serial = $faker->numberBetween(1, 9999);
                $nikKK = $generateNik($area6, $dobKK, $serial, $genderKK);
                $tries++;
                if ($tries > 40) { // fallback: change area or serial strategy
                    $area6 = $faker->randomElement($areaCodes);
                    $serial = $faker->unique()->numberBetween(1000, 9999);
                    $nikKK = $generateNik($area6, $dobKK, $serial, $genderKK);
                }
            } while (in_array($nikKK, $existingNiks) || KepalaKeluarga::where('nik', $nikKK)->exists());

            $existingNiks[] = $nikKK;

            // create kepala keluarga record
            $kk = KepalaKeluarga::create([
                'nik' => $nikKK,
                'nama' => strtoupper($faker->name('male')),
                'phone' => $faker->phoneNumber(),
                'alamat' => $faker->streetAddress() . ' ' . $faker->city(),
                'rt' => str_pad((string)$faker->numberBetween(1,30), 2, '0', STR_PAD_LEFT),
                'rw' => str_pad((string)$faker->numberBetween(1,40), 2, '0', STR_PAD_LEFT),
                'desa_id' => $desa->id,
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'updated_at' => now(),
            ]);

            // prepare anggota list in-memory first to control order before inserting
            $anggotaList = [];

            // decide if there is a wife (max 1). let's set prob 70%
            if ($faker->boolean(70)) {
                // wife's DOB: usually similar age but can be slightly younger
                $dobIstri = $faker->dateTimeBetween($dobKK->format('Y-m-d') . ' -15 years', $dobKK->format('Y-m-d') . ' +5 years');
                $genderIstri = 'P';

                // unique nik for wife
                $triesI = 0;
                do {
                    $serialI = $faker->numberBetween(1, 9999);
                    $nikIstri = $generateNik($area6, $dobIstri, $serialI, $genderIstri);
                    $triesI++;
                    if ($triesI > 40) {
                        $serialI = $faker->unique()->numberBetween(1000,9999);
                        $nikIstri = $generateNik($area6, $dobIstri, $serialI, $genderIstri);
                    }
                } while (in_array($nikIstri, $existingNiks) || Anggota::where('nik', $nikIstri)->exists());

                $existingNiks[] = $nikIstri;

                $anggotaList[] = [
                    'nik' => $nikIstri,
                    'nama' => strtoupper($faker->name('female')),
                    'jenis_kelamin' => 'P',
                    'status_keluarga' => 'ISTRI',
                    'tempat_lahir' => strtoupper($faker->city()),
                    'tanggal_lahir' => $dobIstri->format('Y-m-d'),
                    'hubungan' => null,
                ];
            }

            // create N anak (0..5)
            $numAnak = $faker->numberBetween(0, 5);
            $anakTemp = [];
            for ($a = 0; $a < $numAnak; $a++) {
                $dobA = $faker->dateTimeBetween('-30 years', 'now'); // children could be adult, but ok
                $genderA = $faker->randomElement(['L','P']);

                // produce unique nik for child
                $triesA = 0;
                do {
                    $serialA = $faker->numberBetween(1, 9999);
                    $nikA = $generateNik($area6, $dobA, $serialA, $genderA);
                    $triesA++;
                    if ($triesA > 40) {
                        $serialA = $faker->unique()->numberBetween(1000,9999);
                        $nikA = $generateNik($area6, $dobA, $serialA, $genderA);
                    }
                } while (in_array($nikA, $existingNiks) || Anggota::where('nik', $nikA)->exists());

                $existingNiks[] = $nikA;

                $anakTemp[] = [
                    'nik' => $nikA,
                    'nama' => strtoupper($faker->name($genderA == 'P' ? 'female' : null)),
                    'jenis_kelamin' => $genderA,
                    'status_keluarga' => 'ANAK',
                    'tempat_lahir' => strtoupper($faker->city()),
                    'tanggal_lahir' => $dobA->format('Y-m-d'),
                ];
            }

            // sort anak by tanggal_lahir ascending (tua -> muda)
            usort($anakTemp, function($x, $y){
                return strcmp($x['tanggal_lahir'], $y['tanggal_lahir']);
            });

            // append anak to anggotaList in sorted order
            foreach ($anakTemp as $an) $anggotaList[] = $an;

            // some "keluarga lain" members (0..2)
            $numLain = $faker->numberBetween(0, 2);
            for ($b = 0; $b < $numLain; $b++) {
                $dobL = $faker->dateTimeBetween('-70 years', 'now');
                $genderL = $faker->randomElement(['L','P']);
                // unique nik
                $triesL = 0;
                do {
                    $serialL = $faker->numberBetween(1, 9999);
                    $nikL = $generateNik($area6, $dobL, $serialL, $genderL);
                    $triesL++;
                    if ($triesL > 40) {
                        $serialL = $faker->unique()->numberBetween(1000,9999);
                        $nikL = $generateNik($area6, $dobL, $serialL, $genderL);
                    }
                } while (in_array($nikL, $existingNiks) || Anggota::where('nik', $nikL)->exists());

                $existingNiks[] = $nikL;

                $anggotaList[] = [
                    'nik' => $nikL,
                    'nama' => strtoupper($faker->name($genderL == 'P' ? 'female' : null)),
                    'jenis_kelamin' => $genderL,
                    'status_keluarga' => 'KELUARGA LAIN',
                    'tempat_lahir' => strtoupper($faker->city()),
                    'tanggal_lahir' => $dobL->format('Y-m-d'),
                ];
            }

            // Insert anggota in the specified order: istri -> anak(sorted) -> keluarga lain
            $insertedCount = 0;
            foreach ($anggotaList as $aData) {
                Anggota::create(array_merge($aData, [
                    'kepala_keluarga_id' => $kk->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $insertedCount++;
            }

            // update desa cached count
            if ($desa) {
                Desa::where('id', $desa->id)->increment('jumlah_penduduk_cached', 1 + $insertedCount);
            }
        } // end KK loop

        $this->command->info("Selesai membuat {$TOTAL_KK} kepala keluarga dengan aturan: kepala laki-laki, maksimal 1 istri (perempuan), urutan anggota: istri->anak(sorted)->keluarga lain.");
    }
}
