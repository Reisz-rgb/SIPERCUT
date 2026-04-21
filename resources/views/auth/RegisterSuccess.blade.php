<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F8F9FA] min-h-screen flex flex-col">

    <header class="bg-[#B92B27] py-8 text-center text-white border-b-4 border-blue-400 shadow-sm">
        <div class="flex justify-center mb-3">
            <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo" class="h-16 w-auto object-contain">
        </div>
        <h1 class="text-3xl font-bold tracking-wide mb-1">SIPERCUT</h1>
        <p class="text-sm font-light opacity-90 tracking-wide">Sistem Informasi Cuti Pegawai</p>
    </header>

    <main class="flex-grow flex flex-col items-center justify-center px-4 py-10">
        <h2 class="text-3xl font-normal text-gray-800 mb-8 text-center">Akun Anda Berhasil Dibuat!</h2>

        <div class="mb-6">
            <div class="bg-[#2ECC71] rounded-full w-24 h-24 flex items-center justify-center shadow-lg mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <div class="text-center text-gray-600 mb-10 text-sm leading-relaxed">
            <p>Akun Anda telah berhasil didaftarkan di sistem SIPERCUT.</p>
            <p>Silakan login untuk mengajukan cuti dan mengakses layanan lainnya.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 w-full max-w-xl relative">
            <div class="flex items-center gap-2 mb-4 text-gray-500 font-medium text-lg">
                <span class="text-gray-600">Tips:</span>
            </div>

            <ul class="list-disc list-outside ml-6 text-gray-500 space-y-3 text-sm mb-12">
                <li>Akun Anda telah terdaftar dan sedang menunggu aktivasi oleh administrator.</li>
                <li>Anda akan dapat login setelah akun diaktifkan.</li>
            </ul>

            <div class="absolute bottom-8 right-8">
                <a href="{{ route('login') }}" class="bg-[#9E2A2B] text-white px-6 py-2 rounded shadow hover:bg-red-800 transition text-sm font-semibold">
                    Kembali Ke Login
                </a>
            </div>
        </div>
    </main>

</body>
</html>
