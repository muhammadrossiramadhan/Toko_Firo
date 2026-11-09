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
        Schema::table('daftar_item', function (Blueprint $table) {
            if (!Schema::hasColumn('daftar_item', 'link_shopee')) {
                $table->string('link_shopee', 255)->nullable();
            }
            if (!Schema::hasColumn('daftar_item', 'deskripsi_produk')) {
                $table->text('deskripsi_produk')->nullable()->after('link_shopee');
            }
            if (!Schema::hasColumn('daftar_item', 'gambar_url')) {
                $table->string('gambar_url', 255)->nullable()->after('deskripsi_produk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_item', function (Blueprint $table) {
            $table->dropColumn(['link_shopee', 'deskripsi_produk', 'gambar_url']);
        });
    }
};

