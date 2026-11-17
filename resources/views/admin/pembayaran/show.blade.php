@extends('layouts.app')

@section('title', 'Detail Pembayaran — VOLTPAY')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">Bukti Pembayaran
                    #{{ $pembayaran->id_pembayaran }}</h1>
                <p class="text-xs text-zinc-500 mt-1">
                    Divalidasi pada {{ date('d F Y', strtotime($pembayaran->tanggal_pembayaran)) }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('pembayaran.index') }}"
                    class="rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">
                    ← Riwayat Pembayaran
                </a>
                @unless ($pembayaran->isBatal())
                    <a href="{{ route('pembayaran.edit', $pembayaran->id_pembayaran) }}"
                        class="rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-50 transition-colors">
                        Edit
                    </a>
                @endunless
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                    </svg>
                    Cetak Struk
                </button>
            </div>
        </div>

        <x-flash-message />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Area Struk / Informasi Utama (Ikut Tercetak saat Print) -->
            <div class="lg:col-span-2 space-y-6 print-section">
                <!-- Informasi Pembayaran -->
                <div class="bg-white border border-zinc-200 rounded-xl p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h2 class="text-sm font-semibold text-zinc-950">Informasi Transaksi</h2>
                        @if ($pembayaran->isBatal())
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-zinc-100 border border-zinc-300 px-2.5 py-0.5 text-xs font-medium text-zinc-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                Dibatalkan
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Lunas
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-5 text-sm">
                        <div>
                            <p class="text-xs text-zinc-400">ID Pembayaran</p>
                            <p class="font-mono font-medium text-zinc-900 mt-0.5">#{{ $pembayaran->id_pembayaran }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Tanggal Pembayaran</p>
                            <p class="font-medium text-zinc-900 mt-0.5">
                                {{ date('d F Y', strtotime($pembayaran->tanggal_pembayaran)) }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Bulan Bayar</p>
                            <p class="font-medium text-zinc-900 mt-0.5">{{ $pembayaran->bulan_bayar }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Petugas Loket</p>
                            <p class="font-medium text-zinc-900 mt-0.5">
                                {{ $pembayaran->user->nama_admin ?? ($pembayaran->user->name ?? 'N/A') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Detail Tagihan & Meter -->
                <div class="bg-white border border-zinc-200 rounded-xl p-6 space-y-5">
                    <h2 class="text-sm font-semibold text-zinc-950 border-b border-zinc-100 pb-3">Rincian Pemakaian &
                        Tagihan</h2>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 text-sm">
                        <div>
                            <p class="text-xs text-zinc-400">ID Tagihan</p>
                            <p class="font-mono font-medium text-zinc-900 mt-0.5">#{{ $pembayaran->tagihan->id_tagihan }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Periode Pemakaian</p>
                            <p class="font-medium text-zinc-900 mt-0.5">
                                {{ DateTime::createFromFormat('!m', $pembayaran->tagihan->bulan)->format('F') }}
                                {{ $pembayaran->tagihan->tahun }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Tarif per kWh</p>
                            <p class="font-medium text-zinc-900 tabular-nums mt-0.5">
                                Rp {{ number_format($pembayaran->tagihan->tarif_per_kwh, 2) }}
                            </p>
                        </div>

                        @if ($pembayaran->tagihan->penggunaan)
                            <div>
                                <p class="text-xs text-zinc-400">Stand Meter Awal</p>
                                <p class="font-medium text-zinc-900 tabular-nums mt-0.5">
                                    {{ number_format($pembayaran->tagihan->penggunaan->meter_awal, 0, ',', '.') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-zinc-400">Stand Meter Akhir</p>
                                <p class="font-medium text-zinc-900 tabular-nums mt-0.5">
                                    {{ number_format($pembayaran->tagihan->penggunaan->meter_akhir, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs text-zinc-400">Total Pemakaian</p>
                            <p class="font-semibold text-zinc-950 tabular-nums mt-0.5">
                                {{ number_format($pembayaran->tagihan->jumlah_meter, 0, ',', '.') }} kWh
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Pelanggan, Ringkasan Nominal & Pembatalan -->
            <div class="space-y-6">
                <!-- Informasi Pelanggan -->
                <div class="bg-white border border-zinc-200 rounded-xl p-6 space-y-4">
                    <h2 class="text-sm font-semibold text-zinc-950 border-b border-zinc-100 pb-3">Data Pelanggan</h2>

                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-zinc-400">Nama Pelanggan</p>
                            <p class="font-medium text-zinc-900 mt-0.5">{{ $pembayaran->pelanggan->nama_pelanggan }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Nomor KWH</p>
                            <p class="font-mono text-zinc-900 mt-0.5">{{ $pembayaran->pelanggan->nomor_kwh }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Golongan Daya</p>
                            <p class="font-medium text-zinc-900 mt-0.5">
                                {{ number_format($pembayaran->pelanggan->tarif->daya) }} VA
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Alamat</p>
                            <p class="text-zinc-600 text-xs leading-relaxed mt-0.5">{{ $pembayaran->pelanggan->alamat }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Pembayaran -->
                <div class="bg-white border border-zinc-200 rounded-xl p-6 space-y-4">
                    <h2 class="text-sm font-semibold text-zinc-950 border-b border-zinc-100 pb-3">Ringkasan Biaya</h2>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Pokok Tagihan</span>
                            <span class="font-medium text-zinc-900 tabular-nums">
                                Rp {{ number_format($pembayaran->tagihan->total_tagihan, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-zinc-500">Biaya Admin</span>
                            <span class="font-medium text-zinc-900 tabular-nums">
                                Rp {{ number_format($pembayaran->biaya_admin, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="border-t border-zinc-200 pt-3 mt-3 flex justify-between items-baseline">
                            <span class="text-sm font-semibold text-zinc-950">Total Bayar</span>
                            <span class="text-xl font-bold text-zinc-950 tabular-nums">
                                Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Pembatalan -->
                @if ($pembayaran->isBatal())
                    <div class="bg-white border border-zinc-200 rounded-xl p-6 space-y-3">
                        <h2 class="text-sm font-semibold text-zinc-950 border-b border-zinc-100 pb-3">Pembayaran Dibatalkan
                        </h2>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-xs text-zinc-400">Alasan</p>
                                <p class="text-zinc-900 mt-0.5">{{ $pembayaran->alasan_batal ?: '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-400">Waktu</p>
                                <p class="text-zinc-900 tabular-nums mt-0.5">
                                    {{ $pembayaran->dibatalkan_pada?->format('d/m/Y H:i') ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-400">Dibatalkan oleh</p>
                                <p class="text-zinc-900 mt-0.5">{{ $pembayaran->userPembatal->nama_admin ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white border border-zinc-200 rounded-xl p-5">
                        <form action="{{ route('pembayaran.destroy', $pembayaran->id_pembayaran) }}" method="POST"
                            class="space-y-3"
                            onsubmit="return confirm('Yakin ingin membatalkan pembayaran ini? Tagihan akan kembali menjadi belum lunas.')">
                            @csrf
                            @method('DELETE')
                            <div>
                                <label for="alasan"
                                    class="block text-[11px] font-semibold uppercase tracking-wider text-zinc-500 mb-1.5">Alasan
                                    Pembatalan</label>
                                <input type="text" id="alasan" name="alasan" maxlength="255" required
                                    placeholder="Contoh: salah input tagihan"
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-950 focus:outline-none">
                            </div>
                            <button type="submit"
                                class="w-full rounded-lg border border-red-200 bg-red-50/60 px-4 py-2 text-xs font-semibold text-red-700 hover:bg-red-100 transition-colors cursor-pointer">
                                Batalkan Pembayaran
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print-section,
            .print-section * {
                visibility: visible;
            }

            .print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
@endsection
