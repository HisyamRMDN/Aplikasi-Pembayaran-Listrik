@extends ('layouts.app')

@section ('title', 'Proses Pembayaran — VOLTPAY')

@section ('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header
            title="Proses Pembayaran Listrik"
            subtitle="Pilih tagihan belum lunas dan konfirmasi tanggal pelunasan di loket"
        >
            <x-slot:actions>
                <a
                    href="{{ route('pembayaran.index') }}"
                    class="text-xs font-medium text-zinc-500 hover:text-zinc-950 transition-colors"
                >
                    ← Kembali
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-flash-message />

        <div class="bg-white border border-zinc-200 rounded-xl p-6 sm:p-8">
            <form method="POST" action="{{ route('pembayaran.store') }}" class="space-y-6">
                @csrf

                <!-- Pilih Tagihan -->
                <div>
                    <label
                        for="id_tagihan"
                        class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2"
                    >
                        Tagihan Belum Lunas
                    </label>
                    <select
                        id="id_tagihan"
                        name="id_tagihan"
                        required
                        class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('id_tagihan') @enderror"
                    >
                        <option value="">Pilih tagihan pelanggan...</option>
                        @foreach ($tagihans as $tagihan)
                            @php
                                $namaBulan = \Carbon\Carbon::create()->month((int) $tagihan->bulan)->translatedFormat('F');
                                $jatuhTempoIso = $tagihan->tanggal_jatuh_tempo ? $tagihan->tanggal_jatuh_tempo->format('Y-m-d') : '';
                                $jatuhTempoLabel = $tagihan->tanggal_jatuh_tempo ? $tagihan->tanggal_jatuh_tempo->format('d/m/Y') : '-';
                            @endphp
                            <option
                                value="{{ $tagihan->id_tagihan }}"
                                data-pokok="{{ (float) $tagihan->total_tagihan }}"
                                data-jatuh-tempo="{{ $jatuhTempoIso }}"
                                data-jatuh-tempo-label="{{ $jatuhTempoLabel }}"
                                data-meter="{{ number_format($tagihan->jumlah_meter) }}"
                                {{
                                    old('id_tagihan', request('id_tagihan')) == $tagihan->id_tagihan
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $tagihan->pelanggan->nama_pelanggan }} ({{ $tagihan->pelanggan->nomor_kwh }}) — {{ $namaBulan }} {{ $tagihan->tahun }} •
                                Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error ('id_tagihan')
                        <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Bayar & Biaya Admin -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label
                            for="tanggal_pembayaran"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2"
                        >
                            Tanggal Pembayaran
                        </label>
                        <input
                            type="date"
                            id="tanggal_pembayaran"
                            name="tanggal_pembayaran"
                            value="{{ old('tanggal_pembayaran', now()->format('Y-m-d')) }}"
                            required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 tabular-nums focus:border-zinc-950 focus:outline-none @error('tanggal_pembayaran') @enderror"
                        />
                        @error ('tanggal_pembayaran')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="biaya_admin"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2"
                        >
                            Biaya Admin Loket (Rp)
                        </label>
                        <input
                            type="number"
                            id="biaya_admin"
                            name="biaya_admin"
                            min="0"
                            step="100"
                            value="{{ old('biaya_admin', config('voltpay.biaya_admin_default', 2500)) }}"
                            required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 tabular-nums focus:border-zinc-950 focus:outline-none @error('biaya_admin') @enderror"
                        />
                        @error ('biaya_admin')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Panel Rincian Kalkulasi Real-time -->
                <div class="rounded-xl border border-zinc-200 bg-zinc-50/70 p-5 space-y-3">
                    <div class="flex items-center justify-between text-xs text-zinc-500">
                        <span>Jatuh Tempo Tagihan</span>
                        <span id="previewJatuhTempo" class="font-medium text-zinc-800 tabular-nums">—</span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-zinc-500">
                        <span>Pokok Tagihan (<span id="previewMeter">0</span> kWh)</span>
                        <span id="previewPokok" class="font-medium text-zinc-900 tabular-nums">Rp 0</span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-zinc-500">
                            Denda Keterlambatan
                            <span
                                id="badgeTerlambat"
                                class="ml-1.5 inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-700"
                            >
                                Melewati Jatuh Tempo
                            </span>
                        </span>
                        <span id="previewDenda" class="font-medium text-zinc-900 tabular-nums">Rp 0</span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-zinc-500">
                        <span>Biaya Admin Loket</span>
                        <span id="previewAdmin" class="font-medium text-zinc-900 tabular-nums">Rp 0</span>
                    </div>

                    <div class="pt-3 border-t border-zinc-200 flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-700"
                            >Total yang Harus Dibayar</span
                        >
                        <span id="previewTotal" class="text-xl font-bold text-zinc-950 tabular-nums">Rp 0</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2">
                    <a
                        href="{{ route('pembayaran.index') }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="rounded-lg bg-zinc-950 px-5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors cursor-pointer"
                    >
                        Konfirmasi Pelunasan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dendaFlat = @json ((float) config('voltpay.denda_keterlambatan', 10000));
            const selectTagihan = document.getElementById('id_tagihan');
            const inputTanggal = document.getElementById('tanggal_pembayaran');
            const inputAdmin = document.getElementById('biaya_admin');

            const elJatuhTempo = document.getElementById('previewJatuhTempo');
            const elMeter = document.getElementById('previewMeter');
            const elPokok = document.getElementById('previewPokok');
            const elDenda = document.getElementById('previewDenda');
            const elAdmin = document.getElementById('previewAdmin');
            const elTotal = document.getElementById('previewTotal');
            const badgeTerlambat = document.getElementById('badgeTerlambat');

            const formatRupiah = (angka) => 'Rp ' + Math.round(angka).toLocaleString('id-ID');

            function hitungKalkulasi() {
                const option = selectTagihan.options[selectTagihan.selectedIndex];
                const admin = parseFloat(inputAdmin.value || 0);

                if (!option || !option.value) {
                    elJatuhTempo.textContent = '—';
                    elMeter.textContent = '0';
                    elPokok.textContent = 'Rp 0';
                    elDenda.textContent = 'Rp 0';
                    elDenda.className = 'font-medium text-zinc-900 tabular-nums';
                    elAdmin.textContent = formatRupiah(admin);
                    elTotal.textContent = formatRupiah(admin);
                    badgeTerlambat.classList.add('hidden');
                    return;
                }

                const pokok = parseFloat(option.dataset.pokok || 0);
                const jatuhTempo = option.dataset.jatuhTempo;
                const jatuhTempoLabel = option.dataset.jatuhTempoLabel || '—';
                const meter = option.dataset.meter || '0';
                const tanggalBayar = inputTanggal.value;

                const terlambat = Boolean(jatuhTempo && tanggalBayar && tanggalBayar > jatuhTempo);
                const denda = terlambat ? dendaFlat : 0;
                const total = pokok + denda + admin;

                elJatuhTempo.textContent = jatuhTempoLabel;
                elMeter.textContent = meter;
                elPokok.textContent = formatRupiah(pokok);
                elDenda.textContent = formatRupiah(denda);
                elAdmin.textContent = formatRupiah(admin);
                elTotal.textContent = formatRupiah(total);

                if (terlambat) {
                    badgeTerlambat.classList.remove('hidden');
                    elDenda.className = 'font-semibold text-red-600 tabular-nums';
                } else {
                    badgeTerlambat.classList.add('hidden');
                    elDenda.className = 'font-medium text-zinc-900 tabular-nums';
                }
            }

            selectTagihan.addEventListener('change', hitungKalkulasi);
            inputTanggal.addEventListener('change', hitungKalkulasi);
            inputAdmin.addEventListener('input', hitungKalkulasi);

            hitungKalkulasi();
        });
    </script>
@endsection
