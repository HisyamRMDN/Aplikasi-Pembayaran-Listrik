@extends('layouts.app')

@section('title', 'Tambah Pembayaran')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Pembayaran</h1>
        <p class="text-gray-600">Proses pembayaran tagihan listrik pelanggan</p>
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

        <form method="POST" action="{{ route('pembayaran.store') }}" id="pembayaranForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tagihan -->
                <div class="md:col-span-2">
                    <label for="id_tagihan" class="block text-sm font-medium text-gray-700 mb-2">Tagihan</label>
                    <select id="id_tagihan" name="id_tagihan" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_tagihan') border-red-500 @enderror">
                        <option value="">Pilih Tagihan</option>
                        @foreach($tagihans as $tagihan)
                        <option value="{{ $tagihan->id_tagihan }}"
                                data-pelanggan="{{ $tagihan->pelanggan->nama_pelanggan }}"
                                data-nomor-kwh="{{ $tagihan->pelanggan->nomor_kwh }}"
                                data-bulan="{{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }}"
                                data-tahun="{{ $tagihan->tahun }}"
                                data-total="{{ $tagihan->total_tagihan }}"
                                {{ old('id_tagihan') == $tagihan->id_tagihan ? 'selected' : '' }}>
                            {{ $tagihan->pelanggan->nama_pelanggan }} - {{ DateTime::createFromFormat('!m', $tagihan->bulan)->format('F') }} {{ $tagihan->tahun }}
                            (Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }})
                        </option>
                        @endforeach
                    </select>
                    @error('id_tagihan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Tagihan -->
                <div class="md:col-span-2" id="infoTagihan" style="display: none;">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">Detail Tagihan</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700">Pelanggan:</span>
                                <span id="namaPelanggan" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">No. KWH:</span>
                                <span id="nomorKwh" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Periode:</span>
                                <span id="periode" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Total Tagihan:</span>
                                <span id="totalTagihan" class="font-medium text-green-600"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tanggal Pembayaran -->
                <div>
                    <label for="tanggal_pembayaran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pembayaran</label>
                    <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal_pembayaran') border-red-500 @enderror">
                    @error('tanggal_pembayaran')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Biaya Admin -->
                <div>
                    <label for="biaya_admin" class="block text-sm font-medium text-gray-700 mb-2">Biaya Admin (Rp)</label>
                    <input type="number" id="biaya_admin" name="biaya_admin" value="{{ old('biaya_admin', '2500') }}" min="0" step="100" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('biaya_admin') border-red-500 @enderror">
                    @error('biaya_admin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Bayar (Read Only) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total yang Harus Dibayar</label>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600" id="totalBayar">Rp 0</div>
                        <div class="text-sm text-green-700">Total Tagihan + Biaya Admin</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('pembayaran.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg">
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                    Proses Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tagihanSelect = document.getElementById('id_tagihan');
    const biayaAdminInput = document.getElementById('biaya_admin');
    const infoTagihan = document.getElementById('infoTagihan');
    const namaPelanggan = document.getElementById('namaPelanggan');
    const nomorKwh = document.getElementById('nomorKwh');
    const periode = document.getElementById('periode');
    const totalTagihan = document.getElementById('totalTagihan');
    const totalBayar = document.getElementById('totalBayar');

    function updateTagihanInfo() {
        const selectedOption = tagihanSelect.options[tagihanSelect.selectedIndex];

        if (selectedOption.value) {
            const pelanggan = selectedOption.getAttribute('data-pelanggan');
            const nomor = selectedOption.getAttribute('data-nomor-kwh');
            const bulan = selectedOption.getAttribute('data-bulan');
            const tahun = selectedOption.getAttribute('data-tahun');
            const total = parseInt(selectedOption.getAttribute('data-total')) || 0;

            namaPelanggan.textContent = pelanggan;
            nomorKwh.textContent = nomor;
            periode.textContent = bulan + ' ' + tahun;
            totalTagihan.textContent = 'Rp ' + total.toLocaleString('id-ID');

            infoTagihan.style.display = 'block';
            updateTotalBayar(total);
        } else {
            infoTagihan.style.display = 'none';
            updateTotalBayar(0);
        }
    }

    function updateTotalBayar(tagihanAmount = null) {
        if (tagihanAmount === null) {
            const selectedOption = tagihanSelect.options[tagihanSelect.selectedIndex];
            tagihanAmount = selectedOption.value ? parseInt(selectedOption.getAttribute('data-total')) || 0 : 0;
        }

        const biayaAdmin = parseInt(biayaAdminInput.value) || 0;
        const total = tagihanAmount + biayaAdmin;

        totalBayar.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    tagihanSelect.addEventListener('change', updateTagihanInfo);
    biayaAdminInput.addEventListener('input', () => updateTotalBayar());

    // Initialize on page load
    updateTagihanInfo();
});
</script>
@endsection
