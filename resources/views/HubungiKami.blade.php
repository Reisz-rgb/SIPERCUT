<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - SIPERCUT</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-custom-red { background-color: #961E1E; }
        .text-custom-red { color: #961E1E; }
        @keyframes scaleIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .animate-scale-in { animation: scaleIn 0.3s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-800/50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden animate-scale-in relative">
        
        <div class="flex justify-between items-center px-8 py-5 border-b border-gray-100">
            <h2 class="text-xl font-bold text-slate-800">Hubungi Kami</h2>
            <a href="{{ route('user.dashboard') }}" class="text-slate-400 hover:text-red-500 transition text-2xl">
                <i class="fa-solid fa-xmark"></i>
            </a>
        </div>

        <div class="flex flex-col md:flex-row">
            
            <div class="bg-custom-red text-white p-8 md:w-1/3 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -top-10 -left-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-10 right-10 w-40 h-40 bg-white/5 rounded-full blur-xl"></div>

                <div class="relative z-10">
                    <h3 class="font-bold text-lg mb-4">Informasi Kontak</h3>
                    <p class="text-sm font-light opacity-90 mb-8 leading-relaxed">
                        Kami siap membantu Anda terkait pengajuan cuti dan pertanyaan lainnya.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="mt-1"><i class="fa-solid fa-location-dot text-lg"></i></div>
                            <div>
                                <h4 class="font-semibold text-sm">Disdikbudpora Kab. Semarang</h4>
                                <p class="text-xs font-light opacity-80 mt-1">Jl. Gatot Subroto No.20 B, Ungaran, Kab. Semarang, Jawa Tengah 50517</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="mt-1"><i class="fa-solid fa-phone text-lg"></i></div>
                            <div>
                                <h4 class="font-semibold text-sm">Pelayanan Pelanggan</h4>
                                <p class="text-xs font-light opacity-80 mt-1">(024) 6921134</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="mt-1"><i class="fa-regular fa-clock text-lg"></i></div>
                            <div>
                                <h4 class="font-semibold text-sm">Jam Kerja</h4>
                                <p class="text-xs font-light opacity-80 mt-1">Senin - Kamis: 07.00 - 15.30 WIB</p>
                                <p class="text-xs font-light opacity-80">Jumat: 07.00 - 11.30 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 text-[10px] opacity-60 text-center relative z-10">
                    SIPERCUT &copy; 2026 Disdikbudpora
                </div>
            </div>

            <div class="p-8 md:w-2/3 bg-white">
                <form action="#" method="POST"> 
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-2">Nama Lengkap</label>
                            <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#961E1E] focus:ring-1 focus:ring-[#961E1E] transition" placeholder="Masukkan nama anda">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-3 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#961E1E] focus:ring-1 focus:ring-[#961E1E] transition" placeholder="email@instansi.go.id">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-600 mb-2">Subjek</label>
                        <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#961E1E] focus:ring-1 focus:ring-[#961E1E] transition" placeholder="Perihal pesan anda">
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-bold text-slate-600 mb-2">Pesan</label>
                        <textarea rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-200 text-sm focus:outline-none focus:border-[#961E1E] focus:ring-1 focus:ring-[#961E1E] transition resize-none" placeholder="Tuliskan pesan atau pertanyaan anda disini..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="kirimPesan()" class="bg-custom-red text-white font-bold py-3 px-8 rounded-lg shadow hover:bg-[#7a1818] transition flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane text-sm"></i> Kirim Pesan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <script>
        function kirimPesan() {
            // Arahkan ke dashboard membawa sinyal notif=terkirim
            window.location.href = "{{ route('user.dashboard') }}?notif=terkirim"; 
        }
    </script>
</body>
</html>