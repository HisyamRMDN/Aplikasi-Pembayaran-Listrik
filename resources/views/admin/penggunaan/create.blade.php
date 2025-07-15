@extends('layouts.app')

@section('title', 'Tambah Penggunaan')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Data Penggunaan</h1>
        <p class="text-gray-600">Input data penggunaan listrik pelanggan</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('penggunaan.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pelanggan -->
                <div class="md:col-span-2">
                    <label for="id_pelanggan" class="block text-sm font-medium text-gray-700 mb-2">Pelanggan</label>
                    <select id="id_pelanggan" name="id_pelanggan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_pelanggan') border-red-500 @enderror">
                        <option value="">Pilih Pelanggan</option>
                        @foreach($pelanggans as $pelanggan)
                        <option value="{{ $pelanggan->id_pelanggan }}" {{ old('id_pelanggan') == $pelanggan->id_pelanggan ? 'selected' : '' }}>
                            {{ $pelanggan->nama_pelanggan }} - {{ $pelanggan->nomor_kwh }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_pelanggan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bulan -->
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <select id="bulan" name="bulan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('bulan') border-red-500 @enderror">
                        <option value="">Pilih Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ old('bulan') == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                        @endfor
                    </select>
                    @error('bulan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun -->
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select id="tahun" name="tahun" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tahun') border-red-500 @enderror">
                        <option value="">Pilih Tahun</option>
                        @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ old('tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    @error('tahun')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meter Awal -->
                <div>
                    <label for="meter_awal" class="block text-sm font-medium text-gray-700 mb-2">Meter Awal</label>
                    <input type="number" id="meter_awal" name="meter_awal" value="{{ old('meter_awal') }}" required min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('meter_awal') border-red-500 @enderror">
                    @error('meter_awal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meter Akhir -->
                <div>
                    <label for="meter_akhir" class="block text-sm font-medium text-gray-700 mb-2">Meter Akhir</label>
                    <input type="number" id="meter_akhir" name="meter_akhir" value="{{ old('meter_akhir') }}" required min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('meter_akhir') border-red-500 @enderror">
                    @error('meter_akhir')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Display calculated usage -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800">Informasi:</h3>
                <ul class="text-sm text-blue-700 mt-2 space-y-1">
                    <li>• Meter akhir harus lebih besar dari meter awal</li>
                    <li>• Sistem akan otomatis membuat tagihan setelah data penggunaan disimpan</li>
                    <li>• Pastikan tidak ada duplikasi data untuk pelanggan yang sama di bulan/tahun yang sama</li>
                </ul>
            </div>

            @if ($errors->any())
                <div class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('penggunaan.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const meterAwal = document.getElementById('meter_awal');
    const meterAkhir = document.getElementById('meter_akhir');

    function validateMeters() {
        if (meterAwal.value && meterAkhir.value) {
            if (parseInt(meterAkhir.value) <= parseInt(meterAwal.value)) {
                meterAkhir.setCustomValidity('Meter akhir harus lebih besar dari meter awal');
            } else {
                meterAkhir.setCustomValidity('');
            }
        }
    }

    meterAwal.addEventListener('input', validateMeters);
    meterAkhir.addEventListener('input', validateMeters);
});
</script>
@endsection
