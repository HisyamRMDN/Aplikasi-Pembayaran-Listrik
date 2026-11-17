<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_histories', function (Blueprint $table) {
            $table->id('id_tarif_history');
            $table->unsignedBigInteger('id_tarif');
            $table->decimal('tarif_lama', 10, 2);
            $table->decimal('tarif_baru', 10, 2);
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();

            $table->foreign('id_tarif')->references('id_tarif')->on('tarifs')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_histories');
    }
};
