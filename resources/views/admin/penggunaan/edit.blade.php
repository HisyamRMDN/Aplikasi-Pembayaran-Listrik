@extends('layouts.app')

@section('title', 'Edit Penggunaan')

@section('content')
    <div class="p-6 max-w-3xl">
        <x-page-header title="Edit Penggunaan" subtitle="Perbarui data meter. Tagihan terkait ikut diperbarui." />

        <x-flash-message />

        <div class="rounded-xl border border-zinc-200 bg-white p-6">
            <form method="POST" action="{{ route('penggunaan.update', $penggunaan->id_penggunaan) }}">
                @csrf
                @method('PUT')

                @include('admin.penggunaan._form')

                <div class="mt-5 rounded-lg border border-zinc-200 px-4 py-3 text-xs text-zinc-500">
                    Tersimpan saat ini:
                    <span class="font-mono tabular-nums text-zinc-900">{{ number_format($penggunaan->meter_awal) }}</span>
                    →
                    <span class="font-mono tabular-nums text-zinc-900">{{ number_format($penggunaan->meter_akhir) }}</span>
                    =
                    <span
                        class="font-mono font-semibold tabular-nums text-zinc-950">{{ number_format($penggunaan->jumlah_meter) }}
                        kWh</span>
                </div>

                <div class="mt-6 flex justify-end gap-2 border-t border-zinc-200 pt-5">
                    <a href="{{ route('penggunaan.show', $penggunaan->id_penggunaan) }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
