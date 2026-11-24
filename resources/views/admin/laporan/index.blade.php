@extends ('layouts.app')

@section ('title', 'Laporan Pemasukan & Tunggakan — VOLTPAY')

@section ('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <x-page-header
                title="Laporan Pemasukan & Tunggakan"
                subtitle="Rekapitulasi transaksi pelunasan berdasarkan rentang tanggal serta daftar piutang berjalan"
            />

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('laporan.export-csv', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-xs font-medium text-zinc-900 hover:bg-zinc-50 transition-colors"
                >
                    Export CSV (Excel)
                </a>
                <button
                    type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-950 px-3.5 py-2 text-xs font-medium text-white hover:bg-zinc-800 transition-colors"
                >
                    Cetak Laporan
                </button>
            </div>
        </div>

        {{-- Kop Cetak Khusus Print --}}
        <div class="hidden print:block border-b border-zinc-300 pb-4">
            <h1 class="text-lg font-bold text-zinc-950">VOLTPAY — Laporan Rekapitulasi Pembayaran Listrik</h1>
            <p class="text-xs text-zinc-600 mt-1">
                Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d F Y') }}
            </p>
        </div>

        {{-- Filter Rentang Tanggal --}}
        <form
            method="GET"
            action="{{ route('laporan.index') }}"
            class="bg-white border border-zinc-200 rounded-xl p-4 flex flex-wrap items-end gap-4 print:hidden"
        >
            <div>
                <label for="tanggal_mulai" class="block text-xs font-medium text-zinc-600 mb-1">Tanggal Mulai</label>
                <input
                    type="date"
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="{{ $tanggalMulai }}"
                    class="rounded-lg border border-zinc-200 px-3 py-1.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none"
                />
            </div>
            <div>
                <label for="tanggal_selesai" class="block text-xs font-medium text-zinc-600 mb-1"
                    >Tanggal Selesai</label
                >
                <input
                    type="date"
                    id="tanggal_selesai"
                    name="tanggal_selesai"
                    value="{{ $tanggalSelesai }}"
                    class="rounded-lg border border-zinc-200 px-3 py-1.5 text-sm text-zinc-900 focus:border-zinc-950 focus:outline-none"
                />
            </div>
            <button
                type="submit"
                class="rounded-lg bg-zinc-950 px-4 py-2 text-xs font-medium text-white hover:bg-zinc-800 transition-colors"
            >
                Terapkan Filter
            </button>
        </form>

        {{-- Ringkasan 4 Kartu KPI --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-zinc-200 rounded-xl p-4">
                <p class="text-xs text-zinc-500">Total Pokok Listrik ({{ $ringkasan['jumlah_transaksi'] }} trx)</p>
                <p class="text-lg font-semibold text-zinc-950 tabular-nums mt-1">
                    Rp {{ number_format($ringkasan['total_pokok'], 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white border border-zinc-200 rounded-xl p-4">
                <p class="text-xs text-zinc-500">Total Denda Terkumpul</p>
                <p class="text-lg font-semibold text-zinc-950 tabular-nums mt-1">
                    Rp {{ number_format($ringkasan['total_denda'], 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white border border-zinc-200 rounded-xl p-4">
                <p class="text-xs text-zinc-500">Total Biaya Admin</p>
                <p class="text-lg font-semibold text-zinc-950 tabular-nums mt-1">
                    Rp {{ number_format($ringkasan['total_admin'], 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-zinc-950 text-white rounded-xl p-4">
                <p class="text-xs text-zinc-400">Total Pemasukan Bersih</p>
                <p class="text-lg font-semibold tabular-nums mt-1">
                    Rp {{ number_format($ringkasan['grand_total'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Tabel Pemasukan Berhasil --}}
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-zinc-200 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-zinc-950">Rincian Pembayaran Masuk</h2>
                <span class="text-xs text-zinc-500">{{ $pembayarans->count() }} transaksi berhasil</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50/70">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Tanggal
                            </th>
                            <th
                                class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pelanggan
                            </th>
                            <th
                                class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Periode
                            </th>
                            <th
                                class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pokok
                            </th>
                            <th
                                class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Denda
                            </th>
                            <th
                                class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Admin
                            </th>
                            <th
                                class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Total Bayar
                            </th>
                            <th
                                class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Petugas
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($pembayarans as $p)
                            <tr>
                                <td class="px-4 py-3 text-zinc-700 tabular-nums whitespace-nowrap">
                                    {{
                                        \Carbon\Carbon::parse($p->tanggal_pembayaran)->format(
                                            'd/m/Y',
                                        )
                                    }}
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-zinc-900">{{ $p->tagihan?->pelanggan?->nama_pelanggan ?? '-' }}</p>
                                    <p class="text-xs text-zinc-400 tabular-nums">{{ $p->tagihan?->pelanggan?->nomor_kwh ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-zinc-700">{{ $p->bulan_bayar }}</td>
                                <td class="px-4 py-3 text-right text-zinc-700 tabular-nums">
                                    Rp {{ number_format($p->tagihan?->total_tagihan ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-zinc-700 tabular-nums">
                                    Rp {{ number_format($p->tagihan?->denda ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-zinc-700 tabular-nums">
                                    Rp {{ number_format($p->biaya_admin, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-zinc-950 tabular-nums">
                                    Rp {{ number_format($p->total_bayar, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-xs text-zinc-600">
                                    {{
                                        $p->channel_pembayaran === 'midtrans'
                                            ? 'Online Payment'
                                            : $p->user?->nama_admin ?? '—'
                                    }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-zinc-500">
                                    Tidak ada transaksi pembayaran pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel Tunggakan Berjalan --}}
        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-zinc-200 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-zinc-950">Daftar Tagihan Belum Lunas (Tunggakan Berjalan)</h2>
                    <p class="text-xs text-zinc-500">
                        Total Pokok: Rp {{ number_format($totalTunggakanPokok, 0, ',', '.') }} • Estimasi Denda: Rp {{ number_format($totalEstimasiDenda, 0, ',', '.') }}
                    </p>
                </div>
                <span class="text-xs font-medium text-zinc-700">{{ $tunggakans->count() }} tagihan aktif</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50/70">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pelanggan
                            </th>
                            <th
                                class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Periode
                            </th>
                            <th
                                class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Jatuh Tempo
                            </th>
                            <th
                                class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pokok + Denda
                            </th>
                            <th
                                class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500 print:hidden"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($tunggakans as $t)
                            @php $denda = $t->estimasiDenda(); @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-zinc-900">{{ $t->pelanggan?->nama_pelanggan ?? '-' }}</p>
                                    <p class="text-xs text-zinc-400 tabular-nums">{{ $t->pelanggan?->nomor_kwh ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-zinc-700">{{ $t->bulan }}/{{ $t->tahun }}</td>
                                <td class="px-4 py-3 text-zinc-700 tabular-nums">
                                    {{
                                        $t->tanggal_jatuh_tempo
                                            ? $t->tanggal_jatuh_tempo->format('d/m/Y')
                                            : '-'
                                    }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-zinc-950 tabular-nums">
                                    Rp {{ number_format((float) $t->total_tagihan + $denda, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-status-badge :status="$t->status_tampil" />
                                </td>
                                <td class="px-4 py-3 text-right print:hidden">
                                    <a
                                        href="{{ route('pembayaran.create', ['id_tagihan' => $t->id_tagihan]) }}"
                                        class="inline-flex items-center rounded-lg bg-zinc-950 px-3 py-1.5 text-xs font-medium text-white hover:bg-zinc-800 transition-colors"
                                    >
                                        Proses Bayar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-zinc-500">
                                    Seluruh tagihan pelanggan sudah lunas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
