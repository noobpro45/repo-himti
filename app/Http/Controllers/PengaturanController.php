<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanController extends Controller
{
    public function index()
    {
        $settings = Pengaturan::all()->pluck('nilai', 'kunci')->toArray();
        return view('admin.pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_upload_size_mb' => 'required|numeric|min:10',
            'chunk_size_mb' => 'required|numeric|min:1|max:100',
            'allowed_mime_types' => 'required|string',
            'ffmpeg_preset' => 'required|in:veryfast,fast,medium,slow,veryslow',
            'nama_organisasi' => 'required|string|max:255',
            'storage_path' => 'required|string|max:255',
            'wa_admin' => 'nullable|string|max:20',
        ]);

        foreach ($validated as $key => $value) {
            Pengaturan::setel($key, $value);
        }

        LogAktivitas::catat(Auth::id(), 'Memperbarui pengaturan sistem', $request->ip());

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
