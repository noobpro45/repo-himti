<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Album::withCount('media')->withSum('media', 'ukuran_byte')->latest('tanggal_acara');

        // Filter search
        if ($request->filled('q')) {
            $query->where('nama_acara', 'like', '%' . $request->q . '%');
        }

        // Filter tahun
        if ($request->filled('tahun') && $request->tahun !== 'all') {
            $query->whereYear('tanggal_acara', $request->tahun);
        }

        $albums = $query->paginate(12)->withQueryString();

        // Ambil list tahun unik untuk filter
        $availableYears = Album::selectRaw('YEAR(tanggal_acara) as tahun')
                                ->distinct()
                                ->orderByDesc('tahun')
                                ->pluck('tahun');

        return view('katalog.index', compact('albums', 'availableYears'));
    }

    public function show(Album $album)
    {
        $album->load('user');
        
        $query = $album->media();
        if (request()->filled('tipe') && request('tipe') !== 'all') {
            $query->where('tipe', request('tipe'));
        }
        
        $media = $query->orderBy('nama_file_asli')->get();

        return view('katalog.show', compact('album', 'media'));
    }
}
