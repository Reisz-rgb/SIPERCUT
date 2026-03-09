@extends('layouts.user')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')
@section('page_subtitle', 'Selamat datang kembali di portal layanan cuti.')

@section('content')
    @php
        $authUser = $user ?? auth()->user();
        $recent = $recentLeaves ?? $latestLeaves ?? [];
    @endphp

    {{-- Welcome / CTA (mengikuti benchmark dashboard yang Anda kirim) --}}
    <section class="bg-white border-0 rounded-[2.5rem] p-8 md:p-10 shadow-soft flex flex-col justify-center">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 rounded-full px-3 py-1 mb-4">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-extrabold tracking-widest uppercase">{{ $authUser->status ?? 'AKTIF' }}</span>
                </div>

                <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 leading-tight">Halo, {{ explode(' ', $authUser->name ?? 'User')[0] }}! 👋</h3>
                <p class="text-slate-500 text-base font-medium mt-2 leading-relaxed md:w-3/4">Pantau sisa jatah cuti dan status pengajuan Anda.</p>
            </div>

            <div class="flex-shrink-0">
                <a href="{{ route('user.cuti.create') }}" class="btn-primary text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-red-900/20 text-sm md:text-base inline-flex items-center justify-center w-full md:w-auto transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="bi bi-plus-circle-fill mr-3 text-lg"></i>
                    Ajukan Cuti Baru
                </a>
            </div>
        </div>
    </section>

    {{-- Stats + Recent (layout mengikuti dashboard benchmark) --}}
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mt-6">
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-transform flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center flex-shrink-0 border border-blue-100 text-blue-500">
                    <i class="bi bi-files text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase">Jatah Tahunan</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-slate-800 leading-none">{{ $totalQuota ?? 0 }}</h3>
                        <p class="text-[11px] text-slate-400 font-bold uppercase">Hari</p>
                    </div>
                    @php
                        $quotaMax = (int)($annualQuota ?? ($totalQuota ?? 0));
                        $remainAnnual = (int)($totalQuota ?? 0);
                        $pctAnnual = ($quotaMax > 0) ? min(100, max(0, ($remainAnnual / $quotaMax) * 100)) : 0;
                    @endphp
                    <div class="mt-3 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-500 h-full" style="width: {{ $pctAnnual }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:-translate-y-1 transition-transform flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0 border border-orange-100 text-orange-500">
                    <i class="bi bi-hourglass-split text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase">Telah Diambil</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-orange-600 leading-none">{{ $usedLeave ?? 0 }}</h3>
                        <p class="text-[11px] text-slate-400 font-bold uppercase">Hari</p>
                    </div>
                    <div class="mt-3 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        @php
                            $quota = max((int)($totalQuota ?? 0), 1);
                            $used = (int)($usedLeave ?? 0);
                            $pct = min(100, max(0, ($used / $quota) * 100));
                        @endphp
                        <div class="bg-orange-500 h-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-emerald-100 shadow-sm hover:-translate-y-1 transition-transform flex items-center gap-5 bg-gradient-to-r from-white to-emerald-50/30">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-content-center flex-shrink-0 border border-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="bi bi-check-lg text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-extrabold text-slate-400 tracking-wider uppercase">Sisa Cuti Tersedia</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-slate-800 leading-none">{{ $remainingLeave ?? 0 }}</h3>
                        <p class="text-[11px] text-slate-400 font-bold uppercase">Hari</p>
                    </div>
                    @php
                        $maxAvail = (int)($maxAvailable ?? ($remainingLeave ?? 0));
                        $remainAvail = (int)($remainingLeave ?? 0);
                        $pctAvail = ($maxAvail > 0) ? min(100, max(0, ($remainAvail / $maxAvail) * 100)) : 0;
                    @endphp
                    <div class="mt-3 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-emerald-500 h-full" style="width: {{ $pctAvail }}%"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="lg:col-span-7">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-clock-history text-[var(--maroon)]"></i>
                        Pengajuan Terbaru
                    </h4>
                    <a href="{{ route('user.riwayat') }}" class="text-[11px] font-bold text-[var(--maroon)] hover:underline uppercase tracking-tighter">Lihat Semua</a>
                </div>

                <div class="p-5 space-y-4">
                    @forelse($recent as $leave)
                        @php
                            $statusColor = [
                                'approved' => 'emerald',
                                'pending' => 'orange',
                                'rejected' => 'red',
                            ][$leave->status] ?? 'slate';

                            $statusIcon = [
                                'approved' => 'bi-check-circle-fill',
                                'pending' => 'bi-hourglass-split',
                                'rejected' => 'bi-x-circle-fill',
                            ][$leave->status] ?? 'bi-info-circle';
                        @endphp

                        <div class="flex items-center gap-4 p-4 rounded-2xl border border-slate-50 hover:bg-slate-50 transition-all group">
                            <div class="w-12 h-12 rounded-xl bg-{{ $statusColor }}-50 border border-{{ $statusColor }}-100 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="bi {{ $statusIcon }} text-{{ $statusColor }}-500 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-3">
                                    <h5 class="font-bold text-slate-800 text-sm truncate">{{ $leave->jenis_cuti }}</h5>
                                    <span class="text-[10px] font-bold bg-{{ $statusColor }}-50 text-{{ $statusColor }}-600 px-2.5 py-1 rounded-lg capitalize border border-{{ $statusColor }}-100">{{ $leave->status }}</span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium mt-1">
                                    {{ $leave->start_date->format('d M Y') }} • <span class="text-slate-800">{{ $leave->duration }} Hari</span>
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="bi bi-folder2-open text-slate-200 text-3xl"></i>
                            </div>
                            <p class="text-slate-400 text-sm font-medium">Belum ada riwayat pengajuan cuti.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <div class="text-center pt-8 pb-4">
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">© {{ now()->year }} Pemerintah Kabupaten Semarang • Disdikbudpora</p>
    </div>
@endsection
