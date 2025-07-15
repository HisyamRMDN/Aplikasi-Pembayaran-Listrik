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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('nomor_kwh', 20)->unique();
            $table->string('nama_pelanggan', 100);
            $table->text('alamat');
            $table->unsignedBigInteger('id_tarif');
            $table->foreign('id_tarif')->references('id_tarif')->on('tarifs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
