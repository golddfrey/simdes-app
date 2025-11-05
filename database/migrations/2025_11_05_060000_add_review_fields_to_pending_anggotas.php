<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_anggotas', function (Blueprint $table) {
            // kolom untuk menyimpan siapa yang submit (jika belum ada)
            if (! Schema::hasColumn('pending_anggotas', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->nullable()->after('status')->index();
            }

            // kolom approval / review
            if (! Schema::hasColumn('pending_anggotas', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('submitted_by')->index();
            }
            if (! Schema::hasColumn('pending_anggotas', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('reviewed_by');
            }
            if (! Schema::hasColumn('pending_anggotas', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }
            if (! Schema::hasColumn('pending_anggotas', 'alasan')) {
                $table->text('alasan')->nullable()->after('rejected_at');
            }

            // (opsional) jika kamu ingin FK -> users: jangan aktifkan langsung kecuali tabel users ada & kamu mau FK
            // $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pending_anggotas', function (Blueprint $table) {
            if (Schema::hasColumn('pending_anggotas', 'alasan')) {
                $table->dropColumn('alasan');
            }
            if (Schema::hasColumn('pending_anggotas', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            if (Schema::hasColumn('pending_anggotas', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('pending_anggotas', 'reviewed_by')) {
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('pending_anggotas', 'submitted_by')) {
                $table->dropColumn('submitted_by');
            }
        });
    }
};
