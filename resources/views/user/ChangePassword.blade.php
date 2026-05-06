@extends('layouts.user')

@section('title', 'Ubah Password')
@section('page_title', 'Keamanan Akun')
@section('page_subtitle', 'Perbarui password untuk menjaga keamanan akun Anda.')

@section('content')
    <div class="max-w-xl mx-auto">
        <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">

            {{-- Card header --}}
            <div class="p-6 md:p-8 border-b border-slate-50">
                <h3 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-shield-lock-fill text-[var(--maroon)]"></i>
                    Ubah Password
                </h3>
                <p class="text-sm text-slate-500 font-medium mt-1">
                    Jika belum pernah mengubah password, gunakan NIP Anda sebagai password lama.
                </p>
            </div>

            {{-- Card body --}}
            <div class="p-6 md:p-8">

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                        <ul class="text-sm text-red-700 space-y-1 font-semibold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.password.update.user') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Password Lama --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Password Lama</label>
                        <input type="password"
                               name="current_password"
                               required
                               placeholder="Masukkan password lama atau NIP"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                        <p class="text-[11px] text-slate-400 mt-2 font-semibold">Jika belum pernah ubah password, gunakan NIP Anda</p>
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Password Baru</label>
                        <input type="password"
                               name="password"
                               required
                               placeholder="Minimal 6 karakter"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Konfirmasi Password Baru</label>
                        <input type="password"
                               name="password_confirmation"
                               required
                               placeholder="Ulangi password baru"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                    </div>

                    {{-- Actions --}}
                    <div class="pt-2 flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                                class="flex-1 btn-primary text-white font-extrabold py-3 rounded-2xl transition shadow-lg shadow-red-900/10 inline-flex items-center justify-center gap-2">
                            <i class="bi bi-key-fill"></i>
                            Ubah Password
                        </button>
                        <a href="{{ route('user.profil') }}"
                           class="flex-1 bg-white border border-slate-200 text-slate-700 font-extrabold py-3 rounded-2xl hover:bg-slate-50 transition text-center">
                            Kembali
                        </a>
                    </div>

                </form>
            </div>

        </section>
    </div>
@endsection