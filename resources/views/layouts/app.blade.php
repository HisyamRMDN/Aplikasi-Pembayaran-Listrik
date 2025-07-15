<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Pembayaran Listrik')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold">
                            @auth
                                <a href="{{ route('dashboard') }}" class="hover:text-blue-200">
                                    Aplikasi Pembayaran Listrik
                                </a>
                            @elseauth('pelanggan')
                                <a href="{{ route('pelanggan.dashboard') }}" class="hover:text-blue-200">
                                    Aplikasi Pembayaran Listrik
                                </a>
                            @else
                                Aplikasi Pembayaran Listrik
                            @endauth
                        </h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        @auth
                            <span class="text-blue-200">{{ Auth::user()->nama_admin }}</span>
                            <div class="relative">
                                <button onclick="toggleDropdown()" class="flex items-center space-x-2 text-blue-200 hover:text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseauth('pelanggan')
                            <span class="text-blue-200">{{ Auth::guard('pelanggan')->user()->nama_pelanggan }}</span>
                            <div class="relative">
                                <button onclick="toggleDropdownPelanggan()" class="flex items-center space-x-2 text-blue-200 hover:text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div id="dropdownPelanggan" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                                    <a href="{{ route('pelanggan.tagihan') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Lihat Tagihan
                                    </a>
                                    <form method="POST" action="{{ route('pelanggan.logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
            <!-- Admin Sidebar -->
            <div class="w-64 bg-white shadow-lg min-h-screen">
                <div class="p-4">
                    <nav class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L2 12.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-4.586l.293.293a1 1 0 001.414-1.414l-9-9z"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('pelanggan.index') }}" class="flex items-center space-x-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Pelanggan</span>
                        </a>

                        <a href="{{ route('penggunaan.index') }}" class="flex items-center space-x-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Penggunaan</span>
                        </a>

                        <a href="{{ route('tagihan.index') }}" class="flex items-center space-x-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Tagihan</span>
                        </a>

                        <a href="{{ route('pembayaran.index') }}" class="flex items-center space-x-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Pembayaran</span>
                        </a>
                    </nav>
                </div>
            </div>

            <div class="flex-1 p-6">
                <!-- Content -->
                <div class="bg-white rounded-lg shadow-sm">
                    @yield('content')
                </div>
            </div>
        </div>
        @elseauth('pelanggan')
        <div class="flex">
            <!-- Customer Sidebar -->
            <div class="w-64 bg-white shadow-lg min-h-screen">
                <div class="p-4">
                    <nav class="space-y-2">
                        <a href="{{ route('pelanggan.dashboard') }}" class="flex items-center space-x-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L2 12.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-4.586l.293.293a1 1 0 001.414-1.414l-9-9z"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('pelanggan.tagihan') }}" class="flex items-center space-x-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg p-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Tagihan Listrik</span>
                        </a>
                    </nav>
                </div>
            </div>

            <div class="flex-1 p-6">
                <!-- Content -->
                <div class="bg-white rounded-lg shadow-sm">
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
            dropdown.classList.toggle('hidden');
        }

        function toggleDropdownPelanggan() {
            const dropdown = document.getElementById('dropdownPelanggan');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown');
            const dropdownPelanggan = document.getElementById('dropdownPelanggan');
            const button = event.target.closest('button');

            if (!button || (button.getAttribute('onclick') !== 'toggleDropdown()' && button.getAttribute('onclick') !== 'toggleDropdownPelanggan()')) {
                if (dropdown) dropdown.classList.add('hidden');
                if (dropdownPelanggan) dropdownPelanggan.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
