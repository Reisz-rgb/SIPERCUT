@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <div class="flex justify-center">
        <span class="text-4xl font-bold text-primary-600">🔴 SIPERCUT</span>
    </div>
    <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
        Buat Akun Baru
    </h2>
    <p class="mt-2 text-center text-sm text-gray-600">
        Silakan lengkapi data diri Anda untuk mendaftar
    </p>
</div>

<div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
    <div class="bg-white py-8 px-4 shadow-lg rounded-lg sm:px-10 border border-gray-200">
        @if(session('error'))
            <x-alert type="danger" :message="session('error')" />
        @endif

        <form class="space-y-6" action="{{ route('register.post') }}" method="POST">
            @csrf
            
            <x-input 
                name="name" 
                type="text" 
                label="Nama Lengkap" 
                placeholder="John Doe"
                :required="true" />
            
            <x-input 
                name="nip" 
                type="text" 
                label="NIP/NIK" 
                placeholder="1234567890"
                :required="true" />
            
            <x-input 
                name="email" 
                type="email" 
                label="Alamat Email" 
                placeholder="nama@perusahaan.com"
                :required="true" />
            
            <x-input 
                name="phone" 
                type="tel" 
                label="Nomor Telepon" 
                placeholder="08123456789"
                :required="true" />
            
            <x-select 
                name="position" 
                label="Jabatan"
                :options="[
                    'staff' => 'Staff',
                    'supervisor' => 'Supervisor',
                    'manager' => 'Manager',
                    'director' => 'Direktur',
                ]"
                placeholder="Pilih jabatan"
                :required="true" />
            
            <x-select 
                name="department" 
                label="Departemen"
                :options="[
                    'it' => 'IT & Teknologi',
                    'hr' => 'Human Resources',
                    'finance' => 'Keuangan',
                    'marketing' => 'Marketing',
                    'operations' => 'Operasional',
                ]"
                placeholder="Pilih departemen"
                :required="true" />
            
            <x-input 
                name="password" 
                type="password" 
                label="Password" 
                placeholder="••••••••"
                :required="true" />
            
            <x-input 
                name="password_confirmation" 
                type="password" 
                label="Konfirmasi Password" 
                placeholder="••••••••"
                :required="true" />
            
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="terms" 
                        name="terms" 
                        type="checkbox" 
                        required
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="text-gray-700">
                        Saya menyetujui 
                        <a href="#" class="font-medium text-primary-600 hover:text-primary-500">Syarat & Ketentuan</a> 
                        dan 
                        <a href="#" class="font-medium text-primary-600 hover:text-primary-500">Kebijakan Privasi</a>
                    </label>
                </div>
            </div>

            <x-button type="submit" variant="primary" size="lg" :fullWidth="true">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
                Daftar Sekarang
            </x-button>
        </form>

        <div class="mt-6">
            <p class="text-center text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>
@endsection