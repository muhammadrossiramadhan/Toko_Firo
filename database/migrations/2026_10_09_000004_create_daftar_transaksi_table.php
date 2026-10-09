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
        Schema::create('daftar_transaksi', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->string('kode_item', 20);
            $table->integer('jumlah_item')->default(0);
            $table->integer('item_keluar')->default(0);
            $table->integer('item_masuk')->default(0);
            $table->integer('retur_penjualan')->default(0);
            $table->integer('retur_pembelian')->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->string('kode_penjualan', 20)->nullable();
            $table->string('kode_pembelian', 20)->nullable();
            $table->dateTime('tanggal_log')->useCurrent();

            $table->foreign('kode_item')->references('kode_item')->on('daftar_item');
            $table->foreign('kode_penjualan')->references('kode_transaksi')->on('daftar_penjualan');
            $table->foreign('kode_pembelian')->references('kode_transaksi')->on('daftar_pembelian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_transaksi');
    }
};
