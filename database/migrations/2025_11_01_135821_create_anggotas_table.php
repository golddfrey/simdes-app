<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kepala_keluarga_id')->constrained('kepala_keluargas')->onDelete('cascade');
            $table->string('nik', 32)->nullable()->unique(); // NIK anggota
            $table->string('nama'); // Nama anggota
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable(); // Jenis kelamin
            $table->string('status_keluarga')->nullable(); // Status keluarga (suami, istri, anak, dll)
            $table->string('tempat_lahir')->nullable(); // Tempat lahir
            $table->date('tanggal_lahir')->nullable(); // Tanggal lahir
            $table->string('hubungan')->nullable(); // Hubungan dengan kepala keluarga
            $table->timestamps();

            // Index untuk memudahkan pencarian berdasarkan kepala keluarga
            $table->index(['kepala_keluarga_id'], 'anggota_kk_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('anggotas');
    }
};
