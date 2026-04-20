<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('LandingPage'))->name('landing');
Route::get('/hubungi-kami', fn () => view('HubungiKami'))->name('contact');

/*
|--------------------------------------------------------------------------
| GUEST ONLY (belum login)
|--------------------------------------------------------------------------
*/

Route::middleware(['guest', 'no.cache'])->group(function () {

    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle.login')
    ->name('login.process');
    
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');

    Route::get('/panduan-login', fn () => view('auth.PanduanLogin'))->name('panduan.login');

    // Password reset
    Route::get('/lupa-password',  [PasswordResetController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/link-reset-terkirim', fn () => view('auth.kirimlink'))->name('password.sent');
    Route::get('/reset-password/{token}',  [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED (semua role)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user', 'no.cache'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/register-success', fn () => view('auth.RegisterSuccess'))->name('register.success');
});

/*
|--------------------------------------------------------------------------
| USER AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user', 'no.cache'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

        // Profil
        Route::get('/profil',      [UserController::class, 'profile'])->name('profil');
        Route::get('/edit-profil', [UserController::class, 'editProfile'])->name('profil.edit');
        Route::post('/edit-profil', [UserController::class, 'updateProfile'])->name('profil.update');

        // Password
        Route::get('/ubah-password',  [UserController::class, 'showChangePassword'])->name('password.change');
        Route::post('/ubah-password', [UserController::class, 'updatePassword'])->name('password.update.user');

        // Riwayat
        Route::get('/riwayat', [UserController::class, 'history'])->name('riwayat');

        // Pengajuan cuti
        Route::get('/pengajuan-cuti',  [CutiController::class, 'create'])->name('cuti.create');
        Route::post('/pengajuan-cuti', [CutiController::class, 'store'])->name('cuti.store');

        // Download surat cuti — clean.output ditambah di sini saja (bukan double-group)
        Route::get('/cuti/{leave}/download', [UserController::class, 'downloadSuratCuti'])
            ->middleware('clean.output')
            ->name('cuti.download');
});

/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard & profil
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/profil', fn () => view('admin.profil_admin'))->name('profil');

        // Kelola pengajuan cuti
        Route::get('/kelola-pengajuan',    [AdminController::class, 'kelolaPengajuan'])->name('kelola_pengajuan');
        Route::get('/pengajuan/{id}',      [AdminController::class, 'show'])->name('pengajuan.show');
        Route::put('/pengajuan/{id}',      [AdminController::class, 'updateStatus'])->name('pengajuan.update');
        Route::get('/pengajuan/{id}/lampiran', [AdminController::class, 'downloadLampiran'])->name('pengajuan.lampiran');

        // Laporan
        Route::get('/laporan',        [AdminController::class, 'laporan'])->name('laporan');
        Route::get('/download-excel', [AdminController::class, 'downloadExcel'])->name('download.excel');
        Route::get('/download-pdf',   [AdminController::class, 'downloadPdf'])->name('download.pdf');

        // Kelola pegawai — CRUD lengkap
        Route::get('/kelola-pegawai',            [PegawaiController::class, 'index'])->name('kelola_pegawai');
        Route::get('/tambah-pegawai',            [PegawaiController::class, 'create'])->name('tambah_pegawai');
        Route::post('/pegawai/store',            [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{id}/edit',         [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{id}',              [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::delete('/pegawai/{id}',           [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
        Route::post('/pegawai/{id}/reset-password', [PegawaiController::class, 'resetPassword'])->name('pegawai.reset_password');
    });