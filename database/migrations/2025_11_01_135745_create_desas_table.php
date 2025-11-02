<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedBigInteger('kecamatan_id')->nullable();
            $table->bigInteger('luas_m2')->nullable();
            // koordinat sebagai text; nanti bisa ubah ke geometry jika pakai PostGIS
            $table->string('koordinat')->nullable();
            $table->json('potensi')->nullable();
            $table->integer('jumlah_penduduk_cached')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('desas');
    }
};
