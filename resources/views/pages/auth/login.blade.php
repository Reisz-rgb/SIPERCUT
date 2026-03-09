<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-10">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-primary-600">SIPERCUT</h1>
            <p class="text-slate-500 mt-2 font-medium">Masuk untuk mengelola cuti Anda</p>
        </div>
        <form action="{{ route('dashboard') }}" method="GET">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email Pegawai</label>
                    <input type="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-600 outline-none transition-all" placeholder="contoh@perusahaan.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kata Sandi</label>
                    <input type="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-600 outline-none transition-all">
                </div>
                <x-button size="lg" class="w-full shadow-lg shadow-primary-200">Masuk Sekarang</x-button>
            </div>
        </form>
    </div>
</body>
</html>