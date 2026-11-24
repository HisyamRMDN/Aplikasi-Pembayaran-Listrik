<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Tarif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function buatTagihanBelumLunas(): array
    {
        $tarif = Tarif::create([
            'daya' => 1300,
            'tarif_per_kwh' => 1444.70,
        ]);

        $pelanggan = Pelanggan::create([
            'username' => 'pelanggan_midtrans',
            'password' => bcrypt('password'),
            'nomor_kwh' => '543210987654',
            'nama_pelanggan' => 'Hisyam Arasy',
            'alamat' => 'Depok, Jawa Barat',
            'id_tarif' => $tarif->id_tarif,
        ]);

        $tagihan = Tagihan::create([
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'bulan' => 12,
            'tahun' => 2026,
            'jumlah_meter' => 100,
            'tarif_per_kwh' => 1444.70,
            'total_tagihan' => 144470,
            'denda' => 0,
            'status' => 'belum_lunas',
        ]);

        return [$pelanggan, $tagihan];
    }

    public function test_webhook_menolak_signature_tidak_valid(): void
    {
        config(['midtrans.signature_key' => 'rahasia-sandbox-test']);

        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => 'VP-1-20261124-INVALID',
            'status_code' => '200',
            'gross_amount' => '146970.00',
            'signature_key' => 'signature-palsu',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
    }

    public function test_webhook_melunasi_tagihan_saat_settlement_valid(): void
    {
        config(['midtrans.signature_key' => 'rahasia-sandbox-test']);

        [$pelanggan, $tagihan] = $this->buatTagihanBelumLunas();

        $orderId = 'VP-' . $tagihan->id_tagihan . '-20261124-OK';
        $statusCode = '200';
        $grossAmount = '146970.00';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . 'rahasia-sandbox-test');

        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'custom_field1' => (string) $tagihan->id_tagihan,
            'custom_field2' => (string) $pelanggan->id_pelanggan,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tagihans', [
            'id_tagihan' => $tagihan->id_tagihan,
            'status' => 'lunas',
        ]);

        $this->assertDatabaseHas('pembayarans', [
            'id_tagihan' => $tagihan->id_tagihan,
            'midtrans_order_id' => $orderId,
            'channel_pembayaran' => 'midtrans',
            'id_user' => null,
            'status' => 'berhasil',
            'bulan_bayar' => 'Desember 2026',
        ]);
    }

    public function test_webhook_idempoten_jika_notifikasi_dikirim_ulang(): void
    {
        config(['midtrans.signature_key' => 'rahasia-sandbox-test']);

        [$pelanggan, $tagihan] = $this->buatTagihanBelumLunas();

        $orderId = 'VP-' . $tagihan->id_tagihan . '-20261124-DUPLIKAT';
        $statusCode = '200';
        $grossAmount = '146970.00';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . 'rahasia-sandbox-test');

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'custom_field1' => (string) $tagihan->id_tagihan,
            'custom_field2' => (string) $pelanggan->id_pelanggan,
        ];

        $this->postJson('/webhooks/midtrans', $payload)->assertStatus(200);
        $this->postJson('/webhooks/midtrans', $payload)->assertStatus(200);

        $this->assertDatabaseCount('pembayarans', 1);
    }
}
