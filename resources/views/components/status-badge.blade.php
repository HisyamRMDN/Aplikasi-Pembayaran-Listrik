@props(['status'])

@php
    $styles = match ($status) {
        'lunas' => [
            'wrapper' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
            'dot' => 'bg-emerald-500',
            'label' => 'Lunas',
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
