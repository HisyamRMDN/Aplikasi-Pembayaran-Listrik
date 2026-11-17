@php
    $isEdit = isset($penggunaan);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- Pelanggan -->
    <div class="md:col-span-2">
        <label for="id_pelanggan" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Pelanggan
        </label>
        <select id="id_pelanggan" name="id_pelanggan" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('id_pelanggan') @enderror">
            <option value="">Pilih pelanggan</option>
            @foreach ($pelanggans as $pelanggan)
                <option value="{{ $pelanggan->id_pelanggan }}"
                    {{ old('id_pelanggan', $penggunaan->id_pelanggan ?? '') == $pelanggan->id_pelanggan ? 'selected' : '' }}>
                    {{ $pelanggan->nama_pelanggan }} — {{ $pelanggan->nomor_kwh }}
                </option>
            @endforeach
        </select>
        @error('id_pelanggan')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Bulan -->
    <div>
        <label for="bulan" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Bulan Pemakaian
        </label>
        <select id="bulan" name="bulan" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('bulan') @enderror">
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}"
                    {{ (int) old('bulan', $penggunaan->bulan ?? now()->month) === $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
        @error('bulan')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tahun -->
    <div>
        <label for="tahun" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Tahun Pemakaian
        </label>
        <input type="number" id="tahun" name="tahun" min="2020" max="2100"
            value="{{ old('tahun', $penggunaan->tahun ?? now()->year) }}" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 tabular-nums focus:border-zinc-950 focus:outline-none @error('tahun') @enderror">
        @error('tahun')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Meter Awal -->
    <div>
        <div class="flex items-center justify-between mb-2">
            <label for="meter_awal" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600">
                Meter Awal (kWh)
            </label>
            <span id="badgeMeterAwal" class="hidden text-[11px] font-medium text-zinc-500"></span>
        </div>
        <input type="number" id="meter_awal" name="meter_awal" min="0"
            value="{{ old('meter_awal', $penggunaan->meter_awal ?? '') }}" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 tabular-nums focus:border-zinc-950 focus:outline-none @error('meter_awal') @enderror">
        <p id="hintMeterAwal" class="text-[11px] text-zinc-400 mt-1.5">
            Otomatis terisi dan terkunci dari meter akhir periode sebelumnya.
        </p>
        @error('meter_awal')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Meter Akhir -->
    <div>
        <label for="meter_akhir" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Meter Akhir (kWh)
        </label>
        <input type="number" id="meter_akhir" name="meter_akhir" min="0"
            value="{{ old('meter_akhir', $penggunaan->meter_akhir ?? '') }}" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 tabular-nums focus:border-zinc-950 focus:outline-none @error('meter_akhir') @enderror">
        <p id="previewPemakaian" class="text-[11px] font-medium text-zinc-500 mt-1.5 tabular-nums"></p>
        @error('meter_akhir')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isEdit = @json($isEdit);
        const selectPelanggan = document.getElementById('id_pelanggan');
        const selectBulan = document.getElementById('bulan');
        const inputTahun = document.getElementById('tahun');
        const inputMeterAwal = document.getElementById('meter_awal');
        const inputMeterAkhir = document.getElementById('meter_akhir');
        const hintMeterAwal = document.getElementById('hintMeterAwal');
        const badgeMeterAwal = document.getElementById('badgeMeterAwal');
        const previewPemakaian = document.getElementById('previewPemakaian');

        function hitungPreview() {
            const awal = parseInt(inputMeterAwal.value || 0, 10);
            const akhir = parseInt(inputMeterAkhir.value || 0, 10);
            if (inputMeterAkhir.value !== '' && akhir > awal) {
                previewPemakaian.textContent =
                    `Estimasi pemakaian: ${(akhir - awal).toLocaleString('id-ID')} kWh`;
                previewPemakaian.className = 'text-[11px] font-medium text-zinc-700 mt-1.5 tabular-nums';
            } else if (inputMeterAkhir.value !== '') {
                previewPemakaian.textContent = 'Meter akhir harus lebih besar dari meter awal';
                previewPemakaian.className = 'text-[11px] font-medium text-red-600 mt-1.5';
            } else {
                previewPemakaian.textContent = '';
            }
        }

        async function muatMeterTerakhir() {
            if (isEdit) return;
            const idPelanggan = selectPelanggan.value;
            if (!idPelanggan) return;

            const params = new URLSearchParams({
                id_pelanggan: idPelanggan,
                bulan: selectBulan.value,
                tahun: inputTahun.value,
            });

            try {
                const res = await fetch(`{{ route('penggunaan.meter-terakhir') }}?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                if (data.ada_riwayat) {
                    inputMeterAwal.value = data.meter_awal;
                    inputMeterAwal.readOnly = true;
                    inputMeterAwal.classList.add('bg-zinc-100', 'text-zinc-600', 'cursor-not-allowed');
                    badgeMeterAwal.textContent = `Terkunci (Periode ${data.periode_sebelumnya})`;
                    badgeMeterAwal.classList.remove('hidden');
                    hintMeterAwal.textContent = 'Diambil otomatis dari meter akhir periode sebelumnya.';
                } else {
                    inputMeterAwal.readOnly = false;
                    inputMeterAwal.classList.remove('bg-zinc-100', 'text-zinc-600', 'cursor-not-allowed');
                    if (inputMeterAwal.value === '') inputMeterAwal.value = 0;
                    badgeMeterAwal.textContent = 'Pencatatan Perdana';
                    badgeMeterAwal.classList.remove('hidden');
                    hintMeterAwal.textContent =
                        'Pelanggan baru belum memiliki riwayat meter. Isi stand meter awal secara manual.';
                }

                hitungPreview();
            } catch (e) {
                console.error(e);
            }
        }

        if (!isEdit) {
            selectPelanggan.addEventListener('change', muatMeterTerakhir);
            selectBulan.addEventListener('change', muatMeterTerakhir);
            inputTahun.addEventListener('change', muatMeterTerakhir);
            if (selectPelanggan.value) muatMeterTerakhir();
        }

        inputMeterAwal.addEventListener('input', hitungPreview);
        inputMeterAkhir.addEventListener('input', hitungPreview);
        hitungPreview();
    });
</script>
