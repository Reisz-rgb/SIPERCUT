@extends('layouts.user')

@section('title', 'Ajukan Cuti')
@section('page_title', 'Pengajuan Cuti')
@section('page_subtitle', 'Lengkapi formulir berikut untuk mengajukan cuti.')

@section('content')
    @php($authUser = $user ?? auth()->user())

    <div class="max-w-5xl mx-auto space-y-6">
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white border border-red-200 flex items-center justify-center text-red-600">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-red-800">Periksa kembali input Anda</h4>
                        <ul class="list-disc list-inside text-sm text-red-700 mt-2 space-y-1 font-semibold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('user.cuti.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- I. DATA PEGAWAI --}}
            <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 md:px-8 py-5 border-b border-slate-50 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[var(--maroon)]">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-sm md:text-base font-extrabold text-slate-800">I. Data Pegawai</h3>
                            <p class="text-xs md:text-sm text-slate-500 font-medium">Data ini otomatis dari akun Anda.</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Wajib</span>
                </div>

                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Nama Lengkap</label>
                        <input type="text" value="{{ $authUser->name }}" readonly class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 font-semibold" />
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">NIP</label>
                        <input type="text" value="{{ $authUser->nip }}" readonly class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 font-semibold" />
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Jabatan</label>
                        <input type="text" value="{{ $authUser->jabatan ?? '-' }}" readonly class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 font-semibold" />
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Masa Kerja</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex items-center gap-2">
                                <input type="text" value="{{ floor($workYears ?? 0) }}" readonly class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 font-semibold" />
                                <span class="text-xs text-slate-500 font-bold">Tahun</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" value="{{ floor($workMonths ?? 0) }}" readonly class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 font-semibold" />
                                <span class="text-xs text-slate-500 font-bold">Bulan</span>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Unit Kerja</label>
                        <input type="text" value="{{ $authUser->bidang_unit ?? 'DISDIKBUDPORA KABUPATEN SEMARANG' }}" readonly class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 font-semibold" />
                    </div>
                </div>
            </section>

            {{-- II. JENIS CUTI --}}
            <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 md:px-8 py-5 border-b border-slate-50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[var(--maroon)]">
                        <i class="bi bi-ui-radios"></i>
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-extrabold text-slate-800">II. Jenis Cuti yang Diambil</h3>
                        <p class="text-xs md:text-sm text-slate-500 font-medium">Pilih salah satu jenis cuti.</p>
                    </div>
                </div>

                <div class="p-6 md:p-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php($jenisOld = old('jenis_cuti', 'Cuti Tahunan'))
                    @foreach([
                        'Cuti Tahunan' => 'Cuti Tahunan',
                        'Cuti Besar' => 'Cuti Besar',
                        'Cuti Sakit' => 'Cuti Sakit',
                        'Cuti Melahirkan' => 'Cuti Melahirkan',
                        'Cuti Alasan Penting' => 'Cuti Karena Alasan Penting',
                        'Cuti Luar Tanggungan' => 'Cuti di Luar Tanggungan Negara',
                    ] as $value => $label)
                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                            <input type="radio" name="jenis_cuti" value="{{ $value }}" class="h-4 w-4 text-[var(--maroon)]" {{ $jenisOld === $value ? 'checked' : '' }}>
                            <div class="min-w-0">
                                <div class="text-sm font-extrabold text-slate-800">{{ $label }}</div>
                                <div class="text-xs text-slate-500 font-medium">{{ $value }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </section>

            {{-- III. ALASAN CUTI --}}
            <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 md:px-8 py-5 border-b border-slate-50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[var(--maroon)]">
                        <i class="bi bi-chat-left-text-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-extrabold text-slate-800">III. Alasan Cuti</h3>
                        <p class="text-xs md:text-sm text-slate-500 font-medium">Minimal 20 karakter agar mudah diverifikasi.</p>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    <label class="block text-xs font-extrabold text-slate-600 mb-2">Uraian Alasan <span class="text-red-600">*</span></label>
                    <textarea name="alasan" minlength="20" required
                              class="w-full min-h-[140px] px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-medium focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]"
                              placeholder="Jelaskan alasan cuti secara detail...">{{ old('alasan') }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-2 font-semibold">Berikan alasan yang jelas, minimal 20 karakter.</p>
                </div>
            </section>

            {{-- IV. LAMA CUTI --}}
            <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 md:px-8 py-5 border-b border-slate-50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[var(--maroon)]">
                        <i class="bi bi-calendar2-week-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-extrabold text-slate-800">IV. Lamanya Cuti</h3>
                        <p class="text-xs md:text-sm text-slate-500 font-medium">Isi lama hari dan rentang tanggal cuti.</p>
                    </div>
                </div>

                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Selama (Hari) <span class="text-red-600">*</span></label>
                        <input type="number" name="lama_hari" min="1" required value="{{ old('lama_hari') }}"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]"
                               placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Mulai Tanggal <span class="text-red-600">*</span></label>
                        <input type="date" name="tanggal_mulai" required value="{{ old('tanggal_mulai') }}"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">s/d Tanggal <span class="text-red-600">*</span></label>
                        <input type="date" name="tanggal_selesai" required value="{{ old('tanggal_selesai') }}"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]">
                    </div>
                </div>
            </section>

            {{-- V. CATATAN CUTI --}}
            <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 md:px-8 py-5 border-b border-slate-50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[var(--maroon)]">
                        <i class="bi bi-table"></i>
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-extrabold text-slate-800">V. Catatan Cuti</h3>
                        <p class="text-xs md:text-sm text-slate-500 font-medium">Ringkasan sisa cuti berdasarkan tahun.</p>
                    </div>
                </div>

                <div class="p-6 md:p-8 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="py-3 pr-4">Tahun</th>
                                <th class="py-3 pr-4">Sisa Cuti</th>
                                <th class="py-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-4 pr-4 font-extrabold text-slate-700">N-2 ({{ $leaveBalance['n2']['year'] ?? '-' }})</td>
                                <td class="py-4 pr-4"><input type="text" readonly value="{{ $leaveBalance['n2']['remaining'] ?? 0 }}" class="w-28 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-semibold"></td>
                                <td class="py-4"><input type="text" readonly value="Bonus: {{ $leaveBalance['n2']['bonus'] ?? 0 }} hari (setengah dari sisa)" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 font-medium"></td>
                            </tr>
                            <tr>
                                <td class="py-4 pr-4 font-extrabold text-slate-700">N-1 ({{ $leaveBalance['n1']['year'] ?? '-' }})</td>
                                <td class="py-4 pr-4"><input type="text" readonly value="{{ $leaveBalance['n1']['remaining'] ?? 0 }}" class="w-28 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-semibold"></td>
                                <td class="py-4"><input type="text" readonly value="Bonus: {{ $leaveBalance['n1']['bonus'] ?? 0 }} hari (setengah dari sisa)" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 font-medium"></td>
                            </tr>
                            <tr>
                                <td class="py-4 pr-4 font-extrabold text-slate-700">N ({{ $leaveBalance['n']['year'] ?? '-' }}) - Tahun Berjalan</td>
                                <td class="py-4 pr-4"><input type="text" readonly value="{{ $leaveBalance['n']['remaining'] ?? 0 }}" class="w-28 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-[var(--maroon)] font-extrabold"></td>
                                <td class="py-4"><input type="text" readonly value="Sisa cuti tahun ini (dari {{ $leaveBalance['n']['quota'] ?? 0 }} hari)" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 font-medium"></td>
                            </tr>
                            <tr class="bg-emerald-50/30">
                                <td class="py-4 pr-4 font-black text-[var(--maroon)]">TOTAL TERSEDIA</td>
                                <td class="py-4 pr-4"><input type="text" readonly value="{{ $leaveBalance['total_available'] ?? 0 }}" class="w-28 px-3 py-2 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 font-black"></td>
                                <td class="py-4"><input type="text" readonly value="Total cuti yang dapat diambil saat ini" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-600 font-medium"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- VI. ALAMAT CUTI --}}
            <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 md:px-8 py-5 border-b border-slate-50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[var(--maroon)]">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-extrabold text-slate-800">VI. Alamat Selama Menjalankan Cuti</h3>
                        <p class="text-xs md:text-sm text-slate-500 font-medium">Isi alamat dan kontak yang bisa dihubungi.</p>
                    </div>
                </div>

                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Alamat Lengkap <span class="text-red-600">*</span></label>
                        <textarea name="alamat_cuti" required class="w-full min-h-[110px] px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-medium focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten...">{{ old('alamat_cuti') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Nomor Telepon / HP <span class="text-red-600">*</span></label>
                        <input type="text" name="no_telepon" required value="{{ old('no_telepon') }}" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-800 font-semibold focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="hidden md:block"></div>
                </div>
            </section>

            {{-- VII. DOKUMEN PENDUKUNG --}}
            <section class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 md:px-8 py-5 border-b border-slate-50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-[var(--maroon)]">
                        <i class="bi bi-paperclip"></i>
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-extrabold text-slate-800">VII. Dokumen Pendukung (Opsional)</h3>
                        <p class="text-xs md:text-sm text-slate-500 font-medium">Unggah lampiran jika diperlukan (surat dokter, dsb).</p>
                    </div>
                </div>

                <div class="p-6 md:p-8 space-y-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Catatan Tambahan</label>
                        <textarea name="catatan_tambahan" class="w-full min-h-[110px] px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50/30 text-slate-800 font-medium focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-[var(--maroon)]" placeholder="Tambahkan catatan jika ada hal spesifik...">{{ old('catatan_tambahan') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-600 mb-2">Dokumen Lampiran</label>
                        <div id="dropZone" class="relative rounded-2xl border border-dashed border-slate-200 bg-slate-50/40 p-6 flex flex-col items-center justify-center text-center">
                            <input type="file" name="dokumen_pendukung" id="fileUpload" accept=".pdf,.doc,.docx,.jpg,.png,.xls,.xlsx" class="absolute inset-0 opacity-0 cursor-pointer" />
                            <div id="uploadIcon" class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-500">
                                <i class="bi bi-upload text-2xl"></i>
                            </div>
                            <div id="uploadText" class="mt-4 text-sm font-extrabold text-slate-700">Klik atau seret file ke sini</div>
                            <div id="uploadHint" class="mt-2 text-xs text-slate-500 font-medium">Supported: PDF, DOC, JPG, PNG, XLS (Maks 5MB)</div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-5 flex gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                            <i class="bi bi-lightbulb-fill"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold text-blue-800">Tips</div>
                            <p class="text-sm text-blue-700/80 font-medium mt-1">Pastikan melengkapi dokumen pendukung agar proses verifikasi berjalan lancar.</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="pt-2">
                <button type="submit" class="w-full btn-primary text-white px-6 py-4 rounded-2xl font-extrabold shadow-lg shadow-red-900/15 inline-flex items-center justify-center gap-2">
                    <i class="bi bi-send-fill"></i>
                    Ajukan Permohonan Cuti
                </button>
                <p class="text-center text-[11px] text-slate-400 font-semibold mt-3">Dengan menekan tombol ini, Anda menyatakan bahwa data yang diisi adalah benar.</p>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const fileInput = document.getElementById('fileUpload');
        const uploadText = document.getElementById('uploadText');
        const uploadHint = document.getElementById('uploadHint');
        const uploadIcon = document.getElementById('uploadIcon');
        const dropZone = document.getElementById('dropZone');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];

                    // Ubah tampilan menjadi mode "File Terpilih" (logika tetap)
                    uploadText.innerText = "File Siap Diupload!";
                    uploadText.classList.add('text-emerald-700');

                    uploadHint.innerHTML = `<strong>Berhasil memilih:</strong> ${file.name} (${(file.size/1024).toFixed(1)} KB)`;
                    uploadHint.classList.add('text-emerald-700');

                    uploadIcon.innerHTML = '<i class="bi bi-check-circle-fill text-2xl text-emerald-600"></i>';
                    dropZone.classList.remove('border-slate-200');
                    dropZone.classList.add('border-emerald-300');
                    dropZone.classList.add('bg-emerald-50/40');
                }
            });
        }
    </script>
@endpush
