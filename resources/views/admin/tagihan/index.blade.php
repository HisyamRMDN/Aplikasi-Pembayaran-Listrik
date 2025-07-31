@extends('layouts.app')

@section('title', 'Kelola Tagihan')

@section('content')
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Tagihan</h1>
                <p class="text-gray-600">Daftar tagihan listrik pelanggan</p>
            </div>
            <a href="{{ route('tagihan.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                + Tambah Tagihan
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('tagihan.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama pelanggan atau nomor KWH..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select name="status"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas
                        </option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div>
                    <select name="bulan"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Cari
                </button>
            </form>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tagihan Belum Lunas</p>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ $tagihans->where('status', 'belum_lunas')->count() }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tagihan Lunas</p>
                    <p class="text-2xl font-bold text-green-600">{{ $tagihans->where('status', 'lunas')->count() }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Semua Tagihan</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $tagihans->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor KWH</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bulan/Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penggunaan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Tagihan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tagihans as $index => $tagihan)
                            <tr class="{{ $tagihan->status == 'belum_lunas' ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tagihans->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tagihan->pelanggan->nama_pelanggan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tagihan->pelanggan->nomor_kwh }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }}
                                    {{ $tagihan->tahun }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-semibold text-blue-600">
                                        {{ number_format($tagihan->jumlah_meter) }} kWh
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-semibold">
                                        Rp {{ number_format($tagihan->total_tagihan, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($tagihan->status === 'lunas')
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Belum
                                            Lunas</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}"
                                            class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                            👁️
                                        </a>
                                        <a href="{{ route('tagihan.edit', $tagihan->id_tagihan) }}"
                                            class="text-green-600 hover:text-green-900" title="Edit">
                                            ✏️
                                        </a>
                                        @if ($tagihan->status === 'belum_lunas')
                                            <a href="{{ route('pembayaran.create', ['tagihan' => $tagihan->id_tagihan]) }}"
                                                class="text-purple-600 hover:text-purple-900" title="Bayar">
                                                💳
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('tagihan.destroy', $tagihan->id_tagihan) }}"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data tagihan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tagihans->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
