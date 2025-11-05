<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\KepalaKeluarga;
use App\Models\Anggota;
use App\Models\Desa;

class KepalaKeluargaSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');

        $TOTAL_KK = 10; // jumlah total KK (termasuk 1 KK khusus untuk login uji coba)

        // Area code representatif (Sulsel: Makassar & Gowa – contoh)
        $areaCodes = [
            '737101','737102','737103','737104','737105', // Makassar
            '737201','737202','737203','737204',          // Gowa
            '730101','730102'                             // variasi lain
        ];

        // Pastikan ada desa
        if (Desa::count() === 0) {
            Desa::create(['nama' => 'Tamalanrea',    'potensi' => null, 'jumlah_penduduk_cached' => 0, 'luas_m2' => 500000]);
            Desa::create(['nama' => 'Tallo',         'potensi' => null, 'jumlah_penduduk_cached' => 0, 'luas_m2' => 420000]);
            Desa::create(['nama' => 'Sungguminasa',  'potensi' => null, 'jumlah_penduduk_cached' => 0, 'luas_m2' => 380000]);
        }

        $desas = Desa::all();

        // Reset data (SQLite-friendly)
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('anggotas')->truncate();
        DB::table('kepala_keluargas')->truncate();
        // Jangan truncate users seluruhnya agar admin/custom user lain tidak hilang – kita upsert saja.
        DB::statement('PRAGMA foreign_keys = ON');

        // Cache nik agar unik
        $existingNiks = [];

        // Helper generator NIK 16 digit
        $generateNik = function (string $area6, \DateTime $dob, int $serial4, string $gender) {
            $d = (int) $dob->format('d');
            if ($gender === 'P') $d += 40; // aturan NIK: +40 untuk perempuan
            $day = str_pad((string) $d, 2, '0', STR_PAD_LEFT);
            $mm  = $dob->format('m');
            $yy  = $dob->format('y');
            $datePart = $day.$mm.$yy;          // 6 char
            $serial   = str_pad((string)$serial4, 4, '0', STR_PAD_LEFT);
            return $area6.$datePart.$serial;   // total 16
        };

        // ==============
        // 1) BUAT KK KHUSUS UNTUK UJI LOGIN
        // ==============
        $desaSample = $desas->random();
        $nikSample  = '3201010101010001'; // tetap 16 digit mudah diingat
        $existingNiks[] = $nikSample;

        $kkSample = KepalaKeluarga::create([
            'nik'        => $nikSample,
            'nama'       => 'SAMPEL KEPALA KELUARGA',
            'phone'      => '081234567890',
            'alamat'     => 'JL. CONTOH NO. 1',
            'rt'         => '01',
            'rw'         => '01',
            'desa_id'    => $desaSample->id,
            'created_at' => now()->subMonths(2),
            'updated_at' => now(),
        ]);

        // Buat user untuk KK sample (login memakai NIK; email dibuat unik)
        User::updateOrCreate(
            ['kepala_keluarga_id' => $kkSample->id, 'role' => 'kepala_keluarga'],
            [
                'name'                => $kkSample->nama,
                'email'               => $nikSample.'@kk.local',
                'password'            => 'password', // akan di-hash oleh mutator User
                'meta'                => ['seed' => true],
            ]
        );

        // Tambahkan istri + 2 anak untuk contoh
        $this->insertAnggotaBerurutan(
            $kkSample->id,
            $desaSample,
            $existingNiks,
            $generateNik,
            $faker,
            withIstri: true,
            jumlahAnak: 2,
            jumlahLain: 0
        );

        // Update cache jumlah penduduk desa sample (1 kk + anggota yang dimasukkan di atas)
        // hitung anggota yang barusan ditambahkan
        $anggotaSampleCount = Anggota::where('kepala_keluarga_id', $kkSample->id)->count();
        Desa::where('id', $desaSample->id)->increment('jumlah_penduduk_cached', 1 + $anggotaSampleCount);

        // ==============
        // 2) SISANYA: SEED KK RANDOM
        // ==============
        for ($i = 1; $i < $TOTAL_KK; $i++) {
            $desa  = $desas->random();
            $area6 = $faker->randomElement($areaCodes);

            // KK pria
            $dobKK    = $faker->dateTimeBetween('-70 years', '-18 years');
            $genderKK = 'L';

            // Nik unik
            $tries = 0;
            do {
                $serial = $faker->numberBetween(1, 9999);
                $nikKK  = $generateNik($area6, $dobKK, $serial, $genderKK);
                $tries++;
                if ($tries > 40) {
                    $area6 = $faker->randomElement($areaCodes);
                    $serial = $faker->unique()->numberBetween(1000, 9999);
                    $nikKK  = $generateNik($area6, $dobKK, $serial, $genderKK);
                }
            } while (in_array($nikKK, $existingNiks) || KepalaKeluarga::where('nik', $nikKK)->exists());

            $existingNiks[] = $nikKK;

            $kk = KepalaKeluarga::create([
                'nik'        => $nikKK,
                'nama'       => strtoupper($faker->name('male')),
                'phone'      => $faker->phoneNumber(),
                'alamat'     => $faker->streetAddress().' '.$faker->city(),
                'rt'         => str_pad((string)$faker->numberBetween(1,30), 2, '0', STR_PAD_LEFT),
                'rw'         => str_pad((string)$faker->numberBetween(1,40), 2, '0', STR_PAD_LEFT),
                'desa_id'    => $desa->id,
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'updated_at' => now(),
            ]);

            // Buat user utk KK ini, gunakan email unik berbasis NIK
            User::updateOrCreate(
                ['kepala_keluarga_id' => $kk->id, 'role' => 'kepala_keluarga'],
                [
                    'name'     => $kk->nama,
                    'email'    => $nikKK.'@kk.local',
                    'password' => Str::random(10), // acak (bukan untuk testing), KK real akan diatur admin
                    'meta'     => ['seed' => true],
                ]
            );

            // Tambahkan anggota (istri probabilitas 70%, anak 0..5, keluarga lain 0..2)
            $withIstri   = $faker->boolean(70);
            $jumlahAnak  = $faker->numberBetween(0, 5);
            $jumlahLain  = $faker->numberBetween(0, 2);

            $this->insertAnggotaBerurutan(
                $kk->id,
                $desa,
                $existingNiks,
                $generateNik,
                $faker,
                withIstri: $withIstri,
                jumlahAnak: $jumlahAnak,
                jumlahLain: $jumlahLain
            );

            // Update cache penduduk desa (1 kk + semua anggota yang baru dibuat)
            $newAnggota = Anggota::where('kepala_keluarga_id', $kk->id)->count();
            Desa::where('id', $desa->id)->increment('jumlah_penduduk_cached', 1 + $newAnggota);
        }

        // ==============
        // 3) PASTIKAN ADMIN ADA
        // ==============
        User::updateOrCreate(
            ['email' => 'admin@simdes.local'],
            [
                'name'                 => 'Administrator',
                'password'             => 'admin123', // di-hash oleh mutator User
                'role'                 => 'admin',
                'kepala_keluarga_id'   => null,
                'meta'                 => ['seed' => true],
            ]
        );

        $this->command->warn('');
        $this->command->info('Seeder selesai:');
        $this->command->line('- Kepala Keluarga total   : '.$TOTAL_KK);
        $this->command->line('- KK sample for login     : NIK 3201010101010001, password: password');
        $this->command->line('- Admin                   : admin@simdes.local / admin123');
        $this->command->warn('Catatan: email untuk setiap KK di-set ke <nik>@kk.local agar unik.');
    }

    /**
     * Buat anggota keluarga berurutan: (opsional) istri -> anak (sorted) -> keluarga lain.
     */
    private function insertAnggotaBerurutan(
        int $kepalaKeluargaId,
        Desa $desa,
        array &$existingNiks,
        \Closure $generateNik,
        \Faker\Generator $faker,
        bool $withIstri = true,
        int $jumlahAnak = 0,
        int $jumlahLain = 0
    ): void {
        // Ambil 6 digit acak untuk area, gunakan variasi kecil dari desa agar tetap "masuk akal"
        $area6 = $faker->randomElement(['737101','737102','737103','737201','737202','730101']);

        $list = [];

        // Istri (opsional)
        if ($withIstri) {
            $dobIstri    = $faker->dateTimeBetween('-55 years', '-18 years');
            $genderIstri = 'P';
            $nikIstri    = $this->uniqueNik($existingNiks, $generateNik, $faker, $area6, $dobIstri, $genderIstri);

            $list[] = [
                'nik'             => $nikIstri,
                'nama'            => strtoupper($faker->name('female')),
                'jenis_kelamin'   => 'P',
                'status_keluarga' => 'ISTRI',
                'tempat_lahir'    => strtoupper($faker->city()),
                'tanggal_lahir'   => $dobIstri->format('Y-m-d'),
            ];
        }

        // Anak
        $anakTemp = [];
        for ($i = 0; $i < $jumlahAnak; $i++) {
            $dobA    = $faker->dateTimeBetween('-30 years', 'now');
            $genderA = $faker->randomElement(['L','P']);
            $nikA    = $this->uniqueNik($existingNiks, $generateNik, $faker, $area6, $dobA, $genderA);

            $anakTemp[] = [
                'nik'             => $nikA,
                'nama'            => strtoupper($faker->name($genderA === 'P' ? 'female' : null)),
                'jenis_kelamin'   => $genderA,
                'status_keluarga' => 'ANAK',
                'tempat_lahir'    => strtoupper($faker->city()),
                'tanggal_lahir'   => $dobA->format('Y-m-d'),
            ];
        }

        // Sort anak dari tua -> muda
        usort($anakTemp, fn($x,$y) => strcmp($x['tanggal_lahir'], $y['tanggal_lahir']));
        foreach ($anakTemp as $row) $list[] = $row;

        // Keluarga lain
        for ($j = 0; $j < $jumlahLain; $j++) {
            $dobL    = $faker->dateTimeBetween('-70 years', 'now');
            $genderL = $faker->randomElement(['L','P']);
            $nikL    = $this->uniqueNik($existingNiks, $generateNik, $faker, $area6, $dobL, $genderL);

            $list[] = [
                'nik'             => $nikL,
                'nama'            => strtoupper($faker->name($genderL === 'P' ? 'female' : null)),
                'jenis_kelamin'   => $genderL,
                'status_keluarga' => 'KELUARGA LAIN',
                'tempat_lahir'    => strtoupper($faker->city()),
                'tanggal_lahir'   => $dobL->format('Y-m-d'),
            ];
        }

        // Insert sesuai urutan
        foreach ($list as $data) {
            Anggota::create($data + [
                'kepala_keluarga_id' => $kepalaKeluargaId,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }

    /**
     * Membuat NIK unik terhadap memori & DB anggota.
     */
    private function uniqueNik(array &$existing, \Closure $gen, \Faker\Generator $faker, string $area6, \DateTime $dob, string $gender): string
    {
        $tries = 0;
        do {
            $serial = $faker->numberBetween(1, 9999);
            $nik    = $gen($area6, $dob, $serial, $gender);
            $tries++;
            if ($tries > 40) {
                $serial = $faker->unique()->numberBetween(1000, 9999);
                $nik    = $gen($area6, $dob, $serial, $gender);
            }
        } while (in_array($nik, $existing) || Anggota::where('nik', $nik)->exists() || KepalaKeluarga::where('nik', $nik)->exists());

        $existing[] = $nik;
        return $nik;
    }
}
