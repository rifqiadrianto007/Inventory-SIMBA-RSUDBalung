<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_bast', function (Blueprint $table) {
            $table->id('id_detail_bast');
            $table->unsignedBigInteger('id_bast');
            $table->unsignedBigInteger('id_item');
            $table->unsignedBigInteger('id_satuan');
            $table->decimal('volume', 15, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_bast')->references('id_bast')->on('bast');
            $table->foreign('id_item')->references('id_item')->on('item');
            $table->foreign('id_satuan')->references('id_satuan')->on('satuan');
        });
    }
};
