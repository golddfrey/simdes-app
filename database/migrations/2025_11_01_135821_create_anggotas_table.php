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
            $table->string('nik', 32)->nullable()->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('status_keluarga')->nullable(); // suami/istri/anak/dll
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('hubungan')->nullable();
            $table->timestamps();

            $table->index(['kepala_keluarga_id'], 'anggota_kk_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('anggotas');
    }
};
