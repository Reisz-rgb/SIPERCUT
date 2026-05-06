@extends('layouts.user')

@section('title', 'Edit Profil')
@section('page_title', 'Edit Profil')
@section('page_subtitle', 'Perbarui informasi akun Anda (tanpa mengubah NIP).')

@section('content')
    @php($authUser = $user ?? auth()->user())

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main form --}}
        <section class="lg:col-span-8 bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">

            {{-- Card header --}}
            <div class="p-6 md:p-8 border-b border-slate-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center">
                        <span class="text-[var(--maroon)] text-lg font-extrabold">
                            {{ strtoupper(substr(($authUser->name ?? 'U'), 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-extrabold text-slate-800">{{ $authUser->name ?? '-' }}</h3>
                        <p class="text-sm text-slate-500 font-semibold">
                            {{ $authUser->jabatan ?? 'Pegawai' }} • {{ $authUser->bidang_unit ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Card body --}}
            <div class="p-6 md:p-8">

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                        <h6 class="text-sm font-extrabold text-red-800 mb-2 flex items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Periksa kembali input Anda
                        </h6>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.profil.update') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Nama Lengkap</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $authUser->name) }}"
                               required
                               placeholder="Nama lengkap"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Email</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email', $authUser->email) }}"
                               placeholder="Email aktif"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                    </div>

                    {{-- Nomor HP --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Nomor HP</label>
                        <input type="text"
                               name="phone"
                               value="{{ old('phone', $authUser->phone) }}"
                               required
                               placeholder="08xxxxxxxxxx"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                    </div>

                    {{-- NIP (readonly) --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">NIP</label>
                        <input type="text"
                               value="{{ $authUser->nip }}"
                               readonly
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-500 font-semibold cursor-not-allowed">
                        <p class="text-[11px] text-slate-400 mt-2 font-semibold">NIP tidak dapat diubah</p>
                    </div>

                    {{-- Jabatan --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Jabatan</label>
                        <input type="text"
                               name="jabatan"
                               value="{{ old('jabatan', $authUser->jabatan) }}"
                               placeholder="Jabatan saat ini"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                    </div>

                    {{-- Sekolah / Unit Kerja --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Sekolah/Unit Kerja</label>
                        <input type="text"
                               name="bidang_unit"
                               value="{{ old('bidang_unit', $authUser->bidang_unit) }}"
                               placeholder="Unit kerja"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                    </div>

                    {{-- Actions --}}
                    <div class="pt-2 flex flex-col md:flex-row gap-3">
                        <button type="submit"
                                class="flex-1 btn-primary text-white font-extrabold py-3 rounded-2xl transition shadow-lg shadow-red-900/10 inline-flex items-center justify-center gap-2">
                            <i class="bi bi-check-circle-fill"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('user.profil') }}"
                           class="flex-1 bg-white border border-slate-200 text-slate-700 font-extrabold py-3 rounded-2xl hover:bg-slate-50 transition text-center">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </section>

        {{-- Sidebar note --}}
        <aside class="lg:col-span-4">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-soft p-6 md:p-7">
                <h4 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-info-circle-fill text-blue-500"></i>
                    Catatan
                </h4>
                <p class="text-sm text-slate-600 font-medium leading-relaxed mt-3">
                    Untuk mengubah data yang lebih kompleks seperti NIP atau informasi kontrak, silakan hubungi Bagian HRD.
                    Beberapa data mungkin terlindungi dan hanya dapat diubah oleh administrator.
                </p>
            </div>
        </aside>

    </div>
@endsection