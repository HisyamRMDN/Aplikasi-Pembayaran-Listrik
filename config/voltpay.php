<?php

return [
    // Tanggal jatuh tempo pada bulan berikutnya setelah periode pemakaian (default: tanggal 20)
    'hari_jatuh_tempo' => env('VOLTPAY_HARI_JATUH_TEMPO', 20),

    // Denda keterlambatan flat sekali per tagihan (dalam Rupiah)
    'denda_keterlambatan' => env('VOLTPAY_DENDA_KETERLAMBATAN', 10000),

    // Biaya admin default loket pembayaran
    'biaya_admin_default' => env('VOLTPAY_BIAYA_ADMIN', 2500),
];
