<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('kepala_keluargas', function (Blueprint $table) {
            $table->index('nik', 'kk_nik_idx');
            $table->index('nama', 'kk_nama_idx');
        });
    }

    public function down()
    {
        Schema::table('kepala_keluargas', function (Blueprint $table) {
            $table->dropIndex('kk_nik_idx');
            $table->dropIndex('kk_nama_idx');
        });
    }
};
