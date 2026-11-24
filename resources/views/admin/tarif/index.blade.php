@extends ('layouts.app')

@section ('title', 'Golongan Tarif — VOLTPAY')

@section ('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header
            title="Golongan Tarif Listrik"
            subtitle="Pengaturan daya VA, tarif dasar per kWh, dan log perubahan tarif"
        >
            <x-slot:actions>
                <a
                    href="{{ route('tarif.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Tarif
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-flash-message />

        <!-- Tabel Daftar Tarif -->
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
                                Golongan Daya
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Tarif / kWh
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pelanggan Aktif
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($tarifs as $index => $tarif)
                            <tr class="hover:bg-zinc-50/70 transition-colors">
                                <td class="px-5 py-3.5 whitespace-nowrap text-zinc-400 tabular-nums">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-semibold text-zinc-950 tabular-nums">
                                    {{ number_format($tarif->daya) }} VA
                                </td>
                                <td
                                    class="px-5 py-3.5 whitespace-nowrap text-right font-medium text-zinc-900 tabular-nums"
                                >
                                    Rp {{ number_format($tarif->tarif_per_kwh, 2) }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-600 tabular-nums">
                                    {{ number_format($tarif->pelanggans_count) }} pelanggan
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <a
                                            href="{{ route('tarif.show', $tarif->id_tarif) }}"
                                            title="Riwayat perubahan tarif"
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
                                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </a>
                                        <a
                                            href="{{ route('tarif.edit', $tarif->id_tarif) }}"
                                            title="Edit tarif"
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
                                        @if ($tarif->pelanggans_count === 0)
                                            <form
                                                action="{{ route('tarif.destroy', $tarif->id_tarif) }}"
                                                method="POST"
                                                data-confirm-title="Hapus Data Penggunaan"
                                                data-confirm="Yakin ingin menghapus golongan tarif {{ number_format($tarif->daya) }} VA?"
                                                data-confirm-text="Ya, Hapus Data"
                                                class="inline"
                                            >
                                                @csrf
                                                @method ('DELETE')
                                                <button
                                                    type="submit"
                                                    title="Hapus tarif"
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
                                <td colspan="5" class="px-5 py-8 text-center text-xs text-zinc-400">
                                    Belum ada data golongan tarif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Log Riwayat Perubahan Tarif Terbaru -->
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-200">
                <h2 class="text-sm font-semibold text-zinc-950">Log Perubahan Tarif Terbaru</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Perubahan tarif hanya berlaku untuk tagihan baru; snapshot tagihan lama tetap terjaga</p>
            </div>

            @if ($recentHistories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50/70">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                                >
                                    Waktu
                                </th>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                                >
                                    Golongan Daya
                                </th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                                >
                                    Tarif Lama
                                </th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                                >
                                    Tarif Baru
                                </th>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                                >
                                    Petugas
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 bg-white">
                            @foreach ($recentHistories as $history)
                                <tr class="hover:bg-zinc-50/70 transition-colors">
                                    <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-500 tabular-nums">
                                        {{ $history->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap font-medium text-zinc-900 tabular-nums">
                                        {{ number_format($history->tarif->daya) }} VA
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-500 tabular-nums">
                                        Rp {{ number_format($history->tarif_lama, 2) }}
                                    </td>
                                    <td
                                        class="px-5 py-3.5 whitespace-nowrap text-right font-semibold text-zinc-950 tabular-nums"
                                    >
                                        Rp {{ number_format($history->tarif_baru, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-600">
                                        {{ $history->user->nama_admin ?? 'Sistem' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-5 py-8 text-center text-xs text-zinc-400">Belum ada riwayat perubahan tarif.</p>
            @endif
        </div>
    </div>
@endsection
