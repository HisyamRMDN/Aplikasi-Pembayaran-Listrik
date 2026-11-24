<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    private function bootMidtrans(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production', false);
        Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('midtrans.is_3ds', true);
    }

    /**
     * Membuat Snap Token untuk tagihan pelanggan yang sedang login.
     */
    public function createSnapToken(Request $request, string $idTagihan)
    {
        $pelanggan = Auth::guard('pelanggan')->user();

        $tagihan = Tagihan::with('pelanggan.tarif')
            ->where('id_tagihan', $idTagihan)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->firstOrFail();

        if ($tagihan->isLunas()) {
            return response()->json([
                'message' => 'Tagihan periode ini sudah berstatus lunas.',
            ], 422);
        }

        $denda = (float) ($tagihan->denda_berjalan ?? $tagihan->denda ?? 0);
        $biayaAdmin = 2500.0;
        $grossAmount = (int) round($tagihan->total_tagihan + $denda + $biayaAdmin);

        $orderId = 'VP-' . $tagihan->id_tagihan . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));

        $this->bootMidtrans();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $pelanggan->nama_pelanggan,
                'email' => $pelanggan->email ?: ($pelanggan->username . '@voltpay.local'),
                'phone' => $pelanggan->nomor_kwh,
            ],
            'item_details' => [
                [
                    'id' => 'TAGIHAN-' . $tagihan->id_tagihan,
                    'price' => (int) round($tagihan->total_tagihan),
                    'quantity' => 1,
                    'name' => 'Listrik Periode ' . $tagihan->bulan . '/' . $tagihan->tahun,
                ],
                [
                    'id' => 'DENDA',
                    'price' => (int) round($denda),
                    'quantity' => 1,
                    'name' => 'Denda Keterlambatan',
                ],
                [
                    'id' => 'ADMIN',
                    'price' => (int) round($biayaAdmin),
                    'quantity' => 1,
                    'name' => 'Biaya Admin PPOB',
                ],
            ],
            'custom_field1' => (string) $tagihan->id_tagihan,
            'custom_field2' => (string) $pelanggan->id_pelanggan,
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
        ]);
    }

    /**
     * Endpoint Webhook HTTP Notification dari Midtrans.
     */
    public function webhook(Request $request)
    {
        $signatureSecret = trim((string) config('midtrans.signature_key'));
        $payload = $request->all();

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $signatureSecret);

        if (!$signatureSecret || !hash_equals($expectedSignature, $signatureKey)) {
            return response()->json(['message' => 'Invalid signature'], 403);
}

        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');

        $isPaid = ($transactionStatus === 'settlement')
            || ($transactionStatus === 'capture' && $fraudStatus === 'accept');

        if (!$isPaid) {
            return response()->json(['message' => 'Ignored status: ' . $transactionStatus]);
        }

        // Ambil id_tagihan dari custom_field1 atau parse dari order_id (VP-{id_tagihan}-...)
        $idTagihan = $request->input('custom_field1');
        if (!$idTagihan && preg_match('/^VP-(\d+)-/', $orderId, $matches)) {
            $idTagihan = $matches[1];
        }

        if (!$idTagihan) {
            return response()->json(['message' => 'Tagihan reference missing'], 400);
        }

        DB::transaction(function () use ($idTagihan, $orderId, $grossAmount) {
            // Idempotency check jika Midtrans mengirim webhook lebih dari sekali
            if (Pembayaran::where('midtrans_order_id', $orderId)->exists()) {
                return;
            }

            $tagihan = Tagihan::lockForUpdate()->find($idTagihan);
            if (!$tagihan || $tagihan->isLunas()) {
                return;
            }

            $denda = (float) ($tagihan->denda_berjalan ?? $tagihan->denda ?? 0);
            $biayaAdmin = 2500.0;
            $totalBayar = max((float) $grossAmount, (float) ($tagihan->total_tagihan + $denda + $biayaAdmin));

            Pembayaran::create([
            'id_tagihan' => $tagihan->id_tagihan,
            'midtrans_order_id' => $orderId,
            'id_pelanggan' => $tagihan->id_pelanggan,
            'tanggal_pembayaran' => now()->toDateString(),
            'bulan_bayar' => \Carbon\Carbon::createFromDate((int) $tagihan->tahun, (int) $tagihan->bulan, 1)
            ->locale('id')
            ->translatedFormat('F Y'),
            'biaya_admin' => $biayaAdmin,
            'total_bayar' => $totalBayar,
            'channel_pembayaran' => 'midtrans',
            'id_user' => null,
            'status' => 'berhasil', // sesuaikan dengan hasil SHOW COLUMNS (misal 'berhasil' / 'valid')
        ]);

            $tagihan->update([
                'status' => 'lunas',
                'denda' => $denda,
            ]);
        });

        return response()->json(['message' => 'OK']);
    }
}
