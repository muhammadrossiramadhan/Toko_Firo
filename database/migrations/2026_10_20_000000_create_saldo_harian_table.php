<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('saldo_harian')) {
            Schema::create('saldo_harian', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->decimal('saldo_awal', 15, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('saldo_harian');
    }
};
