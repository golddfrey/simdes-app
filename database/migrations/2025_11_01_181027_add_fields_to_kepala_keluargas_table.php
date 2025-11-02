<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pastikan tabel ada dulu
        if (Schema::hasTable('kepala_keluargas')) {
            Schema::table('kepala_keluargas', function (Blueprint $table) {
                // Tambah kolom nik jika belum ada
                if (! Schema::hasColumn('kepala_keluargas', 'nik')) {
                    $table->string('nik', 32)->unique()->after('id');
                }

                // Nama biasanya sudah ada; tambahkan phone & alamat bila belum
                if (! Schema::hasColumn('kepala_keluargas', 'phone')) {
                    $table->string('phone')->nullable()->after('nama');
                }

                if (! Schema::hasColumn('kepala_keluargas', 'alamat')) {
                    $table->text('alamat')->nullable()->after('phone');
                }

                // Contoh menambahkan desa_id jika ingin relasi ke tabel desas
                if (! Schema::hasColumn('kepala_keluargas', 'desa_id')) {
                    $table->unsignedBigInteger('desa_id')->nullable()->after('alamat');
                    // Jangan buat foreign key otomatis di sini jika belum yakin struktur tabel desas.
                    // Jika tabel desas sudah ada dan ingin FK, uncomment baris berikut:
                    // $table->foreign('desa_id')->references('id')->on('desas')->onDelete('set null');
                }
            });
        } else {
            // Jika tabel belum ada (jarang), buat tabel minimal
            Schema::create('kepala_keluargas', function (Blueprint $table) {
                $table->id();
                $table->string('nik', 32)->unique();
                $table->string('nama');
                $table->string('phone')->nullable();
                $table->text('alamat')->nullable();
                $table->unsignedBigInteger('desa_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('kepala_keluargas')) {
            Schema::table('kepala_keluargas', function (Blueprint $table) {
                // Hati-hati saat drop, hanya drop bila benar-benar ingin rollback
                if (Schema::hasColumn('kepala_keluargas', 'desa_id')) {
                    $table->dropColumn('desa_id');
                }
                if (Schema::hasColumn('kepala_keluargas', 'alamat')) {
                    $table->dropColumn('alamat');
                }
                if (Schema::hasColumn('kepala_keluargas', 'phone')) {
                    $table->dropColumn('phone');
                }
                if (Schema::hasColumn('kepala_keluargas', 'nik')) {
                    $table->dropColumn('nik');
                }
            });
        }
    }
};
