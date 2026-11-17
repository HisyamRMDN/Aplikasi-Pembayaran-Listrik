<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->enum('status', ['berhasil', 'batal'])->default('berhasil')->after('total_bayar');
            $table->timestamp('dibatalkan_pada')->nullable()->after('status');
            $table->string('alasan_batal')->nullable()->after('dibatalkan_pada');
            $table->unsignedBigInteger('id_user_batal')->nullable()->after('alasan_batal');
            $table->foreign('id_user_batal')->references('id_user')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropForeign(['id_user_batal']);
            $table->dropColumn(['status', 'dibatalkan_pada', 'alasan_batal', 'id_user_batal']);
        });
    }
};
