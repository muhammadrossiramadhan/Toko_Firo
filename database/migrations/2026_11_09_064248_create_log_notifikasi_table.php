<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_notifikasi', function (Blueprint $table) {
            $table->id('id_log');
            $table->string('tipe_notifikasi', 50)->nullable();
            $table->text('pesan');
            $table->string('status', 20)->default('MENUNGGU');
            $table->timestamp('waktu')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_notifikasi');
    }
};