<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('item', function (Blueprint $table) {
            $table->id('id_item');
            $table->string('name');
            $table->unsignedBigInteger('id_category');
            $table->integer('stock_item')->default(0);
            $table->unsignedBigInteger('id_unit');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_category')->references('id_category')->on('category');
            $table->foreign('id_unit')->references('id_satuan')->on('satuan');
        });
    }

    public function down(): void {
        Schema::dropIfExists('item');
    }
};
