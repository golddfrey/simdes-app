<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kepala_keluargas', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 32)->unique();
            $table->string('nama');
            $table->string('phone')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->unsignedBigInteger('kelurahan_id')->nullable();
            $table->unsignedBigInteger('kecamatan_id')->nullable();
            $table->unsignedBigInteger('provinsi_id')->nullable();
            $table->foreignId('desa_id')->nullable()->constrained('desas')->nullOnDelete();
            $table->timestamps();

            // indeks cepat untuk pencarian by lokasi
            $table->index(['kelurahan_id', 'rt', 'rw'], 'kk_kelurahan_rt_rw_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kepala_keluargas');
    }
};
