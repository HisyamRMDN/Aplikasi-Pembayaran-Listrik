@extends ('layouts.app')

@section ('title', 'Manajemen Petugas')

@section ('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Manajemen Petugas</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola akun Administrator dan Petugas Loket beserta hak aksesnya.</p>
            </div>
            <a
                href="{{ route('petugas.create') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition"
            >
                + Tambah Petugas
            </a>
        </div>

        {{-- Filter & Pencarian --}}
        <form
            method="GET"
            action="{{ route('petugas.index') }}"
            class="bg-white border border-zinc-200 rounded-2xl p-4 flex flex-col sm:flex-row gap-3"
        >
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama atau username petugas..."
                class="flex-1 rounded-xl border border-zinc-300 px-3.5 py-2 text-sm focus:border-zinc-900 focus:outline-none"
            />
            <select
                name="id_level"
                class="rounded-xl border border-zinc-300 px-3.5 py-2 text-sm focus:border-zinc-900 focus:outline-none"
            >
                <option value="">Semua Level</option>
                @foreach ($levels as $level)
                    <option value="{{ $level->id_level }}" @selected (request('id_level') == $level->id_level)>
                        {{ $level->nama_level }}
                    </option>
                @endforeach
            </select>
            <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition"
            >
                Filter
            </button>
        </form>

        {{-- Tabel Data Petugas --}}
        <div class="bg-white border border-zinc-200 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-50 border-b border-zinc-200 text-xs uppercase tracking-wider text-zinc-500">
                        <tr>
                            <th class="px-5 py-3.5">Nama Petugas</th>
                            <th class="px-5 py-3.5">Username</th>
                            <th class="px-5 py-3.5">Level Akses</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($petugas as $item)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-5 py-3.5 font-medium text-zinc-900">
                                    {{ $item->nama_admin }}
                                    @if ((int) $item->id_user === (int) Auth::id())
                                        <span
                                            class="ml-2 px-2 py-0.5 text-xs font-mono bg-zinc-100 text-zinc-700 rounded-md"
                                            >Anda</span
                                        >
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 font-mono text-zinc-600">{{ $item->username }}</td>
                                <td class="px-5 py-3.5">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->isAdministrator() ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700' }}"
                                    >
                                        {{ $item->level->nama_level ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-2">
                                    <a
                                        href="{{ route('petugas.edit', $item->id_user) }}"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg transition"
                                    >
                                        Edit / Reset Sandi
                                    </a>
                                    @if ((int) $item->id_user !== (int) Auth::id())
                                        <form
                                            action="{{ route('petugas.destroy', $item->id_user) }}"
                                            data-confirm-title="Hapus Data Penggunaan"
                                            data-confirm="Hapus data penggunaan ini? Tagihan terkait juga akan terhapus."
                                            data-confirm-text="Ya, Hapus Data"
                                            method="POST"
                                            class="inline"
                                        >
                                            @csrf
                                            @method ('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-zinc-500">Belum ada data petugas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($petugas->hasPages())
                <div class="px-5 py-3.5 border-t border-zinc-200">{{ $petugas->links() }}</div>
            @endif
        </div>
    </div>
@endsection
