<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_anggotas', function (Blueprint $table) {
    $table->id();
    $table->string('nik')->unique();
    $table->string('nama');
    $table->enum('jenis_kelamin', ['L','P']);
    $table->string('status_keluarga');
    $table->string('tempat_lahir')->nullable();
    $table->date('tanggal_lahir')->nullable();

    $table->unsignedBigInteger('kepala_keluarga_id');
    $table->foreign('kepala_keluarga_id')->references('id')->on('kepala_keluargas');

    // Menambahkan kolom untuk menyimpan data JSON
    $table->text('data_json')->nullable();  // Pastikan kolom ini ada!

    // Status approval
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('pending_anggotas');
    }
};
