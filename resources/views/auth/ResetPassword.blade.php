<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SIPERCUT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        .input-field {
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-field:focus {
            border-color: #9E2A2B;
            box-shadow: 0 0 0 3px rgba(158, 42, 43, 0.12);
        }

        .toggle-password {
            cursor: pointer;
            color: #9ca3af;
            transition: color 0.2s;
        }
        .toggle-password:hover {
            color: #9E2A2B;
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-10">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl overflow-hidden relative">

        {{-- Header --}}
        <div class="bg-[#9E2A2B] py-8 px-8 text-center text-white border-b-4 border-[#802223]">
            <div class="flex justify-center mb-3">
                <img src="{{ asset('logokabupatensemarang.png') }}" alt="Logo Kab Semarang" class="h-16 w-auto object-contain drop-shadow-md">
            </div>
            <h1 class="text-2xl font-bold tracking-wide mb-1">SIPERCUT</h1>
            <p class="text-sm font-light opacity-90">Sistem Informasi Cuti Pegawai</p>
        </div>

        <div class="p-10">

            {{-- Title --}}
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="bg-red-50 rounded-full p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#9E2A2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Buat Password Baru</h2>
                <p class="text-gray-500 text-sm leading-relaxed px-4">
                    Masukkan password baru Anda di bawah ini. Pastikan password cukup kuat dan mudah diingat.
                </p>
            </div>

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-600 text-sm flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                            </svg>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf

                {{-- Hidden Fields --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email ?? request('email') }}">

                {{-- Password Baru --}}
                <div class="mb-5">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="password"
                               name="password"
                               class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none placeholder-gray-300 text-gray-700 pr-12"
                               placeholder="Minimal 6 karakter"
                               oninput="checkStrength(this.value)"
                               required>
                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2" onclick="toggleVisibility('password', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>

                    {{-- Password Strength --}}
                    <div class="mt-2">
                        <div class="flex gap-1 mb-1">
                            <div id="bar1" class="strength-bar flex-1 bg-gray-200"></div>
                            <div id="bar2" class="strength-bar flex-1 bg-gray-200"></div>
                            <div id="bar3" class="strength-bar flex-1 bg-gray-200"></div>
                            <div id="bar4" class="strength-bar flex-1 bg-gray-200"></div>
                        </div>
                        <p id="strength-text" class="text-xs text-gray-400"></p>
                    </div>
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-8">
                    <label class="block text-gray-700 font-semibold mb-2 text-sm">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none placeholder-gray-300 text-gray-700 pr-12"
                               placeholder="Ulangi password baru"
                               oninput="checkMatch()"
                               required>
                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2" onclick="toggleVisibility('password_confirmation', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <p id="match-text" class="text-xs mt-1 hidden"></p>
                </div>

                <button type="submit"
                        class="w-full bg-[#9E2A2B] text-white font-bold py-3 rounded-lg hover:bg-red-800 transition shadow-md mb-8 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Password Baru
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

    <script>
        // Toggle show/hide password
        function toggleVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.innerHTML = isPassword
                ? `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                   </svg>`
                : `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                   </svg>`;
        }

        // Password strength checker
        function checkStrength(val) {
            const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3'), document.getElementById('bar4')];
            const text = document.getElementById('strength-text');

            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
            const labels = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat'];

            bars.forEach((bar, i) => {
                bar.style.backgroundColor = i < score ? colors[score - 1] : '#e5e7eb';
            });

            if (val.length === 0) {
                text.textContent = '';
            } else {
                text.textContent = 'Kekuatan: ' + labels[score - 1];
                text.style.color = colors[score - 1];
            }

            checkMatch();
        }

        // Password match checker
        function checkMatch() {
            const pw = document.getElementById('password').value;
            const cf = document.getElementById('password_confirmation').value;
            const text = document.getElementById('match-text');

            if (cf.length === 0) {
                text.classList.add('hidden');
                return;
            }

            text.classList.remove('hidden');
            if (pw === cf) {
                text.textContent = '✓ Password cocok';
                text.style.color = '#22c55e';
            } else {
                text.textContent = '✗ Password tidak cocok';
                text.style.color = '#ef4444';
            }
        }
    </script>

</body>
</html>