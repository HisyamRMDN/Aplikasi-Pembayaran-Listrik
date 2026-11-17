@extends('layouts.app')

@section('title', 'Proses Pembayaran — VOLTPAY')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-zinc-200 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">Proses Pembayaran</h1>
                <p class="text-sm text-zinc-500 mt-1">Validasi pelunasan tagihan listrik pascabayar pelanggan</p>
            </div>
            <a href="{{ route('pembayaran.index') }}"
                class="text-xs font-medium text-zinc-500 hover:text-zinc-950 transition-colors">
                ← Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-zinc-200 rounded-xl p-6 sm:p-8">
            <x-flash-message />

            <form method="POST" action="{{ route('pembayaran.store') }}" id="pembayaranForm" class="space-y-5">
                @csrf

                <!-- Pilih Tagihan -->
                @php
                    $namaBulan = fn($i) => \Carbon\Carbon::create(2000, $i, 1)->locale('id')->translatedFormat('F');
                    $terpilih = old('id_tagihan', request('tagihan'));
                @endphp
                <div>
                    <label for="id_tagihan" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                        Tagihan Belum Lunas
                    </label>
                    <select id="id_tagihan" name="id_tagihan" required
                        class="w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:outline-none {{ $errors->has('id_tagihan') ? 'border-red-500' : 'border-zinc-200 focus:border-zinc-950' }}">
                        <option value="">Pilih tagihan pelanggan</option>
                        @foreach ($tagihans as $tagihan)
                            <option value="{{ $tagihan->id_tagihan }}"
                                data-pelanggan="{{ $tagihan->pelanggan->nama_pelanggan }}"
                                data-nomor-kwh="{{ $tagihan->pelanggan->nomor_kwh }}"
                                data-bulan="{{ $namaBulan($tagihan->bulan) }}" data-tahun="{{ $tagihan->tahun }}"
                                data-total="{{ $tagihan->total_tagihan }}"
                                {{ $terpilih == $tagihan->id_tagihan ? 'selected' : '' }}>
                                {{ $tagihan->pelanggan->nama_pelanggan }} ({{ $tagihan->pelanggan->nomor_kwh }}) —
                                {{ $namaBulan($tagihan->bulan) }} {{ $tagihan->tahun }}
                                (Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_tagihan')
                        <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Detail Tagihan -->
                <div class="hidden" id="infoTagihan">
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50/80 p-4 space-y-3">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500">Rincian Tagihan
                            Terpilih</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                            <div>
                                <span class="text-zinc-400 block">Pelanggan</span>
                                <span id="namaPelanggan" class="font-semibold text-zinc-900 mt-0.5 block"></span>
                            </div>
                            <div>
                                <span class="text-zinc-400 block">Nomor KWH</span>
                                <span id="nomorKwh" class="font-mono font-medium text-zinc-900 mt-0.5 block"></span>
                            </div>
                            <div>
                                <span class="text-zinc-400 block">Periode</span>
                                <span id="periode" class="font-medium text-zinc-900 mt-0.5 block"></span>
                            </div>
                            <div>
                                <span class="text-zinc-400 block">Pokok Tagihan</span>
                                <span id="totalTagihan"
                                    class="font-semibold text-zinc-950 tabular-nums mt-0.5 block"></span>
                            </div>
                        </div>
                    </div>
                </div>

                @include('admin.pembayaran._form')

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-zinc-100">
                    <a href="{{ route('pembayaran.index') }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-zinc-950 px-5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors cursor-pointer">
                        Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('id_tagihan');
            const info = document.getElementById('infoTagihan');

            function update() {
                const opt = select.options[select.selectedIndex];

                if (opt && opt.value) {
                    const total = parseFloat(opt.dataset.total) || 0;

                    document.getElementById('namaPelanggan').textContent = opt.dataset.pelanggan;
                    document.getElementById('nomorKwh').textContent = opt.dataset.nomorKwh;
                    document.getElementById('periode').textContent = opt.dataset.bulan + ' ' + opt.dataset.tahun;
                    document.getElementById('totalTagihan').textContent = 'Rp ' + total.toLocaleString('id-ID', {
                        maximumFractionDigits: 2
                    });

                    info.classList.remove('hidden');
                    window.setPokokTagihan(total);
                } else {
                    info.classList.add('hidden');
                    window.setPokokTagihan(0);
                }
            }

            select.addEventListener('change', update);
            update();
        });
    </script>
@endsection
