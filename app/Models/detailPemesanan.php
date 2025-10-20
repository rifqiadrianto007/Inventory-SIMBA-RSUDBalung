<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('detail_pemesanan', function (Blueprint $table) {
            $table->id('id_detail_pemesanan');
            $table->unsignedBigInteger('id_pemesanan');
            $table->unsignedBigInteger('id_satuan');
            $table->decimal('volume', 15, 2);
            $table->timestamps();

            $table->foreign('id_pemesanan')->references('id_pemesanan')->on('pemesanan');
            $table->foreign('id_satuan')->references('id_satuan')->on('satuan');
        });
    }

    public function down(): void {
        Schema::dropIfExists('detail_pemesanan');
    }
};
