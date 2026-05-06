@extends('layouts.user')

@section('title', 'Profil Saya')
@section('page_title', 'Profil')
@section('page_subtitle', 'Periksa dan kelola informasi akun Anda.')

@section('content')
    @php($authUser = $user ?? auth()->user())

    {{-- Card profil --}}
    <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">

        {{-- Header --}}
        <div class="p-6 md:p-8 bg-gradient-to-br from-[var(--maroon)] to-[var(--maroon-dark)] text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
                        <div class="w-12 h-12 rounded-xl bg-white text-[var(--maroon)] flex items-center justify-center font-extrabold text-lg">
                            {{ strtoupper(substr(($authUser->name ?? 'U'), 0, 2)) }}
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-extrabold leading-tight">{{ $authUser->name ?? '-' }}</h2>
                        <p class="text-white/80 text-sm font-medium mt-1">{{ $authUser->jabatan ?? 'Pegawai' }}</p>
                        <p class="text-white/70 text-xs font-semibold mt-1">{{ $authUser->bidang_unit ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ route('user.profil.edit') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white text-[var(--maroon)] font-extrabold text-sm hover:bg-slate-50 transition">
                        <i class="bi bi-pencil-square"></i>
                        Edit Profil
                    </a>
                </div>

            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Nama Lengkap --}}
                <div class="rounded-2xl border border-slate-100 bg-slate-50/40 p-5">
                    <p class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase">Nama Lengkap</p>
                    <p class="text-slate-800 font-semibold text-base mt-1">{{ $authUser->name ?? '-' }}</p>
                </div>

                {{-- Email --}}
                <div class="rounded-2xl border p-5 {{ empty($authUser->email) ? 'border-amber-200 bg-amber-50/60' : 'border-slate-100 bg-slate-50/40' }}">
                    <p class="text-[10px] font-extrabold tracking-wider uppercase {{ empty($authUser->email) ? 'text-amber-500' : 'text-slate-400' }}">
                        Email
                    </p>

                    @if(empty($authUser->email))
                        <div class="flex items-center justify-between gap-3 mt-1">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-exclamation-circle-fill text-amber-500 text-sm"></i>
                                <p class="text-amber-700 font-semibold text-sm">Belum diisi</p>
                            </div>
                            <a href="{{ route('user.profil.edit') }}"
                               class="text-[11px] font-extrabold text-amber-700 bg-amber-100 hover:bg-amber-200 px-3 py-1.5 rounded-lg border border-amber-200 transition">
                                + Tambah Email
                            </a>
                        </div>
                        <p class="text-[11px] text-amber-600 font-medium mt-2">
                            Diperlukan untuk fitur lupa password.
                        </p>
                    @else
                        <p class="text-slate-800 font-semibold text-base mt-1">{{ $authUser->email }}</p>
                    @endif
                </div>

                {{-- No. HP --}}
                <div class="rounded-2xl border border-slate-100 bg-slate-50/40 p-5">
                    <p class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase">No. HP</p>
                    <p class="text-slate-800 font-semibold text-base mt-1">{{ $authUser->phone ?? 'Belum diisi' }}</p>
                </div>

                {{-- NIP --}}
                <div class="rounded-2xl border border-slate-100 bg-slate-50/40 p-5">
                    <p class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase">NIP</p>
                    <p class="text-slate-800 font-semibold text-base mt-1">{{ $authUser->nip ?? '-' }}</p>
                </div>

                {{-- Jabatan --}}
                <div class="rounded-2xl border border-slate-100 bg-slate-50/40 p-5">
                    <p class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase">Jabatan</p>
                    <p class="text-slate-800 font-semibold text-base mt-1">{{ $authUser->jabatan ?? '-' }}</p>
                </div>

                {{-- Bidang / Unit --}}
                <div class="rounded-2xl border border-slate-100 bg-slate-50/40 p-5">
                    <p class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase">Bidang / Unit</p>
                    <p class="text-slate-800 font-semibold text-base mt-1">{{ $authUser->bidang_unit ?? '-' }}</p>
                </div>

            </div>

            {{-- Actions --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-3">
                <a href="{{ route('user.password.change') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-extrabold text-sm bg-amber-500 hover:bg-amber-600 text-white transition">
                    <i class="bi bi-shield-lock-fill"></i>
                    Ubah Password
                </a>
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-extrabold text-sm bg-red-50 hover:bg-red-100 text-red-700 transition">
                        <i class="bi bi-box-arrow-left"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

    </section>
@endsection