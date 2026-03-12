@extends('layouts.user')

@section('title', 'Riwayat Cuti')
@section('page_title', 'Riwayat Pengajuan')
@section('page_subtitle', 'Lihat status pengajuan cuti dan detailnya dalam satu tempat.')

@push('head')
    <style>
        /* Modal helper: JS lama pakai class "open" */
        #modal{ display:none; }
        #modal.open{ display:flex; }
    </style>
@endpush

@section('content')
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

                <button class="btn-detail-sm inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-extrabold border border-[var(--maroon)] text-[var(--maroon)] hover:bg-[var(--maroon)] hover:text-white transition-all"
                        onclick="openModal(this)"
                        data-leave='@json($leaveData)'>
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
        <div class="pt-2">
            {{ $leaves->appends(['status' => $status])->links() }}
        </div>
    @endif
</div>


            @if(isset($leaves) && method_exists($leaves, 'hasPages') && $leaves->hasPages())
                <div class="pt-2">
                    {{ $leaves->appends(['status' => $status])->links() }}
                </div>
            @endif
        </div>
    </section>

    <div class="text-center pt-8 pb-4">
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">SIPERCUT © {{ now()->year }} • Disdikbudpora Kab Semarang</p>
    </div>

    {{-- Modal detail (tetap pakai id & JS lama agar logic tidak berubah) --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-[9999] items-center justify-center p-4" id="modal">
        <div class="w-full max-w-3xl bg-white rounded-2xl overflow-hidden shadow-2xl border border-black/10 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h4 id="modalTitle" class="text-base md:text-lg font-extrabold text-slate-800">Detail Pengajuan Cuti</h4>
                <button class="w-10 h-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xl flex items-center justify-center" id="closeBtn" type="button">×</button>
            </div>

            <div class="p-6 overflow-y-auto">
                <div id="statusAlert" class="mb-4 hidden rounded-xl px-4 py-3 text-sm font-bold"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">ID Pengajuan</label>
                        <input id="f_id" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Status Saat Ini</label>
                        <input id="f_status" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-extrabold" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Jenis Cuti</label>
                        <input id="f_jenis" type="text" readonly 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Lampiran</label>
                        <div id="drop-area" class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-4 text-sm">
                            <div class="flex items-center gap-3 text-slate-600">
                                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center">📂</div>
                                <div>
                                    <div class="font-bold" id="drop-text-label">Drag & drop file surat di sini</div>
                                    <div id="file-name-display" class="mt-1 text-[var(--maroon)] font-extrabold"></div>
                                </div>
                            </div>
                            <input type="file" id="f_lampiran_input" hidden accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <input type="hidden" id="f_lampiran_text">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Tanggal Mulai</label>
                        <input id="f_mulai" type="date" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Tanggal Selesai</label>
                        <input id="f_selesai" type="date" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">Alamat Selama Cuti</label>
                        <input id="f_alamat" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-600">No. Kontak</label>
                        <input id="f_kontak" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-extrabold text-slate-600">Alasan / Keterangan</label>
                        <textarea id="f_alasan" readonly class="w-full min-h-[110px] rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm"></textarea>
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-extrabold text-slate-600">Catatan Admin</label>
                        <textarea id="f_catatan" readonly class="w-full min-h-[110px] rounded-xl border border-amber-200 bg-amber-50/60 px-4 py-3 text-sm"></textarea>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <button class="px-5 py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-extrabold text-slate-700" id="cancelBtn" type="button">Tutup</button>

                <div class="flex gap-2">
                    {{-- Tombol Download Surat (Hanya untuk approved) --}}
                    <a href="#" data-download-url-template="{{ route('user.cuti.download', ['id' => '__ID__']) }}" id="btnDownloadSurat" class="hidden sm:inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-extrabold text-white bg-green-600 hover:bg-green-700 transition-all">
                        <i class="bi bi-download"></i>
                        Download Surat
                    </a>
                    
                    {{-- Tombol Edit (Hanya untuk rejected) --}}
                    <a href="{{ route('user.cuti.create') }}" id="btnEditLink" class="hidden sm:inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-extrabold text-white btn-primary">
                        <i class="bi bi-pencil-square"></i>
                        Perbaiki Pengajuan
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const modal            = document.getElementById("modal");
    const closeBtn         = document.getElementById("closeBtn");
    const cancelBtn        = document.getElementById("cancelBtn");
    const modalTitle       = document.getElementById("modalTitle");
    const statusAlert      = document.getElementById("statusAlert");
    const dropArea         = document.getElementById("drop-area");
    const fileInput        = document.getElementById("f_lampiran_input");
    const fileNameDisplay  = document.getElementById("file-name-display");
    const dropTextLabel    = document.getElementById("drop-text-label");
    const btnEditLink      = document.getElementById("btnEditLink");
    const btnDownloadSurat = document.getElementById("btnDownloadSurat");

    // Cegah klik di dalam konten modal bubble ke overlay (yang akan menutup modal)
    const modalContent = modal ? modal.querySelector('.w-full.max-w-3xl') : null;
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    const f = {
        id:           document.getElementById("f_id"),
        status:       document.getElementById("f_status"),
        jenis:        document.getElementById("f_jenis"),
        lampiranText: document.getElementById("f_lampiran_text"),
        mulai:        document.getElementById("f_mulai"),
        selesai:      document.getElementById("f_selesai"),
        alamat:       document.getElementById("f_alamat"),
        kontak:       document.getElementById("f_kontak"),
        alasan:       document.getElementById("f_alasan"),
        catatan:      document.getElementById("f_catatan")
    };

    let activeBtn  = null;
    let activeData = null;

    function openModal(btn) {
        activeBtn  = btn;
        activeData = JSON.parse(btn.getAttribute("data-leave") || "{}");

        f.id.value           = activeData.id       || "";
        f.status.value       = activeData.status   || "";
        f.jenis.value        = activeData.jenis    || "";
        f.lampiranText.value = activeData.lampiran || "";
        f.mulai.value        = activeData.mulai    || "";
        f.selesai.value      = activeData.selesai  || "";
        f.alamat.value       = activeData.alamat   || "";
        f.kontak.value       = activeData.kontak   || "";
        f.alasan.value       = activeData.alasan   || "";
        f.catatan.value      = activeData.catatan  || "";
        fileInput.value      = "";

        if (activeData.lampiran && activeData.lampiran !== "-") {
            fileNameDisplay.innerHTML = `📎 ${activeData.lampiran}`;
            dropTextLabel.textContent = "File saat ini:";
        } else {
            fileNameDisplay.innerHTML = "";
            dropTextLabel.textContent = "Tidak ada lampiran.";
        }

        configureViewMode(activeData.status);
        modal.classList.add("open");
        document.body.style.overflow = "hidden";
    }

    function configureViewMode(status) {
        // Reset semua field ke readonly
        [f.mulai, f.selesai, f.alamat, f.kontak, f.alasan].forEach(el => {
            el.disabled = true;
        });

        if (dropArea)  dropArea.classList.add('disabled');
        if (fileInput) fileInput.disabled = true;

        // Reset tombol aksi
        btnEditLink.style.display      = 'none';
        btnDownloadSurat.style.display = 'none';

        // Reset download button state (kalau sebelumnya loading)
        btnDownloadSurat.innerHTML = '<i class="bi bi-download"></i> Download Surat';
        btnDownloadSurat.disabled  = false;

        statusAlert.classList.add('hidden');

        if (status === "Ditolak") {
            modalTitle.textContent = "Detail Pengajuan (Ditolak)";
            f.status.style.color   = "#b42318";
            showAlert("#fde9ea", "#b42318", "Pengajuan ini ditolak. Silakan perbaiki data.");
            btnEditLink.style.display = 'inline-flex';

        } else if (status === "Diterima") {
            modalTitle.textContent = "Detail Pengajuan";
            f.status.style.color   = "#1f7a46";
            showAlert("#e8f6ee", "#1f7a46", "Pengajuan ini telah disetujui.");

            // Bangun URL download dari template
            const leaveId = activeData.leaveId
                ?? activeBtn?.closest('.h-card')?.getAttribute('data-leave-id');

            if (leaveId) {
                const tpl = btnDownloadSurat.dataset.downloadUrlTemplate || '';
                const url = tpl
                    ? tpl.replace('__ID__', leaveId)
                    : `/user/cuti/${leaveId}/download`;

                btnDownloadSurat.style.display = 'inline-flex';

                // Simpan URL di dataset tombol — listener sudah dipasang sekali di bawah
                btnDownloadSurat.dataset.currentUrl = url;
            }

        } else {
            // pending
            modalTitle.textContent = "Detail Pengajuan";
            f.status.style.color   = "#a56a00";
            showAlert("#fff2df", "#a56a00", "Pengajuan sedang dalam proses verifikasi.");
        }
    }

    function showAlert(bg, color, text) {
        statusAlert.classList.remove('hidden');
        statusAlert.style.background = bg;
        statusAlert.style.color      = color;
        statusAlert.textContent      = text;
    }

    // ── Pasang listener download SEKALI saja (tidak pakai cloneNode) ──
    if (btnDownloadSurat) {
        btnDownloadSurat.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const url = btnDownloadSurat.dataset.currentUrl;
            if (!url) return;

            // Loading state
            btnDownloadSurat.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyiapkan...';
            btnDownloadSurat.disabled  = true;

            // Buka di tab baru → file terdownload, modal TIDAK tertutup
            window.open(url, '_blank');

            // Reset tombol setelah 3 detik
            setTimeout(function() {
                btnDownloadSurat.innerHTML = '<i class="bi bi-download"></i> Download Surat';
                btnDownloadSurat.disabled  = false;
            }, 3000);
        });
    }

    function closeModal() {
        modal.classList.remove("open");
        document.body.style.overflow = "";
    }

    if (closeBtn)  closeBtn.addEventListener("click",  closeModal);
    if (cancelBtn) cancelBtn.addEventListener("click", closeModal);

    // Tutup modal hanya kalau klik tepat di overlay hitam
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }
</script>
@endpush