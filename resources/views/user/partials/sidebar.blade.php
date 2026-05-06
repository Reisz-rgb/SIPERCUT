@php
    $authUser = $user ?? auth()->user();
@endphp

<aside id="sidebar"
       class="w-[270px] bg-white border-r border-[var(--border)] flex-shrink-0 fixed md:static inset-y-0 left-0 z-50 -translate-x-full md:translate-x-0 transition-transform duration-300 flex flex-col">

    {{-- Brand --}}
    <div class="p-6 border-b border-dashed border-[var(--border)] mb-2">
        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3">
            <div class="w-12 h-12 flex items-center justify-center bg-slate-50 rounded-xl p-1">
                <img src="{{ asset('logokabupatensemarang.png') }}"
                     alt="Logo Kab Semarang"
                     class="w-full h-full object-contain">
            </div>
            <div class="leading-tight">
                <div class="text-xl font-extrabold tracking-tight text-[var(--maroon)]">SIPERCUT</div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kab. Semarang</div>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
        <p class="text-[11px] font-bold text-slate-400 tracking-wider px-4 mb-3 mt-2 uppercase">Main Menu</p>

        <a href="{{ route('user.dashboard') }}"
           class="menu-btn {{ request()->routeIs('user.dashboard') ? 'active-menu' : 'text-slate-500 hover:text-[var(--maroon)]' }} w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium">
            <span class="w-6 text-center text-lg"><i class="bi bi-grid-fill"></i></span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('user.cuti.create') }}"
           class="menu-btn {{ request()->routeIs('user.cuti.create') ? 'active-menu' : 'text-slate-500 hover:text-[var(--maroon)]' }} w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium">
            <span class="w-6 text-center text-lg"><i class="bi bi-plus-circle"></i></span>
            <span>Ajukan Cuti</span>
        </a>

        <a href="{{ route('user.riwayat') }}"
           class="menu-btn {{ request()->routeIs('user.riwayat') ? 'active-menu' : 'text-slate-500 hover:text-[var(--maroon)]' }} w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium">
            <span class="w-6 text-center text-lg"><i class="bi bi-file-earmark-text"></i></span>
            <span>Riwayat Cuti</span>
        </a>
    </nav>

    {{-- Logout --}}
    <div class="p-4 border-t border-dashed border-[var(--border)]">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition-all">
                <i class="bi bi-box-arrow-left"></i>
                <span>Keluar Aplikasi</span>
            </button>
        </form>
    </div>

</aside>