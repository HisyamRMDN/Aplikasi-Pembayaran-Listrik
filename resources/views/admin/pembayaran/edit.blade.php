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
            <x-flash-message />

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

                @include('admin.pembayaran._form')

                <!-- Catatan Ringkas -->
                <div
                    class="flex items-start gap-2.5 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-xs text-zinc-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 mt-1.5 shrink-0"></span>
                    <p>Perubahan tanggal pembayaran atau biaya admin akan langsung memperbarui nominal total pada struk dan
                        rekapitulasi harian. Pokok tagihan tidak bisa diubah dari sini.</p>
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
@endsection
