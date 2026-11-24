<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->unsignedBigInteger('id_user')->nullable()->change();
            $table->string('midtrans_order_id', 64)->nullable()->unique()->after('id_tagihan');
            $table->string('channel_pembayaran', 30)->default('loket')->after('total_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['midtrans_order_id', 'channel_pembayaran']);
        });
    }
};
