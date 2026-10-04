<?php


use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy',
    ])->name('profile.destroy');
});

Route::middleware([
    'auth',
    'role:admin',
])->group(function () {
    Route::view('/kelas', 'classes.index')
        ->name('classes.index');
});

Route::middleware([
    'auth',
    'role:admin,wali_kelas,guru_bk',
])->group(function () {
    Route::view('/siswa', 'students.index')
        ->name('students.index');
});

Route::middleware([
    'auth',
    'role:admin,kepala_sekolah,wali_kelas,guru_bk,guru',
])->group(function () {
    Route::view('/absensi-siswa', 'attendances.index')
        ->name('attendances.index');
});

Route::middleware([
    'auth',
    'role:admin',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/check-access', function () {
        return response()->json([
            'success' => true,
            'message' => 'Anda memiliki akses admin.',
            'user' => auth()->user()->name,
        ]);
    })->name('check-access');
});


Route::middleware([
    'auth',
    'role:admin,wali_kelas,guru_bk,guru',
])->group(function () {
    Route::view(
        '/catatan-perkembangan',
        'development-notes.index'
    )->name('development-notes.index');
});

Route::middleware([
    'auth',
    'role:admin,kepala_sekolah,wali_kelas,guru_bk,guru',
])->group(function () {
    Route::view(
        '/pelanggaran-siswa',
        'violations.index'
    )->name('violations.index');
});

Route::middleware([
    'auth',
    'role:admin,kepala_sekolah,wali_kelas,guru_bk,guru',
])->group(function () {
    Route::view(
        '/prestasi-siswa',
        'achievements.index'
    )->name('achievements.index');
});

Route::middleware([
    'auth',
    'role:admin,kepala_sekolah,wali_kelas,guru_bk,guru',
])->group(function () {
    Route::view(
        '/perizinan-siswa',
        'student-permissions.index'
    )->name('student-permissions.index');
});

Route::middleware([
    'auth',
    'role:admin,kepala_sekolah,wali_kelas,guru_bk,guru',
])->group(function () {
    Route::view(
        '/bimbingan-konseling',
        'counseling.index'
    )->name('counseling.index');
});

Route::middleware([
    'auth',
    'role:admin,kepala_sekolah,wali_kelas,guru_bk,guru',
])->group(function () {
    Route::view(
        '/profil-siswa',
        'student-profiles.index'
    )->name('student-profiles.index');
});

Route::middleware([
    'auth',
    'role:admin,kepala_sekolah,wali_kelas,guru_bk,guru',
])->prefix('laporan')->name('reports.')->group(function () {
    Route::view('/', 'reports.index')
        ->name('index');

    Route::get(
        '/absensi/pdf',
        [ReportController::class, 'attendance']
    )->name('attendance.pdf');

    Route::get(
        '/pelanggaran/pdf',
        [ReportController::class, 'violations']
    )->name('violations.pdf');

    Route::get(
        '/prestasi/pdf',
        [ReportController::class, 'achievements']
    )->name('achievements.pdf');

    Route::get(
        '/perizinan/pdf',
        [ReportController::class, 'permissions']
    )->name('permissions.pdf');

    Route::get(
        '/konseling/pdf',
        [ReportController::class, 'counseling']
    )->name('counseling.pdf');

    Route::get(
        '/profil-siswa/{student}/pdf',
        [ReportController::class, 'studentProfile']
    )->name('student-profile.pdf');
});

require __DIR__.'/auth.php';
