@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="mb-12">
            <h1 class="text-3xl font-light text-gray-900 mb-2">Dashboard</h1>
            <p class="text-gray-600">Sistem pembayaran listrik pascabayar</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Total Pelanggan -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-gray-300 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalPelanggan }}</p>
                    <p class="text-sm text-gray-600">Total Pelanggan</p>
                </div>
            </div>

            <!-- Data Penggunaan -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-gray-300 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalPenggunaan }}</p>
                    <p class="text-sm text-gray-600">Data Penggunaan</p>
                </div>
            </div>

            <!-- Total Tagihan -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-gray-300 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalTagihan }}</p>
                    <p class="text-sm text-gray-600">Total Tagihan</p>
                </div>
            </div>

            <!-- Total Pembayaran -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:border-gray-300 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalPembayaran }}</p>
                    <p class="text-sm text-gray-600">Total Pembayaran</p>
                </div>
            </div>
        </div>

        <!-- Clean Alert -->
        @if ($tagihanBelumLunas > 0)
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-12">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-red-800">
                            <span class="font-medium">Perhatian:</span>
                            Terdapat {{ $tagihanBelumLunas }} tagihan yang belum lunas.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Simple Quick Actions -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Menu Utama</h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('pelanggan.create') }}"
                        class="group flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-blue-300 transition-all">
                        <div
                            class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-blue-700">Tambah Pelanggan</p>
                            <p class="text-xs text-gray-500">Daftar pelanggan baru</p>
                        </div>
                    </a>

                    <a href="{{ route('penggunaan.create') }}"
                        class="group flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-emerald-300 transition-all">
                        <div
                            class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-emerald-700">Input Penggunaan</p>
                            <p class="text-xs text-gray-500">Catat meter listrik</p>
                        </div>
                    </a>

                    <a href="{{ route('tagihan.index') }}"
                        class="group flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-amber-300 transition-all">
                        <div
                            class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-amber-700">Kelola Tagihan</p>
                            <p class="text-xs text-gray-500">Management billing</p>
                        </div>
                    </a>

                    <a href="{{ route('pembayaran.create') }}"
                        class="group flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-purple-300 transition-all">
                        <div
                            class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900 group-hover:text-purple-700">Proses Pembayaran</p>
                            <p class="text-xs text-gray-500">Validasi payment</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
