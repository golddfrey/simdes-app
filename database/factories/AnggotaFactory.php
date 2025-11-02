<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Anggota>
 */
class AnggotaFactory extends Factory
{
    public function definition()
    {
        $faker = \Faker\Factory::create('id_ID');

        $gender = $faker->randomElement(['L', 'P']);
        $dob = $faker->dateTimeBetween('-60 years', '-0 years'); // wide range for children/adults

        return [
            'kepala_keluarga_id' => null, // assign di seeder
            'nik' => null, // optional: akan diisi di seeder (agar unik)
            'nama' => strtoupper($faker->name($gender == 'P' ? 'female' : null)),
            'jenis_kelamin' => $gender,
            'status_keluarga' => $faker->randomElement(['ANAK','ISTRI','SUAMI','KELUARGA LAIN','ORANG TUA']),
            'tempat_lahir' => strtoupper($faker->city()),
            'tanggal_lahir' => $dob->format('Y-m-d'),
            'hubungan' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
