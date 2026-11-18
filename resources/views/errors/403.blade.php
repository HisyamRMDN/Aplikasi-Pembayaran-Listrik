<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>403 Akses Ditolak — VOLTPAY</title>
    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-50 text-zinc-900 antialiased min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white border border-zinc-200 rounded-2xl p-8 text-center space-y-5">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-zinc-950 text-white mx-auto">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"
                />
            </svg>
        </div>

        <div class="space-y-1.5">
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400">Error 403</p>
            <h1 class="text-xl font-bold text-zinc-950">Akses Tidak Diizinkan</h1>
            <p class="text-sm text-zinc-500">
                {{
                    $exception->getMessage() ?:
                        'Akun Anda tidak memiliki hak akses untuk membuka halaman atau tindakan ini.'
                }}
            </p>
        </div>

        <div class="pt-2 flex items-center justify-center gap-3">
            <button
                type="button"
                onclick="history.back()"
                class="rounded-lg border border-zinc-200 bg-white px-4 py-2 text-xs font-medium text-zinc-700 hover:bg-zinc-50 transition-colors cursor-pointer"
            >
                Kembali
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
