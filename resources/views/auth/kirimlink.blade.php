<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Reset Terkirim - SIPERCUT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8F9FA] min-h-screen flex flex-col relative overflow-x-hidden">

    <header class="bg-[#B92B27] py-8 text-center text-white border-b-4 border-blue-400 shadow-sm">
        <div class="flex justify-center mb-3">
            <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo Kab Semarang" class="h-16 w-auto object-contain drop-shadow-md">
        </div>
        <h1 class="text-3xl font-bold tracking-wide mb-1">SIPERCUT</h1>
        <p class="text-sm font-light opacity-90 tracking-wide">Sistem Informasi Cuti Pegawai</p>
    </header>

    <main class="flex-grow flex flex-col items-center justify-center px-4 py-10">
        <h2 class="text-3xl font-normal text-gray-800 mb-8 text-center tracking-tight">Link Reset Password Terkirim!</h2>

        <div class="mb-6">
            <div class="bg-[#2ECC71] rounded-full w-24 h-24 flex items-center justify-center shadow-lg mx-auto transform transition hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <div class="text-center text-gray-600 mb-10 text-sm">
            <p>Link reset password telah dikirim ke nomor HP Anda.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 w-full max-w-xl relative">
            <div class="flex items-center gap-2 mb-4 text-gray-500 font-medium text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <span class="text-gray-600">Tips:</span>
            </div>
            <ul class="list-disc list-outside ml-6 text-gray-500 space-y-4 text-sm leading-relaxed">
                <li>Periksa pesan masuk di aplikasi SMS Anda.</li>
                <li>Klik link yang ada di dalam SMS untuk reset password Anda.</li>
            </ul>
        </div>

        <div class="mt-12 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center text-gray-500 hover:text-[#9E2A2B] transition font-medium text-sm">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Halaman Utama
            </a>
        </div>
    </main>

    <div id="notification-toast" class="fixed bottom-10 right-10 z-50 flex items-center gap-4 bg-white px-6 py-5 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-xl border border-gray-100 max-w-md">
        
        <div class="w-8 h-8 bg-black rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-check text-white text-sm"></i>
        </div>

        <div>
            <h4 class="font-bold text-gray-900 text-[15px] leading-tight mb-1">Pesan berhasil dikirim!</h4>
            <p class="text-sm text-gray-500 leading-snug">Link reset password telah dikirim ke nomor Anda.</p>
        </div>

        <button onclick="closeToast()" class="ml-2 text-gray-300 hover:text-gray-500">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <script>
        // Script hanya untuk menutup notifikasi (tombol silang)
        // Tidak ada lagi script untuk memunculkan (karena sudah muncul otomatis)
        function closeToast() {
            const toast = document.getElementById("notification-toast");
            toast.style.display = 'none'; // Langsung hilangkan
        }
        
        // Opsional: Hilang otomatis setelah 5 detik
        setTimeout(() => { closeToast(); }, 5000);
    </script>

</body>
</html>