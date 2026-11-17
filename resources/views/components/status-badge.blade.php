@props(['status'])

@php
    $styles = match ($status) {
        'lunas', 'berhasil' => [
            'wrapper' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
            'dot' => 'bg-emerald-500',
            'label' => $status === 'berhasil' ? 'Berhasil' : 'Lunas',
        ],
        'terlambat' => [
            'wrapper' => 'bg-red-50 border-red-200 text-red-700',
            'dot' => 'bg-red-500',
            'label' => 'Terlambat',
        ],
        'batal' => [
            'wrapper' => 'bg-zinc-100 border-zinc-300 text-zinc-600',
            'dot' => 'bg-zinc-400',
            'label' => 'Dibatalkan',
        ],
        'belum_aktivasi' => [
            'wrapper' => 'bg-zinc-100 border-zinc-300 text-zinc-700',
            'dot' => 'bg-zinc-500',
            'label' => 'Belum Aktivasi',
        ],
        default => [
            'wrapper' => 'bg-amber-50 border-amber-200 text-amber-700',
            'dot' => 'bg-amber-500',
            'label' => 'Belum Lunas',
        ],
    };
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium ' . $styles['wrapper']]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $styles['dot'] }}"></span>
    {{ $styles['label'] }}
</span>
