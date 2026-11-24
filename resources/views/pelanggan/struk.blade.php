@extends ('layouts.app')

@section ('title', 'Struk Pembayaran Listrik — VOLTPAY')

@section ('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div class="flex items-center justify-between print:hidden">
            <a
                href="{{ route('pelanggan.tagihan') }}"
                class="text-xs font-medium text-zinc-600 hover:text-zinc-950 transition-colors"
            >
                ← Kembali ke Daftar Tagihan
            </a>
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-lg bg-zinc-950 px-4 py-2 text-xs font-medium text-white hover:bg-zinc-800 transition-colors"
            >
                Cetak / Simpan PDF
            </button>
        </div>

        <div
            class="bg-white border border-zinc-200 rounded-xl p-6 sm:p-8 space-y-6 print:border-zinc-400 print:shadow-none"
        >
            <div class="flex items-start justify-between border-b border-zinc-200 pb-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400">VOLTPAY</p>
                    <h1 class="text-lg font-semibold text-zinc-950 mt-0.5">Struk Bukti Pembayaran Listrik</h1>
                    <p class="text-xs text-zinc-500 mt-1">
                        No. Referensi:
                        <span class="font-mono text-zinc-800"
                            >VP-{{ str_pad($pembayaran->id_pembayaran, 6, '0', STR_PAD_LEFT) }}</span
                        >
                    </p>
                </div>
                <x-status-badge status="lunas" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1">
                    <p class="text-zinc-400">ID Pelanggan / Nomor KWH</p>
                    <p class="font-semibold text-zinc-950 tabular-nums">{{ $pelanggan->nomor_kwh }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-400">Nama Pelanggan</p>
                    <p class="font-semibold text-zinc-950">{{ $pelanggan->nama_pelanggan }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-400">Golongan Tarif / Daya</p>
                    <p class="font-medium text-zinc-800 tabular-nums">
                        {{ number_format($pelanggan->tarif->daya) }} VA (Rp {{ number_format($tagihan->tarif_per_kwh, 2, ',', '.') }}/kWh)
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-400">Periode Tagihan</p>
                    <p class="font-medium text-zinc-800">
                        {{
                            \Carbon\Carbon::create($tagihan->tahun, $tagihan->bulan, 1)->translatedFormat(
                                'F Y',
                            )
                        }}
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-400">Tanggal Pembayaran</p>
                    <p class="font-medium text-zinc-800 tabular-nums">
                        {{
                            \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->translatedFormat(
                                'd F Y',
                            )
                        }}
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-400">Kanal / Petugas</p>
                    <p class="font-medium text-zinc-800">
                        @if ($pembayaran->channel_pembayaran === 'midtrans')
                            Pembayaran Online (Midtrans)
                        @else
                            {{
                                $pembayaran->user?->nama_admin ??
                                    'Loket Resmi VOLTPAY'
                            }}
                        @endif
                    </p>
                </div>
                @if ($pembayaran->midtrans_order_id)
                    <div class="space-y-1 sm:col-span-2">
                        <p class="text-zinc-400">ID Transaksi Midtrans</p>
                        <p class="font-mono text-zinc-800">{{ $pembayaran->midtrans_order_id }}</p>
                    </div>
                @endif
            </div>

            @if ($tagihan->penggunaan)
                <div
                    class="rounded-lg bg-zinc-50 border border-zinc-200/80 px-4 py-3 flex items-center justify-between text-xs"
                >
                    <span class="text-zinc-500">Stand Meter</span>
                    <span class="font-medium text-zinc-900 tabular-nums">
                        {{ number_format($tagihan->penggunaan->meter_awal) }} → {{ number_format($tagihan->penggunaan->meter_akhir) }} ({{ number_format($tagihan->jumlah_meter) }} kWh)
                    </span>
                </div>
            @endif

            <div class="border-t border-zinc-200 pt-4 space-y-2.5 text-sm">
                <div class="flex justify-between text-zinc-600">
                    <span>Tagihan Pokok Listrik</span>
                    <span class="tabular-nums">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-zinc-600">
                    <span>Denda Keterlambatan</span>
                    <span class="tabular-nums">Rp {{ number_format($tagihan->denda, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-zinc-600">
                    <span>{{
                        $pembayaran->channel_pembayaran === 'midtrans'
                            ? 'Biaya Admin Layanan'
                            : 'Biaya Admin Loket'
                    }}</span>
                    <span class="tabular-nums">Rp {{ number_format($pembayaran->biaya_admin, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-zinc-200 pt-3 font-semibold text-base text-zinc-950">
                    <span>Total Dibayar</span>
                    <span class="tabular-nums">Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}</span>
                </div>
            </div>

            <p class="text-[11px] text-zinc-400 text-center pt-2">Struk ini merupakan bukti pembayaran sah yang diterbitkan secara elektronik oleh sistem VOLTPAY.</p>
        </div>
    </div>
@endsection
