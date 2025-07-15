@extends('layouts.app')

@section('title', 'Login - Aplikasi Pembayaran Listrik')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Login ke Aplikasi
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Aplikasi Pembayaran Listrik Pascabayar
            </p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input id="username" name="username" type="text" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                           placeholder="Username" value="{{ old('username') }}">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                           placeholder="Password">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Login sebagai:</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="login_type" value="admin" class="form-radio text-blue-600" checked>
                        <span class="ml-2">Administrator</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="login_type" value="pelanggan" class="form-radio text-blue-600">
                        <span class="ml-2">Pelanggan</span>
                    </label>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Login
                </button>
            </div>
        </form>

        <div class="mt-6">
            <div class="bg-gray-100 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Akun Demo:</h3>
                <div class="text-xs text-gray-600 space-y-1">
                    <p><strong>Admin:</strong> admin / admin123</p>
                    <p><strong>Petugas:</strong> petugas / petugas123</p>
                    <p><strong>Pelanggan:</strong> budi / budi123</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
