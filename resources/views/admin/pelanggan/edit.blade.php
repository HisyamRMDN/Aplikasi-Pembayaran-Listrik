@extends('layouts.app')

@section('title', 'Edit Pelanggan — VOLTPAY')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header title="Edit Pelanggan" subtitle="Perbarui informasi meteran dan golongan daya pelanggan">
            <x-slot:actions>
                <a href="{{ route('pelanggan.show', $pelanggan->id_pelanggan) }}"
                    class="text-xs font-medium text-zinc-500 hover:text-zinc-950 transition-colors">
                    ← Kembali
                </a>
            </x-slot:actions>
        </x-page-header>

        <div class="bg-white border border-zinc-200 rounded-xl p-6 sm:p-8">
            <form method="POST" action="{{ route('pelanggan.update', $pelanggan->id_pelanggan) }}" class="space-y-5">
                @csrf
                @method('PUT')

                @include('admin.pelanggan._form')

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-zinc-100">
                    <a href="{{ route('pelanggan.show', $pelanggan->id_pelanggan) }}"
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
