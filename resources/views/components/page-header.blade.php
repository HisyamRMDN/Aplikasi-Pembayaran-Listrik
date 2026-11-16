@props(['title', 'subtitle' => null])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 pb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-950">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-zinc-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex flex-wrap items-center gap-2.5 self-start sm:self-auto">
            {{ $actions }}
        </div>
    @endif
</div>
