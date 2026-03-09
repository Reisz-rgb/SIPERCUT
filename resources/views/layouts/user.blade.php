<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'SIPERCUT') - {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
        :root{
            --maroon: #9E2A2B;
            --maroon-dark: #781F1F;
            --bg: #F1F5F9;
            --border: #E2E8F0;
            --text: #334155;
            --shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.10);
        }
        body{ background: var(--bg); color: var(--text); }
        ::-webkit-scrollbar{ width: 6px; height: 6px; }
        ::-webkit-scrollbar-track{ background: transparent; }
        ::-webkit-scrollbar-thumb{ background: #CBD5E1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover{ background: #94A3B8; }
        .shadow-soft{ box-shadow: var(--shadow); }
        .active-menu{
            background: linear-gradient(90deg, var(--maroon) 0%, var(--maroon-dark) 100%);
            color: white !important;
            box-shadow: 0 4px 12px rgba(158, 42, 43, 0.30);
            border: none;
        }
        .menu-btn{ transition: all .2s ease; }
        .menu-btn:hover:not(.active-menu){ background: #FEF2F2; color: var(--maroon); }
        .glass-profile {
            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.20);
        }
        .btn-primary{ background: var(--maroon); transition: transform .15s ease, box-shadow .15s ease, background .15s ease; }
        .btn-primary:hover{ background: var(--maroon-dark); transform: translateY(-1px); box-shadow: 0 10px 18px rgba(158, 42, 43, 0.18); }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: {
                            DEFAULT: '#9E2A2B',
                            dark: '#781F1F',
                        }
                    }
                }
            }
        }
    </script>

    @stack('head')
</head>

<body class="flex h-screen overflow-hidden">
    <div id="overlay" class="hidden fixed inset-0 bg-black/50 z-40 md:hidden" onclick="closeSidebar()"></div>

    @include('user.partials.sidebar')

    <main class="flex-1 overflow-y-auto relative bg-[var(--bg)]">
        @php($authUser = $user ?? auth()->user())

        <div class="bg-gradient-to-br from-[#9E2A2B] to-[#561616] min-h-[280px] md:min-h-[300px] rounded-b-[40px] p-6 md:p-10 relative z-0">
            <div class="flex justify-between items-start gap-4">
                <div class="flex items-start md:items-center gap-4">
                    <button class="md:hidden text-white text-2xl mt-1" type="button" onclick="openSidebar()">
                        <i class="bi bi-list"></i>
                    </button>

                    <div>
                        {{-- ✅ FIX: jangan nested @yield di default parameter --}}
                        <h1 class="text-2xl md:text-3xl font-extrabold text-white leading-tight">
                            @hasSection('page_title')
                                @yield('page_title')
                            @else
                                @yield('title', 'Portal User')
                            @endif
                        </h1>

                        <p class="text-white/75 text-sm mt-2">
                            @yield('page_subtitle', 'Kelola layanan cuti Anda dengan cepat dan rapi.')
                        </p>
                    </div>
                </div>

                <div class="relative">
                    <button type="button" id="userMenuBtn" class="glass-profile px-4 py-2 rounded-full flex items-center gap-3 text-white focus:outline-none" aria-haspopup="true" aria-expanded="false">
                        <div class="w-9 h-9 rounded-full bg-white text-[var(--maroon)] flex items-center justify-center font-extrabold text-sm shadow-inner">
                            {{ strtoupper(substr(($authUser->name ?? 'U'), 0, 2)) }}
                        </div>
                        <div class="hidden md:block text-sm font-semibold">{{ $authUser->name ?? 'User' }}</div>
                        <i class="bi bi-chevron-down text-xs hidden md:block"></i>
                    </button>

                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-soft border border-slate-100 p-2 z-50">
                        <a href="{{ route('user.profil') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Profile Saya</a>
                        <div class="my-2 border-t border-slate-200"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <div class="px-5 md:px-10 -mt-20 md:-mt-20 pb-10 relative z-10 w-full">
            @yield('content')
        </div>
    </main>

    <script>
        function openSidebar(){
            const el = document.getElementById('sidebar');
            const ov = document.getElementById('overlay');
            if(!el || !ov) return;
            el.classList.remove('-translate-x-full');
            ov.classList.remove('hidden');
        }
        function closeSidebar(){
            const el = document.getElementById('sidebar');
            const ov = document.getElementById('overlay');
            if(!el || !ov) return;
            el.classList.add('-translate-x-full');
            ov.classList.add('hidden');
        }

        // User dropdown (Profile / Logout)
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');

        function closeUserMenu(){
            if(!userMenuBtn || !userMenu) return;
            userMenu.classList.add('hidden');
            userMenuBtn.setAttribute('aria-expanded', 'false');
        }

        function toggleUserMenu(){
            if(!userMenuBtn || !userMenu) return;
            userMenu.classList.toggle('hidden');
            userMenuBtn.setAttribute('aria-expanded', userMenu.classList.contains('hidden') ? 'false' : 'true');
        }

        if(userMenuBtn){
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleUserMenu();
            });

            document.addEventListener('click', () => closeUserMenu());
            document.addEventListener('keydown', (e) => {
                if(e.key === 'Escape') closeUserMenu();
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
