@extends('layouts.app')

@section('title', 'Edit Tarif — VOLTPAY')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header title="Edit Golongan Tarif" subtitle="Perubahan tarif akan dicatat ke dalam riwayat perubahan sistem">
            <x-slot:actions>
                <a href="{{ route('tarif.index') }}"
                    class="text-xs font-medium text-zinc-500 hover:text-zinc-950 transition-colors">
                    ← Kembali
                </a>
            </x-slot:actions>
        </x-page-header>

        <div class="bg-white border border-zinc-200 rounded-xl p-6 sm:p-8 space-y-5">
            <div
                class="flex items-start gap-2.5 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-xs text-zinc-700">
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-500 mt-1.5 shrink-0"></span>
                <p>Golongan daya ini saat ini digunakan oleh <span class="font-semibold">{{ $tarif->pelanggans_count }}
                        pelanggan</span>. Tagihan lama yang sudah terbit tidak akan berubah.</p>
            </div>

            <form method="POST" action="{{ route('tarif.update', $tarif->id_tarif) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('admin.tarif._form')

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-zinc-100">
                    <a href="{{ route('tarif.index') }}"
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
