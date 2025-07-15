@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pembayaran</h1>
            <p class="text-gray-600">Informasi lengkap pembayaran listrik</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('pembayaran.edit', $pembayaran->id_pembayaran) }}"
               class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium">
                Edit Pembayaran
            </a>
            <a href="{{ route('pembayaran.index') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Pembayaran -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID Pembayaran</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $pembayaran->id_pembayaran }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembayaran</label>
                        <p class="text-sm text-gray-900">{{ date('d F Y', strtotime($pembayaran->tanggal_pembayaran)) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bulan Bayar</label>
                        <p class="text-sm text-gray-900">{{ $pembayaran->bulan_bayar }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Petugas</label>
                        <p class="text-sm text-gray-900">{{ $pembayaran->user->name ?? 'N/A' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Lunas
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detail Tagihan -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Tagihan</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID Tagihan</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $pembayaran->tagihan->id_tagihan }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode Tagihan</label>
                        <p class="text-sm text-gray-900">
                            {{ DateTime::createFromFormat('!m', $pembayaran->tagihan->bulan)->format('F') }} {{ $pembayaran->tagihan->tahun }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah kWh</label>
                        <p class="text-sm text-gray-900">{{ number_format($pembayaran->tagihan->jumlah_meter, 0, ',', '.') }} kWh</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tarif per kWh</label>
                        <p class="text-sm text-gray-900">Rp {{ number_format($pembayaran->tagihan->pelanggan->tarif->tarif_per_kwh, 0, ',', '.') }}</p>
                    </div>

                    @if($pembayaran->tagihan->penggunaan)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meter Awal</label>
                        <p class="text-sm text-gray-900">{{ number_format($pembayaran->tagihan->penggunaan->meter_awal, 0, ',', '.') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meter Akhir</label>
                        <p class="text-sm text-gray-900">{{ number_format($pembayaran->tagihan->penggunaan->meter_akhir, 0, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary dan Informasi Pelanggan -->
        <div class="space-y-6">
            <!-- Informasi Pelanggan -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelanggan</h2>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                        <p class="text-sm text-gray-900">{{ $pembayaran->pelanggan->nama_pelanggan }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor KWH</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $pembayaran->pelanggan->nomor_kwh }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <p class="text-sm text-gray-900">{{ $pembayaran->pelanggan->alamat }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tarif</label>
                        <p class="text-sm text-gray-900">{{ $pembayaran->pelanggan->tarif->daya }}VA - {{ $pembayaran->pelanggan->tarif->tarif_per_kwh }}/kWh</p>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pembayaran -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pembayaran</h2>

                <div class="space-y-3">
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-600">Total Tagihan</span>
                        <span class="text-sm font-medium text-gray-900">
                            Rp {{ number_format($pembayaran->tagihan->total_tagihan, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between py-2">
                        <span class="text-sm text-gray-600">Biaya Admin</span>
                        <span class="text-sm font-medium text-gray-900">
                            Rp {{ number_format($pembayaran->biaya_admin, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between py-2">
                            <span class="text-base font-medium text-gray-900">Total Bayar</span>
                            <span class="text-base font-bold text-green-600">
                                Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h2>

                <div class="space-y-3">
                    <button onclick="window.print()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium">
                        Cetak Struk
                    </button>

                    <form action="{{ route('pembayaran.destroy', $pembayaran->id_pembayaran) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin membatalkan pembayaran ini? Tagihan akan kembali menjadi belum lunas.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-medium">
                            Batalkan Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-section, .print-section * {
        visibility: visible;
    }
    .print-section {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endsection
