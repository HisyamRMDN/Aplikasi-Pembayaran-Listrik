@extends ('layouts.app')

@section ('title', 'Daftar Tagihan — VOLTPAY')

@section ('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header
            title="Daftar Tagihan Listrik"
            subtitle="Pantau pokok tagihan, tanggal jatuh tempo, status keterlambatan, dan pelunasan"
        >
            <x-slot:actions>
                <a
                    href="{{ route('tagihan.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tagihan Manual
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-flash-message />

        <!-- Filter Pencarian -->
        <div class="bg-white border border-zinc-200 rounded-xl p-4">
            <form method="GET" action="{{ route('tagihan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama pelanggan atau No. KWH..."
                    class="rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-950 focus:outline-none"
                />

                <select
                    name="status"
                    class="rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs text-zinc-900 focus:border-zinc-950 focus:outline-none"
                >
                    <option value="">Semua Status</option>
                    <option value="belum_lunas" {{ request('status') === 'belum_lunas' ? 'selected' : '' }}>
                        Belum Lunas
                    </option>
                    <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>

                <select
                    name="bulan"
                    class="rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs text-zinc-900 focus:border-zinc-950 focus:outline-none"
                >
                    <option value="">Semua Bulan</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (int) request('bulan') === $m ? 'selected' : '' }}>
                            {{
                                \Carbon\Carbon::create()
                                    ->month($m)
                                    ->translatedFormat('F')
                            }}
                        </option>
                    @endfor
                </select>

                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 rounded-lg bg-zinc-900 px-4 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors cursor-pointer"
                    >
                        Filter
                    </button>

                    @if (request()->hasAny(['search', 'status', 'bulan']))
                        <a
                            href="{{ route('tagihan.index') }}"
                            class="rounded-lg border border-zinc-200 px-3 py-2 text-xs font-medium text-zinc-600 hover:bg-zinc-50 transition-colors"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Tagihan -->
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50/70">
                        <tr>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pelanggan
                            </th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Periode
                            </th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Jatuh Tempo
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pemakaian
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pokok & Denda
                            </th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Status
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($tagihans as $tagihan)
                            @php
                                $denda = $tagihan->estimasiDenda();
                            @endphp
                            <tr class="hover:bg-zinc-50/70 transition-colors">
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-zinc-900">{{ $tagihan->pelanggan->nama_pelanggan }}</p>
                                    <p class="text-xs font-mono text-zinc-400">
                                        {{ $tagihan->pelanggan->nomor_kwh }}
                                        @if ($tagihan->isManual())
                                            •
                                            <span class="font-sans text-zinc-500">Manual</span>
                                        @endif
                                    </p>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-zinc-700">
                                    {{
                                        \Carbon\Carbon::create()
                                            ->month((int) $tagihan->bulan)
                                            ->translatedFormat('F')
                                    }} {{ $tagihan->tahun }}
                                </td>
                                <td
                                    class="px-5 py-3.5 whitespace-nowrap text-xs tabular-nums {{ $tagihan->isTerlambat() ? 'text-red-600 font-semibold' : 'text-zinc-600' }}"
                                >
                                    {{
                                        $tagihan->tanggal_jatuh_tempo
                                            ? $tagihan->tanggal_jatuh_tempo->format('d/m/Y')
                                            : '—'
                                    }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-600 tabular-nums">
                                    {{ number_format($tagihan->jumlah_meter) }} kWh
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right tabular-nums">
                                    <p class="font-semibold text-zinc-950">
                                        Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                                    </p>
                                    @if ($denda > 0)
                                        <p class="text-[11px] text-red-600 font-medium">
                                            + Denda Rp {{ number_format($denda, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <x-status-badge :status="$tagihan->status_tampil" />
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        @if (!$tagihan->isLunas())
                                            <a
                                                href="{{ route('pembayaran.create', ['id_tagihan' => $tagihan->id_tagihan]) }}"
                                                class="inline-flex items-center gap-1 rounded-lg bg-zinc-950 px-2.5 py-1 text-[11px] font-medium text-white hover:bg-zinc-800 transition-colors"
                                            >
                                                Proses Bayar
                                            </a>
                                        @endif
                                        <a
                                            href="{{ route('tagihan.show', $tagihan->id_tagihan) }}"
                                            title="Detail tagihan"
                                            class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.75"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                            </svg>
                                        </a>

                                        @if (!$tagihan->isLunas() && $tagihan->isManual())
                                            <a
                                                href="{{ route('tagihan.edit', $tagihan->id_tagihan) }}"
                                                title="Edit tagihan manual"
                                                class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.75"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"
                                                    />
                                                </svg>
                                            </a>
                                            <form
                                                action="{{ route('tagihan.destroy', $tagihan->id_tagihan) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus tagihan manual ini?');"
                                            >
                                                @csrf
                                                @method ('DELETE')
                                                <button
                                                    type="submit"
                                                    title="Hapus tagihan"
                                                    class="p-1.5 rounded-lg text-zinc-500 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        stroke-width="1.75"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                                        />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-xs text-zinc-400">
                                    Tidak ada data tagihan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tagihans->hasPages())
                <div class="px-5 py-3.5 border-t border-zinc-200 bg-zinc-50/50">{{ $tagihans->links() }}</div>
            @endif
        </div>
    </div>
@endsection
