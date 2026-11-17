@extends('layouts.app')

@section('title', 'Tambah Penggunaan')

@section('content')
    <div class="p-6 max-w-3xl">
        <x-page-header title="Tambah Penggunaan" subtitle="Catat meter listrik pelanggan. Tagihan dibuat otomatis." />

        <x-flash-message />

        <div class="rounded-xl border border-zinc-200 bg-white p-6">
            <form method="POST" action="{{ route('penggunaan.store') }}">
                @csrf

                @include('admin.penggunaan._form')

                <ul class="mt-5 space-y-1 text-xs text-zinc-500">
                    <li>— Meter akhir harus lebih besar dari meter awal.</li>
                    <li>— Satu pelanggan hanya boleh punya satu data per bulan dan tahun.</li>
                    <li>— Tagihan berstatus belum lunas akan dibuat setelah data disimpan.</li>
                </ul>

                <div class="mt-6 flex justify-end gap-2 border-t border-zinc-200 pt-5">
                    <a href="{{ route('penggunaan.index') }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
