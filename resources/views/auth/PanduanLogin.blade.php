<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Login - SIPERCUT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-10">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
        
        <div class="bg-[#9E2A2B] p-8 text-center text-white">
            <div class="flex justify-center mb-3">
                <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo" class="h-16 w-auto object-contain drop-shadow-sm">
            </div>
            <h1 class="text-2xl font-bold tracking-wide">Panduan Login SIPERCUT</h1>
            <p class="text-sm font-light opacity-90">Sistem Informasi Cuti Pegawai</p>
        </div>

        <div class="p-8">
            
            <h2 class="text-xl font-bold text-gray-800 mb-4">Untuk Pegawai Baru</h2>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="font-semibold text-blue-800 mb-2">Login Pertama Kali:</p>
                <ol class="list-decimal list-inside text-sm text-blue-900 space-y-1">
                    <li>Buka halaman <a href="{{ route('login') }}" class="underline font-semibold">Login</a></li>
                    <li>Masukkan <strong>NIP</strong> Anda</li>
                    <li>Masukkan <strong>NIP</strong> Anda sebagai password</li>
                    <li>Klik <strong>Masuk</strong></li>
                </ol>
            </div>

            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <p class="font-semibold text-green-800 mb-2">Setelah Login:</p>
                <ol class="list-decimal list-inside text-sm text-green-900 space-y-1">
                    <li>Buka menu <strong>Profil</strong></li>
                    <li>Klik <strong>Edit Profil</strong></li>
                    <li>Ubah password Anda</li>
                    <li>Update nomor HP/WhatsApp</li>
                    <li>Simpan perubahan</li>
                </ol>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                <p class="font-semibold text-yellow-800 mb-2">Atau Gunakan Fitur Register:</p>
                <ol class="list-decimal list-inside text-sm text-yellow-900 space-y-1">
                    <li>Buka halaman <a href="{{ route('register') }}" class="underline font-semibold">Register</a></li>
                    <li>Masukkan <strong>Nama Lengkap</strong> (sesuai data pegawai)</li>
                    <li>Masukkan <strong>NIP</strong> Anda</li>
                    <li>Masukkan <strong>Nomor HP</strong></li>
                    <li>Buat <strong>Password Baru</strong></li>
                    <li>Konfirmasi password</li>
                    <li>Klik <strong>Daftar</strong></li>
                </ol>
            </div>

            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <p class="font-semibold text-red-800 mb-2">Catatan Penting:</p>
                <ul class="list-disc list-inside text-sm text-red-900 space-y-1">
                    <li>Data pegawai sudah terdaftar di sistem</li>
                    <li>Nama dan NIP harus sesuai dengan database</li>
                    <li>Jika ada kesalahan, hubungi admin</li>
                </ul>
            </div>

            <div class="text-center mt-8 space-x-4">
                <a href="{{ route('login') }}" class="inline-block bg-[#9E2A2B] text-white font-bold py-3 px-6 rounded-lg hover:bg-red-800 transition">
                    Login Sekarang
                </a>
                <a href="{{ route('register') }}" class="inline-block bg-gray-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-700 transition">
                    Daftar Akun
                </a>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('landing') }}" class="text-gray-500 hover:text-[#9E2A2B] transition text-sm">
                    ← Kembali ke Halaman Utama
                </a>
            </div>

        </div>
    </div>

</body>
</html>