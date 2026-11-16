<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VOLTPAY — Sistem Pembayaran Listrik')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-50 text-zinc-900 antialiased min-h-screen flex flex-col">

    @if (!request()->routeIs('login') && !request()->routeIs('pelanggan.activate'))
        <!-- Top Header Minimalis -->
        <header class="sticky top-0 z-40 bg-white border-b border-zinc-200">
            <div class="px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if (Auth::check() || Auth::guard('pelanggan')->check())
                        <button type="button" onclick="toggleMobileSidebar()"
                            class="lg:hidden p-2 -ml-2 rounded-lg text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 cursor-pointer"
                            aria-label="Buka menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    @endif

                    <!-- Brand Wordmark Serasi dengan Login -->
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-zinc-950 text-white flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                </svg>
                            </span>
                            <span class="text-sm font-extrabold tracking-wider uppercase text-zinc-950">
                                VOLT<span class="font-normal text-zinc-400">PAY</span>
                            </span>
                            <span class="hidden sm:inline-block text-xs text-zinc-300">|</span>
                            <span class="hidden sm:inline-block text-xs font-medium text-zinc-500">Panel Petugas</span>
                        </a>
                    @elseauth('pelanggan')
                        <a href="{{ route('pelanggan.dashboard') }}" class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-zinc-950 text-white flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                </svg>
                            </span>
                            <span class="text-sm font-extrabold tracking-wider uppercase text-zinc-950">
                                VOLT<span class="font-normal text-zinc-400">PAY</span>
                            </span>
                            <span class="hidden sm:inline-block text-xs text-zinc-300">|</span>
                            <span class="hidden sm:inline-block text-xs font-medium text-zinc-500">Portal Pelanggan</span>
                        </a>
                    @else
                        <span class="text-sm font-extrabold tracking-wider uppercase text-zinc-950">
                            VOLT<span class="font-normal text-zinc-400">PAY</span>
                        </span>
                    @endauth
                </div>

                <!-- User Menu Kanan -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="relative">
                            <button type="button" onclick="toggleDropdown()"
                                class="flex items-center gap-2.5 rounded-full border border-zinc-200 bg-white pl-1.5 pr-3 py-1 text-xs font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 transition-colors cursor-pointer">
                                <span
                                    class="w-6 h-6 rounded-full bg-zinc-900 text-white flex items-center justify-center text-[11px] font-semibold">
                                    {{ strtoupper(substr(Auth::user()->nama_admin, 0, 1)) }}
                                </span>
                                <span class="hidden sm:inline">{{ Auth::user()->nama_admin }}</span>
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div id="dropdown"
                                class="hidden absolute right-0 mt-2 w-52 rounded-xl bg-white border border-zinc-200 shadow-lg py-1.5 z-50">
                                <div class="px-3.5 py-2 border-b border-zinc-100">
                                    <p class="text-[11px] text-zinc-400">Masuk sebagai</p>
                                    <p class="text-xs font-semibold text-zinc-900 truncate">{{ Auth::user()->nama_admin }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseauth('pelanggan')
                        <div class="relative">
                            <button type="button" onclick="toggleDropdownPelanggan()"
                                class="flex items-center gap-2.5 rounded-full border border-zinc-200 bg-white pl-1.5 pr-3 py-1 text-xs font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 transition-colors cursor-pointer">
                                <span
                                    class="w-6 h-6 rounded-full bg-zinc-900 text-white flex items-center justify-center text-[11px] font-semibold">
                                    {{ strtoupper(substr(Auth::guard('pelanggan')->user()->nama_pelanggan, 0, 1)) }}
                                </span>
                                <span class="hidden sm:inline">{{ Auth::guard('pelanggan')->user()->nama_pelanggan }}</span>
                                <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div id="dropdownPelanggan"
                                class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-white border border-zinc-200 shadow-lg py-1.5 z-50">
                                <div class="px-3.5 py-2 border-b border-zinc-100">
                                    <p class="text-[11px] text-zinc-400">IDPEL:
                                        {{ Auth::guard('pelanggan')->user()->nomor_kwh }}</p>
                                    <p class="text-xs font-semibold text-zinc-900 truncate">
                                        {{ Auth::guard('pelanggan')->user()->nama_pelanggan }}</p>
                                </div>
                                <a href="{{ route('pelanggan.tagihan') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-zinc-700 hover:bg-zinc-50 transition-colors">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor"
                                        stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    Tagihan Listrik
                                </a>
                                <form method="POST" action="{{ route('pelanggan.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </header>
    @endif

    <!-- Main Body -->
    @auth
        <div class="flex-1 flex">
            <!-- Overlay Mobile -->
            <div id="sidebarBackdrop" onclick="toggleMobileSidebar()"
                class="hidden fixed inset-0 z-30 bg-zinc-950/30 lg:hidden"></div>

            <!-- Admin Sidebar Bersih -->
            <aside id="appSidebar"
                class="fixed inset-y-0 left-0 z-30 w-60 bg-white border-r border-zinc-200 pt-14 lg:pt-0 lg:static lg:block -translate-x-full lg:translate-x-0 transition-transform duration-200">
                <div class="p-4 space-y-6">
                    <div>
                        <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                            Menu Operasional
                        </p>
                        <nav class="space-y-1">
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                                Dashboard
                            </a>

                            <a href="{{ route('pelanggan.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pelanggan.*') ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                                Pelanggan
                            </a>

                            <a href="{{ route('penggunaan.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('penggunaan.*') ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                                Penggunaan kWh
                            </a>

                            <a href="{{ route('tagihan.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('tagihan.*') ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                Tagihan
                            </a>

                            <a href="{{ route('pembayaran.index') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pembayaran.*') ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                </svg>
                                Pembayaran
                            </a>
                        </nav>
                    </div>
                </div>
            </aside>

            <!-- Area Konten Utama Tanpa Wrapper Berlapis -->
            <main class="flex-1 min-w-0">
                @yield('content')
            </main>
        </div>
    @elseauth('pelanggan')
        <div class="flex-1 flex">
            <div id="sidebarBackdrop" onclick="toggleMobileSidebar()"
                class="hidden fixed inset-0 z-30 bg-zinc-950/30 lg:hidden"></div>

            <!-- Customer Sidebar Bersih -->
            <aside id="appSidebar"
                class="fixed inset-y-0 left-0 z-30 w-60 bg-white border-r border-zinc-200 pt-14 lg:pt-0 lg:static lg:block -translate-x-full lg:translate-x-0 transition-transform duration-200">
                <div class="p-4 space-y-6">
                    <div>
                        <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                            Layanan Mandiri
                        </p>
                        <nav class="space-y-1">
                            <a href="{{ route('pelanggan.dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pelanggan.dashboard') ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                </svg>
                                Ringkasan Akun
                            </a>

                            <a href="{{ route('pelanggan.tagihan') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('pelanggan.tagihan') ? 'bg-zinc-950 text-white' : 'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-950' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                Tagihan Listrik
                            </a>
                        </nav>
                    </div>
                </div>
            </aside>

            <main class="flex-1 min-w-0">
                @yield('content')
            </main>
        </div>
    @else
        <div class="flex-1">
            @yield('content')
        </div>
    @endauth

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            if (dropdown) dropdown.classList.toggle('hidden');
        }

        function toggleDropdownPelanggan() {
            const dropdown = document.getElementById('dropdownPelanggan');
            if (dropdown) dropdown.classList.toggle('hidden');
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (!sidebar) return;
            sidebar.classList.toggle('-translate-x-full');
            if (backdrop) backdrop.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown');
            const dropdownPelanggan = document.getElementById('dropdownPelanggan');
            const isDropdownClick = event.target.closest('button[onclick*="toggleDropdown"]');

            if (!isDropdownClick) {
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
                if (dropdownPelanggan && !dropdownPelanggan.classList.contains('hidden')) {
                    dropdownPelanggan.classList.add('hidden');
                }
            }
        });
    </script>
</body>

</html>
