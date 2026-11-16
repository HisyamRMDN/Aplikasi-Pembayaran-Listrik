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
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pembayaran.store') }}" id="pembayaranForm" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Pilih Tagihan -->
                    <div class="md:col-span-2">
                        <label for="id_tagihan"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                            Tagihan Belum Lunas
                        </label>
                        <select id="id_tagihan" name="id_tagihan" required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('id_tagihan') border-red-500 @enderror">
                            <option value="">Pilih tagihan pelanggan</option>
                            @foreach ($tagihans as $tagihan)
                                <option value="{{ $tagihan->id_tagihan }}"
                                    data-pelanggan="{{ $tagihan->pelanggan->nama_pelanggan }}"
                                    data-nomor-kwh="{{ $tagihan->pelanggan->nomor_kwh }}"
                                    data-bulan="{{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }}"
                                    data-tahun="{{ $tagihan->tahun }}" data-total="{{ $tagihan->total_tagihan }}"
                                    {{ old('id_tagihan') == $tagihan->id_tagihan ? 'selected' : '' }}>
                                    {{ $tagihan->pelanggan->nama_pelanggan }} ({{ $tagihan->pelanggan->nomor_kwh }}) —
                                    {{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }}
                                    {{ $tagihan->tahun }}
                                    (Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_tagihan')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preview Detail Tagihan -->
                    <div class="md:col-span-2 hidden" id="infoTagihan">
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

                    <!-- Tanggal Pembayaran -->
                    <div>
                        <label for="tanggal_pembayaran"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                            Tanggal Pembayaran
                        </label>
                        <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran"
                            value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}" required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('tanggal_pembayaran') border-red-500 @enderror">
                        @error('tanggal_pembayaran')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Biaya Admin -->
                    <div>
                        <label for="biaya_admin"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                            Biaya Admin (Rp)
                        </label>
                        <input type="number" id="biaya_admin" name="biaya_admin" value="{{ old('biaya_admin', '2500') }}"
                            min="0" step="100" required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 tabular-nums focus:border-zinc-950 focus:outline-none @error('biaya_admin') border-red-500 @enderror">
                        @error('biaya_admin')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Bayar -->
                    <div class="md:col-span-2">
                        <div
                            class="rounded-xl bg-zinc-950 text-white p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="text-xs font-medium text-zinc-400">Total yang Harus Dibayar</p>
                                <p class="text-[11px] text-zinc-500 mt-0.5">Pokok tagihan + biaya administrasi loket</p>
                            </div>
                            <div class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums" id="totalBayar">
                                Rp 0
                            </div>
                        </div>
                    </div>
                </div>

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
            const tagihanSelect = document.getElementById('id_tagihan');
            const biayaAdminInput = document.getElementById('biaya_admin');
            const infoTagihan = document.getElementById('infoTagihan');
            const namaPelanggan = document.getElementById('namaPelanggan');
            const nomorKwh = document.getElementById('nomorKwh');
            const periode = document.getElementById('periode');
            const totalTagihan = document.getElementById('totalTagihan');
            const totalBayar = document.getElementById('totalBayar');

            function updateTagihanInfo() {
                const selectedOption = tagihanSelect.options[tagihanSelect.selectedIndex];

                if (selectedOption && selectedOption.value) {
                    const pelanggan = selectedOption.getAttribute('data-pelanggan');
                    const nomor = selectedOption.getAttribute('data-nomor-kwh');
                    const bulan = selectedOption.getAttribute('data-bulan');
                    const tahun = selectedOption.getAttribute('data-tahun');
                    const total = parseInt(selectedOption.getAttribute('data-total')) || 0;

                    namaPelanggan.textContent = pelanggan;
                    nomorKwh.textContent = nomor;
                    periode.textContent = bulan + ' ' + tahun;
                    totalTagihan.textContent = 'Rp ' + total.toLocaleString('id-ID');

                    infoTagihan.classList.remove('hidden');
                    updateTotalBayar(total);
                } else {
                    infoTagihan.classList.add('hidden');
                    updateTotalBayar(0);
                }
            }

            function updateTotalBayar(tagihanAmount = null) {
                if (tagihanAmount === null) {
                    const selectedOption = tagihanSelect.options[tagihanSelect.selectedIndex];
                    tagihanAmount = (selectedOption && selectedOption.value) ?
                        parseInt(selectedOption.getAttribute('data-total')) || 0 :
                        0;
                }

                const biayaAdmin = parseInt(biayaAdminInput.value) || 0;
                const total = tagihanAmount + biayaAdmin;

                totalBayar.textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            tagihanSelect.addEventListener('change', updateTagihanInfo);
            biayaAdminInput.addEventListener('input', () => updateTotalBayar());

            updateTagihanInfo();
        });
    </script>
@endsection
