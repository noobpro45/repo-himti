<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Jobs\TranscodeVideoJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StorageController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        // ── Kapasitas Disk Server ──
        $diskPath = Storage::disk('media_private')->path('');
        $diskTotal = disk_total_space($diskPath);
        $diskFree  = disk_free_space($diskPath);
        $diskUsed  = $diskTotal - $diskFree;
        $diskPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        // ── Hitung Cache (Chunks) ──
        $chunksDisk = Storage::disk('chunks');
        $chunkFiles = $chunksDisk->allFiles();
        $chunkSize = 0;
        foreach ($chunkFiles as $file) {
            $chunkSize += $chunksDisk->size($file);
        }
        $chunkCount = count($chunkFiles);

        // ── Hitung Orphaned Files (raw + thumbnails + transcoded) ──
        $mediaDisk = Storage::disk('media_private');
        $allRawFiles        = $mediaDisk->allFiles('raw');
        $allThumbFiles      = $mediaDisk->allFiles('thumbnails');
        $allTranscodedFiles = $mediaDisk->allFiles('transcoded');
        
        $validRawPaths   = Media::whereNotNull('path_file')->pluck('path_file')->toArray();
        $validThumbPaths = Media::whereNotNull('path_thumbnail')->pluck('path_thumbnail')->toArray();

        $orphanedRaw        = array_diff($allRawFiles, $validRawPaths);
        $orphanedThumb      = array_diff($allThumbFiles, $validThumbPaths);
        $orphanedTranscoded = array_diff($allTranscodedFiles, $validRawPaths);
        $orphanedFiles      = array_merge($orphanedRaw, $orphanedThumb, $orphanedTranscoded);

        $orphanSize = 0;
        foreach ($orphanedFiles as $file) {
            try { $orphanSize += $mediaDisk->size($file); } catch (\Exception $e) {}
        }
        $orphanCount = count($orphanedFiles);

        // ── Video Gagal ──
        $failedVideos = Media::where('status_proses', 'gagal')
            ->with('album:id_album,nama_acara')
            ->get(['id_media', 'nama_file_asli', 'id_album', 'ukuran_byte', 'created_at']);
        $failedCount = $failedVideos->count();

        return view('admin.storage.index', compact(
            'diskTotal', 'diskFree', 'diskUsed', 'diskPercent',
            'chunkCount', 'chunkSize', 
            'orphanCount', 'orphanSize',
            'failedVideos', 'failedCount'
        ));
    }

    public function clearChunks()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $chunksDisk = Storage::disk('chunks');
        $directories = $chunksDisk->directories();
        foreach ($directories as $dir) {
            $chunksDisk->deleteDirectory($dir);
        }
        $files = $chunksDisk->files();
        foreach ($files as $file) {
            if ($file !== '.gitignore') {
                $chunksDisk->delete($file);
            }
        }

        \App\Models\LogAktivitas::catat(Auth::id(), 'Membersihkan cache upload (chunks)', request()->ip());
        return redirect()->back()->with('success', 'Cache upload berhasil dibersihkan.');
    }

    public function clearOrphans()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $mediaDisk = Storage::disk('media_private');
        $allRawFiles        = $mediaDisk->allFiles('raw');
        $allThumbFiles      = $mediaDisk->allFiles('thumbnails');
        $allTranscodedFiles = $mediaDisk->allFiles('transcoded');
        
        $validRawPaths   = Media::whereNotNull('path_file')->pluck('path_file')->toArray();
        $validThumbPaths = Media::whereNotNull('path_thumbnail')->pluck('path_thumbnail')->toArray();

        $orphanedRaw        = array_diff($allRawFiles, $validRawPaths);
        $orphanedThumb      = array_diff($allThumbFiles, $validThumbPaths);
        $orphanedTranscoded = array_diff($allTranscodedFiles, $validRawPaths);
        $orphanedFiles      = array_merge($orphanedRaw, $orphanedThumb, $orphanedTranscoded);

        $count = 0;
        foreach ($orphanedFiles as $file) {
            $mediaDisk->delete($file);
            $count++;
        }

        \App\Models\LogAktivitas::catat(Auth::id(), 'Membersihkan ' . $count . ' orphaned files', request()->ip());
        return redirect()->back()->with('success', $count . ' file tak terpakai berhasil dihapus.');
    }

    public function retryFailed()
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $failedVideos = Media::where('status_proses', 'gagal')->get();
        $count = 0;

        foreach ($failedVideos as $media) {
            $media->update(['status_proses' => 'diproses']);
            TranscodeVideoJob::dispatch($media);
            $count++;
        }

        \App\Models\LogAktivitas::catat(Auth::id(), 'Mencoba ulang ' . $count . ' video gagal', request()->ip());
        return redirect()->back()->with('success', $count . ' video gagal sedang diproses ulang.');
    }
}

