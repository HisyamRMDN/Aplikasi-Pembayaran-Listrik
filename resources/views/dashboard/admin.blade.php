@extends('layouts.app')

@section('title', 'Dashboard — VOLTPAY')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Header Bar + Aksi Cepat -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">Ringkasan Operasional</h1>
                <p class="text-sm text-zinc-500 mt-1">Monitoring pelanggan, pemakaian kWh, dan status penagihan</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('pelanggan.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-50 hover:border-zinc-300 transition-colors">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Pelanggan Baru
                </a>
                <a href="{{ route('penggunaan.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Catat Meter kWh
                </a>
            </div>
        </div>

        <!-- Banner Peringatan Tagihan Belum Lunas (Minimalis & Actionable) -->
        @if ($tagihanBelumLunas > 0)
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50/70 px-4 py-3.5">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <p class="text-xs sm:text-sm text-amber-950">
                        Terdapat <span class="font-semibold">{{ $tagihanBelumLunas }} tagihan</span> berstatus belum lunas
                        yang menunggu pembayaran.
                    </p>
                </div>
                <a href="{{ route('tagihan.index') }}"
                    class="text-xs font-semibold text-amber-900 hover:text-amber-700 underline underline-offset-4 shrink-0">
                    Tinjau Tagihan →
                </a>
            </div>
        @endif

        <!-- Metric Grid Monokromatik (Tanpa Ikon Pastel Warna-warni) -->
        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-zinc-200 border border-zinc-200 rounded-xl overflow-hidden">
            <!-- Total Pelanggan -->
            <div class="bg-white p-5 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-500">Total Pelanggan</span>
                    <a href="{{ route('pelanggan.index') }}"
                        class="text-xs text-zinc-400 hover:text-zinc-900 transition-colors">
                        Detail →
                    </a>
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-zinc-950 tabular-nums">
                    {{ number_format($totalPelanggan) }}
                </p>
                <p class="mt-1 text-xs text-zinc-400">Meteran terdaftar aktif</p>
            </div>

            <!-- Data Penggunaan -->
            <div class="bg-white p-5 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-500">Catatan Meter</span>
                    <a href="{{ route('penggunaan.index') }}"
                        class="text-xs text-zinc-400 hover:text-zinc-900 transition-colors">
                        Detail →
                    </a>
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-zinc-950 tabular-nums">
                    {{ number_format($totalPenggunaan) }}
                </p>
                <p class="mt-1 text-xs text-zinc-400">Periode pemakaian tercatat</p>
            </div>

            <!-- Total Tagihan -->
            <div class="bg-white p-5 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-500">Total Tagihan</span>
                    <a href="{{ route('tagihan.index') }}"
                        class="text-xs text-zinc-400 hover:text-zinc-900 transition-colors">
                        Detail →
                    </a>
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-zinc-950 tabular-nums">
                    {{ number_format($totalTagihan) }}
                </p>
                <p class="mt-1 text-xs text-zinc-400">{{ $tagihanBelumLunas }} belum lunas</p>
            </div>

            <!-- Total Pembayaran -->
            <div class="bg-white p-5 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-500">Transaksi Lunas</span>
                    <a href="{{ route('pembayaran.index') }}"
                        class="text-xs text-zinc-400 hover:text-zinc-900 transition-colors">
                        Detail →
                    </a>
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-zinc-950 tabular-nums">
                    {{ number_format($totalPembayaran) }}
                </p>
                <p class="mt-1 text-xs text-zinc-400">Pembayaran tervalidasi</p>
            </div>
        </div>

        <!-- Tabel Tagihan Terbaru -->
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-950">Tagihan Terbaru</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">Daftar tagihan listrik pelanggan pada periode terkini</p>
                </div>
                <a href="{{ route('tagihan.index') }}"
                    class="text-xs font-medium text-zinc-600 hover:text-zinc-950 transition-colors">
                    Lihat Semua
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50/70">
                        <tr>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Pelanggan</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Nomor KWH</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Periode</th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Pemakaian</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Status</th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($tagihanTerbaru as $item)
                            <tr class="hover:bg-zinc-50/70 transition-colors">
                                <td class="px-5 py-3.5 whitespace-nowrap font-medium text-zinc-900">
                                    {{ $item->pelanggan->nama_pelanggan ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs text-zinc-600">
                                    {{ $item->pelanggan->nomor_kwh ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-zinc-600">
                                    {{ str_pad($item->bulan, 2, '0', STR_PAD_LEFT) }}/{{ $item->tahun }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right font-medium text-zinc-900 tabular-nums">
                                    {{ number_format($item->jumlah_meter) }} kWh
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($item->status === 'lunas')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Lunas
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Belum Lunas
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-xs font-medium">
                                    @if ($item->status !== 'lunas')
                                        <a href="{{ route('pembayaran.create') }}"
                                            class="inline-flex items-center rounded-md bg-zinc-950 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-zinc-800 transition-colors">
                                            Proses Bayar
                                        </a>
                                    @else
                                        <span class="text-zinc-400">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-xs text-zinc-400">
                                    Belum ada data tagihan tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
