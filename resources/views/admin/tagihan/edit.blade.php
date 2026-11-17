@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('content')
    @php
        $namaBulan = fn($i) => \Carbon\Carbon::create(2000, $i, 1)->locale('id')->translatedFormat('F');
        $label = 'text-xs font-semibold uppercase tracking-wider text-zinc-500';
        $tarif = $tagihan->pelanggan->tarif;
    @endphp

    <div class="p-6 max-w-3xl">
        <x-page-header title="Edit Tagihan Manual"
            subtitle="{{ $namaBulan($tagihan->bulan) }} {{ $tagihan->tahun }} · {{ $tagihan->pelanggan->nama_pelanggan }}" />

        <x-flash-message />

        <div class="rounded-xl border border-zinc-200 bg-white p-6">
            <!-- Info read-only -->
            <dl class="mb-6 grid grid-cols-1 gap-4 rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-sm md:grid-cols-3">
                <div>
                    <dt class="{{ $label }}">Pelanggan</dt>
                    <dd class="mt-1 text-zinc-900">{{ $tagihan->pelanggan->nama_pelanggan }}</dd>
                </div>
                <div>
                    <dt class="{{ $label }}">Nomor KWH</dt>
                    <dd class="mt-1 font-mono text-zinc-900">{{ $tagihan->pelanggan->nomor_kwh }}</dd>
                </div>
                <div>
                    <dt class="{{ $label }}">Tarif Pada Tagihan</dt>
                    <dd class="mt-1 text-zinc-900">
                        <span class="tabular-nums">{{ number_format($tarif->daya) }}</span> VA
                        <span class="text-zinc-500">· Rp <span
                                class="tabular-nums">{{ number_format($tagihan->tarif_per_kwh, 2) }}</span>/kWh</span>
                    </dd>
                </div>
            </dl>

            <form method="POST" action="{{ route('tagihan.update', $tagihan->id_tagihan) }}">
                @csrf
                @method('PUT')

                @include('admin.tagihan._form')

                <!-- Pratinjau total -->
                <div class="mt-5 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="{{ $label }}">Pemakaian</span>
                        <span id="preview-usage"
                            class="font-mono tabular-nums text-zinc-900">{{ number_format($tagihan->jumlah_meter) }}
                            kWh</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-sm">
                        <span class="{{ $label }}">Total Tagihan</span>
                        <span id="preview-total" class="font-mono text-lg font-semibold tabular-nums text-zinc-950">Rp
                            {{ number_format($tagihan->total_tagihan, 2) }}</span>
                    </div>
                </div>

                <ul class="mt-5 space-y-1 text-xs text-zinc-500">
                    <li>— Mengubah kWh akan mengubah total tagihan secara otomatis.</li>
                    <li>— Status pembayaran tidak bisa diubah di sini. Gunakan menu Pembayaran.</li>
                </ul>

                <div class="mt-6 flex justify-end gap-2 border-t border-zinc-200 pt-5">
                    <a href="{{ route('tagihan.show', $tagihan->id_tagihan) }}"
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('jumlah_meter');
            const usage = document.getElementById('preview-usage');
            const total = document.getElementById('preview-total');
            const tarif = {{ (float) $tagihan->tarif_per_kwh }};

            input.addEventListener('input', function() {
                const kwh = parseInt(this.value, 10) || 0;
                usage.textContent = kwh.toLocaleString('id-ID') + ' kWh';
                total.textContent = 'Rp ' + (kwh * tarif).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            });
        });
    </script>
@endsection
