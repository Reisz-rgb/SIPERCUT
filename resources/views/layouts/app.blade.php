<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SIPERCUT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#f0f9ff', 100: '#e0f2fe', 600: '#0284c7', 700: '#0369a1' },
                        success: { 50: '#f0fdf4', 100: '#dcfce7', 600: '#16a34a' },
                        warning: { 100: '#fef9c3', 600: '#ca8a04' },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col flex-shrink-0">
        <div class="p-6 border-b border-slate-100">
            <h1 class="text-2xl font-bold text-primary-600 tracking-tight">SIPERCUT</h1>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                Dashboard
            </a>
            <a href="{{ route('cuti.create') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('cuti.create') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                Pengajuan Cuti
            </a>
            <a href="{{ route('cuti.riwayat') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('cuti.riwayat') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }}">
                Riwayat Cuti
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
            <h2 class="text-lg font-semibold text-slate-800">@yield('title')</h2>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-slate-600">John Doe</span>
                <div class="w-8 h-8 rounded-full bg-primary-100 border border-primary-200"></div>
            </div>
        </header>

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