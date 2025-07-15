@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Tagihan</h1>
        <p class="text-gray-600">Perbarui data tagihan listrik</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('tagihan.update', $tagihan->id_tagihan) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Info (Read Only) -->
                <div class="md:col-span-2 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Informasi Pelanggan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <label class="font-medium text-gray-600">Nama:</label>
                            <p class="text-gray-900">{{ $tagihan->pelanggan->nama_pelanggan }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Nomor KWH:</label>
                            <p class="text-gray-900">{{ $tagihan->pelanggan->nomor_kwh }}</p>
                        </div>
                        <div>
                            <label class="font-medium text-gray-600">Tarif:</label>
                            <p class="text-gray-900">{{ $tagihan->pelanggan->tarif->daya }} VA - Rp {{ number_format($tagihan->pelanggan->tarif->tarif_per_kwh, 2) }}/kWh</p>
                        </div>
                    </div>
                </div>

                <!-- Period Info (Read Only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periode Tagihan</label>
                    <input type="text" value="{{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }} {{ $tagihan->tahun }}" readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                </div>

                <!-- Current Usage -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Penggunaan Saat Ini</label>
                    <input type="text" value="{{ number_format($tagihan->jumlah_meter) }} kWh" readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                </div>

                <!-- Editable Usage -->
                <div>
                    <label for="jumlah_meter" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Penggunaan (kWh)</label>
                    <input type="number" id="jumlah_meter" name="jumlah_meter" value="{{ old('jumlah_meter', $tagihan->jumlah_meter) }}" required min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jumlah_meter') border-red-500 @enderror">
                    @error('jumlah_meter')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="belum_lunas" {{ old('status', $tagihan->status) == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="lunas" {{ old('status', $tagihan->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Calculation Preview -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800 mb-2">Preview Perhitungan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-blue-700">Penggunaan:</p>
                        <p class="font-semibold text-blue-900" id="preview-usage">{{ number_format($tagihan->jumlah_meter) }} kWh</p>
                    </div>
                    <div>
                        <p class="text-blue-700">Tarif per kWh:</p>
                        <p class="font-semibold text-blue-900">Rp {{ number_format($tagihan->pelanggan->tarif->tarif_per_kwh, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-blue-700">Total Tagihan:</p>
                        <p class="font-semibold text-blue-900 text-lg" id="preview-total">Rp {{ number_format($tagihan->total_tagihan, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Current Bill Details -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-800 mb-2">Tagihan Saat Ini:</h3>
                <div class="text-sm text-gray-600">
                    <p>Total Tagihan: <span class="font-semibold">Rp {{ number_format($tagihan->total_tagihan, 2) }}</span></p>
                    <p>Status:
                        @if($tagihan->status === 'lunas')
                            <span class="text-green-600 font-semibold">Lunas</span>
                        @else
                            <span class="text-red-600 font-semibold">Belum Lunas</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Information -->
            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <h3 class="text-sm font-medium text-yellow-800">Perhatian:</h3>
                <ul class="text-sm text-yellow-700 mt-2 space-y-1">
                    <li>• Perubahan jumlah penggunaan akan mengubah total tagihan secara otomatis</li>
                    <li>• Jika status diubah menjadi "Lunas", pastikan pembayaran sudah benar-benar diterima</li>
                    <li>• Perubahan data akan mempengaruhi laporan dan perhitungan</li>
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
                <a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}"
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahMeterInput = document.getElementById('jumlah_meter');
    const previewUsage = document.getElementById('preview-usage');
    const previewTotal = document.getElementById('preview-total');
    const tarifPerKwh = {{ $tagihan->pelanggan->tarif->tarif_per_kwh }};

    jumlahMeterInput.addEventListener('input', function() {
        const usage = parseInt(this.value) || 0;
        const total = usage * tarifPerKwh;

        previewUsage.textContent = usage.toLocaleString('id-ID') + ' kWh';
        previewTotal.textContent = 'Rp ' + total.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    });
});
</script>
@endsection
