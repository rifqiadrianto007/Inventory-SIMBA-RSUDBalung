<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('detail_penerimaan', function (Blueprint $table) {
            $table->id('id_detail_penerimaan');
            $table->unsignedBigInteger('id_penerimaan');
            $table->unsignedBigInteger('id_item');
            $table->unsignedBigInteger('id_category');
            $table->decimal('volume', 15, 2);
            $table->unsignedBigInteger('id_satuan');
            $table->decimal('harga', 15, 2);
            $table->boolean('is_layak')->default(true);
            $table->timestamps();

            $table->foreign('id_penerimaan')->references('id_penerimaan')->on('penerimaan');
            $table->foreign('id_item')->references('id_item')->on('item');
            $table->foreign('id_category')->references('id_category')->on('category');
            $table->foreign('id_satuan')->references('id_satuan')->on('satuan');
        });
    }

    public function down(): void {
        Schema::dropIfExists('detail_penerimaan');
    }
};
