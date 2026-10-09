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
        Schema::create('daftar_item', function (Blueprint $table) {
            $table->string('kode_item', 20)->primary();
            $table->string('nama_item', 100);
            $table->string('jenis', 50)->nullable();
            $table->string('merek', 50)->nullable();
            $table->string('rak', 20)->nullable();
            $table->string('tipe_item', 20)->nullable();
            $table->string('satuan', 20)->nullable();
            $table->decimal('harga_pokok', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->text('keterangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_item');
    }
};
