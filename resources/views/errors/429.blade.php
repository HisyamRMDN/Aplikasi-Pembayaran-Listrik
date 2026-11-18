<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>429 - Terlalu Banyak Percobaan | VOLTPAY</title>
    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 flex items-center justify-center p-6 antialiased">
    <div class="max-w-md w-full bg-white border border-zinc-200 rounded-2xl p-8 text-center shadow-sm">
        <span
            class="inline-block px-3 py-1 text-xs font-mono font-semibold tracking-wider uppercase bg-zinc-100 text-zinc-700 rounded-full mb-4"
        >
            Error 429
        </span>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 mb-2">Terlalu Banyak Percobaan</h1>
        <p class="text-sm text-zinc-600 leading-relaxed mb-6">Demi keamanan akun, akses login atau aktivasi sementara dibatasi karena terlalu banyak percobaan dalam waktu singkat. Silakan tunggu 1 menit sebelum mencoba kembali.</p>
        <a
            href="{{ url('/') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition"
        >
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
