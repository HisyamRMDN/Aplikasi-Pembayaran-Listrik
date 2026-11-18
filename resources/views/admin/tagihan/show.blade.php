@extends ('layouts.app')

@section ('title', 'Detail Tagihan')

@section ('content')
    @php
        $namaBulan = fn($i) => \Carbon\Carbon::create(2000, $i, 1)->locale('id')->translatedFormat('F');
        $label = 'text-xs font-semibold uppercase tracking-wider text-zinc-500';
        $tarif = $tagihan->pelanggan->tarif;
    @endphp

    <div class="p-6">
        <x-page-header
            title="Detail Tagihan"
            subtitle="{{ $namaBulan($tagihan->bulan) }} {{ $tagihan->tahun }} · {{ $tagihan->pelanggan->nama_pelanggan }}"
        >
            <x-slot:actions>
                <a
                    href="{{ route('tagihan.index') }}"
                    class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
                >
                    Kembali
                </a>
                @if ($tagihan->bisaDiubah())
                    <a
                        href="{{ route('tagihan.edit', $tagihan->id_tagihan) }}"
                        class="rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
                    >
                        Edit
                    </a>
                @elseif (!$tagihan->isLunas())
                    <a
                        href="{{ route('pembayaran.create', ['id_tagihan' => $tagihan->id_tagihan]) }}"
                        class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800"
                    >
                        Proses Bayar
                    </a>
                @endif
                @if ($tagihan->status === 'belum_lunas')
                    <a
                        href="{{ route('pembayaran.create', ['tagihan' => $tagihan->id_tagihan]) }}"
                        class="rounded-lg bg-zinc-950 px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800"
                    >
                        Proses Pembayaran
                    </a>
                @endif
            </x-slot:actions>
        </x-page-header>

        <x-flash-message />

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <!-- Informasi tagihan -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-5 text-sm font-semibold text-zinc-950">Informasi Tagihan</h2>

                <dl class="space-y-5">
                    <div>
                        <dt class="{{ $label }}">Periode</dt>
                        <dd class="mt-1 text-sm text-zinc-900">
                            {{ $namaBulan($tagihan->bulan) }} <span class="tabular-nums">{{ $tagihan->tahun }}</span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="{{ $label }}">Pemakaian</dt>
                            <dd class="mt-1 font-mono text-lg tabular-nums text-zinc-900">
                                {{ number_format($tagihan->jumlah_meter) }} kWh
                            </dd>
                        </div>
                        <div>
                            <dt class="{{ $label }}">Tarif per kWh</dt>
                            <dd class="mt-1 font-mono text-lg tabular-nums text-zinc-900">
                                Rp {{ number_format($tagihan->tarif_per_kwh, 2) }}
                            </dd>
                        </div>
                    </div>
                    <div class="border-t border-zinc-200 pt-5">
                        <dt class="{{ $label }}">Total Tagihan</dt>
                        <dd class="mt-1 font-mono text-3xl font-semibold tabular-nums text-zinc-950">
                            Rp {{ number_format($tagihan->total_tagihan, 2) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="{{ $label }}">Status Pembayaran</dt>
                        <dd class="mt-1.5"><x-status-badge :status="$tagihan->status" /></dd>
                    </div>
                </dl>
            </section>

            <!-- Informasi pelanggan -->
            <section class="rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-5 text-sm font-semibold text-zinc-950">Informasi Pelanggan</h2>

                <dl class="space-y-5">
                    <div>
                        <dt class="{{ $label }}">Nama Pelanggan</dt>
                        <dd class="mt-1 text-sm text-zinc-900">{{ $tagihan->pelanggan->nama_pelanggan }}</dd>
                    </div>
                    <div>
                        <dt class="{{ $label }}">Username</dt>
                        <dd class="mt-1 font-mono text-sm text-zinc-900">{{ $tagihan->pelanggan->username ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="{{ $label }}">Nomor KWH</dt>
                        <dd class="mt-1 font-mono text-sm text-zinc-900">{{ $tagihan->pelanggan->nomor_kwh }}</dd>
                    </div>
                    <div>
                        <dt class="{{ $label }}">Golongan Tarif Saat Ini</dt>
                        <dd class="mt-1 text-sm text-zinc-900">
                            <span class="tabular-nums">{{ number_format($tarif->daya) }}</span> VA
                            <span class="text-zinc-500"
                                >· Rp
                                <span class="tabular-nums">{{ number_format($tarif->tarif_per_kwh, 2) }}</span
                                >/kWh</span
                            >
                        </dd>
                    </div>
                    <div>
                        <dt class="{{ $label }}">Alamat</dt>
                        <dd class="mt-1 text-sm text-zinc-900">{{ $tagihan->pelanggan->alamat }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <!-- Detail penggunaan -->
        @if ($tagihan->penggunaan)
            <section class="mt-5 rounded-xl border border-zinc-200 bg-white p-6">
                <h2 class="mb-5 text-sm font-semibold text-zinc-950">Detail Penggunaan</h2>

                <dl class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-lg border border-zinc-200 p-4">
                        <dt class="{{ $label }}">Meter Awal</dt>
                        <dd class="mt-1 font-mono text-2xl font-semibold tabular-nums text-zinc-900">
                            {{ number_format($tagihan->penggunaan->meter_awal) }}
                        </dd>
                    </div>
                    <div class="rounded-lg border border-zinc-200 p-4">
                        <dt class="{{ $label }}">Meter Akhir</dt>
                        <dd class="mt-1 font-mono text-2xl font-semibold tabular-nums text-zinc-900">
                            {{ number_format($tagihan->penggunaan->meter_akhir) }}
                        </dd>
                    </div>
                    <div class="rounded-lg border border-zinc-950 bg-zinc-50 p-4">
                        <dt class="{{ $label }}">Pemakaian</dt>
                        <dd class="mt-1 font-mono text-2xl font-semibold tabular-nums text-zinc-950">
                            {{ number_format($tagihan->jumlah_meter) }} kWh
                        </dd>
                    </div>
                </dl>
            </section>
        @endif

        <!-- Riwayat pembayaran -->
        @if ($tagihan->pembayarans->count() > 0)
            <section class="mt-5 overflow-hidden rounded-xl border border-zinc-200 bg-white">
                <div class="border-b border-zinc-200 px-6 py-4">
                    <h2 class="text-sm font-semibold text-zinc-950">Riwayat Pembayaran</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-zinc-600">
                                <th class="px-6 py-3">Tanggal Bayar</th>
                                <th class="px-6 py-3">Bulan Bayar</th>
                                <th class="px-6 py-3 text-right">Biaya Admin</th>
                                <th class="px-6 py-3 text-right">Total Bayar</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Petugas</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 text-sm">
                            @foreach ($tagihan->pembayarans as $pembayaran)
                                <tr class="hover:bg-zinc-50">
                                    <td class="whitespace-nowrap px-6 py-3.5 tabular-nums text-zinc-900">
                                        {{ $pembayaran->tanggal_pembayaran->format('d/m/Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-zinc-900">
                                        {{ $pembayaran->bulan_bayar }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-3.5 text-right font-mono tabular-nums text-zinc-600"
                                    >
                                        Rp {{ number_format($pembayaran->biaya_admin, 2) }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-3.5 text-right font-mono font-semibold tabular-nums text-zinc-950"
                                    >
                                        Rp {{ number_format($pembayaran->total_bayar, 2) }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if ($pembayaran->status === 'batal')
                                            <span
                                                class="inline-flex rounded-full border border-zinc-300 px-2 py-0.5 text-xs font-medium text-zinc-500"
                                                >Dibatalkan</span
                                            >
                                            @if ($pembayaran->alasan_batal)
                                                <span
                                                    class="mt-1 block text-xs text-zinc-500"
                                                    >{{ $pembayaran->alasan_batal }}</span
                                                >
                                            @endif
                                        @else
                                            <span
                                                class="inline-flex rounded-full border border-zinc-200 px-2 py-0.5 text-xs font-medium text-zinc-700"
                                                >Berhasil</span
                                            >
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-zinc-900">
                                        {{ $pembayaran->user->nama_admin }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-3.5 text-right">
                                        <a
                                            href="{{ route('pembayaran.show', $pembayaran->id_pembayaran) }}"
                                            class="font-medium text-zinc-900 hover:underline"
                                            >Detail</a
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <!-- Perhitungan -->
        <section class="mt-5 rounded-xl border border-zinc-200 bg-white p-6">
            <h2 class="mb-5 text-sm font-semibold text-zinc-950">Detail Perhitungan</h2>
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <p class="font-mono text-sm tabular-nums text-zinc-600">
                    {{ number_format($tagihan->jumlah_meter) }} kWh × Rp {{ number_format($tagihan->tarif_per_kwh, 2) }}
                </p>
                <p class="font-mono text-2xl font-semibold tabular-nums text-zinc-950">
                    Rp {{ number_format($tagihan->total_tagihan, 2) }}
                </p>
            </div>
        </section>
    </div>
@endsection
