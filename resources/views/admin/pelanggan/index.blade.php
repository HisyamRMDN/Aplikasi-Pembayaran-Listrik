@extends('layouts.app')

@section('title', 'Kelola Pelanggan — VOLTPAY')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">Data Pelanggan</h1>
                <p class="text-sm text-zinc-500 mt-1">Daftar meteran terdaftar dan status aktivasi akun pelanggan</p>
            </div>
            <a href="{{ route('pelanggan.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Pelanggan
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div
                class="flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-xs sm:text-sm text-emerald-900">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="flex items-center gap-2.5 rounded-xl border border-red-200 bg-red-50/80 px-4 py-3 text-xs sm:text-sm text-red-900">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Search Bar -->
        <form method="GET" action="{{ route('pelanggan.index') }}" class="flex flex-col sm:flex-row gap-2.5">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                    fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama pelanggan atau nomor KWH..."
                    class="w-full rounded-lg border border-zinc-200 bg-white pl-10 pr-3.5 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-950 focus:outline-none">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                    class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-800 hover:bg-zinc-50 hover:border-zinc-300 transition-colors cursor-pointer">
                    Cari
                </button>
                @if (request('search'))
                    <a href="{{ route('pelanggan.index') }}"
                        class="rounded-lg px-3 py-2 text-xs font-medium text-zinc-500 hover:text-zinc-900 transition-colors">
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
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                No</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Pelanggan</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Akun / Username</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Nomor KWH</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Daya & Tarif</th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Alamat</th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($pelanggans as $index => $pelanggan)
                            @php
                                ($hasHistory = $pelanggan->penggunaans_count > 0) or $pelanggan->tagihans_count > 0;
                            @endphp
                            <tr class="hover:bg-zinc-50/70 transition-colors">
                                <td class="px-5 py-3.5 whitespace-nowrap text-zinc-400 tabular-nums">
                                    {{ $pelanggans->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-medium text-zinc-900">
                                    {{ $pelanggan->nama_pelanggan }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($pelanggan->username)
                                        <span class="text-zinc-700">{{ $pelanggan->username }}</span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Belum Aktivasi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs text-zinc-700">
                                    {{ $pelanggan->nomor_kwh }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="font-medium text-zinc-900">{{ number_format($pelanggan->tarif->daya) }}
                                        VA</span>
                                    <span class="block text-xs text-zinc-400 tabular-nums">Rp
                                        {{ number_format($pelanggan->tarif->tarif_per_kwh, 2) }}/kWh</span>
                                </td>
                                <td class="px-5 py-3.5 text-zinc-600 max-w-xs truncate">
                                    {{ $pelanggan->alamat }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center justify-end gap-1">
                                        <a href="{{ route('pelanggan.show', $pelanggan->id_pelanggan) }}"
                                            title="Detail pelanggan"
                                            class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('pelanggan.edit', $pelanggan->id_pelanggan) }}"
                                            title="Edit pelanggan"
                                            class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>

                                        @if ($hasHistory)
                                            <span
                                                title="Tidak dapat dihapus karena sudah memiliki riwayat penggunaan atau tagihan"
                                                class="p-1.5 rounded-lg text-zinc-300 cursor-not-allowed inline-flex items-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    stroke-width="1.75" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </span>
                                        @else
                                            <form method="POST"
                                                action="{{ route('pelanggan.destroy', $pelanggan->id_pelanggan) }}"
                                                class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus pelanggan"
                                                    class="p-1.5 rounded-lg text-zinc-500 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="1.75" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
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
                                    Tidak ada data pelanggan ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pelanggans->hasPages())
                <div class="px-5 py-3.5 border-t border-zinc-200 bg-zinc-50/40">
                    {{ $pelanggans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
