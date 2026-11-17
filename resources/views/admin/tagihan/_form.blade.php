@php
    $base =
        'w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none';
    $cls = fn($name) => $base .
        ' ' .
        ($errors->has($name) ? 'border-red-500 focus:border-red-600' : 'border-zinc-200 focus:border-zinc-950');
    $label = 'block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2';
    $namaBulan = fn($i) => \Carbon\Carbon::create(2000, $i, 1)->locale('id')->translatedFormat('F');
    $isEdit = isset($tagihan);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    @unless ($isEdit)
        <!-- Pelanggan -->
        <div class="md:col-span-2">
            <label for="id_pelanggan" class="{{ $label }}">Pelanggan</label>
            <select id="id_pelanggan" name="id_pelanggan" required class="{{ $cls('id_pelanggan') }}">
                <option value="">Pilih pelanggan</option>
                @foreach ($pelanggans as $pelanggan)
                    <option value="{{ $pelanggan->id_pelanggan }}"
                        {{ old('id_pelanggan') == $pelanggan->id_pelanggan ? 'selected' : '' }}>
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
            <label for="bulan" class="{{ $label }}">Bulan</label>
            <select id="bulan" name="bulan" required class="{{ $cls('bulan') }}">
                <option value="">Pilih bulan</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ old('bulan') == $i ? 'selected' : '' }}>{{ $namaBulan($i) }}
                    </option>
                @endfor
            </select>
            @error('bulan')
                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tahun -->
        <div>
            <label for="tahun" class="{{ $label }}">Tahun</label>
            <input type="number" id="tahun" name="tahun" min="2020" max="2030" required
                value="{{ old('tahun', date('Y')) }}" class="{{ $cls('tahun') }} font-mono tabular-nums">
            @error('tahun')
                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>
    @endunless

    <!-- Jumlah kWh -->
    <div class="md:col-span-2">
        <label for="jumlah_meter" class="{{ $label }}">Pemakaian (kWh)</label>
        <input type="number" id="jumlah_meter" name="jumlah_meter" min="0" required placeholder="0"
            value="{{ old('jumlah_meter', $tagihan->jumlah_meter ?? '') }}"
            class="{{ $cls('jumlah_meter') }} font-mono tabular-nums">
        <p class="mt-1.5 text-xs text-zinc-500">Total tagihan dihitung dari kWh × tarif per kWh yang tersimpan pada
            tagihan.</p>
        @error('jumlah_meter')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>
</div>
