@if (session('success'))
    <div
        class="flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-xs sm:text-sm text-emerald-900">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div
        class="flex items-center gap-2.5 rounded-xl border border-red-200 bg-red-50/80 px-4 py-3 text-xs sm:text-sm text-red-900">
        <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50/80 px-4 py-3 text-xs text-red-800">
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li class="flex items-center gap-2">
                    <span class="w-1 h-1 rounded-full bg-red-500 shrink-0"></span>
                    <span>{{ $error }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
