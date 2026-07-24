<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\AlbumController;
use Illuminate\Support\Facades\Route;

// ── Guest routes ──
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ── Authenticated routes ──
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (auto-routes by role)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── Super Admin routes ──
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/ringkasan', [DashboardController::class, 'index'])->name('ringkasan');
        
        // Data Anggota (Users)
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'show', 'edit']);
        
        // Semua Album (Pemantauan Global)
        Route::get('albums', [\App\Http\Controllers\AdminAlbumController::class, 'index'])->name('albums.index');
        Route::delete('albums/{id}', [\App\Http\Controllers\AdminAlbumController::class, 'destroy'])->name('albums.destroy');
        
        // Log Aktivitas
        Route::get('logs', [\App\Http\Controllers\LogAktivitasController::class, 'index'])->name('logs.index');
        
        // Pengaturan Sistem
        Route::get('settings', [\App\Http\Controllers\PengaturanController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\PengaturanController::class, 'update'])->name('settings.update');

        // Storage Manager
        Route::get('storage', [\App\Http\Controllers\Admin\StorageController::class, 'index'])->name('storage.index');
        Route::post('storage/clear-chunks', [\App\Http\Controllers\Admin\StorageController::class, 'clearChunks'])->name('storage.clear_chunks');
        Route::post('storage/clear-orphans', [\App\Http\Controllers\Admin\StorageController::class, 'clearOrphans'])->name('storage.clear_orphans');
        Route::post('storage/retry-failed', [\App\Http\Controllers\Admin\StorageController::class, 'retryFailed'])->name('storage.retry_failed');
    });

    // ── Admin PDD routes ──
    Route::middleware('role:admin_pdd')->prefix('pdd')->name('pdd.')->group(function () {
        Route::resource('album', \App\Http\Controllers\AlbumController::class)->except(['index', 'show']);
        Route::post('album/{album}/set-cover/{media}', [\App\Http\Controllers\AlbumController::class, 'setCover'])->name('album.set_cover');
        Route::post('upload/chunk', [\App\Http\Controllers\UploadController::class, 'uploadChunk'])->name('upload.chunk');
        Route::post('upload/merge', [\App\Http\Controllers\UploadController::class, 'mergeChunksApi'])->name('upload.merge');
        Route::post('upload/check-hash', [\App\Http\Controllers\UploadController::class, 'checkHash'])->name('upload.check_hash');
        Route::get('upload/status', [\App\Http\Controllers\UploadController::class, 'uploadStatus'])->name('upload.status');
        
        // Mark notifications as read
        Route::post('notifications/read', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return back();
        })->name('notifications.read');
    });

    // ── Mahasiswa & shared routes (all authenticated users) ──
    Route::get('/katalog', [\App\Http\Controllers\KatalogController::class, 'index'])->name('katalog.index');
    Route::get('/katalog/{album:slug}', [\App\Http\Controllers\KatalogController::class, 'show'])->name('katalog.show');
    Route::get('/riwayat-unduhan', [\App\Http\Controllers\RiwayatUnduhanController::class, 'index'])->name('riwayat.index');

    // Media Download & Stream
    Route::get('/media/{id}/download', [\App\Http\Controllers\MediaController::class, 'download'])->name('media.download');
    Route::get('/media/{id}/stream', [\App\Http\Controllers\MediaController::class, 'stream'])->name('media.stream');
    Route::delete('/media/{media}', [\App\Http\Controllers\MediaController::class, 'destroy'])->name('media.destroy');
    Route::delete('/media/clear/{album}', [\App\Http\Controllers\MediaController::class, 'clearAlbum'])->name('media.clear');
});
