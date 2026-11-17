@extends('layouts.app')

@section('title', 'Tagihan Listrik — VOLTPAY')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <x-page-header title="Tagihan Listrik" subtitle="Daftar tagihan listrik pascabayar dan status pelunasan Anda">
            <x-slot:actions>
                <a href="{{ route('pelanggan.dashboard') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-50 hover:border-zinc-300 transition-colors">
                    ← Ringkasan Akun
                </a>
                <a href="{{ route('pelanggan.penggunaan') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-50 hover:border-zinc-300 transition-colors">
                    Riwayat Penggunaan
                </a>
            </x-slot:actions>
        </x-page-header>

        <!-- Metric Grid Ringkasan Tagihan -->
        @if ($tagihans->count() > 0)
            @php
                $jumlahLunas = $tagihans->where('status', 'lunas')->count();
                $tagihanTunggakan = $tagihans->where('status', 'belum_lunas');
                $jumlahBelumLunas = $tagihanTunggakan->count();
                $nominalBelumLunas = $tagihanTunggakan->sum('total_tagihan');
            @endphp
            <div
                class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-zinc-200 border border-zinc-200 rounded-xl overflow-hidden">
                <div class="bg-white p-5">
                    <span class="text-xs font-medium text-zinc-500">Total Tagihan Tercatat</span>
                    <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                        {{ $tagihans->count() }} <span class="text-sm font-normal text-zinc-400">tagihan</span>
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">Seluruh periode penagihan</p>
                </div>

                <div class="bg-white p-5">
                    <span class="text-xs font-medium text-zinc-500">Sudah Lunas</span>
                    <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                        {{ $jumlahLunas }} <span class="text-sm font-normal text-zinc-400">tagihan</span>
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">Pembayaran terverifikasi</p>
                </div>

                <div class="bg-white p-5">
                    <span class="text-xs font-medium text-zinc-500">Belum Lunas ({{ $jumlahBelumLunas }})</span>
                    <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                        Rp {{ number_format($nominalBelumLunas, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">
                        {{ $jumlahBelumLunas > 0 ? 'Total pokok tagihan tertunggak' : 'Tidak ada tunggakan aktif' }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Tabel Daftar Tagihan -->
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            @if ($tagihans->count() > 0)
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
                                    Tarif / kWh</th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Total Tagihan</th>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Status</th>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Tanggal Terbit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 bg-white">
                            @foreach ($tagihans as $tagihan)
                                @php
                                    $tarifBerlaku =
                                        $tagihan->tarif_per_kwh ?? $tagihan->pelanggan->tarif->tarif_per_kwh;
                                @endphp
                                <tr
                                    class="hover:bg-zinc-50/70 transition-colors {{ $tagihan->status === 'belum_lunas' ? 'bg-amber-50/30' : '' }}">
                                    <td class="px-5 py-3.5 whitespace-nowrap font-medium text-zinc-900">
                                        {{ \Carbon\Carbon::create()->month((int) $tagihan->bulan)->translatedFormat('F') }}
                                        {{ $tagihan->tahun }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-600 tabular-nums">
                                        {{ number_format($tagihan->jumlah_meter) }} kWh
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-500 tabular-nums">
                                        Rp {{ number_format($tarifBerlaku, 2) }}
                                    </td>
                                    <td
                                        class="px-5 py-3.5 whitespace-nowrap text-right font-semibold text-zinc-950 tabular-nums">
                                        Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <x-status-badge :status="$tagihan->status" />
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-500 tabular-nums">
                                        {{ $tagihan->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-12 text-center">
                    <p class="text-sm font-medium text-zinc-900">Belum Ada Tagihan</p>
                    <p class="text-xs text-zinc-400 mt-1">Anda belum memiliki riwayat tagihan listrik.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
