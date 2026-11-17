@extends('layouts.app')

@section('title', 'Kelola Tagihan')

@section('content')
    @php
        $field =
            'rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none';
        $namaBulan = fn($i) => \Carbon\Carbon::create(2000, $i, 1)->locale('id')->translatedFormat('F');
    @endphp

    <div class="p-6">
        <x-page-header title="Kelola Tagihan" subtitle="Daftar tagihan listrik pelanggan.">
            <x-slot:actions>
                <a href="{{ route('tagihan.create') }}"
                    class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
                    Tambah Tagihan
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-flash-message />

        <!-- Filter -->
        <form method="GET" action="{{ route('tagihan.index') }}"
            class="mb-5 flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-4 sm:flex-row">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama pelanggan atau nomor KWH"
                class="{{ $field }} flex-1 placeholder:text-zinc-400">

            <select name="status" class="{{ $field }}">
                <option value="">Semua status</option>
                <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum lunas</option>
                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>

            <select name="bulan" class="{{ $field }}">
                <option value="">Semua bulan</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ $namaBulan($i) }}
                    </option>
                @endfor
            </select>

            <div class="flex gap-2">
                <button type="submit"
                    class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
                    Cari
                </button>
                @if (request()->hasAny(['search', 'status', 'bulan']))
                    <a href="{{ route('tagihan.index') }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Tabel -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200">
                    <thead class="bg-zinc-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-zinc-600">
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Pelanggan</th>
                            <th class="px-5 py-3">Nomor KWH</th>
                            <th class="px-5 py-3">Periode</th>
                            <th class="px-5 py-3 text-right">Pemakaian</th>
                            <th class="px-5 py-3 text-right">Total Tagihan</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 text-sm">
                        @forelse ($tagihans as $index => $tagihan)
                            <tr class="hover:bg-zinc-50">
                                <td class="whitespace-nowrap px-5 py-3.5 tabular-nums text-zinc-500">
                                    {{ $tagihans->firstItem() + $index }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 font-medium text-zinc-900">
                                    {{ $tagihan->pelanggan->nama_pelanggan }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 font-mono text-zinc-600">
                                    {{ $tagihan->pelanggan->nomor_kwh }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-zinc-900">
                                    {{ $namaBulan($tagihan->bulan) }} <span
                                        class="tabular-nums">{{ $tagihan->tahun }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right font-mono tabular-nums text-zinc-600">
                                    {{ number_format($tagihan->jumlah_meter) }} kWh
                                </td>
                                <td
                                    class="whitespace-nowrap px-5 py-3.5 text-right font-mono font-semibold tabular-nums text-zinc-950">
                                    Rp {{ number_format($tagihan->total_tagihan, 2) }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5">
                                    <x-status-badge :status="$tagihan->status" />
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-4 text-sm">
                                        <a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}"
                                            class="font-medium text-zinc-900 hover:underline">Detail</a>
                                        @if ($tagihan->bisaDiubah())
                                            <a href="{{ route('tagihan.edit', $tagihan->id_tagihan) }}"
                                                class="font-medium text-zinc-600 hover:text-zinc-950 hover:underline">Edit</a>
                                        @endif
                                        @if ($tagihan->status === 'belum_lunas')
                                            <a href="{{ route('pembayaran.create', ['tagihan' => $tagihan->id_tagihan]) }}"
                                                class="font-medium text-zinc-900 underline underline-offset-2">Bayar</a>
                                        @endif
                                        @if ($tagihan->bisaDiubah())
                                            <form method="POST"
                                                action="{{ route('tagihan.destroy', $tagihan->id_tagihan) }}"
                                                onsubmit="return confirm('Hapus tagihan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="font-medium text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-sm text-zinc-500">
                                    Tidak ada data tagihan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tagihans->hasPages())
                <div class="border-t border-zinc-200 px-5 py-4">
                    {{ $tagihans->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
