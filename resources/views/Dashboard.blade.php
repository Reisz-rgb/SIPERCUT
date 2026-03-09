<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPERCUT - Disdikbudpora Kab. Semarang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white">

    <header class="bg-[#9E2A2B] text-white py-4 shadow-md">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo Kabupaten Semarang" class="h-16 w-auto object-contain">
                
                <div class="leading-tight">
                    <h1 class="font-bold text-lg tracking-wide">DISDIKBUDPORA</h1>
                    <p class="text-xs font-light tracking-wider">KAB. SEMARANG</p>
                </div>
            </div>

            <a href="{{ url('/login') }}" class="bg-white text-[#9E2A2B] px-6 py-2 rounded-md font-bold text-sm flex items-center gap-2 hover:bg-gray-100 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Login/Register
            </a>
        </div>
    </header>

    <section class="text-center pt-16 pb-12 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-[#9E2A2B] font-bold text-2xl mb-2">Selamat Datang di Aplikasi</h2>
            <h1 class="text-black font-extrabold text-5xl mb-8">SIPERCUT</h1>

            <div class="flex justify-center mb-8">
                <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo Kabupaten Semarang" class="h-32 w-auto object-contain drop-shadow-md">
            </div>

            <div class="max-w-2xl mx-auto text-gray-600 text-sm leading-relaxed">
                <p class="font-bold text-gray-800">Sistem Informasi Cuti Pegawai <span class="font-normal text-gray-600">Disdikbudpora Kab. Semarang. Aplikasi</span></p>
                <p>resmi untuk pengajuan dan pengelolaan izin cuti pegawai di lingkungan Dinas</p>
                <p>Pendidikan, Kebudayaan, Kepemudaan dan Olahraga.</p>
            </div>
        </div>
    </section>

    <section class="bg-[#F8F9FA] py-16">
        <div class="container mx-auto px-4 text-center">
            <h3 class="text-[#9E2A2B] font-bold text-2xl mb-2">Layanan Kami</h3>
            <div class="h-1 w-16 bg-[#9E2A2B] mx-auto mb-4 rounded-full"></div>
            <p class="text-gray-500 text-sm mb-12">Silakan pilih layanan yang tersedia di bawah ini untuk memulai pengajuan.</p>

            <div class="flex justify-center">
                <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-sm hover:shadow-xl transition duration-300 border border-gray-100">
                    <div class="flex justify-center mb-6">
                        <div class="bg-red-50 p-4 rounded-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#9E2A2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <h4 class="text-gray-800 font-bold text-lg mb-2">Pengajuan Cuti</h4>
                    <p class="text-gray-500 text-xs mb-8 leading-relaxed">
                        Layanan pengajuan berbagai jenis cuti pegawai secara online.
                    </p>
                    
                    <a href="{{ url('/login') }}" class="block w-full bg-[#9E2A2B] text-white font-semibold py-3 rounded-lg hover:bg-red-800 transition flex justify-center items-center gap-2">
                        Ajukan Sekarang
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-[#9E2A2B] text-white py-6 mt-auto">
        <div class="container mx-auto px-4 text-center text-xs opacity-90">
            <span class="font-bold">© 2026</span> &nbsp; Dinas Pendidikan, Kebudayaan, Kepemudaan dan Olahraga Kabupaten Semarang
        </div>
    </footer>

</body>
</html>