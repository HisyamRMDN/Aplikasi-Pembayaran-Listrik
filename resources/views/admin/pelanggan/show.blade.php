@extends('layouts.app')

@section('title', 'Detail Pelanggan — VOLTPAY')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">{{ $pelanggan->nama_pelanggan }}</h1>
                <p class="text-xs font-mono text-zinc-500 mt-1">IDPEL / Nomor KWH: {{ $pelanggan->nomor_kwh }}</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('pelanggan.index') }}"
                    class="rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">
                    ← Daftar Pelanggan
                </a>
                <a href="{{ route('pelanggan.edit', $pelanggan->id_pelanggan) }}"
                    class="rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors">
                    Edit Data
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informasi Pelanggan -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-zinc-200 rounded-xl p-6 space-y-5">
                    <h2 class="text-sm font-semibold text-zinc-950 border-b border-zinc-100 pb-3">Profil Meteran</h2>

                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-xs text-zinc-400">Nama Pelanggan</p>
                            <p class="font-medium text-zinc-900 mt-0.5">{{ $pelanggan->nama_pelanggan }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Status Akun / Username</p>
                            <div class="mt-1">
                                @if ($pelanggan->username)
                                    <span class="font-medium text-zinc-900">{{ $pelanggan->username }}</span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Belum Aktivasi Mandiri
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Nomor KWH</p>
                            <p class="font-mono text-zinc-900 mt-0.5">{{ $pelanggan->nomor_kwh }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Golongan Daya & Tarif</p>
                            <p class="font-medium text-zinc-900 mt-0.5">{{ number_format($pelanggan->tarif->daya) }} VA</p>
                            <p class="text-xs text-zinc-500 tabular-nums">Rp
                                {{ number_format($pelanggan->tarif->tarif_per_kwh, 2) }}/kWh</p>
                        </div>

                        <div>
                            <p class="text-xs text-zinc-400">Alamat Pemasangan</p>
                            <p class="text-zinc-700 mt-0.5 leading-relaxed">{{ $pelanggan->alamat }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Penggunaan & Tagihan -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Usage History -->
                <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-zinc-200">
                        <h2 class="text-sm font-semibold text-zinc-950">Riwayat Penggunaan kWh</h2>
                    </div>

                    @if ($pelanggan->penggunaans->count() > 0)
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
                                    @foreach ($pelanggan->penggunaans->sortByDesc('tahun')->sortByDesc('bulan') as $penggunaan)
                                        <tr class="hover:bg-zinc-50/70">
                                            <td class="px-5 py-3 whitespace-nowrap text-zinc-900">
                                                {{ DateTime::createFromFormat('!m', $penggunaan->bulan)->format('F') }}
                                                {{ $penggunaan->tahun }}
                                            </td>
                                            <td class="px-5 py-3 whitespace-nowrap text-right text-zinc-600 tabular-nums">
                                                {{ number_format($penggunaan->meter_awal) }}
                                            </td>
                                            <td class="px-5 py-3 whitespace-nowrap text-right text-zinc-600 tabular-nums">
                                                {{ number_format($penggunaan->meter_akhir) }}
                                            </td>
                                            <td
                                                class="px-5 py-3 whitespace-nowrap text-right font-medium text-zinc-900 tabular-nums">
                                                {{ number_format($penggunaan->jumlah_meter) }} kWh
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-xs text-zinc-400 text-center py-8">Belum ada catatan penggunaan listrik.</p>
                    @endif
                </div>

                <!-- Bills History -->
                <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-zinc-200">
                        <h2 class="text-sm font-semibold text-zinc-950">Riwayat Tagihan</h2>
                    </div>

                    @if ($pelanggan->tagihans->count() > 0)
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
                                    @foreach ($pelanggan->tagihans->sortByDesc('tahun')->sortByDesc('bulan') as $tagihan)
                                        <tr class="hover:bg-zinc-50/70">
                                            <td class="px-5 py-3 whitespace-nowrap text-zinc-900">
                                                {{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }}
                                                {{ $tagihan->tahun }}
                                            </td>
                                            <td class="px-5 py-3 whitespace-nowrap text-right text-zinc-600 tabular-nums">
                                                {{ number_format($tagihan->jumlah_meter) }} kWh
                                            </td>
                                            <td
                                                class="px-5 py-3 whitespace-nowrap text-right font-medium text-zinc-900 tabular-nums">
                                                Rp {{ number_format($tagihan->total_tagihan, 2) }}
                                            </td>
                                            <td class="px-5 py-3 whitespace-nowrap">
                                                @if ($tagihan->status === 'lunas')
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-xs text-zinc-400 text-center py-8">Belum ada riwayat tagihan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
