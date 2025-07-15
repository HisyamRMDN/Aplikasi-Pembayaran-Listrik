@extends('layouts.app')

@section('title', 'Edit Pembayaran')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Pembayaran</h1>
        <p class="text-gray-600">Ubah data pembayaran listrik pelanggan</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- Error Messages -->
        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Informasi Tagihan (Read Only) -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h4 class="font-medium text-blue-900 mb-2">Informasi Tagihan</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-blue-700">Pelanggan:</span>
                    <span class="font-medium">{{ $pembayaran->pelanggan->nama_pelanggan }}</span>
                </div>
                <div>
                    <span class="text-blue-700">No. KWH:</span>
                    <span class="font-medium">{{ $pembayaran->pelanggan->nomor_kwh }}</span>
                </div>
                <div>
                    <span class="text-blue-700">Periode:</span>
                    <span class="font-medium">{{ $pembayaran->bulan_bayar }}</span>
                </div>
                <div>
                    <span class="text-blue-700">Total Tagihan:</span>
                    <span class="font-medium text-green-600">Rp {{ number_format($pembayaran->tagihan->total_tagihan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('pembayaran.update', $pembayaran->id_pembayaran) }}" id="editPembayaranForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal Pembayaran -->
                <div>
                    <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran</label>
                    <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran"
                           value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal_pembayaran') border-red-500 @enderror">
                    @error('tanggal_pembayaran')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Biaya Admin -->
                <div>
                    <label for="biaya_admin" class="block text-sm font-medium text-gray-700 mb-2">Biaya Admin (Rp)</label>
                    <input type="number" id="biaya_admin" name="biaya_admin"
                           value="{{ old('biaya_admin', $pembayaran->biaya_admin) }}" min="0" step="100" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('biaya_admin') border-red-500 @enderror">
                    @error('biaya_admin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Bayar (Read Only) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total yang Harus Dibayar</label>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600" id="totalBayar">
                            Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}
                        </div>
                        <div class="text-sm text-green-700">Total Tagihan + Biaya Admin</div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="md:col-span-2">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h5 class="font-medium text-gray-900 mb-2">Informasi Pembayaran</h5>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">ID Pembayaran:</span>
                                <span class="font-medium">{{ $pembayaran->id_pembayaran }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Petugas:</span>
                                <span class="font-medium">{{ $pembayaran->user->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Tanggal Dibuat:</span>
                                <span class="font-medium">{{ date('d/m/Y H:i', strtotime($pembayaran->created_at)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('pembayaran.show', $pembayaran->id_pembayaran) }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg">
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Perubahan -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan</h2>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Peringatan</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Perubahan tanggal pembayaran akan mempengaruhi laporan.</li>
                            <li>Perubahan biaya admin akan mengubah total pembayaran.</li>
                            <li>Pastikan data yang diubah sudah benar sebelum menyimpan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const biayaAdminInput = document.getElementById('biaya_admin');
    const totalBayar = document.getElementById('totalBayar');
    const totalTagihan = {{ $pembayaran->tagihan->total_tagihan }};

    function updateTotalBayar() {
        const biayaAdmin = parseInt(biayaAdminInput.value) || 0;
        const total = totalTagihan + biayaAdmin;
        totalBayar.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    biayaAdminInput.addEventListener('input', updateTotalBayar);

    // Initialize on page load
    updateTotalBayar();
});
</script>
@endsection
