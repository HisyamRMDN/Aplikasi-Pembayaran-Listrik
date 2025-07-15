@extends('layouts.app')

@section('title', 'Edit Pelanggan')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Pelanggan</h1>
        <p class="text-gray-600">Perbarui data pelanggan</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('pelanggan.update', $pelanggan->id_pelanggan) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $pelanggan->username) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('username') border-red-500 @enderror">
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                           placeholder="Kosongkan jika tidak ingin mengubah password">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-xs mt-1">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <!-- Nomor KWH -->
                <div>
                    <label for="nomor_kwh" class="block text-sm font-medium text-gray-700 mb-2">Nomor KWH</label>
                    <input type="text" id="nomor_kwh" name="nomor_kwh" value="{{ old('nomor_kwh', $pelanggan->nomor_kwh) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nomor_kwh') border-red-500 @enderror">
                    @error('nomor_kwh')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Pelanggan -->
                <div>
                    <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-2">Nama Pelanggan</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_pelanggan') border-red-500 @enderror">
                    @error('nama_pelanggan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tarif -->
                <div class="md:col-span-2">
                    <label for="id_tarif" class="block text-sm font-medium text-gray-700 mb-2">Tarif Listrik</label>
                    <select id="id_tarif" name="id_tarif" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_tarif') border-red-500 @enderror">
                        <option value="">Pilih Tarif</option>
                        @foreach($tarifs as $tarif)
                        <option value="{{ $tarif->id_tarif }}" {{ old('id_tarif', $pelanggan->id_tarif) == $tarif->id_tarif ? 'selected' : '' }}>
                            {{ $tarif->daya }} VA - Rp {{ number_format($tarif->tarif_per_kwh, 2) }}/kWh
                        </option>
                        @endforeach
                    </select>
                    @error('id_tarif')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('alamat') border-red-500 @enderror">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                    @error('alamat')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('pelanggan.show', $pelanggan->id_pelanggan) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
