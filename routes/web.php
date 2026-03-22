<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CutiController;

/*
|--------------------------------------------------------------------------
| PUBLIC AREA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('LandingPage');
})->name('landing');

Route::get('/hubungi-kami', function () {
    return view('HubungiKami');
})->name('contact');


/*
|--------------------------------------------------------------------------
| AUTH - Guest Only
|--------------------------------------------------------------------------
*/

Route::middleware(['guest','no.cache'])->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');

    // Password Reset
    Route::get('/lupa-password', [PasswordResetController::class, 'showForgotPassword'])
        ->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendResetLink'])
        ->name('password.email');
    
    Route::get('/link-reset-terkirim', fn() => view('auth.kirimlink'))
        ->name('password.sent');
    
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->name('password.update');

    Route::get('/panduan-login', fn() => view('auth.PanduanLogin'))->name('panduan.login');    
});


Route::get('/register-success', fn() => view('auth.RegisterSuccess'))
    ->middleware(['auth','active.user','no.cache'])
    ->name('register.success');
    
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware(['auth','active.user','no.cache']);

/*
|--------------------------------------------------------------------------
| USER AREA - Authenticated Users Only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active.user'])->prefix('user')->name('user.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    // Profil
    Route::get('/profil', [UserController::class, 'profile'])->name('profil');
    Route::get('/edit-profil', [UserController::class, 'editProfile'])->name('profil.edit');
    Route::post('/edit-profil', [UserController::class, 'updateProfile'])->name('profil.update');
    
    // Change Password
    Route::get('/ubah-password', [UserController::class, 'showChangePassword'])->name('password.change');
    Route::post('/ubah-password', [UserController::class, 'updatePassword'])->name('password.update.user');
    
    // Riwayat
    Route::get('/riwayat', [UserController::class, 'history'])->name('riwayat');

    // Download Surat Cuti
    Route::middleware(['auth', 'active.user', 'clean.output'])->group(function () {
        Route::get('/cuti/{id}/download', [UserController::class, 'downloadSuratCuti'])
            ->name('cuti.download');
    });

    // Pengajuan Cuti
    Route::get('/pengajuan-cuti', [CutiController::class, 'create'])->name('cuti.create');
    Route::post('/pengajuan-cuti', [CutiController::class, 'store'])->name('cuti.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active.user', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // 1. DASHBOARD & PROFIL
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profil', function() {
        return view('admin.profil_admin');
    })->name('profil');
    
    // 2. KELOLA PENGAJUAN CUTI (Detail & Update Status)
    Route::get('/pengajuan/{id}', [AdminController::class, 'show'])->name('pengajuan.show');
    Route::put('/pengajuan/{id}', [AdminController::class, 'updateStatus'])->name('pengajuan.update');
    
    Route::get('/pengajuan/{id}/lampiran', [AdminController::class, 'downloadLampiran'])->name('pengajuan.lampiran');

    // 3. DOWNLOAD LAPORAN (Excel & PDF)
    Route::get('/download-excel', [AdminController::class, 'downloadExcel'])->name('download.excel');
    Route::get('/download-pdf', [AdminController::class, 'downloadPdf'])->name('download.pdf');

    // 4. KELOLA PEGAWAI
    Route::get('/kelola-pegawai', [PegawaiController::class, 'index'])->name('kelola_pegawai');
    Route::get('/tambah-pegawai', [PegawaiController::class, 'create'])->name('tambah_pegawai');
    Route::post('/pegawai/store', [PegawaiController::class, 'store'])->name('pegawai.store');
    Route::get('/pegawai/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/pegawai/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
    Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
    Route::post('/pegawai/{id}/reset-password', [PegawaiController::class, 'resetPassword'])->name('pegawai.reset_password');
    
    // 5. KELOLA PENGAJUAN 
    Route::get('/kelola-pengajuan', [AdminController::class, 'kelolaPengajuan'])->name('kelola_pengajuan');
    
    // 6. LAPORAN
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan'); 
});

Route::get('/test-template', function() {
    $templatePath = storage_path('app/template/surat_cuti_template.docx');
    
    if (!file_exists($templatePath)) {
        return response()->json(['error' => 'Template tidak ditemukan'], 404);
    }
    
    try {
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        
        $variables = $templateProcessor->getVariables();
        
        foreach ($variables as $var) {
            $templateProcessor->setValue($var, 'TEST_' . $var);
        }
        
        $testFile = storage_path('app/temp/test_output.docx');
        $templateProcessor->saveAs($testFile);
        
        return response()->json([
            'status' => 'success',
            'template_size' => filesize($templatePath) . ' bytes',
            'output_size' => filesize($testFile) . ' bytes',
            'variables_found' => $variables,
            'total_variables' => count($variables),
            'test_file_created' => file_exists($testFile),
            'download_test' => url('/download-test-file'),
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::get('/download-test-file', function() {
    $testFile = storage_path('app/temp/test_output.docx');
    if (file_exists($testFile)) {
        return response()->download($testFile, 'test_output.docx');
    }
    return 'File test tidak ditemukan';
});