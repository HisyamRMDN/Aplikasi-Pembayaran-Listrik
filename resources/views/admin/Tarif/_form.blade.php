<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- Daya (VA) -->
    <div>
        <label for="daya" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Golongan Daya (VA)
        </label>
        <input type="number" id="daya" name="daya" min="100" step="50"
            value="{{ old('daya', $tarif->daya ?? '') }}" placeholder="900" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 tabular-nums placeholder:text-zinc-400 focus:border-zinc-950 focus:outline-none @error('daya') @enderror">
        @error('daya')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tarif per kWh -->
    <div>
        <label for="tarif_per_kwh" class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
            Tarif per kWh (Rp)
        </label>
        <input type="number" id="tarif_per_kwh" name="tarif_per_kwh" min="0" step="0.01"
            value="{{ old('tarif_per_kwh', $tarif->tarif_per_kwh ?? '') }}" placeholder="1352.00" required
            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 tabular-nums placeholder:text-zinc-400 focus:border-zinc-950 focus:outline-none @error('tarif_per_kwh') @enderror">
        @error('tarif_per_kwh')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>
</div>
