<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller
{
    public function index()
    {
        // Actually handled in DashboardController for Admin PDD
        return redirect()->route('dashboard');
    }

    public function create()
    {
        return view('album.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_acara' => 'required|string|max:255',
            'tipe_tanggal' => 'required|in:single,range',
            'tanggal_acara' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_acara',
            'deskripsi' => 'nullable|string',
        ]);

        $tanggalAcara = \Carbon\Carbon::parse($validated['tanggal_acara']);
        if ($validated['tipe_tanggal'] === 'range' && !empty($validated['tanggal_selesai'])) {
            // For now, save only the start date as per schema, or handle it via a new column
            // Our schema only has `tanggal_acara`, so we just save the start date.
        }

        $album = Album::create([
            'id_user' => Auth::id(),
            'nama_acara' => $validated['nama_acara'],
            'tanggal_acara' => $tanggalAcara,
            'deskripsi' => $validated['deskripsi'],
        ]);

        LogAktivitas::catat(Auth::id(), 'Membuat album ' . $album->nama_acara, $request->ip());

        return redirect()->route('pdd.album.edit', ['album' => $album->slug, 'tab' => 'upload'])
                         ->with('success', 'Album berhasil dibuat. Silakan unggah media.');
    }

    public function edit(Album $album)
    {
        if (Auth::user()->isAdminPdd() && $album->id_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke album ini.');
        }

        // Load semua media yang sudah diunggah untuk ditampilkan dan dihapus (opsional) di halaman edit
        $media = $album->media()->latest()->get();

        return view('album.edit', compact('album', 'media'));
    }

    public function update(Request $request, Album $album)
    {
        if (Auth::user()->isAdminPdd() && $album->id_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke album ini.');
        }

        $validated = $request->validate([
            'nama_acara' => 'required|string|max:255',
            'tanggal_acara' => 'required|date',
            'deskripsi' => 'nullable|string',
        ]);

        $album->update([
            'nama_acara' => $validated['nama_acara'],
            'tanggal_acara' => \Carbon\Carbon::parse($validated['tanggal_acara']),
            'deskripsi' => $validated['deskripsi'],
        ]);

        LogAktivitas::catat(Auth::id(), 'Memperbarui album ' . $album->nama_acara, $request->ip());

        return redirect()->back()->with('success', 'Album berhasil diperbarui.');
    }

    public function destroy(Request $request, Album $album)
    {
        if (Auth::user()->isAdminPdd() && $album->id_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke album ini.');
        }

        $nama = $album->nama_acara;
        $album->delete();

        LogAktivitas::catat(Auth::id(), 'Menghapus album ' . $nama, $request->ip());

        return redirect()->route('dashboard')->with('success', 'Album berhasil dihapus.');
    }

    public function setCover(Request $request, Album $album, \App\Models\Media $media)
    {
        if (Auth::user()->isAdminPdd() && $album->id_user !== Auth::id()) {
            if ($request->wantsJson()) return response()->json(['error' => 'Anda tidak memiliki akses ke album ini.'], 403);
            abort(403, 'Anda tidak memiliki akses ke album ini.');
        }

        if ($media->id_album !== $album->id_album) {
            if ($request->wantsJson()) return response()->json(['error' => 'Media tidak berada dalam album ini.'], 400);
            abort(400, 'Media tidak berada dalam album ini.');
        }

        if (!$media->is_foto) {
            if ($request->wantsJson()) return response()->json(['error' => 'Hanya foto yang dapat dijadikan cover album.'], 400);
            return back()->with('error', 'Hanya foto yang dapat dijadikan cover album.');
        }

        $position = $request->input('cover_position', 'center center');

        $album->update([
            'id_media_cover' => $media->id_media,
            'cover_position' => $position,
        ]);

        LogAktivitas::catat(Auth::id(), 'Mengubah cover album ' . $album->nama_acara, $request->ip());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cover album berhasil diperbarui.'
            ]);
        }

        return back()->with('success', 'Cover album berhasil diperbarui.');
    }
}
