<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom 'status_item'
     */
    public function up(): void
    {
        Schema::table('detail_pemesanan', function (Blueprint $table) {
            $table->string('status_item')->default('ok');
        });
    }

    /**
     * Rollback perubahan jika dibatalkan
     */
    public function down(): void
    {
        Schema::table('detail_pemesanan', function (Blueprint $table) {
            // Hapus kolom saat rollback
            $table->dropColumn('status_item');
        });
    }
};
