<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_anggotas', function (Blueprint $table) {
            // nullable supaya tidak memecah data lama
            $table->unsignedBigInteger('submitted_by')->nullable()->after('status')->index();
            // jika ingin foreign key ke users: (opsional)
            // $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pending_anggotas', function (Blueprint $table) {
            // drop foreign key jika dibuat, lalu kolom
            // $table->dropForeign(['submitted_by']);
            $table->dropColumn('submitted_by');
        });
    }
};
