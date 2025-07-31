@extends('layouts.app')

@section('title', 'Tambah Tagihan')

@section('content')
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Tambah Tagihan</h1>
            <p class="text-gray-600">Input tagihan listrik pelanggan</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tagihan.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pelanggan -->
                    <div class="md:col-span-2">
                        <label for="id_pelanggan" class="block text-sm font-medium text-gray-700 mb-2">Pelanggan</label>
                        <select id="id_pelanggan" name="id_pelanggan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_pelanggan') border-red-500 @enderror">
                            <option value="">Pilih Pelanggan</option>
                            @foreach ($pelanggans as $pelanggan)
                                <option value="{{ $pelanggan->id_pelanggan }}"
                                    {{ old('id_pelanggan') == $pelanggan->id_pelanggan ? 'selected' : '' }}>
                                    {{ $pelanggan->nama_pelanggan }} - {{ $pelanggan->nomor_kwh }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_pelanggan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Penggunaan (Optional) -->
                    <div class="md:col-span-2">
                        <label for="id_penggunaan" class="block text-sm font-medium text-gray-700 mb-2">Penggunaan
                            (Opsional)</label>
                        <select id="id_penggunaan" name="id_penggunaan"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_penggunaan') border-red-500 @enderror">
                            <option value="">Pilih Penggunaan (Kosongkan jika tagihan manual)</option>
                            @foreach ($penggunaans as $penggunaan)
                                <option value="{{ $penggunaan->id_penggunaan }}"
                                    {{ old('id_penggunaan') == $penggunaan->id_penggunaan ? 'selected' : '' }}>
                                    {{ $penggunaan->pelanggan->nama_pelanggan }} -
                                    {{ DateTime::createFromFormat('!m', $penggunaan->bulan)->format('F') }}
                                    {{ $penggunaan->tahun }} ({{ $penggunaan->jumlah_meter }} kWh)
                                </option>
                            @endforeach
                        </select>
                        @error('id_penggunaan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bulan -->
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <select id="bulan" name="bulan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('bulan') border-red-500 @enderror">
                            <option value="">Pilih Bulan</option>
                            @for ($i = 1; $i <= 12; $i++)
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
                        <input type="number" id="tahun" name="tahun" value="{{ old('tahun', date('Y')) }}"
                            min="2020" max="2030" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tahun') border-red-500 @enderror">
                        @error('tahun')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Meter -->
                    <div>
                        <label for="jumlah_meter" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Meter
                            (kWh)</label>
                        <input type="number" id="jumlah_meter" name="jumlah_meter" value="{{ old('jumlah_meter') }}"
                            min="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jumlah_meter') border-red-500 @enderror">
                        @error('jumlah_meter')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            <option value="">Pilih Status</option>
                            <option value="belum_lunas" {{ old('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas
                            </option>
                            <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('tagihan.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                        Simpan Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
