<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
    public function download($id)
    {
        $media = Media::findOrFail($id);

        // Hanya super_admin, admin_pdd, atau uploader asli yang bisa mengakses, 
        // tapi di aplikasi ini, mahasiswa juga bisa mengunduh asalkan sudah login (Auth)
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        // Cek file fisik
        $filePath = $media->path_file; // e.g. raw/123-uuid.mp4
        
        if (!Storage::disk('media_private')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        // Catat riwayat unduhan (gunakan updateOrCreate agar tidak duplikat jika file yang sama diunduh berkali-kali)
        \App\Models\RiwayatUnduhan::updateOrCreate(
            [
                'id_user' => Auth::id(),
                'id_media' => $media->id_media,
            ],
            [
                'updated_at' => now(), // update timestamp jika diunduh ulang
            ]
        );

        // Force download
        return Storage::disk('media_private')->download(
            $filePath, 
            $media->nama_file_asli
        );
    }

    public function stream($id)
    {
        $media = Media::findOrFail($id);

        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $filePath = $media->path_file;
        
        // Cek jika request thumbnail dan thumbnail tersedia
        if (request()->query('thumb') && $media->path_thumbnail && Storage::disk('media_private')->exists($media->path_thumbnail)) {
            $filePath = $media->path_thumbnail;
        }
        
        if (!Storage::disk('media_private')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        // Use response()->file() for proper HTTP Range support (206 Partial Content) to allow seeking
        $absolutePath = Storage::disk('media_private')->path($filePath);
        return response()->file($absolutePath);
    }

    public function destroy(Media $media)
    {
        // Hanya super_admin, atau admin_pdd yang memiliki album terkait
        if (!Auth::user()->isSuperAdmin() && !(Auth::user()->isAdminPdd() && $media->album->id_user === Auth::id())) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus media ini.');
        }

        $namaFile = $media->nama_file_asli;
        $media->delete();

        \App\Models\LogAktivitas::catat(Auth::id(), 'Menghapus media ' . $namaFile . ' dari album ' . $media->album->nama_acara, request()->ip());

        return redirect()->back()->with('success', 'Media berhasil dihapus.');
    }
    public function clearAlbum($albumId)
    {
        $album = \App\Models\Album::findOrFail($albumId);

        if (!Auth::user()->isSuperAdmin() && !(Auth::user()->isAdminPdd() && $album->id_user === Auth::id())) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus media di album ini.');
        }

        $mediaList = Media::where('id_album', $albumId)->get();
        foreach ($mediaList as $media) {
            $media->delete();
        }

        \App\Models\LogAktivitas::catat(Auth::id(), 'Menghapus semua media dari album ' . $album->nama_acara, request()->ip());

        return redirect()->back()->with('success', 'Semua media di album berhasil dihapus.');
    }
}
