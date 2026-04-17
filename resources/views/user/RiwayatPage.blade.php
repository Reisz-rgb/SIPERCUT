@extends('layouts.user')

@section('title', 'Riwayat Cuti')
@section('page_title', 'Riwayat Pengajuan')
@section('page_subtitle', 'Lihat status pengajuan cuti dan detailnya dalam satu tempat.')

@push('head')
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush

@section('content')
<div x-data="leaveModal()" class="p-6">
    @php($status = $status ?? 'all')

    <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
        <div class="p-6 md:p-7 border-b border-slate-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[var(--maroon)]">
                    <i class="bi bi-folder2-open text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800 leading-tight">Riwayat Cuti</h3>
                    <p class="text-sm text-slate-500 font-medium">Filter berdasarkan status, lalu klik detail untuk melihat data lengkap.</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('user.riwayat') }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border transition-all {{ $status == 'all' ? 'active-menu' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50' }}">Semua</a>
                <a href="{{ route('user.riwayat', ['status' => 'approved']) }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border transition-all {{ $status == 'approved' ? 'active-menu' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50' }}">Disetujui</a>
                <a href="{{ route('user.riwayat', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border transition-all {{ $status == 'pending' ? 'active-menu' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50' }}">Tertunda</a>
                <a href="{{ route('user.riwayat', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider border transition-all {{ $status == 'rejected' ? 'active-menu' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50' }}">Ditolak</a>
            </div>
        </div>

<div class="p-5 md:p-7 space-y-4">
    @forelse ($leaves as $leave)
        @php($statusKey = $leave->status ?? 'pending')

        @php($statusUi = [
            'approved' => ['c' => 'emerald', 'i' => 'bi-check-circle-fill', 'l' => 'Disetujui'],
            'pending'  => ['c' => 'orange',  'i' => 'bi-hourglass-split',  'l' => 'Diproses'],
            'rejected' => ['c' => 'red',     'i' => 'bi-x-circle-fill',    'l' => 'Ditolak'],
        ][$statusKey] ?? ['c' => 'slate', 'i' => 'bi-info-circle', 'l' => ucfirst($statusKey)])

        @php($leaveData = [
            'id' => 'CUTI-' . now()->year . '-' . str_pad($leave->id, 4, '0', STR_PAD_LEFT),
            'leaveId' => $leave->id,
            'status' => $statusKey === 'approved' ? 'Diterima' : ($statusKey === 'pending' ? 'Diproses' : 'Ditolak'),
            'jenis' => $leave->jenis_cuti,
            'mulai' => optional($leave->start_date)->format('Y-m-d'),
            'selesai' => optional($leave->end_date)->format('Y-m-d'),
            'alamat' => $leave->address ?? '-',
            'kontak' => $leave->phone ?? '-',
            'kontakDarurat' => $leave->emergency_phone ?? '-',
            'hubunganDarurat' => $leave->emergency_relationship ?? '-',
            'alasan' => $leave->reason,
            'lampiran' => $leave->file_path ? basename($leave->file_path) : '-',
            'catatan' => $leave->rejection_reason ?? '-',
        ])

        <div class="h-card bg-white rounded-2xl border border-slate-100 shadow-sm hover:-translate-y-1 transition-transform p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
             data-status="{{ $statusKey }}"
             data-leave-id="{{ $leave->id }}">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-{{ $statusUi['c'] }}-50 border border-{{ $statusUi['c'] }}-100 flex items-center justify-center flex-shrink-0">
                    <i class="bi {{ $statusUi['i'] }} text-{{ $statusUi['c'] }}-600 text-lg"></i>
                </div>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h4 class="text-sm md:text-base font-extrabold text-slate-800 truncate">{{ $leave->jenis_cuti }}</h4>
                        <span class="text-[10px] font-bold bg-{{ $statusUi['c'] }}-50 text-{{ $statusUi['c'] }}-700 px-2.5 py-1 rounded-lg border border-{{ $statusUi['c'] }}-100 uppercase tracking-wider">
                            {{ $statusUi['l'] }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 font-medium mt-1">
                        <i class="bi bi-calendar3 mr-1"></i>
                        {{ optional($leave->start_date)->format('d M Y') }} s/d {{ optional($leave->end_date)->format('d M Y') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-3">
                <span class="text-xs font-extrabold bg-slate-50 border border-slate-100 px-3 py-2 rounded-xl text-slate-700">
                    {{ $leave->duration }} Hari
                </span>

                <button 
                    class="btn-detail-sm inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-extrabold border border-[var(--maroon)] text-[var(--maroon)] hover:bg-[var(--maroon)] hover:text-white transition-all"
                    @click='openModal(@json($leaveData), "{{ route('user.cuti.download', $leave) }}")'
                >
                    <i class="bi bi-eye"></i>
                    Detail
                </button>
            </div>
        </div>
    @empty
        <div class="text-center py-14">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-folder2-open text-slate-200 text-3xl"></i>
            </div>
            <p class="text-slate-400 text-sm font-medium">Belum ada riwayat pengajuan cuti.</p>
            <a href="{{ route('user.cuti.create') }}"
               class="btn-primary text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-red-900/15 text-sm inline-flex items-center justify-center mt-6">
                <i class="bi bi-plus-circle-fill mr-2"></i>
                Ajukan Cuti
            </a>
        </div>
    @endforelse

    @if(isset($leaves) && method_exists($leaves, 'hasPages') && $leaves->hasPages())
        <div class="pt-4 flex items-center justify-between text-sm">
            <p class="text-slate-500 font-medium text-xs">
                Menampilkan {{ $leaves->firstItem() }}–{{ $leaves->lastItem() }} dari {{ $leaves->total() }} data
            </p>
            <div class="flex items-center gap-1">
                {{-- Tombol Prev --}}
                @if($leaves->onFirstPage())
                    <span class="px-3 py-2 rounded-xl border border-slate-200 text-slate-300 text-xs font-bold cursor-not-allowed">‹</span>
                @else
                    <a href="{{ $leaves->appends(['status' => $status])->previousPageUrl() }}"
                    class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs font-bold transition-all">‹</a>
                @endif

                {{-- Nomor halaman --}}
                @foreach($leaves->getUrlRange(1, $leaves->lastPage()) as $page => $url)
                    @if($page == $leaves->currentPage())
                        <span class="px-3 py-2 rounded-xl text-xs font-extrabold text-white"
                            style="background-color: var(--maroon);">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&status={{ $status }}"
                        class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs font-bold transition-all">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if($leaves->hasMorePages())
                    <a href="{{ $leaves->appends(['status' => $status])->nextPageUrl() }}"
                    class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs font-bold transition-all">›</a>
                @else
                    <span class="px-3 py-2 rounded-xl border border-slate-200 text-slate-300 text-xs font-bold cursor-not-allowed">›</span>
                @endif
            </div>
        </div>
    @endif
</div>

    <div class="text-center pt-8 pb-4">
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">SIPERCUT © {{ now()->year }} • Disdikbudpora Kab Semarang</p>
    </div>

    {{-- Modal detail --}}
    <div 
    x-show="open"
    x-cloak
    @click.self="closeModal()" 
    x-transition
    class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-[9999] flex items-center justify-center p-4"
    >
        <div class="w-full max-w-3xl bg-white rounded-2xl overflow-hidden shadow-2xl border border-black/10 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h4 id="modalTitle" class="text-base md:text-lg font-extrabold text-slate-800">Detail Pengajuan Cuti</h4>
                <button @click="closeModal()" type="button" class="w-10 h-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xl flex items-center justify-center">×</button>
            </div>

            <div class="p-6 overflow-y-auto">
                <div 
                    x-show="status"
                    class="mb-4 rounded-xl px-4 py-3 text-sm font-bold"
                    :class="{
                        'bg-red-100 text-red-700': status === 'Ditolak',
                        'bg-green-100 text-green-700': status === 'Diterima',
                        'bg-amber-100 text-amber-700': status === 'Diproses'
                    }"
                    x-text="
                        status === 'Ditolak' ? 'Pengajuan ini ditolak. Silakan perbaiki data.' :
                        status === 'Diterima' ? 'Pengajuan ini telah disetujui.' :
                        'Pengajuan sedang dalam proses verifikasi.'
                    "
                ></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">ID Pengajuan</label>
                        <input  :value="data.id" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Status Saat Ini</label>
                        <input  :value="data.status" 
                        :class="{
                            'text-red-600': status === 'Ditolak',
                            'text-green-600': status === 'Diterima',
                            'text-amber-600': status === 'Diproses'
                        }"
                        type="text" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-extrabold" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Jenis Cuti</label>
                        <select  x-model="data.jenis" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                            <option>Cuti Tahunan</option>
                            <option>Cuti Sakit</option>
                            <option>Cuti Besar</option>
                            <option>Cuti Melahirkan</option>
                            <option>Cuti Karena Alasan Penting</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Lampiran</label>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Data Lampiran
                                    </span>
                                    
                                    <div 
                                        class="mt-0.5 truncate font-extrabold text-[var(--maroon)]"
                                        x-text="data.lampiran !== '-' ? data.lampiran : 'Tidak ada berkas terlampir'"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Tanggal Mulai</label>
                        <input  :value="data.mulai" type="date" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Tanggal Selesai</label>
                        <input  :value="data.selesai" type="date" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Alamat Selama Cuti</label>
                        <input  :value="data.alamat" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">No. Kontak</label>
                        <input  :value="data.kontak" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">No. Kontak Darurat</label>
                        <input 
                            :value="data.kontakDarurat" 
                            type="text" 
                            readonly 
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" 
                        />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Hubungan dengan yang Bersangkutan</label>
                        <input 
                            :value="data.hubunganDarurat" 
                            type="text" 
                            readonly 
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" 
                        />
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-extrabold text-slate-600">Alasan / Keterangan</label>
                        <textarea x-bind:value="data.alasan" readonly class="w-full min-h-[110px] rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm"></textarea>
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-extrabold text-slate-600">Catatan Admin</label>
                        <textarea x-bind:value="data.catatan" readonly class="w-full min-h-[110px] rounded-xl border border-amber-200 bg-amber-50/60 px-4 py-3 text-sm"></textarea>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <button @click="closeModal()" type="button" class="px-5 py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-extrabold text-slate-700">
                    Tutup
                </button>

                <div class="flex gap-2">
                    {{-- Tombol Download Surat (Hanya untuk approved) --}}
                    <button 
                        @click.prevent="downloadFile"
                        x-show="status === 'Diterima'"
                        class="sm:inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-extrabold text-white bg-green-600 hover:bg-green-700 transition-all"
                    >
                        <i class="bi bi-download"></i>
                        Download Surat
                    </button>
                    
                    {{-- Tombol Edit (Hanya untuk rejected) --}}
                    <a  href="{{ route('user.cuti.create') }}" x-show="status === 'Ditolak'" class="sm:inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-extrabold text-white btn-primary">
                        <i class="bi bi-pencil-square"></i>
                        Perbaiki Pengajuan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function leaveModal() {
            return {
                open: false,
                data: {
                    id: '',
                    leaveId: '',
                    status: '',
                    jenis: '',
                    mulai: '',
                    selesai: '',
                    alamat: '',
                    kontak: '',
                    kontakDarurat: '',
                    hubunganDarurat: '',
                    alasan: '',
                    lampiran: '',
                    catatan: ''
                },
                downloadUrl: '',
                status: '',

                openModal(leaveData, url) {
                    this.data = leaveData;

                    this.downloadUrl = url;
                    this.status = this.data.status;
                    this.open = true;
                    document.body.style.overflow = 'hidden';
                    console.log(leaveData);
                    console.log(this.data.kontakDarurat);
                    console.log(this.data.hubunganDarurat);
                },

                closeModal() {
                    this.open = false;
                    document.body.style.overflow = '';
                },

                downloadFile() {
                    if (!this.downloadUrl) {
                        alert('URL download tidak tersedia!');
                        return;
                    }
                    window.open(this.downloadUrl, '_blank');
                }
            }
        }
    </script>
@endpush