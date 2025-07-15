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
        Schema::create('penggunaans', function (Blueprint $table) {
            $table->id('id_penggunaan');
            $table->unsignedBigInteger('id_pelanggan');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('meter_awal');
            $table->integer('meter_akhir');
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggunaans');
    }
};
