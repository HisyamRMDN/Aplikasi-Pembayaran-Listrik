@extends('layouts.app')

@section('title', 'Detail Penggunaan')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Penggunaan</h1>
            <p class="text-gray-600">Informasi lengkap data penggunaan listrik</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('penggunaan.edit', $penggunaan->id_penggunaan) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Edit
            </a>
            <a href="{{ route('penggunaan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Usage Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Penggunaan</h2>

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Periode</label>
                    <p class="text-gray-900 text-lg">{{ DateTime::createFromFormat('!m', $penggunaan->bulan)->format('F') }} {{ $penggunaan->tahun }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Meter Awal</label>
                        <p class="text-gray-900 text-lg font-semibold">{{ number_format($penggunaan->meter_awal) }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Meter Akhir</label>
                        <p class="text-gray-900 text-lg font-semibold">{{ number_format($penggunaan->meter_akhir) }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <label class="text-sm font-medium text-gray-500">Total Penggunaan</label>
                    <p class="text-blue-600 text-2xl font-bold">{{ number_format($penggunaan->jumlah_meter) }} kWh</p>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelanggan</h2>

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nama Pelanggan</label>
                    <p class="text-gray-900">{{ $penggunaan->pelanggan->nama_pelanggan }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Nomor KWH</label>
                    <p class="text-gray-900">{{ $penggunaan->pelanggan->nomor_kwh }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Tarif Listrik</label>
                    <p class="text-gray-900">{{ $penggunaan->pelanggan->tarif->daya }} VA</p>
                    <p class="text-sm text-gray-600">Rp {{ number_format($penggunaan->pelanggan->tarif->tarif_per_kwh, 2) }}/kWh</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Alamat</label>
                    <p class="text-gray-900">{{ $penggunaan->pelanggan->alamat }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Bills -->
    <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tagihan Terkait</h2>

        @if($penggunaan->tagihans->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bulan/Tahun</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Penggunaan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Tagihan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($penggunaan->tagihans as $tagihan)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-900">
                            {{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }} {{ $tagihan->tahun }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($tagihan->jumlah_meter) }} kWh</td>
                        <td class="px-4 py-2 text-sm text-gray-900">Rp {{ number_format($tagihan->total_tagihan, 2) }}</td>
                        <td class="px-4 py-2">
                            @if($tagihan->status === 'lunas')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Lunas
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Belum Lunas
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Belum ada tagihan untuk penggunaan ini</p>
        @endif
    </div>

    <!-- Calculation Details -->
    <div class="mt-6 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">Detail Perhitungan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-blue-700 font-medium">Meter Akhir - Meter Awal</p>
                <p class="text-blue-900">{{ number_format($penggunaan->meter_akhir) }} - {{ number_format($penggunaan->meter_awal) }}</p>
            </div>
            <div>
                <p class="text-blue-700 font-medium">Total Penggunaan</p>
                <p class="text-blue-900 text-lg font-bold">{{ number_format($penggunaan->jumlah_meter) }} kWh</p>
            </div>
            <div>
                <p class="text-blue-700 font-medium">Tarif per kWh</p>
                <p class="text-blue-900">Rp {{ number_format($penggunaan->pelanggan->tarif->tarif_per_kwh, 2) }}</p>
            </div>
        </div>
        @if($penggunaan->tagihans->first())
        <div class="mt-4 pt-4 border-t border-blue-200">
            <p class="text-blue-700 font-medium">Estimasi Tagihan</p>
            <p class="text-blue-900 text-xl font-bold">Rp {{ number_format($penggunaan->tagihans->first()->total_tagihan, 2) }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
