@extends('layouts.app')

@section('title', 'Riwayat Penggunaan')

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Penggunaan Listrik</h1>
            <p class="text-gray-600">Berikut adalah daftar penggunaan listrik oleh {{ $pelanggan->nama_pelanggan }}</p>
        </div>

        @if ($penggunaan->count() > 0)
            <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bulan/Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter Awal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter Akhir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penggunaan (kWh)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($penggunaan as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->bulan }}/{{ $item->tahun }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($item->meter_awal) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($item->meter_akhir) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($item->jumlah_meter) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 mt-8 text-center">Belum ada data penggunaan.</p>
        @endif
    </div>
@endsection
