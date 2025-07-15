@extends('layouts.app')

@section('title', 'Detail Tagihan')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Tagihan</h1>
            <p class="text-gray-600">Informasi lengkap tagihan listrik</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('tagihan.edit', $tagihan->id_tagihan) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Edit
            </a>
            @if($tagihan->status === 'belum_lunas')
            <a href="{{ route('pembayaran.create', ['tagihan' => $tagihan->id_tagihan]) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Proses Pembayaran
            </a>
            @endif
            <a href="{{ route('tagihan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Bill Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tagihan</h2>

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Periode</label>
                    <p class="text-gray-900 text-lg">{{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }} {{ $tagihan->tahun }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Penggunaan Listrik</label>
                    <p class="text-blue-600 text-2xl font-bold">{{ number_format($tagihan->jumlah_meter) }} kWh</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Tarif per kWh</label>
                    <p class="text-gray-900">Rp {{ number_format($tagihan->pelanggan->tarif->tarif_per_kwh, 2) }}</p>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <label class="text-sm font-medium text-gray-500">Total Tagihan</label>
                    <p class="text-green-600 text-3xl font-bold">Rp {{ number_format($tagihan->total_tagihan, 2) }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Status Pembayaran</label>
                    <div class="mt-1">
                        @if($tagihan->status === 'lunas')
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Lunas
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                Belum Lunas
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelanggan</h2>

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nama Pelanggan</label>
                    <p class="text-gray-900">{{ $tagihan->pelanggan->nama_pelanggan }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Username</label>
                    <p class="text-gray-900">{{ $tagihan->pelanggan->username }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Nomor KWH</label>
                    <p class="text-gray-900">{{ $tagihan->pelanggan->nomor_kwh }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Tarif Listrik</label>
                    <p class="text-gray-900">{{ $tagihan->pelanggan->tarif->daya }} VA</p>
                    <p class="text-sm text-gray-600">Rp {{ number_format($tagihan->pelanggan->tarif->tarif_per_kwh, 2) }}/kWh</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Alamat</label>
                    <p class="text-gray-900">{{ $tagihan->pelanggan->alamat }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Details -->
    @if($tagihan->penggunaan)
    <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Penggunaan</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm font-medium text-gray-600">Meter Awal</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($tagihan->penggunaan->meter_awal) }}</p>
            </div>

            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm font-medium text-gray-600">Meter Akhir</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($tagihan->penggunaan->meter_akhir) }}</p>
            </div>

            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm font-medium text-blue-600">Penggunaan</p>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($tagihan->jumlah_meter) }} kWh</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Payment History -->
    @if($tagihan->pembayarans->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Pembayaran</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Bayar</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bulan Bayar</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Biaya Admin</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Bayar</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($tagihan->pembayarans as $pembayaran)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $pembayaran->tanggal_pembayaran->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $pembayaran->bulan_bayar }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">Rp {{ number_format($pembayaran->biaya_admin, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 font-semibold">Rp {{ number_format($pembayaran->total_bayar, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $pembayaran->user->nama_admin }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('pembayaran.show', $pembayaran->id_pembayaran) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Calculation Details -->
    <div class="mt-6 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">Detail Perhitungan Tagihan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-blue-800 mb-2">Perhitungan:</h4>
                <div class="space-y-2 text-sm text-blue-700">
                    <p>Penggunaan: {{ number_format($tagihan->jumlah_meter) }} kWh</p>
                    <p>Tarif per kWh: Rp {{ number_format($tagihan->pelanggan->tarif->tarif_per_kwh, 2) }}</p>
                    <p>Formula: {{ number_format($tagihan->jumlah_meter) }} × Rp {{ number_format($tagihan->pelanggan->tarif->tarif_per_kwh, 2) }}</p>
                </div>
            </div>
            <div>
                <h4 class="font-medium text-blue-800 mb-2">Total:</h4>
                <p class="text-2xl font-bold text-blue-900">Rp {{ number_format($tagihan->total_tagihan, 2) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
