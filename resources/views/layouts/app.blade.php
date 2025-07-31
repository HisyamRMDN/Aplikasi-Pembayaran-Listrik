<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Pembayaran Listrik')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="min-h-screen">
        <!-- Modern Navigation with Glassmorphism -->
        <nav class="bg-white/70 backdrop-blur-xl border-b border-white/20 shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-4">
                        <!-- Logo dengan gradient modern -->
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h1 class="text-xl font-bold">
                                @auth
                                    <a href="{{ route('dashboard') }}"
                                        class="bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 bg-clip-text text-transparent hover:from-blue-600 hover:to-purple-600 transition-all duration-300">
                                        Aplikasi Pembayaran Listrik
                                    </a>
                                    @elseauth('pelanggan')
                                    <a href="{{ route('pelanggan.dashboard') }}"
                                        class="bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 bg-clip-text text-transparent hover:from-blue-600 hover:to-purple-600 transition-all duration-300">
                                        Aplikasi Pembayaran Listrik
                                    </a>
                                @else
                                    <span
                                        class="bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 bg-clip-text text-transparent">
                                        Aplikasi Pembayaran Listrik
                                    </span>
                                @endauth
                            </h1>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        @auth
                            <!-- User Profile dengan modern design -->
                            <div
                                class="flex items-center space-x-3 bg-white/60 backdrop-blur-sm rounded-full px-4 py-2 border border-white/30 shadow-sm">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 rounded-full flex items-center justify-center ring-2 ring-white/50">
                                    <span
                                        class="text-white text-sm font-semibold">{{ substr(Auth::user()->nama_admin, 0, 1) }}</span>
                                </div>
                                <span class="text-gray-700 font-medium text-sm">{{ Auth::user()->nama_admin }}</span>
                            </div>

                            <!-- Dropdown Button -->
                            <div class="relative">
                                <button onclick="toggleDropdown()"
                                    class="flex items-center justify-center w-10 h-10 text-gray-600 hover:text-gray-900 hover:bg-white/60 rounded-full transition-all duration-200 backdrop-blur-sm border border-transparent hover:border-white/30">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div id="dropdown"
                                    class="absolute right-0 mt-3 w-56 bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 py-2 z-10 hidden transform opacity-0 scale-95 transition-all duration-200">
                                    <div class="px-4 py-3 border-b border-gray-100/50">
                                        <p class="text-sm text-gray-500">Signed in as</p>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->nama_admin }}
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 hover:text-red-600 transition-all duration-200 group">
                                            <svg class="w-4 h-4 mr-3 group-hover:text-red-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @elseauth('pelanggan')
                            <!-- Customer Profile -->
                            <div
                                class="flex items-center space-x-3 bg-white/60 backdrop-blur-sm rounded-full px-4 py-2 border border-white/30 shadow-sm">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-green-500 via-teal-500 to-cyan-600 rounded-full flex items-center justify-center ring-2 ring-white/50">
                                    <span
                                        class="text-white text-sm font-semibold">{{ substr(Auth::guard('pelanggan')->user()->nama_pelanggan, 0, 1) }}</span>
                                </div>
                                <span
                                    class="text-gray-700 font-medium text-sm">{{ Auth::guard('pelanggan')->user()->nama_pelanggan }}</span>
                            </div>

                            <div class="relative">
                                <button onclick="toggleDropdownPelanggan()"
                                    class="flex items-center justify-center w-10 h-10 text-gray-600 hover:text-gray-900 hover:bg-white/60 rounded-full transition-all duration-200 backdrop-blur-sm border border-transparent hover:border-white/30">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div id="dropdownPelanggan"
                                    class="absolute right-0 mt-3 w-56 bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 py-2 z-10 hidden transform opacity-0 scale-95 transition-all duration-200">
                                    <div class="px-4 py-3 border-b border-gray-100/50">
                                        <p class="text-sm text-gray-500">Signed in as</p>
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ Auth::guard('pelanggan')->user()->nama_pelanggan }}</p>
                                    </div>
                                    <a href="{{ route('pelanggan.tagihan') }}"
                                        class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:text-blue-600 transition-all duration-200 group">
                                        <svg class="w-4 h-4 mr-3 group-hover:text-blue-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        Lihat Tagihan
                                    </a>
                                    <form method="POST" action="{{ route('pelanggan.logout') }}" class="block">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 hover:text-red-600 transition-all duration-200 group">
                                            <svg class="w-4 h-4 mr-3 group-hover:text-red-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar and Content -->
        @auth
            <div class="flex">
                <!-- Modern Admin Sidebar -->
                <div class="w-64 bg-white/40 backdrop-blur-xl border-r border-white/20 shadow-2xl min-h-screen">
                    <div class="p-6">
                        <nav class="space-y-3">
                            <a href="{{ route('dashboard') }}"
                                class="group flex items-center space-x-3 text-gray-700 hover:text-white hover:bg-gradient-to-r hover:from-blue-500 hover:to-indigo-600 rounded-2xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
                                <div
                                    class="w-10 h-10 bg-white/20 group-hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L2 12.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-4.586l.293.293a1 1 0 001.414-1.414l-9-9z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="font-medium">Dashboard</span>
                            </a>

                            <a href="{{ route('pelanggan.index') }}"
                                class="group flex items-center space-x-3 text-gray-700 hover:text-white hover:bg-gradient-to-r hover:from-emerald-500 hover:to-teal-600 rounded-2xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
                                <div
                                    class="w-10 h-10 bg-white/20 group-hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="font-medium">Pelanggan</span>
                            </a>

                            <a href="{{ route('penggunaan.index') }}"
                                class="group flex items-center space-x-3 text-gray-700 hover:text-white hover:bg-gradient-to-r hover:from-amber-500 hover:to-orange-600 rounded-2xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
                                <div
                                    class="w-10 h-10 bg-white/20 group-hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="font-medium">Penggunaan</span>
                            </a>

                            <a href="{{ route('tagihan.index') }}"
                                class="group flex items-center space-x-3 text-gray-700 hover:text-white hover:bg-gradient-to-r hover:from-purple-500 hover:to-pink-600 rounded-2xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
                                <div
                                    class="w-10 h-10 bg-white/20 group-hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="font-medium">Tagihan</span>
                            </a>

                            <a href="{{ route('pembayaran.index') }}"
                                class="group flex items-center space-x-3 text-gray-700 hover:text-white hover:bg-gradient-to-r hover:from-rose-500 hover:to-red-600 rounded-2xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
                                <div
                                    class="w-10 h-10 bg-white/20 group-hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"></path>
                                        <path
                                            d="M6 6a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V6zM12 9a1 1 0 100 2h2a1 1 0 100-2h-2z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="font-medium">Pembayaran</span>
                            </a>
                        </nav>
                    </div>
                </div>

                <div class="flex-1 p-8">
                    <!-- Content dengan modern card -->
                    <div class="bg-white/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                        @yield('content')
                    </div>
                </div>
            </div>

            @elseauth('pelanggan')
            <div class="flex">
                <!-- Modern Customer Sidebar -->
                <div class="w-64 bg-white/40 backdrop-blur-xl border-r border-white/20 shadow-2xl min-h-screen">
                    <div class="p-6">
                        <nav class="space-y-3">
                            <a href="{{ route('pelanggan.dashboard') }}"
                                class="group flex items-center space-x-3 text-gray-700 hover:text-white hover:bg-gradient-to-r hover:from-blue-500 hover:to-indigo-600 rounded-2xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
                                <div
                                    class="w-10 h-10 bg-white/20 group-hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L2 12.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-4.586l.293.293a1 1 0 001.414-1.414l-9-9z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="font-medium">Dashboard</span>
                            </a>

                            <a href="{{ route('pelanggan.tagihan') }}"
                                class="group flex items-center space-x-3 text-gray-700 hover:text-white hover:bg-gradient-to-r hover:from-emerald-500 hover:to-teal-600 rounded-2xl p-4 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]">
                                <div
                                    class="w-10 h-10 bg-white/20 group-hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="font-medium">Tagihan Listrik</span>
                            </a>
                        </nav>
                    </div>
                </div>

                <div class="flex-1 p-8">
                    <!-- Content dengan modern card -->
                    <div class="bg-white/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                        @yield('content')
                    </div>
                </div>
            </div>
        @else
            <div class="flex-1">
                @yield('content')
            </div>
        @endauth
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                dropdown.classList.add('opacity-100', 'scale-100');
            } else {
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200);
            }
        }

        function toggleDropdownPelanggan() {
            const dropdown = document.getElementById('dropdownPelanggan');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                dropdown.classList.add('opacity-100', 'scale-100');
            } else {
                dropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200);
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown');
            const dropdownPelanggan = document.getElementById('dropdownPelanggan');
            const isDropdownClick = event.target.closest('button[onclick*="toggleDropdown"]');

            if (!isDropdownClick) {
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        dropdown.classList.add('hidden');
                    }, 200);
                }
                if (dropdownPelanggan && !dropdownPelanggan.classList.contains('hidden')) {
                    dropdownPelanggan.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        dropdownPelanggan.classList.add('hidden');
                    }, 200);
                }
            }
        });
    </script>
</body>

</html>
