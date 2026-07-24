<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\LogAktivitas;
use App\Notifications\AlbumDeletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminAlbumController extends Controller
{
    public function index(Request $request)
    {
        $query = Album::with(['user', 'media'])->latest('created_at');

        if ($request->filled('q')) {
            $query->where('nama_acara', 'like', '%' . $request->q . '%');
        }

        $albums = $query->paginate(20)->withQueryString();

        return view('admin.album.index', compact('albums'));
    }

    public function destroy(Request $request, $id)
    {
        $validated = $request->validate([
            'alasan' => 'required|string|max:1000'
        ]);

        $album = Album::with('user')->findOrFail($id);
        $namaAlbum = $album->nama_acara;
        $pemilik = $album->user;

        // Ambil semua media untuk menghapus file fisiknya
        $mediaList = $album->media;
        foreach ($mediaList as $media) {
            if ($media->path_file && Storage::disk('media_private')->exists($media->path_file)) {
                Storage::disk('media_private')->delete($media->path_file);
            }
            if ($media->cover_path && Storage::disk('media_private')->exists($media->cover_path)) {
                Storage::disk('media_private')->delete($media->cover_path);
            }
        }

        // Kirim notifikasi jika pembuatnya ada dan bukan dirinya sendiri
        if ($pemilik && $pemilik->id_user !== Auth::id()) {
            $pemilik->notify(new AlbumDeletedNotification(
                $namaAlbum,
                $validated['alasan'],
                Auth::user()->nama_lengkap
            ));
        }

        $album->delete();

        LogAktivitas::catat(Auth::id(), 'Menghapus paksa album: ' . $namaAlbum . '. Alasan: ' . $validated['alasan'], $request->ip());

        return redirect()->route('admin.albums.index')->with('success', 'Album berhasil dihapus paksa dan notifikasi telah dikirim ke PDD terkait.');
    }
}
