@extends ('layouts.app')

@section ('title', 'Tagihan Listrik — VOLTPAY')

@section ('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <x-page-header
            title="Tagihan Listrik Saya"
            subtitle="Rincian tagihan bulanan, tanggal jatuh tempo, denda keterlambatan, dan bukti pelunasan"
        />

        <x-flash-message />

        <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50/70">
                        <tr>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Periode
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Pemakaian
                            </th>
                            <th
                                class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Jatuh Tempo
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Tagihan Pokok
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Denda
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Total Tagihan
                            </th>
                            <th
                                class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Status
                            </th>
                            <th
                                class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                            >
                                Bukti Bayar
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @forelse ($tagihans as $tagihan)
                            @php
                                $dendaTampil = $tagihan->isLunas() ? (float) $tagihan->denda : $tagihan->estimasiDenda();
                                $totalTampil = (float) $tagihan->total_tagihan + $dendaTampil;
                            @endphp
                            <tr class="hover:bg-zinc-50/60 transition-colors">
                                <td class="px-5 py-3.5 font-medium text-zinc-900">
                                    {{
                                        \Carbon\Carbon::create($tagihan->tahun, $tagihan->bulan, 1)->translatedFormat(
                                            'F Y',
                                        )
                                    }}
                                </td>
                                <td class="px-5 py-3.5 text-right text-zinc-700 tabular-nums">
                                    {{ number_format($tagihan->jumlah_meter) }} kWh
                                </td>
                                <td class="px-5 py-3.5 text-zinc-700 tabular-nums">
                                    {{
                                        $tagihan->tanggal_jatuh_tempo
                                            ? $tagihan->tanggal_jatuh_tempo->translatedFormat('d M Y')
                                            : '-'
                                    }}
                                </td>
                                <td class="px-5 py-3.5 text-right text-zinc-700 tabular-nums">
                                    Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td
                                    class="px-5 py-3.5 text-right tabular-nums {{ $dendaTampil > 0 ? 'text-red-600 font-medium' : 'text-zinc-400' }}"
                                >
                                    {{
                                        $dendaTampil > 0
                                            ? 'Rp ' . number_format($dendaTampil, 0, ',', '.')
                                            : 'Rp 0'
                                    }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-semibold text-zinc-950 tabular-nums">
                                    Rp {{ number_format($totalTampil, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <x-status-badge :status="$tagihan->status_tampil" />
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if ($tagihan->isLunas())
                                        <a
                                            href="{{ route('pelanggan.tagihan.struk', $tagihan->id_tagihan) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-900 hover:bg-zinc-50 transition-colors"
                                        >
                                            Cetak Struk
                                        </a>
                                    @else
                                        <button
                                            type="button"
                                            data-midtrans-pay="{{ route('pelanggan.tagihan.midtrans-token', $tagihan->id_tagihan) }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-zinc-800 transition-colors cursor-pointer"
                                        >
                                            Bayar Online
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @if ($tagihan->status === 'belum_lunas' && $tagihan->pembayarans->isNotEmpty())
                                @php $batalTerakhir = $tagihan->pembayarans->first(); @endphp
                                <tr class="bg-red-50/40">
                                    <td colspan="8" class="px-5 py-2 text-xs text-red-700">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            <span class="font-medium">Transaksi sebelumnya dibatalkan</span>
                                            @if ($batalTerakhir->dibatalkan_pada)
                                                <span class="text-red-500"
                                                    >({{
                                                        \Carbon\Carbon::parse($batalTerakhir->dibatalkan_pada)->translatedFormat(
                                                            'd M Y, H:i',
                                                        )
                                                    }})</span
                                                >
                                            @endif
                                            — Alasan:
                                            <span class="font-medium"
                                                >"{{ $batalTerakhir->alasan_batal ?? 'Dibatalkan oleh petugas' }}"</span
                                            >
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-zinc-500">
                                    Belum ada tagihan listrik yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

<script
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"
></script>
<script>
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-midtrans-pay]');
        if (!btn) return;

        const url = btn.dataset.midtransPay;
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Memuat...';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    Accept: 'application/json',
                },
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Gagal memulai pembayaran.');

            window.snap.pay(data.snap_token, {
                onSuccess: function () {
                    window.location.reload();
                },
                onPending: function () {
                    window.location.reload();
                },
                onError: function () {
                    alert('Pembayaran gagal diproses oleh gateway.');
                },
            });
        } catch (err) {
            alert(err.message);
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
</script>
