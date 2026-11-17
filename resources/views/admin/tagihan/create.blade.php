@extends('layouts.app')

@section('title', 'Tambah Tagihan')

@section('content')
    <div class="p-6 max-w-3xl">
        <x-page-header title="Tambah Tagihan Manual" subtitle="Untuk tagihan yang tidak berasal dari pencatatan meter." />

        <x-flash-message />

        <div class="rounded-xl border border-zinc-200 bg-white p-6">
            <form method="POST" action="{{ route('tagihan.store') }}">
                @csrf

                @include('admin.tagihan._form')

                <ul class="mt-5 space-y-1 text-xs text-zinc-500">
                    <li>— Tagihan dari pencatatan meter dibuat otomatis di menu Penggunaan.</li>
                    <li>— Tagihan baru selalu berstatus belum lunas. Pelunasan lewat menu Pembayaran.</li>
                    <li>— Tarif per kWh pelanggan saat ini disimpan pada tagihan dan tidak ikut berubah bila tarif diubah
                        nanti.</li>
                </ul>

                <div class="mt-6 flex justify-end gap-2 border-t border-zinc-200 pt-5">
                    <a href="{{ route('tagihan.index') }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
                        Simpan Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
