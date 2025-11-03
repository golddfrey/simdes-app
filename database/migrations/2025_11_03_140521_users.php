<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: This migration creates a simple users table with a "role" column.
     * Roles supported: 'admin', 'kepala_keluarga'.
     * We also include nullable kepala_keluarga_id so a user of role kepala_keluarga
     * can be linked to the kepala_keluargas table (if exists in your app).
     *
     * If your existing kepala_keluargas table name differs, adjust the foreign key or remove it.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Basic identity
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            // Authentication
            $table->string('password');
            $table->rememberToken();

            // Role: 'admin' or 'kepala_keluarga'
            // Using string for simplicity; you can later change to enum if you prefer.
            $table->string('role')->default('kepala_keluarga');

            // Optional link to kepala_keluargas table (assumes that table exists)
            // This allows a user to be associated with a kepala_keluarga record.
            $table->unsignedBigInteger('kepala_keluarga_id')->nullable();

            // Add a small profile JSON field for extra metadata (optional)
            $table->json('meta')->nullable();

            $table->timestamps();

            // Foreign key: only add if table exists in your project
            // If your table name differs or you want to add the FK later, remove this block.
            if (Schema::hasTable('kepala_keluargas')) {
                $table->foreign('kepala_keluarga_id')
                    ->references('id')
                    ->on('kepala_keluargas')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop table (remove foreign key first to be safe)
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'kepala_keluarga_id')) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                // We attempt to drop foreign key if it exists (works in typical setups).
                try {
                    $table->dropForeign(['kepala_keluarga_id']);
                } catch (\Throwable $e) {
                    // ignore if FK doesn't exist
                }
            }
        });

        Schema::dropIfExists('users');
    }
};
