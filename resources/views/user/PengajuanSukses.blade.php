@extends('layouts.user')

@section('title', 'Pengajuan Berhasil')
@section('page_title', 'Pengajuan Berhasil')
@section('page_subtitle', 'Permohonan cuti Anda sudah kami terima.')

@section('content')
    <div class="max-w-2xl mx-auto">
        <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
            <div class="p-8 md:p-10 text-center">

                {{-- Icon --}}
                <div class="w-20 h-20 rounded-3xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mx-auto">
                    <i class="bi bi-check-circle-fill text-4xl text-emerald-600"></i>
                </div>

                {{-- Heading --}}
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 mt-6">Pengajuan Cuti Terkirim</h2>
                <p class="text-slate-500 font-medium mt-3 leading-relaxed">
                    Terima kasih. Pengajuan Anda sedang diproses. Anda bisa memantau statusnya melalui menu
                    <span class="font-extrabold text-slate-800">Riwayat Cuti</span>.
                </p>

                {{-- Actions --}}
                <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('user.riwayat') }}"
                       class="btn-primary text-white px-7 py-3.5 rounded-2xl font-extrabold inline-flex items-center justify-center gap-2">
                        <i class="bi bi-clock-history"></i>
                        Lihat Riwayat
                    </a>
                    <a href="{{ route('user.dashboard') }}"
                       class="bg-white border border-slate-200 text-slate-700 px-7 py-3.5 rounded-2xl font-extrabold inline-flex items-center justify-center gap-2 hover:bg-slate-50 transition">
                        <i class="bi bi-grid-fill"></i>
                        Kembali ke Dashboard
                    </a>
                </div>

            </div>
        </section>
    </div>
@endsection