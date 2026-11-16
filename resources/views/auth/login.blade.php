<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ request()->routeIs('pelanggan.login') ? 'Masuk Pelanggan' : 'Masuk Petugas' }} — Aplikasi Pembayaran
        Listrik</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-zinc-100 text-zinc-900 antialiased flex items-center justify-center p-4 sm:p-6 lg:p-10">
    @php
        $isPelanggan = request()->routeIs('pelanggan.login');
        $formAction = $isPelanggan ? route('pelanggan.login.submit') : route('user.login');
    @endphp


    <!-- Main Split Container -->
    <div
        class="w-full max-w-[1040px] bg-white rounded-[28px] p-3 sm:p-4 shadow-sm border border-zinc-200/80 grid grid-cols-1 lg:grid-cols-12 overflow-hidden">

        <!-- Kolom Kiri: Panel Foto SUTET (electricity.jpg) -->
        <div class="relative lg:col-span-6 min-h-[260px] lg:min-h-[620px] rounded-[22px] rounded-br-none bg-cover bg-center bg-no-repeat overflow-hidden flex flex-col justify-between p-6 sm:p-8 text-white"
            style="
        background-image: url('{{ asset('image/electricity.jpg') }}');
        clip-path: polygon(0 0, 88% 0, 100% 100%, 0 100%);
    ">
            <!-- Gradient gelap halus atas & bawah agar teks foto terbaca tajam -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/45 via-black/15 to-black/75"></div>

            <!-- Baris Atas di Dalam Foto -->
            <div class="relative z-10 flex items-center justify-between gap-3 pr-12">
                <span class="text-xs font-medium tracking-wide text-white/90">
                    Sistem Pascabayar
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ $isPelanggan ? route('login') : route('pelanggan.login') }}"
                        class="rounded-full border border-white/40 bg-black/25 backdrop-blur-xs px-3.5 py-1.5 text-xs font-medium text-white hover:bg-white hover:text-zinc-900 transition-colors">
                        {{ $isPelanggan ? 'Portal Petugas' : 'Portal Pelanggan' }}
                    </a>
                </div>
            </div>

            <!-- Baris Bawah di Dalam Foto (Info Sistem & Indikator Portal) -->
            <div class="relative z-10 flex items-end justify-between gap-4">
                <div class="flex items-center gap-3">
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
                            {{ $isPelanggan ? 'Layanan Pelanggan' : 'Administrasi & Loket' }}
                        </p>
                        <p class="text-xs text-white/75 mt-0.5">
                            Monitoring kWh & Tagihan Listrik
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Area Form Putih Minimalis -->
        <div class="lg:col-span-6 flex flex-col justify-between px-6 py-8 sm:px-12 sm:py-10">

            <!-- Header Brand Kanan Atas (Sejajar dengan referensi) -->
            <div class="flex items-center justify-between">
                <span class="text-sm font-extrabold tracking-wider uppercase text-zinc-900">
                    VOLT<span class="font-normal text-zinc-400">PAY</span>
                </span>
                <span
                    class="inline-flex items-center gap-1.5 rounded-full border border-zinc-200 px-3 py-1 text-[11px] font-medium text-zinc-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ $isPelanggan ? 'Pelanggan' : 'Petugas' }}
                </span>
            </div>

            <!-- Form Utama di Tengah Vertikal -->
            <div class="my-auto py-10 max-w-sm w-full mx-auto">
                <div class="text-center mb-8">
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-zinc-950">
                        {{ $isPelanggan ? 'Halo Pelanggan' : 'Selamat Datang' }}
                    </h1>
                    <p class="mt-2 text-xs sm:text-sm text-zinc-500">
                        {{ $isPelanggan
                            ? 'Masuk untuk mengecek rincian tagihan listrik Anda'
                            : 'Masuk ke panel pengelolaan pembayaran listrik' }}
                    </p>
                </div>

                <!-- Error Alert -->
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ $formAction }}" class="space-y-4">
                    @csrf

                    <!-- Input Username -->
                    <div>
                        <label for="username" class="sr-only">Username</label>
                        <input id="username" name="username" type="text" required autofocus
                            value="{{ old('username') }}" placeholder="Username"
                            class="block w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 placeholder:text-zinc-400 transition-colors focus:border-zinc-900 focus:outline-none">
                    </div>

                    <!-- Input Password + Toggle -->
                    <div class="relative">
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" required placeholder="Password"
                            class="block w-full rounded-xl border border-zinc-200 bg-white pl-4 pr-11 py-3 text-sm text-zinc-900 placeholder:text-zinc-400 transition-colors focus:border-zinc-900 focus:outline-none">
                        <button type="button" onclick="togglePassword()" aria-label="Tampilkan password"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-zinc-400 hover:text-zinc-700 transition-colors cursor-pointer">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Divider Tipis ("atau") sesuai struktur referensi -->
                    <div class="relative py-2 flex items-center justify-center">
                        <div class="w-full border-t border-zinc-100"></div>
                        <span class="bg-white px-3 text-[11px] text-zinc-400">akses</span>
                        <div class="w-full border-t border-zinc-100"></div>
                    </div>

                    <!-- Tombol Pindah Portal (Menggantikan tombol OAuth Google di referensi agar fungsional) -->
                    <a href="{{ $isPelanggan ? route('login') : route('pelanggan.login') }}"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-xs font-medium text-zinc-700 hover:bg-zinc-50 hover:border-zinc-300 transition-colors">
                        <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="1.75"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                        <span>{{ $isPelanggan ? 'Beralih ke Login Petugas' : 'Beralih ke Login Pelanggan' }}</span>
                    </a>

                    <!-- Submit Button: Pill Solid Tegas (Mengikuti bentuk kapsul tombol Login referensi) -->
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full rounded-full bg-zinc-950 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 cursor-pointer">
                            Masuk
                        </button>
                    </div>
                </form>

                <!-- Teks Bawah Form -->
                <p class="mt-6 text-center text-xs text-zinc-500">
                    @if ($isPelanggan)
                        <p class="mt-6 text-center text-xs text-zinc-500">
                            Meteran baru terpasang?
                            <a href="{{ route('pelanggan.activate') }}"
                                class="font-semibold text-zinc-900 hover:underline">
                                Aktivasi akun pelanggan
                            </a>
                        </p>
                    @else
                        Butuh cek tagihan mandiri?
                        <a href="{{ route('pelanggan.login') }}" class="font-semibold text-zinc-900 hover:underline">
                            Masuk Pelanggan
                        </a>
                    @endif
                </p>

            </div>

            <!-- Footer Kanan Bawah Minimalis -->
            <div class="text-center text-[11px] text-zinc-400">
                Aplikasi Pembayaran Listrik Pascabayar © {{ date('Y') }}
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>

</html>
