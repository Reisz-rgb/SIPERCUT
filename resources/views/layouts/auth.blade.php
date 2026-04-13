{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - SIPERCUT</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>

    <script>
        tailwind.config = {
            theme: { extend: { colors: { maroon: { DEFAULT: '#9E2A2B', dark: '#781F1F', light: '#FEF2F2' } } } }
        }
    </script>

    @stack('styles')
</head>

<body class="min-h-screen bg-gradient-to-br from-[#9E2A2B] to-[#561616] flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Logo & Brand --}}
        <div class="text-center mb-8">
            <img src="{{ asset('logokabupatensemarang.png') }}"
                 alt="Logo Kabupaten Semarang"
                 class="w-16 h-16 mx-auto mb-4 drop-shadow-lg">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">SIPERCUT</h1>
            <p class="text-white/70 text-sm mt-1">Sistem Pengajuan Cuti — Kab. Semarang</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 rounded-xl px-4 py-3 text-sm">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->has('login'))
                <div class="mb-4 flex items-center gap-2 bg-red-50 text-red-700 border border-red-200 rounded-xl px-4 py-3 text-sm">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $errors->first('login') }}
                </div>
            @endif

            {{-- Page Content --}}
            @yield('content')
        </div>

        {{-- Footer --}}
        <p class="text-center text-white/50 text-xs mt-6">
            &copy; {{ date('Y') }} Kabupaten Semarang. All rights reserved.
        </p>
    </div>

    @stack('scripts')
</body>
</html>