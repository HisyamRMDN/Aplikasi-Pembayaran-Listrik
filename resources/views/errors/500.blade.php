<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>500 Gangguan Sistem — VOLTPAY</title>
    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-50 text-zinc-900 antialiased min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white border border-zinc-200 rounded-2xl p-8 text-center space-y-5">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-zinc-950 text-white mx-auto">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                />
            </svg>
        </div>

        <div class="space-y-1.5">
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400">Error 500</p>
            <h1 class="text-xl font-bold text-zinc-950">Terjadi Gangguan Internal</h1>
            <p class="text-sm text-zinc-500">Sistem mengalami kendala saat memproses permintaan Anda. Silakan muat ulang halaman atau coba beberapa saat lagi.</p>
        </div>

        <div class="pt-2 flex items-center justify-center gap-3">
            <button
                type="button"
                onclick="location.reload()"
                class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-xs font-medium text-zinc-700 hover:bg-zinc-50 transition-colors cursor-pointer"
            >
                Muat Ulang
            </button>
            <a
                href="{{ url('/') }}"
                class="rounded-lg bg-zinc-950 px-4 py-2 text-xs font-medium text-white hover:bg-zinc-800 transition-colors"
            >
                Ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
