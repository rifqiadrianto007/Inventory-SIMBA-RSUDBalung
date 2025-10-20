<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bast', function (Blueprint $table) {
            $table->id('id_bast');
            $table->string('no_surat');
            $table->unsignedBigInteger('id_penerimaan');
            $table->text('deskripsi')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();

            $table->foreign('id_penerimaan')->references('id_penerimaan')->on('penerimaan');
        });
    }

    public function down(): void {
        Schema::dropIfExists('bast');
    }
};
