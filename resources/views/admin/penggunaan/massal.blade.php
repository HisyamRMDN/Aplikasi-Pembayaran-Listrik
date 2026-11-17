@extends('layouts.app')

@section('title', 'Input Meter Massal — VOLTPAY')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header title="Input Meter Massal"
            subtitle="Catat angka meter akhir puluhan pelanggan sekaligus untuk satu periode penagihan">
            <x-slot:actions>
                <a href="{{ route('penggunaan.index') }}"
                    class="rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">
                    ← Daftar Penggunaan
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-flash-message />

        <!-- Filter Periode Target -->
        <div
            class="bg-white border border-zinc-200 rounded-xl p-5 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <form method="GET" action="{{ route('penggunaan.massal') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="bulan"
                        class="block text-[11px] font-semibold uppercase tracking-wider text-zinc-500 mb-1.5">
                        Bulan Tagihan
                    </label>
                    <select id="bulan" name="bulan"
                        class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-xs font-medium text-zinc-900 focus:border-zinc-950 focus:outline-none">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (int) $bulan === $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="tahun"
                        class="block text-[11px] font-semibold uppercase tracking-wider text-zinc-500 mb-1.5">
                        Tahun
                    </label>
                    <input type="number" id="tahun" name="tahun" min="2020" max="2100"
                        value="{{ $tahun }}"
                        class="w-28 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-xs font-medium text-zinc-900 tabular-nums focus:border-zinc-950 focus:outline-none">
                </div>

                <button type="submit"
                    class="rounded-lg bg-zinc-900 px-4 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors cursor-pointer">
                    Tampilkan Daftar
                </button>
            </form>

            <div class="text-xs text-zinc-500 tabular-nums">
                Periode <span
                    class="font-semibold text-zinc-900">{{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }}
                    {{ $tahun }}</span>:
                <span class="font-semibold text-zinc-900">{{ $jumlahSudahDicatat }}</span> sudah dicatat,
                <span class="font-semibold text-zinc-900">{{ $daftarPelanggan->count() }}</span> belum dicatat.
            </div>
        </div>

        <!-- Tabel Input Massal -->
        @if ($daftarPelanggan->count() > 0)
            <form method="POST" action="{{ route('penggunaan.massal.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
                        <p class="text-xs text-zinc-600">
                            Kosongkan kolom <span class="font-semibold">Meter Akhir</span> jika pelanggan belum dicatat.
                            Gunakan tombol <kbd
                                class="px-1.5 py-0.5 text-[10px] font-mono bg-white border border-zinc-300 rounded">Tab</kbd>
                            untuk berpindah baris dengan cepat.
                        </p>
                        <span id="counterBarisSiap" class="text-xs font-semibold text-zinc-950 tabular-nums">
                            0 baris siap disimpan
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 text-sm">
                            <thead class="bg-zinc-50/70">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                        Pelanggan</th>
                                    <th
                                        class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                        Daya</th>
                                    <th
                                        class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500">
                                        Meter Awal</th>
                                    <th
                                        class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500 w-44">
                                        Meter Akhir</th>
                                    <th
                                        class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500 w-36">
                                        Pemakaian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 bg-white">
                                @foreach ($daftarPelanggan as $pelanggan)
                                    <tr class="hover:bg-zinc-50/70 transition-colors" data-row>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-zinc-900">{{ $pelanggan->nama_pelanggan }}</p>
                                            <p class="text-xs font-mono text-zinc-400">
                                                {{ $pelanggan->nomor_kwh }}
                                                @if ($pelanggan->ada_riwayat_meter)
                                                    • Terakhir {{ $pelanggan->periode_sebelumnya }}
                                                @else
                                                    • <span class="text-amber-600 font-sans">Perdana</span>
                                                @endif
                                            </p>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs text-zinc-600 tabular-nums">
                                            {{ number_format($pelanggan->tarif->daya) }} VA
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right">
                                            <input type="number" name="rows[{{ $pelanggan->id_pelanggan }}][meter_awal]"
                                                value="{{ old("rows.{$pelanggan->id_pelanggan}.meter_awal", $pelanggan->meter_awal_otomatis) }}"
                                                min="0" {{ $pelanggan->ada_riwayat_meter ? 'readonly' : '' }}
                                                tabindex="{{ $pelanggan->ada_riwayat_meter ? '-1' : '0' }}" data-meter-awal
                                                class="w-32 rounded-lg border border-zinc-200 px-3 py-1.5 text-right text-sm tabular-nums focus:border-zinc-950 focus:outline-none {{ $pelanggan->ada_riwayat_meter ? 'bg-zinc-100 text-zinc-600 cursor-not-allowed' : 'bg-white text-zinc-900' }}">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right">
                                            <input type="number" name="rows[{{ $pelanggan->id_pelanggan }}][meter_akhir]"
                                                value="{{ old("rows.{$pelanggan->id_pelanggan}.meter_akhir") }}"
                                                min="{{ $pelanggan->meter_awal_otomatis + 1 }}" placeholder="—"
                                                data-meter-akhir
                                                class="w-36 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-right text-sm text-zinc-900 tabular-nums placeholder:text-zinc-300 focus:border-zinc-950 focus:outline-none">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-semibold tabular-nums"
                                            data-pemakaian>
                                            <span class="text-zinc-300">—</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button type="submit"
                        class="rounded-lg bg-zinc-950 px-5 py-2.5 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors cursor-pointer">
                        Simpan & Terbitkan Tagihan Massal
                    </button>
                </div>
            </form>
        @else
            <div class="bg-white border border-zinc-200 rounded-xl p-12 text-center">
                <p class="text-sm font-semibold text-zinc-900">Semua Pelanggan Sudah Tercatat</p>
                <p class="text-xs text-zinc-500 mt-1">
                    Seluruh pelanggan telah memiliki catatan stand meter untuk periode
                    {{ \Carbon\Carbon::create()->month((int) $bulan)->translatedFormat('F') }} {{ $tahun }}.
                </p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tr[data-row]');
            const counter = document.getElementById('counterBarisSiap');

            function updateSummary() {
                let siap = 0;

                rows.forEach((row) => {
                    const inputAwal = row.querySelector('[data-meter-awal]');
                    const inputAkhir = row.querySelector('[data-meter-akhir]');
                    const cellPemakaian = row.querySelector('[data-pemakaian]');

                    const awal = parseInt(inputAwal.value || 0, 10);
                    const akhirVal = inputAkhir.value.trim();

                    if (akhirVal === '') {
                        inputAkhir.classList.remove('border-red-500', 'border-zinc-950');
                        cellPemakaian.innerHTML = '<span class="text-zinc-300">—</span>';
                        return;
                    }

                    const akhir = parseInt(akhirVal, 10);
                    if (akhir > awal) {
                        siap++;
                        inputAkhir.classList.remove('border-red-500');
                        inputAkhir.classList.add('border-zinc-950');
                        cellPemakaian.innerHTML =
                            `<span class="text-zinc-950">${(akhir - awal).toLocaleString('id-ID')} kWh</span>`;
                    } else {
                        inputAkhir.classList.remove('border-zinc-950');
                        inputAkhir.classList.add('border-red-500');
                        cellPemakaian.innerHTML = '<span class="text-red-600">Tidak valid</span>';
                    }
                });

                if (counter) {
                    counter.textContent = `${siap} baris siap disimpan`;
                }
            }

            rows.forEach((row) => {
                row.querySelector('[data-meter-awal]').addEventListener('input', updateSummary);
                row.querySelector('[data-meter-akhir]').addEventListener('input', updateSummary);
            });

            updateSummary();
        });
    </script>
@endsection
