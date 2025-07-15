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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id('id_tagihan');
            $table->unsignedBigInteger('id_penggunaan');
            $table->unsignedBigInteger('id_pelanggan');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('jumlah_meter');
            $table->enum('status', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->foreign('id_penggunaan')->references('id_penggunaan')->on('penggunaans');
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
