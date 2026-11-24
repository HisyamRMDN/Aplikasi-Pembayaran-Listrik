@extends('layouts.app')

@section('title', 'Detail Tarif — VOLTPAY')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header title="Golongan Daya {{ number_format($tarif->daya) }} VA"
            subtitle="Detail tarif berlaku dan riwayat penyesuaian tarif per kWh">
            <x-slot:actions>
                <a href="{{ route('tarif.index') }}"
                    class="rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">
                    ← Daftar Tarif
                </a>
                <a href="{{ route('tarif.edit', $tarif->id_tarif) }}"
                    class="rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors">
                    Edit Tarif
                </a>
            </x-slot:actions>
        </x-page-header>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-zinc-200 border border-zinc-200 rounded-xl overflow-hidden">
            <div class="bg-white p-5">
                <span class="text-xs font-medium text-zinc-500">Tarif Aktif Saat Ini</span>
                <p class="mt-2 text-2xl font-bold text-zinc-950 tabular-nums">
                    Rp {{ number_format($tarif->tarif_per_kwh, 2) }} <span class="text-xs font-normal text-zinc-400">/
                        kWh</span>
                </p>
            </div>
            <div class="bg-white p-5">
                <span class="text-xs font-medium text-zinc-500">Pelanggan Terdaftar</span>
                <p class="mt-2 text-2xl font-bold text-zinc-950 tabular-nums">
                    {{ number_format($tarif->pelanggans_count) }} <span
                        class="text-xs font-normal text-zinc-400">pelanggan</span>
                </p>
            </div>
        </div>

        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-200">
                <h2 class="text-sm font-semibold text-zinc-950">Riwayat Perubahan Tarif ({{ number_format($tarif->daya) }}
                    VA)</h2>
            </div>

            @if ($tarif->histories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50/70">
                            <tr>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Waktu Perubahan</th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Tarif Lama</th>
                                <th
                                    class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Tarif Baru</th>
                                <th
                                    class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                    Diubah Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 bg-white">
                            @foreach ($tarif->histories as $history)
                                <tr class="hover:bg-zinc-50/70 transition-colors">
                                    <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-600 tabular-nums">
                                        {{ $history->created_at->format('d F Y, H:i') }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-right text-zinc-500 tabular-nums">
                                        Rp {{ number_format($history->tarif_lama, 2) }}
                                    </td>
                                    <td
                                        class="px-5 py-3.5 whitespace-nowrap text-right font-semibold text-zinc-950 tabular-nums">
                                        Rp {{ number_format($history->tarif_baru, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-xs text-zinc-700">
                                        {{ $history->user->nama_admin ?? 'Sistem' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="px-5 py-8 text-center text-xs text-zinc-400">Belum pernah ada perubahan tarif pada golongan daya
                    ini.</p>
            @endif
        </div>
    </div>
@endsection
