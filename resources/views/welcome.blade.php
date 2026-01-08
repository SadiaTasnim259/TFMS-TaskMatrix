<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'TFMS') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50">
        <div class="min-h-screen flex flex-col justify-center items-center">
            <div class="max-w-4xl w-full px-6 text-center">
                <h1 class="text-5xl font-bold text-utm-maroon mb-4">Task Force Management System</h1>
                <p class="text-xl text-gray-600 mb-8">Universiti Teknologi Malaysia</p>
                
                <div class="flex justify-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="bg-utm-maroon text-white px-6 py-3 rounded-lg font-medium hover:bg-red-900 transition">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-utm-maroon text-white px-6 py-3 rounded-lg font-medium hover:bg-red-900 transition">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-white text-utm-maroon border-2 border-utm-maroon px-6 py-3 rounded-lg font-medium hover:bg-gray-50 transition">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
            
            <div class="mt-12 text-gray-400 text-sm">
                &copy; {{ date('Y') }} Universiti Teknologi Malaysia. All rights reserved.
            </div>
        </div>
    </body>
</html>
