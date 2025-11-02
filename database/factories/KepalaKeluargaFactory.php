<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Desa;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KepalaKeluarga>
 */
class KepalaKeluargaFactory extends Factory
{
    public function definition()
    {
        // Faker dengan locale Indonesia
        $faker = \Faker\Factory::create('id_ID');

        // pick a desa id if exists, else null
        $desa = Desa::inRandomOrder()->first();

        // random tanggal lahir kepala keluarga (between 18 and 70 yo)
        $dob = $faker->dateTimeBetween('-70 years', '-18 years');

        // random gender for kepala keluarga (but often male)
        $gender = $faker->randomElement(['L', 'P']);

        return [
            'nik' => null, // akan diisi di seeder supaya unik
            'nama' => strtoupper($faker->name($gender == 'P' ? 'female' : null)),
            'phone' => $faker->phoneNumber(),
            'alamat' => $faker->streetAddress() . ' ' . $faker->city(),
            'rt' => str_pad((string) $faker->numberBetween(1, 30), 2, '0', STR_PAD_LEFT),
            'rw' => str_pad((string) $faker->numberBetween(1, 40), 2, '0', STR_PAD_LEFT),
            'kelurahan_id' => null,
            'kecamatan_id' => null,
            'provinsi_id' => null,
            'desa_id' => $desa ? $desa->id : null,
            'created_at' => now(),
            'updated_at' => now(),
            // we also pass dob & gender via state in seeder
        ];
    }
}
