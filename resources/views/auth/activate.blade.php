<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun Pelanggan — Aplikasi Pembayaran Listrik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-zinc-100 text-zinc-900 antialiased flex items-center justify-center p-4 sm:p-6 lg:p-10">

    <!-- Main Split Container -->
    <div
        class="w-full max-w-[1040px] bg-white rounded-[28px] p-3 sm:p-4 shadow-sm border border-zinc-200/80 grid grid-cols-1 lg:grid-cols-12 overflow-hidden">

        <!-- Kolom Kiri: Panel Foto SUTET -->
        <div class="relative lg:col-span-6 min-h-[260px] lg:min-h-[620px] rounded-[22px] rounded-br-none bg-cover bg-center bg-no-repeat overflow-hidden flex flex-col justify-between p-6 sm:p-8 text-white"
            style="
        background-image: url('{{ asset('image/electricity.jpg') }}');
        clip-path: polygon(0 0, 88% 0, 100% 100%, 0 100%);
    ">
            <div class="absolute inset-0 bg-gradient-to-b from-black/45 via-black/15 to-black/75"></div>

            <div class="relative z-10 flex items-center justify-between gap-3 pr-12">
                <span class="text-xs font-medium tracking-wide text-white/90">
                    Sistem Pascabayar
                </span>
                <a href="{{ route('login') }}"
                    class="rounded-full border border-white/40 bg-black/25 backdrop-blur-xs px-3.5 py-1.5 text-xs font-medium text-white hover:bg-white hover:text-zinc-900 transition-colors">
                    Halaman Masuk
                </a>
            </div>

            <div class="relative z-10 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-white/15 border border-white/25 backdrop-blur-xs flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.75"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold leading-tight text-white">
                        Aktivasi Mandiri Pelanggan
                    </p>
                    <p class="text-xs text-white/75 mt-0.5">
                        Tautkan Nomor KWH terdaftar ke akun Anda
                    </p>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Aktivasi -->
        <div class="lg:col-span-6 flex flex-col justify-between px-6 py-8 sm:px-12 sm:py-10">

            <div class="flex items-center justify-between">
                <span class="text-sm font-extrabold tracking-wider uppercase text-zinc-900">
                    VOLT<span class="font-normal text-zinc-400">PAY</span>
                </span>
                <span
                    class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 px-3 py-1 text-[11px] font-medium text-zinc-600">
                    Aktivasi Meter
                </span>
            </div>

            <div class="my-auto py-8 max-w-sm w-full mx-auto">
                <div class="text-center mb-7">
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-zinc-950">
                        Aktivasi Akun
                    </h1>
                    <p class="mt-2 text-xs sm:text-sm text-zinc-500">
                        Masukkan Nomor KWH Anda untuk membuat akses login mandiri
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('pelanggan.activate.submit') }}" class="space-y-3.5">
                    @csrf

                    <!-- Nomor KWH -->
                    <div>
                        <label for="nomor_kwh" class="sr-only">Nomor KWH</label>
                        <input id="nomor_kwh" name="nomor_kwh" type="text" required autofocus
                            value="{{ old('nomor_kwh') }}" placeholder="Nomor KWH (contoh: 12345678901)"
                            class="block w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-400 transition-colors focus:border-zinc-900 focus:outline-none">
                    </div>

                    <!-- Username Baru -->
                    <div>
                        <label for="username" class="sr-only">Buat Username</label>
                        <input id="username" name="username" type="text" required value="{{ old('username') }}"
                            placeholder="Buat Username Baru"
                            class="block w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-400 transition-colors focus:border-zinc-900 focus:outline-none">
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label for="password" class="sr-only">Buat Password</label>
                        <input id="password" name="password" type="password" required
                            placeholder="Buat Password (min. 6 karakter)"
                            class="block w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-400 transition-colors focus:border-zinc-900 focus:outline-none">
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="sr-only">Ulangi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            placeholder="Ulangi Password"
                            class="block w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-400 transition-colors focus:border-zinc-900 focus:outline-none">
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit"
                            class="w-full rounded-full bg-zinc-950 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 cursor-pointer">
                            Aktifkan Akun
                        </button>
                    </div>
                </form>

                <p class="mt-6 text-center text-xs text-zinc-500">
                    Sudah memiliki akun aktif?
                    <a href="{{ route('login') }}" class="font-semibold text-zinc-900 hover:underline">
                        Masuk sekarang
                    </a>
                </p>
            </div>

            <div class="text-center text-[11px] text-zinc-400">
                Aplikasi Pembayaran Listrik Pascabayar © {{ date('Y') }}
            </div>
        </div>
    </div>
</body>

</html>
