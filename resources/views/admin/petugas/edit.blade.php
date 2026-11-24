@extends ('layouts.app')

@section ('title', 'Edit Petugas')

@section ('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div>
            <a href="{{ route('petugas.index') }}" class="text-sm text-zinc-500 hover:text-zinc-900"
                >← Kembali ke Daftar Petugas</a
            >
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 mt-2">Edit Akun & Reset Sandi Petugas</h1>
        </div>

        <form
            action="{{ route('petugas.update', $petugas->id_user) }}"
            method="POST"
            class="bg-white border border-zinc-200 rounded-2xl p-6 space-y-4"
        >
            @csrf
            @method ('PUT')

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Nama Lengkap Petugas</label>
                <input
                    type="text"
                    name="nama_admin"
                    value="{{ old('nama_admin', $petugas->nama_admin) }}"
                    required
                    class="w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-sm focus:border-zinc-900 focus:outline-none"
                />
                @error ('nama_admin')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Username Login</label>
                <input
                    type="text"
                    name="username"
                    value="{{ old('username', $petugas->username) }}"
                    required
                    class="w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-sm font-mono focus:border-zinc-900 focus:outline-none"
                />
                @error ('username')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Level Akses</label>
                <select
                    name="id_level"
                    required
                    class="w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-sm focus:border-zinc-900 focus:outline-none"
                >
                    @foreach ($levels as $level)
                        <option
                            value="{{ $level->id_level }}"
                            @selected (old('id_level', $petugas->id_level) == $level->id_level)
                        >
                            {{ $level->nama_level }}
                        </option>
                    @endforeach
                </select>
                @error ('id_level')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <hr class="border-zinc-200 my-2" />
            <p class="text-xs text-zinc-500">Kosongkan kolom kata sandi di bawah ini jika tidak ingin mereset password petugas.</p>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Kata Sandi Baru (Opsional)</label>
                <input
                    type="password"
                    name="password"
                    class="w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-sm focus:border-zinc-900 focus:outline-none"
                />
                @error ('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full rounded-xl border border-zinc-300 px-3.5 py-2 text-sm focus:border-zinc-900 focus:outline-none"
                />
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <a
                    href="{{ route('petugas.index') }}"
                    class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-xl transition"
                    >Batal</a
                >
                <button
                    type="submit"
                    class="px-5 py-2 text-sm font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
