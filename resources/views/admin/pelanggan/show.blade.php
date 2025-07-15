@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pelanggan</h1>
            <p class="text-gray-600">Informasi lengkap pelanggan</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('pelanggan.edit', $pelanggan->id_pelanggan) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Edit
            </a>
            <a href="{{ route('pelanggan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pelanggan</h2>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nama Pelanggan</label>
                        <p class="text-gray-900">{{ $pelanggan->nama_pelanggan }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Username</label>
                        <p class="text-gray-900">{{ $pelanggan->username }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Nomor KWH</label>
                        <p class="text-gray-900">{{ $pelanggan->nomor_kwh }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Tarif Listrik</label>
                        <p class="text-gray-900">{{ $pelanggan->tarif->daya }} VA</p>
                        <p class="text-sm text-gray-600">Rp {{ number_format($pelanggan->tarif->tarif_per_kwh, 2) }}/kWh</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Alamat</label>
                        <p class="text-gray-900">{{ $pelanggan->alamat }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Penggunaan</h2>

                @if($pelanggan->penggunaans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bulan/Tahun</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Meter Awal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Meter Akhir</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Penggunaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pelanggan->penggunaans->sortByDesc('tahun')->sortByDesc('bulan') as $penggunaan)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    {{ DateTime::createFromFormat('!m', $penggunaan->bulan)->format('F') }} {{ $penggunaan->tahun }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($penggunaan->meter_awal) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($penggunaan->meter_akhir) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($penggunaan->jumlah_meter) }} kWh</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500 text-center py-4">Belum ada data penggunaan</p>
                @endif
            </div>

            <!-- Bills History -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Tagihan</h2>

                @if($pelanggan->tagihans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bulan/Tahun</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Penggunaan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Tagihan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pelanggan->tagihans->sortByDesc('tahun')->sortByDesc('bulan') as $tagihan)
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500 text-center py-4">Belum ada data tagihan</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
