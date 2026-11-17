@php
    $isEdit = isset($pembayaran);
    $tanggal = old('tanggal_pembayaran', $isEdit ? $pembayaran->tanggal_pembayaran->format('Y-m-d') : date('Y-m-d'));
    $biaya = old('biaya_admin', $isEdit ? (float) $pembayaran->biaya_admin : 2500);
    $pokok = $isEdit ? (float) $pembayaran->tagihan->total_tagihan : 0;
    $base = 'w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-zinc-900 focus:outline-none';
    $cls = fn($name) => $base .
        ' ' .
        ($errors->has($name) ? 'border-red-500 focus:border-red-600' : 'border-zinc-200 focus:border-zinc-950');
    $label = 'block text-xs font-semibold uppercase tracking-wider text-zinc-600 mb-2';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- Tanggal Pembayaran -->
    <div>
        <label for="tanggal_pembayaran" class="{{ $label }}">Tanggal Pembayaran</label>
        <input type="date" id="tanggal_pembayaran" name="tanggal_pembayaran" value="{{ $tanggal }}" required
            class="{{ $cls('tanggal_pembayaran') }}">
        @error('tanggal_pembayaran')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Biaya Admin -->
    <div>
        <label for="biaya_admin" class="{{ $label }}">Biaya Admin (Rp)</label>
        <input type="number" id="biaya_admin" name="biaya_admin" value="{{ $biaya }}" min="0"
            step="100" required class="{{ $cls('biaya_admin') }} tabular-nums">
        @error('biaya_admin')
            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <!-- Total Bayar -->
    <div class="md:col-span-2">
        <div
            class="rounded-xl bg-zinc-950 text-white p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <p class="text-xs font-medium text-zinc-400">
                    {{ $isEdit ? 'Total Pembayaran Terbaru' : 'Total yang Harus Dibayar' }}
                </p>
                <p class="text-[11px] text-zinc-500 mt-0.5">
                    @if ($isEdit)
                        ID #{{ $pembayaran->id_pembayaran }} • Petugas:
                        {{ $pembayaran->user->nama_admin ?? ($pembayaran->user->name ?? 'N/A') }}
                    @else
                        Pokok tagihan + biaya administrasi loket
                    @endif
                </p>
            </div>
            <div class="text-2xl sm:text-3xl font-bold tracking-tight tabular-nums" id="totalBayar"
                data-pokok="{{ $pokok }}">
                Rp 0
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const biaya = document.getElementById('biaya_admin');
        const out = document.getElementById('totalBayar');
        let pokok = parseFloat(out.dataset.pokok) || 0;

        function render() {
            const total = pokok + (parseFloat(biaya.value) || 0);
            out.textContent = 'Rp ' + total.toLocaleString('id-ID', {
                maximumFractionDigits: 2
            });
        }

        // Dipanggil halaman create saat tagihan dipilih
        window.setPokokTagihan = function(nilai) {
            pokok = nilai;
            render();
        };

        biaya.addEventListener('input', render);
        render();
    });
</script>
