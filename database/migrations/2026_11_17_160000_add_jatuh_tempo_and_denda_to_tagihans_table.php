<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->date('tanggal_jatuh_tempo')->nullable()->after('total_tagihan');
            $table->decimal('denda', 12, 2)->default(0)->after('tanggal_jatuh_tempo');
        });

        // Isi otomatis tanggal_jatuh_tempo untuk data tagihan yang sudah ada
        $hariJatuhTempo = (int) config('voltpay.hari_jatuh_tempo', 20);
        $tagihans = DB::table('tagihans')->select('id_tagihan', 'bulan', 'tahun')->get();

        foreach ($tagihans as $tagihan) {
            $jatuhTempo = Carbon::create((int) $tagihan->tahun, (int) $tagihan->bulan, 1)
                ->addMonthNoOverflow()
                ->day($hariJatuhTempo)
                ->toDateString();

            DB::table('tagihans')
                ->where('id_tagihan', $tagihan->id_tagihan)
                ->update(['tanggal_jatuh_tempo' => $jatuhTempo]);
        }
    }

    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_jatuh_tempo', 'denda']);
        });
    }
};
