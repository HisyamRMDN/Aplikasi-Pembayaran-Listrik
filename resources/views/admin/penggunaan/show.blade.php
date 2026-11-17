@extends('layouts.app')

@section('title', 'Detail Penggunaan')

@section('content')
    @php
        $namaBulan = fn($i) => \Carbon\Carbon::create(2000, $i, 1)->locale('id')->translatedFormat('F');
        $label = 'text-xs font-semibold uppercase tracking-wider text-zinc-500';
        $tarif = $penggunaan->pelanggan->tarif;
        $tagihan = $penggunaan->tagihans->first();
    @endphp

    <div class="p-6">
        <x-page-header title="Detail Penggunaan"
            subtitle="{{ $namaBulan($penggunaan->bulan) }} {{ $penggunaan->tahun }} · {{ $penggunaan->pelanggan->nama_pelanggan }}">
            <x-slot:actions>
                <a href="{{ route('penggunaan.index') }}"
                    class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                    Kembali
                </a>
                @unless ($penggunaan->terkunci())
                    <a href="{{ route('penggunaan.edit', $penggunaan->id_penggunaan) }}"
                        class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
                        Edit
                    </a>
                @endunless
            </x-slot:actions>
        </x-page-header>

        <x-flash-message />

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <!-- Informasi penggunaan -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-5 text-sm font-semibold text-zinc-950">Informasi Penggunaan</h2>

                <dl class="space-y-5">
                    <div>
                        <dt class="{{ $label }}">Periode</dt>
                        <dd class="mt-1 text-sm text-zinc-900">
                            {{ $namaBulan($penggunaan->bulan) }} <span class="tabular-nums">{{ $penggunaan->tahun }}</span>
                        </dd>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="{{ $label }}">Meter Awal</dt>
                            <dd class="mt-1 font-mono text-lg tabular-nums text-zinc-900">
                                {{ number_format($penggunaan->meter_awal) }}</dd>
                        </div>
                        <div>
                            <dt class="{{ $label }}">Meter Akhir</dt>
                            <dd class="mt-1 font-mono text-lg tabular-nums text-zinc-900">
                                {{ number_format($penggunaan->meter_akhir) }}</dd>
                        </div>
                    </div>

                    <div class="border-t border-zinc-200 pt-5">
                        <dt class="{{ $label }}">Total Pemakaian</dt>
                        <dd class="mt-1 font-mono text-3xl font-semibold tabular-nums text-zinc-950">
                            {{ number_format($penggunaan->jumlah_meter) }}
                            <span class="text-base font-medium text-zinc-500">kWh</span>
                        </dd>
                    </div>
                </dl>
            </section>

            <!-- Informasi pelanggan -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-5 text-sm font-semibold text-zinc-950">Informasi Pelanggan</h2>

                <dl class="space-y-5">
                    <div>
                        <dt class="{{ $label }}">Nama Pelanggan</dt>
                        <dd class="mt-1 text-sm text-zinc-900">{{ $penggunaan->pelanggan->nama_pelanggan }}</dd>
                    </div>
                    <div>
                        <dt class="{{ $label }}">Nomor KWH</dt>
                        <dd class="mt-1 font-mono text-sm text-zinc-900">{{ $penggunaan->pelanggan->nomor_kwh }}</dd>
                    </div>
                    <div>
                        <dt class="{{ $label }}">Golongan Tarif</dt>
                        <dd class="mt-1 text-sm text-zinc-900">
                            <span class="tabular-nums">{{ number_format($tarif->daya) }}</span> VA
                            <span class="text-zinc-500">· Rp <span
                                    class="tabular-nums">{{ number_format($tarif->tarif_per_kwh, 2) }}</span>/kWh</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="{{ $label }}">Alamat</dt>
                        <dd class="mt-1 text-sm text-zinc-900">{{ $penggunaan->pelanggan->alamat }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <!-- Perhitungan -->
        <section class="mt-5 rounded-xl border border-zinc-200 bg-white p-6">
            <h2 class="mb-5 text-sm font-semibold text-zinc-950">Detail Perhitungan</h2>

            <dl class="grid grid-cols-1 gap-5 text-sm md:grid-cols-3">
                <div>
                    <dt class="{{ $label }}">Meter Akhir − Meter Awal</dt>
                    <dd class="mt-1 font-mono tabular-nums text-zinc-900">
                        {{ number_format($penggunaan->meter_akhir) }} − {{ number_format($penggunaan->meter_awal) }}
                    </dd>
                </div>
                <div>
                    <dt class="{{ $label }}">Total Pemakaian</dt>
                    <dd class="mt-1 font-mono font-semibold tabular-nums text-zinc-950">
                        {{ number_format($penggunaan->jumlah_meter) }} kWh</dd>
                </div>
                <div>
                    <dt class="{{ $label }}">Tarif per kWh</dt>
                    <dd class="mt-1 font-mono tabular-nums text-zinc-900">Rp
                        {{ number_format($tagihan->tarif_per_kwh ?? $tarif->tarif_per_kwh, 2) }}</dd>
                </div>
            </dl>

            @if ($tagihan)
                <div class="mt-5 flex items-center justify-between border-t border-zinc-200 pt-5">
                    <span class="{{ $label }}">Total Tagihan</span>
                    <span class="font-mono text-xl font-semibold tabular-nums text-zinc-950">
                        Rp {{ number_format($tagihan->total_tagihan, 2) }}
                    </span>
                </div>
            @endif
        </section>

        <!-- Tagihan terkait -->
        <section class="mt-5 overflow-hidden rounded-xl border border-zinc-200 bg-white">
            <div class="border-b border-zinc-200 px-6 py-4">
                <h2 class="text-sm font-semibold text-zinc-950">Tagihan Terkait</h2>
            </div>

            @if ($penggunaan->tagihans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-zinc-600">
                                <th class="px-6 py-3">Periode</th>
                                <th class="px-6 py-3 text-right">Pemakaian</th>
                                <th class="px-6 py-3 text-right">Total Tagihan</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 text-sm">
                            @foreach ($penggunaan->tagihans as $item)
                                <tr class="hover:bg-zinc-50">
                                    <td class="whitespace-nowrap px-6 py-3.5 text-zinc-900">
                                        {{ $namaBulan($item->bulan) }} <span
                                            class="tabular-nums">{{ $item->tahun }}</span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-3.5 text-right font-mono tabular-nums text-zinc-600">
                                        {{ number_format($item->jumlah_meter) }} kWh
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-3.5 text-right font-mono font-semibold tabular-nums text-zinc-950">
                                        Rp {{ number_format($item->total_tagihan, 2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5">
                                        <x-status-badge :status="$item->status" />
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-right">
                                        <a href="{{ route('tagihan.show', $item->id_tagihan) }}"
                                            class="font-medium text-zinc-900 hover:underline">Lihat detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-6 py-10 text-center text-sm text-zinc-500">Belum ada tagihan untuk penggunaan ini.</p>
            @endif
        </section>
    </div>
@endsection
