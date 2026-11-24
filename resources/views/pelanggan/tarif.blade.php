@extends('layouts.app')

@section('title', 'Informasi Golongan Tarif — VOLTPAY')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header title="Informasi Golongan Tarif Listrik"
            subtitle="Daftar tarif dasar listrik per kWh berdasarkan golongan daya VA" />

        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-zinc-200 text-sm">
                <thead class="bg-zinc-50/70">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                            Golongan Daya</th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                            Tarif per kWh</th>
                        <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                            Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 bg-white">
                    @foreach ($tarifs as $tarif)
                        <tr class="{{ $pelanggan->id_tarif === $tarif->id_tarif ? 'bg-zinc-50/90' : '' }}">
                            <td class="px-5 py-3.5 font-medium text-zinc-900 tabular-nums">
                                {{ number_format($tarif->daya) }} VA
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-zinc-950 tabular-nums">
                                Rp {{ number_format($tarif->tarif_per_kwh, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if ($pelanggan->id_tarif === $tarif->id_tarif)
                                    <span
                                        class="inline-flex items-center rounded-full bg-zinc-900 px-2.5 py-0.5 text-[11px] font-medium text-white">
                                        Golongan Anda
                                    </span>
                                @else
                                    <span class="text-xs text-zinc-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
