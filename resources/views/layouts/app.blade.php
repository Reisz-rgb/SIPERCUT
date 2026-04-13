{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPERCUT') - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  '#fdf2f2',
                            100: '#fce4e4',
                            600: '#9E2A2B',
                            700: '#781F1F',
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>

<body class="bg-slate-50 flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col flex-shrink-0">
        <div class="p-6 border-b border-slate-100">
            <h1 class="text-2xl font-bold text-primary-600 tracking-tight">SIPERCUT</h1>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            @php $isActive = fn($route) => request()->routeIs($route) ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50'; @endphp

            <a href="{{ route('user.dashboard') }}"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isActive('user.dashboard') }}">
                Dashboard
            </a>
            <a href="{{ route('user.cuti.create') }}"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isActive('user.cuti.create') }}">
                Pengajuan Cuti
            </a>
            <a href="{{ route('user.riwayat') }}"
               class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ $isActive('user.riwayat') }}">
                Riwayat Cuti
            </a>
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-slate-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-3 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <h2 class="text-lg font-semibold text-slate-800">@yield('title')</h2>

            <div class="flex items-center gap-3">
                @auth
                    <span class="text-sm font-medium text-slate-600">{{ Auth::user()->name }}</span>
                    <a href="{{ route('user.profil') }}"
                       class="w-8 h-8 rounded-full bg-primary-100 border border-primary-200 flex items-center justify-center text-xs font-bold text-primary-600">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </a>
                @endauth
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-8">
            @yield('header')
            <div class="mt-6">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>