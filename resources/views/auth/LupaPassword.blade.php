<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SIPERCUT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-10">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl overflow-hidden relative">
        
        <div class="bg-[#9E2A2B] py-8 px-8 text-center text-white border-b-4 border-[#802223]">
            <div class="flex justify-center mb-3">
                <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo Kab Semarang" class="h-16 w-auto object-contain drop-shadow-md">
            </div>
            <h1 class="text-2xl font-bold tracking-wide mb-1">SIPERCUT</h1>
            <p class="text-sm font-light opacity-90">Sistem Informasi Cuti Pegawai</p>
        </div>

        <div class="p-10">
            
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Lupa Password?</h2>
                <p class="text-gray-500 text-sm leading-relaxed px-4">
                    Masukkan Nomor HP anda yang terdaftar untuk menerima link reset password.
                </p>
            </div>

            <form action="{{ url('/link-reset-terkirim') }}" method="GET">
                
                <div class="mb-8">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">
                        Nomor HP: <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9E2A2B] focus:border-transparent transition placeholder-gray-300 text-gray-700" 
                           placeholder="08xxxxx" 
                           required>
                </div>

                <button type="submit" class="w-full bg-[#9E2A2B] text-white font-bold py-3 rounded-lg hover:bg-red-800 transition shadow-md mb-8">
                    Kirim Link Reset
                </button>

            </form>

            <div class="text-center">
                <a href="{{ url('/') }}" class="inline-flex items-center text-gray-500 hover:text-[#9E2A2B] transition font-medium text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Halaman Utama
                </a>
            </div>

        </div>
    </div>

</body>
</html>