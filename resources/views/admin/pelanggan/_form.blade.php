<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- Nomor KWH -->
    <div>
        <label for="nomor_kwh" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Nomor KWH (11–12 Digit)
        </label>
        <input type="text" id="nomor_kwh" name="nomor_kwh" value="{{ old('nomor_kwh', $pelanggan->nomor_kwh ?? '') }}"
            placeholder="531234567890" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 font-mono text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-950 focus:outline-none @error('nomor_kwh') @enderror">
        @error('nomor_kwh')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nama Pelanggan -->
    <div>
        <label for="nama_pelanggan" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Nama Pelanggan
        </label>
        <input type="text" id="nama_pelanggan" name="nama_pelanggan"
            value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan ?? '') }}"
            placeholder="Nama lengkap sesuai identitas" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-950 focus:outline-none @error('nama_pelanggan') @enderror">
        @error('nama_pelanggan')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tarif -->
    <div class="md:col-span-2">
        <label for="id_tarif" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Golongan Daya & Tarif
        </label>
        <select id="id_tarif" name="id_tarif" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('id_tarif') @enderror">
            <option value="">Pilih daya listrik</option>
            @foreach ($tarifs as $tarif)
                <option value="{{ $tarif->id_tarif }}"
                    {{ old('id_tarif', $pelanggan->id_tarif ?? '') == $tarif->id_tarif ? 'selected' : '' }}>
                    {{ number_format($tarif->daya) }} VA — Rp {{ number_format($tarif->tarif_per_kwh, 2) }}/kWh
                </option>
            @endforeach
        </select>
        @error('id_tarif')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Alamat -->
    <div class="md:col-span-2">
        <label for="alamat" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Alamat Pemasangan
        </label>
        <textarea id="alamat" name="alamat" rows="3" required placeholder="Alamat lengkap lokasi meteran terpasang"
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 placeholder:text-zinc-400 focus:border-zinc-950 focus:outline-none @error('alamat') @enderror">{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>
        @error('alamat')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>
</div>
