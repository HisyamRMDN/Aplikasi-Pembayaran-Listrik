@extends('layouts.app')

@section('title', 'Edit Pelanggan — VOLTPAY')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-zinc-200 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">Edit Pelanggan</h1>
                <p class="text-sm text-zinc-500 mt-1">Perbarui informasi meteran dan golongan daya pelanggan</p>
            </div>
            <a href="{{ route('pelanggan.show', $pelanggan->id_pelanggan) }}"
                class="text-xs font-medium text-zinc-500 hover:text-zinc-950 transition-colors">
                ← Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-zinc-200 rounded-xl p-6 sm:p-8">
            <form method="POST" action="{{ route('pelanggan.update', $pelanggan->id_pelanggan) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nomor KWH -->
                    <div>
                        <label for="nomor_kwh"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                            Nomor KWH (11–12 Digit)
                        </label>
                        <input type="text" id="nomor_kwh" name="nomor_kwh"
                            value="{{ old('nomor_kwh', $pelanggan->nomor_kwh) }}" required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 font-mono text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('nomor_kwh') border-red-500 @enderror">
                        @error('nomor_kwh')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Pelanggan -->
                    <div>
                        <label for="nama_pelanggan"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                            Nama Pelanggan
                        </label>
                        <input type="text" id="nama_pelanggan" name="nama_pelanggan"
                            value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}" required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('nama_pelanggan') border-red-500 @enderror">
                        @error('nama_pelanggan')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tarif -->
                    <div class="md:col-span-2">
                        <label for="id_tarif"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                            Golongan Daya & Tarif
                        </label>
                        <select id="id_tarif" name="id_tarif" required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('id_tarif') border-red-500 @enderror">
                            <option value="">Pilih daya listrik</option>
                            @foreach ($tarifs as $tarif)
                                <option value="{{ $tarif->id_tarif }}"
                                    {{ old('id_tarif', $pelanggan->id_tarif) == $tarif->id_tarif ? 'selected' : '' }}>
                                    {{ number_format($tarif->daya) }} VA — Rp
                                    {{ number_format($tarif->tarif_per_kwh, 2) }}/kWh
                                </option>
                            @endforeach
                        </select>
                        @error('id_tarif')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label for="alamat"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                            Alamat Pemasangan
                        </label>
                        <textarea id="alamat" name="alamat" rows="3" required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('alamat') border-red-500 @enderror">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-zinc-100">
                    <a href="{{ route('pelanggan.show', $pelanggan->id_pelanggan) }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-zinc-950 px-5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
