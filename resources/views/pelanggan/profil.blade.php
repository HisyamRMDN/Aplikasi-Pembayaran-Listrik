@extends('layouts.app')

@section('title', 'Profil & Keamanan Akun — VOLTPAY')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header title="Profil & Keamanan Akun"
            subtitle="Kelola informasi alamat penagihan dan perbarui kata sandi akun pelanggan Anda" />

        <x-flash-message />

        {{-- Informasi Akun & Alamat --}}
        <form action="{{ route('pelanggan.profil.update') }}" method="POST"
            class="bg-white border border-zinc-200 rounded-xl p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-sm font-semibold text-zinc-950">Informasi Pelanggan</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Nomor KWH dan golongan daya ditetapkan oleh petugas administrasi.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-500 mb-1.5">Nomor KWH / IDPEL</label>
                    <input type="text" value="{{ $pelanggan->nomor_kwh }}" disabled
                        class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3.5 py-2 text-sm text-zinc-500 tabular-nums cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-500 mb-1.5">Golongan Daya</label>
                    <input type="text"
                        value="{{ number_format($pelanggan->tarif->daya) }} VA (Rp {{ number_format($pelanggan->tarif->tarif_per_kwh, 2, ',', '.') }}/kWh)"
                        disabled
                        class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3.5 py-2 text-sm text-zinc-500 tabular-nums cursor-not-allowed">
                </div>
            </div>

            <div>
                <label for="nama_pelanggan" class="block text-xs font-medium text-zinc-700 mb-1.5">Nama Lengkap</label>
                <input type="text" id="nama_pelanggan" name="nama_pelanggan"
                    value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}" required
                    class="w-full rounded-lg border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none">
                @error('nama_pelanggan')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="alamat" class="block text-xs font-medium text-zinc-700 mb-1.5">Alamat Pemasangan</label>
                <textarea id="alamat" name="alamat" rows="3" required
                    class="w-full rounded-lg border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="rounded-lg bg-zinc-950 px-4 py-2 text-xs font-medium text-white hover:bg-zinc-800 transition-colors">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>

        {{-- Ganti Password --}}
        <form action="{{ route('pelanggan.profil.password') }}" method="POST"
            class="bg-white border border-zinc-200 rounded-xl p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-sm font-semibold text-zinc-950">Ubah Kata Sandi</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Gunakan minimal 6 karakter untuk menjaga keamanan akses portal Anda.
                </p>
            </div>

            <div>
                <label for="current_password" class="block text-xs font-medium text-zinc-700 mb-1.5">Kata Sandi Saat
                    Ini</label>
                <input type="password" id="current_password" name="current_password" required
                    class="w-full rounded-lg border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none">
                @error('current_password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-medium text-zinc-700 mb-1.5">Kata Sandi Baru</label>
                    <input type="password" id="password" name="password" required
                        class="w-full rounded-lg border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none">
                    @error('password')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-medium text-zinc-700 mb-1.5">Konfirmasi
                        Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full rounded-lg border border-zinc-200 px-3.5 py-2 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="rounded-lg bg-zinc-950 px-4 py-2 text-xs font-medium text-white hover:bg-zinc-800 transition-colors">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>
@endsection
