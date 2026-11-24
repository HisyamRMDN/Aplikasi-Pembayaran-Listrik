@extends ('layouts.app')

@section ('title', 'Kelola Pembayaran — VOLTPAY')

@section ('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">Riwayat Pembayaran</h1>
                <p class="text-sm text-zinc-500 mt-1">Catatan transaksi pelunasan tagihan listrik pelanggan</p>
            </div>
            <a
                href="{{ route('pembayaran.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors self-start sm:self-auto"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Proses Pembayaran
            </a>
        </div>

        <x-flash-message />

        <!-- Metric Grid Ringkas -->
        <div
            class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-zinc-200 border border-zinc-200 rounded-xl overflow-hidden"
        >
            <div class="bg-white p-5">
                <span class="text-xs font-medium text-zinc-500">Transaksi Hari Ini</span>
                <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                    {{ number_format($jumlahPembayaranHariIni) }}
                </p>
                <p class="mt-1 text-xs text-zinc-400">Pembayaran tervalidasi hari ini</p>
            </div>

            <div class="bg-white p-5">
                <span class="text-xs font-medium text-zinc-500">Nominal Masuk Hari Ini</span>
                <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                    Rp {{ number_format($totalNominalHariIni, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-zinc-400">Termasuk biaya administrasi</p>
            </div>

            <div class="bg-white p-5">
                <span class="text-xs font-medium text-zinc-500">Total Seluruh Transaksi</span>
                <p class="mt-3 text-2xl font-bold tracking-tight text-zinc-950 tabular-nums">
                    {{ number_format($pembayarans->total()) }}
                </p>
                <p class="mt-1 text-xs text-zinc-400">Akumulasi pembayaran lunas</p>
            </div>
        </div>

        <!-- Search & Filter -->
        <form method="GET" action="{{ route('pembayaran.index') }}" class="flex flex-col sm:flex-row gap-2.5">
            <div class="relative flex-1">
                <svg
                    class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.75"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                    />
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama pelanggan..."
                    class="w-full rounded-lg border border-zinc-200 bg-white pl-10 pr-3.5 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-950 focus:outline-none"
                />
            </div>
            <div>
                <input
                    type="date"
                    name="tanggal"
                    value="{{ request('tanggal') }}"
                    class="w-full sm:w-auto rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm text-zinc-700 focus:border-zinc-950 focus:outline-none"
                />
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-50 hover:border-zinc-300 transition-colors cursor-pointer"
                >
                    Filter
                </button>
                @if (request('search') || request('tanggal'))
                    <a
                        href="{{ route('pembayaran.index') }}"
                        class="rounded-lg px-3 py-2 text-xs font-medium text-zinc-500 hover:text-zinc-900 transition-colors"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50/70">
                        <tr>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                No
                            </th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pelanggan
                            </th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Bulan Bayar
                            </th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Tanggal Bayar
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Biaya Admin
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Total Bayar
                            </th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Petugas
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($pembayarans as $index => $pembayaran)
                            <tr class="hover:bg-zinc-50/70 transition-colors">
                                <td class="px-5 py-3.5 whitespace-nowrap text-zinc-400 tabular-nums">
                                    {{ $pembayarans->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="font-medium text-zinc-900">
                                        {{ $pembayaran->pelanggan->nama_pelanggan }}
                                    </div>
                                    <div class="font-mono text-xs text-zinc-400 mt-0.5">
                                        {{ $pembayaran->pelanggan->nomor_kwh }}
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-zinc-700">
                                    {{ $pembayaran->bulan_bayar }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-zinc-600 tabular-nums">
                                    {{
                                        date(
                                            'd/m/Y',
                                            strtotime($pembayaran->tanggal_pembayaran),
                                        )
                                    }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-500 tabular-nums">
                                    Rp {{ number_format($pembayaran->biaya_admin, 0, ',', '.') }}
                                </td>
                                <td
                                    class="px-5 py-3.5 whitespace-nowrap text-right font-semibold text-zinc-950 tabular-nums"
                                >
                                    Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-600">
                                    {{
                                        $pembayaran->user->nama_admin ??
                                            ($pembayaran->user->name ?? 'N/A')
                                    }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <a
                                            href="{{ route('pembayaran.show', $pembayaran->id_pembayaran) }}"
                                            title="Detail & struk"
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
                                        <a
                                            href="{{ route('pembayaran.edit', $pembayaran->id_pembayaran) }}"
                                            title="Edit pembayaran"
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
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                                                />
                                            </svg>
                                        </a>
                                        <form
                                            action="{{ route('pembayaran.destroy', $pembayaran->id_pembayaran) }}"
                                            data-confirm-title="Batalkan Pembayaran"
                                            data-confirm="Tindakan ini akan mengembalikan status tagihan menjadi Belum Lunas. Masukkan alasan pembatalan:"
                                            data-confirm-text="Ya, Batalkan"
                                            data-confirm-prompt="alasan"
                                            data-confirm-placeholder="Contoh: Salah input tagihan pelanggan..."
                                            method="POST"
                                            class="inline"
                                        >
                                            @csrf
                                            @method ('DELETE')
                                            <input type="hidden" name="alasan" value="" />
                                            <button
                                                type="submit"
                                                title="Batalkan pembayaran"
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
                                                        d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"
                                                    />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-xs text-zinc-400">
                                    Tidak ada data pembayaran ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pembayarans->hasPages())
                <div class="px-5 py-3.5 border-t border-zinc-200 bg-zinc-50/40">{{ $pembayarans->links() }}</div>
            @endif
        </div>
    </div>
@endsection
