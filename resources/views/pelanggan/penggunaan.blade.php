@extends('layouts.app')

@section('title', 'Riwayat Penggunaan — VOLTPAY')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <x-page-header title="Riwayat Penggunaan Listrik"
            subtitle="Catatan stand meter dan konsumsi kWh untuk {{ $pelanggan->nama_pelanggan }}">
            <x-slot:actions>
                <a href="{{ route('pelanggan.dashboard') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-50 hover:border-zinc-300 transition-colors">
                    ← Ringkasan Akun
                </a>
                <a href="{{ route('pelanggan.tagihan') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors">
                    Lihat Tagihan
                </a>
            </x-slot:actions>
        </x-page-header>

        <!-- Ringkasan Meteran & Konsumsi -->
        @if ($penggunaan->count() > 0)
            @php
                $totalPemakaian = $penggunaan->sum('jumlah_meter');
                $rataRataPemakaian = $penggunaan->avg('jumlah_meter');
            @endphp
            <div
                class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-zinc-200 border border-zinc-200 rounded-xl overflow-hidden">
                <div class="bg-white p-5">
                    <span class="text-xs font-medium text-zinc-500">Nomor KWH / IDPEL</span>
                    <p class="mt-3 text-2xl font-bold font-mono tracking-tight text-zinc-950">
                        {{ $pelanggan->nomor_kwh }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">
                        {{ $penggunaan->count() }} periode tercatat
                    </p>
                </div>

                <div class="bg-white p-5">
                    <span class="text-xs font-medium text-zinc-500">Total Konsumsi Tercatat</span>
                    <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                        {{ number_format($totalPemakaian) }} <span class="text-sm font-normal text-zinc-400">kWh</span>
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">Akumulasi seluruh periode</p>
                </div>

                <div class="bg-white p-5">
                    <span class="text-xs font-medium text-zinc-500">Rata-Rata per Bulan</span>
                    <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                        {{ number_format($rataRataPemakaian, 1, ',', '.') }} <span
                            class="text-sm font-normal text-zinc-400">kWh</span>
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">Rerata pemakaian bulanan</p>
                </div>
            </div>
        @endif

        <!-- Tabel Riwayat Penggunaan -->
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            @if ($penggunaan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50/70">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    No</th>
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
                            @foreach ($penggunaan as $index => $item)
                                <tr class="hover:bg-zinc-50/70 transition-colors">
                                    <td class="px-5 py-3.5 whitespace-nowrap text-zinc-400 tabular-nums">
                                        {{ $index + 1 }}
                                    </td>
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
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-medium text-zinc-900">Belum Ada Catatan Penggunaan</p>
                    <p class="text-xs text-zinc-400 mt-1">Data stand meter bulanan Anda belum tersedia.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
