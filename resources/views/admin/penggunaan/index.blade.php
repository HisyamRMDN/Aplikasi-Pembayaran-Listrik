@extends ('layouts.app')

@section ('title', 'Kelola Penggunaan')

@section ('content')
    @php
        $field =
            'rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none';
        $namaBulan = fn($i) => \Carbon\Carbon::create(2000, $i, 1)->locale('id')->translatedFormat('F');
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header title="Kelola Penggunaan" subtitle="Daftar pemakaian listrik pelanggan per periode.">
            <x-slot:actions>
                <a
                    href="{{ route('penggunaan.create') }}"
                    class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800"
                >
                    Tambah Penggunaan
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-flash-message />

        <!-- Filter -->
        <form
            method="GET"
            action="{{ route('penggunaan.index') }}"
            class="mb-5 flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-4 sm:flex-row"
        >
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama pelanggan atau nomor KWH"
                class="{{ $field }} flex-1 placeholder:text-zinc-400"
            />

            <select name="bulan" class="{{ $field }}">
                <option value="">Semua bulan</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                        {{ $namaBulan($i) }}
                    </option>
                @endfor
            </select>

            <select name="tahun" class="{{ $field }} tabular-nums">
                <option value="">Semua tahun</option>
                @for ($i = (int) date('Y'); $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800"
                >
                    Cari
                </button>
                @if (request()->hasAny(['search', 'bulan', 'tahun']))
                    <a
                        href="{{ route('penggunaan.index') }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
                    >
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
                            <th class="px-5 py-3 text-right">Meter Awal</th>
                            <th class="px-5 py-3 text-right">Meter Akhir</th>
                            <th class="px-5 py-3 text-right">Pemakaian</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 text-sm">
                        @forelse ($penggunaans as $index => $penggunaan)
                            <tr class="hover:bg-zinc-50">
                                <td class="whitespace-nowrap px-5 py-3.5 tabular-nums text-zinc-500">
                                    {{ $penggunaans->firstItem() + $index }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 font-medium text-zinc-900">
                                    {{ $penggunaan->pelanggan->nama_pelanggan }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 font-mono text-zinc-600">
                                    {{ $penggunaan->pelanggan->nomor_kwh }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-zinc-900">
                                    {{ $namaBulan($penggunaan->bulan) }}
                                    <span class="tabular-nums">{{ $penggunaan->tahun }}</span>
                                </td>
                                <td
                                    class="whitespace-nowrap px-5 py-3.5 text-right font-mono tabular-nums text-zinc-600"
                                >
                                    {{ number_format($penggunaan->meter_awal) }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-5 py-3.5 text-right font-mono tabular-nums text-zinc-600"
                                >
                                    {{ number_format($penggunaan->meter_akhir) }}
                                </td>
                                <td
                                    class="whitespace-nowrap px-5 py-3.5 text-right font-mono font-semibold tabular-nums text-zinc-950"
                                >
                                    {{ number_format($penggunaan->jumlah_meter) }} kWh
                                </td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-4 text-sm">
                                        <a
                                            href="{{ route('penggunaan.show', $penggunaan->id_penggunaan) }}"
                                            class="font-medium text-zinc-900 hover:underline"
                                            >Detail</a
                                        >
                                        @if ($penggunaan->terkunci())
                                            <span class="text-xs text-zinc-400">Terkunci (lunas)</span>
                                        @else
                                            <a
                                                href="{{ route('penggunaan.edit', $penggunaan->id_penggunaan) }}"
                                                class="font-medium text-zinc-600 hover:text-zinc-950 hover:underline"
                                                >Edit</a
                                            >
                                            <form
                                                method="POST"
                                                action="{{ route('penggunaan.destroy', $penggunaan->id_penggunaan) }}"
                                                data-confirm-title="Hapus Data Penggunaan"
                                                data-confirm="Hapus data penggunaan ini? Tagihan terkait juga akan terhapus."
                                                data-confirm-text="Ya, Hapus Data"
                                            >
                                                @csrf
                                                @method ('DELETE')
                                                <button
                                                    type="submit"
                                                    class="font-medium text-red-600 hover:underline cursor-pointer"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-sm text-zinc-500">
                                    Tidak ada data penggunaan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($penggunaans->hasPages())
                <div class="border-t border-zinc-200 px-5 py-4">{{ $penggunaans->links() }}</div>
            @endif
        </div>
    </div>
@endsection
