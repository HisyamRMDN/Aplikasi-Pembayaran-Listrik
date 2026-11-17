@extends('layouts.app')

@section('title', 'Ringkasan Akun — VOLTPAY')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Header Bar + Aksi Cepat -->
        <x-page-header title="Selamat datang, {{ $pelanggan->nama_pelanggan }}"
            subtitle="Ringkasan pemakaian listrik dan status tagihan pascabayar Anda">
            <x-slot:actions>
                <a href="{{ route('pelanggan.penggunaan') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-50 hover:border-zinc-300 transition-colors">
                    Riwayat Penggunaan
                </a>
                <a href="{{ route('pelanggan.tagihan') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors">
                    Lihat Semua Tagihan
                </a>
            </x-slot:actions>
        </x-page-header>

        <!-- Banner Peringatan Tagihan Belum Lunas -->
        @if ($tagihanBelumLunas > 0)
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50/70 px-4 py-3.5">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <p class="text-xs sm:text-sm text-amber-950">
                        Anda memiliki <span class="font-semibold">{{ $tagihanBelumLunas }} tagihan</span> yang belum lunas.
                        Silakan segera selesaikan pembayaran melalui loket resmi.
                    </p>
                </div>
                <a href="{{ route('pelanggan.tagihan') }}"
                    class="text-xs font-semibold text-amber-900 hover:text-amber-700 underline underline-offset-4 shrink-0">
                    Rincian Tagihan →
                </a>
            </div>
        @endif

        <!-- Metric Grid Informasi Meteran & Status -->
        @php
            $pemakaianTerakhir = $penggunaan->first();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-zinc-200 border border-zinc-200 rounded-xl overflow-hidden">
            <!-- Identitas Meteran -->
            <div class="bg-white p-5 flex flex-col justify-between">
                <span class="text-xs font-medium text-zinc-500">Nomor KWH / IDPEL</span>
                <p class="mt-3 text-2xl font-bold font-mono tracking-tight text-zinc-950">
                    {{ $pelanggan->nomor_kwh }}
                </p>
                <p class="mt-1 text-xs text-zinc-400">
                    Daya {{ number_format($pelanggan->tarif->daya) }} VA • Rp
                    {{ number_format($pelanggan->tarif->tarif_per_kwh, 2) }}/kWh
                </p>
            </div>

            <!-- Pemakaian Periode Terakhir -->
            <div class="bg-white p-5 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-500">Pemakaian Terakhir</span>
                    <a href="{{ route('pelanggan.penggunaan') }}"
                        class="text-xs text-zinc-400 hover:text-zinc-900 transition-colors">
                        Riwayat →
                    </a>
                </div>
                <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                    {{ $pemakaianTerakhir ? number_format($pemakaianTerakhir->jumlah_meter) . ' kWh' : '0 kWh' }}
                </p>
                <p class="mt-1 text-xs text-zinc-400">
                    @if ($pemakaianTerakhir)
                        Periode
                        {{ \Carbon\Carbon::create()->month((int) $pemakaianTerakhir->bulan)->translatedFormat('F') }}
                        {{ $pemakaianTerakhir->tahun }}
                    @else
                        Belum ada catatan meter
                    @endif
                </p>
            </div>

            <!-- Status Tagihan -->
            <div class="bg-white p-5 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-500">Tagihan Belum Lunas</span>
                    <a href="{{ route('pelanggan.tagihan') }}"
                        class="text-xs text-zinc-400 hover:text-zinc-900 transition-colors">
                        Detail →
                    </a>
                </div>
                <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                    {{ $tagihanBelumLunas }} <span class="text-sm font-normal text-zinc-400">tagihan</span>
                </p>
                <p class="mt-1 text-xs text-zinc-400">
                    {{ $tagihanBelumLunas > 0 ? 'Menunggu pelunasan' : 'Seluruh tagihan sudah lunas' }}
                </p>
            </div>
        </div>

        <!-- Tabel Tagihan Terbaru -->
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-950">Tagihan Terbaru</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Ringkasan tagihan listrik periode terakhir</p>
                </div>
                <a href="{{ route('pelanggan.tagihan') }}"
                    class="text-xs font-medium text-zinc-600 hover:text-zinc-950 transition-colors">
                    Lihat Semua
                </a>
            </div>

            @if ($tagihan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50/70">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Periode</th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Pemakaian</th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Total Tagihan</th>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 bg-white">
                            @foreach ($tagihan as $item)
                                <tr class="hover:bg-zinc-50/70 transition-colors">
                                    <td class="px-5 py-3.5 whitespace-nowrap font-medium text-zinc-900">
                                        {{ \Carbon\Carbon::create()->month((int) $item->bulan)->translatedFormat('F') }}
                                        {{ $item->tahun }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-600 tabular-nums">
                                        {{ number_format($item->jumlah_meter) }} kWh
                                    </td>
                                    <td
                                        class="px-5 py-3.5 whitespace-nowrap text-right font-semibold text-zinc-950 tabular-nums">
                                        Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <x-status-badge :status="$item->status" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-xs text-zinc-400 text-center py-8">Belum ada riwayat tagihan tercatat.</p>
            @endif
        </div>

        <!-- Tabel Penggunaan Terbaru -->
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-950">Catatan Stand Meter Terbaru</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Riwayat pencatatan angka meteran kWh Anda</p>
                </div>
                <a href="{{ route('pelanggan.penggunaan') }}"
                    class="text-xs font-medium text-zinc-600 hover:text-zinc-950 transition-colors">
                    Lihat Semua
                </a>
            </div>

            @if ($penggunaan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50/70">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Periode</th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Meter Awal</th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Meter Akhir</th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Pemakaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 bg-white">
                            @foreach ($penggunaan as $item)
                                <tr class="hover:bg-zinc-50/70 transition-colors">
                                    <td class="px-5 py-3.5 whitespace-nowrap font-medium text-zinc-900">
                                        {{ \Carbon\Carbon::create()->month((int) $item->bulan)->translatedFormat('F') }}
                                        {{ $item->tahun }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-600 tabular-nums">
                                        {{ number_format($item->meter_awal) }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-600 tabular-nums">
                                        {{ number_format($item->meter_akhir) }}
                                    </td>
                                    <td
                                        class="px-5 py-3.5 whitespace-nowrap text-right font-semibold text-zinc-950 tabular-nums">
                                        {{ number_format($item->jumlah_meter) }} kWh
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-xs text-zinc-400 text-center py-8">Belum ada data penggunaan tercatat.</p>
            @endif
        </div>

    </div>
@endsection
