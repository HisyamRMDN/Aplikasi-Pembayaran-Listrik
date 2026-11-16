@extends('layouts.app')

@section('title', 'Edit Pembayaran — VOLTPAY')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-zinc-200 pb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-950">Edit Pembayaran</h1>
                <p class="text-sm text-zinc-500 mt-1">Koreksi tanggal pelunasan atau biaya administrasi transaksi</p>
            </div>
            <a href="{{ route('pembayaran.show', $pembayaran->id_pembayaran) }}"
                class="text-xs font-medium text-zinc-500 hover:text-zinc-950 transition-colors">
                ← Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-zinc-200 rounded-xl p-6 sm:p-8 space-y-6">
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Informasi Tagihan (Read Only) -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-50/80 p-4 space-y-3">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500">Informasi Tagihan Terkait</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-zinc-400 block">Pelanggan</span>
                        <span
                            class="font-semibold text-zinc-900 mt-0.5 block">{{ $pembayaran->pelanggan->nama_pelanggan }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block">Nomor KWH</span>
                        <span
                            class="font-mono font-medium text-zinc-900 mt-0.5 block">{{ $pembayaran->pelanggan->nomor_kwh }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block">Periode</span>
                        <span class="font-medium text-zinc-900 mt-0.5 block">{{ $pembayaran->bulan_bayar }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-400 block">Pokok Tagihan</span>
                        <span class="font-semibold text-zinc-950 tabular-nums mt-0.5 block">
                            Rp {{ number_format($pembayaran->tagihan->total_tagihan, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('pembayaran.update', $pembayaran->id_pembayaran) }}"
                id="editPembayaranForm" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Tanggal Pembayaran -->
                    <div>
                        <label for="tanggal_pembayaran"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                            Tanggal Pembayaran
                        </label>
                        <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran"
                            value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran) }}" required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none @error('tanggal_pembayaran') border-red-500 @enderror">
                        @error('tanggal_pembayaran')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Biaya Admin -->
                    <div>
                        <label for="biaya_admin"
                            class="block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2">
                            Biaya Admin (Rp)
                        </label>
                        <input type="number" id="biaya_admin" name="biaya_admin"
                            value="{{ old('biaya_admin', $pembayaran->biaya_admin) }}" min="0" step="100"
                            required
                            class="w-full rounded-lg border border-zinc-200 bg-white px-3.5 py-2.5 text-sm text-zinc-900 tabular-nums focus:border-zinc-950 focus:outline-none @error('biaya_admin') border-red-500 @enderror">
                        @error('biaya_admin')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Bayar -->
                    <div class="md:col-span-2">
                        <div
                            class="rounded-xl bg-zinc-950 text-white p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="text-xs font-medium text-zinc-400">Total Pembayaran Terbaru</p>
                                <p class="text-[11px] text-zinc-500 mt-0.5">
                                    ID #{{ $pembayaran->id_pembayaran }} • Petugas:
                                    {{ $pembayaran->user->nama_admin ?? ($pembayaran->user->name ?? 'N/A') }}
                                </p>
                            </div>
                            <div class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums" id="totalBayar">
                                Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Ringkas -->
                <div
                    class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50/70 px-4 py-3 text-xs text-amber-900">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1.5 shrink-0"></span>
                    <p>Perubahan tanggal pembayaran atau biaya admin akan langsung memperbarui nominal total pada struk dan
                        rekapitulasi harian.</p>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-zinc-100">
                    <a href="{{ route('pembayaran.show', $pembayaran->id_pembayaran) }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-zinc-950 px-5 py-2 text-xs font-semibold text-white hover:bg-zinc-800 transition-colors cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
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
            updateTotalBayar();
        });
    </script>
@endsection
