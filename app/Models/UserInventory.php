<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_inventory', function (Blueprint $table) {
            $table->id('user_id');
            $table->unsignedBigInteger('sso_user_id')->nullable();
            $table->string('username');
            $table->string('password');
            $table->string('role')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('synced_at')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_inventory');
    }
};
