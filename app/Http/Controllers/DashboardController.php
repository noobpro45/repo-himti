<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\LogAktivitas;
use App\Models\Media;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        if ($user->isAdminPdd()) {
            return $this->adminPddDashboard();
        }

        // Mahasiswa -> redirect to catalog
        return redirect()->route('katalog.index');
    }

    private function superAdminDashboard()
    {
        $totalAkun   = User::where('is_aktif', true)->count();
        
        $totalAlbum  = \Illuminate\Support\Facades\Cache::remember('dashboard_total_album', 300, function () {
            return Album::count();
        });
        
        $totalMedia  = \Illuminate\Support\Facades\Cache::remember('dashboard_total_media', 300, function () {
            return Media::count();
        });
        
        $storageUsed = \Illuminate\Support\Facades\Cache::remember('dashboard_storage_used', 300, function () {
            return Media::sum('ukuran_byte') ?? 0;
        });

        $logTerbaru  = LogAktivitas::with('user')
                        ->latest()
                        ->take(10)
                        ->get();

        // Storage stats (Real physical disk size)
        $storagePath = storage_path();
        
        // Coba baca dari folder langsung (bekerja baik di Linux)
        $storageTotal = @disk_total_space($storagePath);
        $storageFree  = @disk_free_space($storagePath);

        // Fallback untuk Windows (PHP di Windows butuh drive letter 'C:' atau 'D:' jika gagal baca full path)
        if (empty($storageTotal)) {
            $driveLetter = substr($storagePath, 0, 2); // Ambil contoh 'd:'
            $storageTotal = @disk_total_space($driveLetter) ?: null;
            $storageFree  = @disk_free_space($driveLetter) ?: null;
        }

        $pendingJobs = \Illuminate\Support\Facades\DB::table('jobs')->count();

        return view('dashboard.super-admin', compact(
            'totalAkun',
            'totalAlbum',
            'totalMedia',
            'logTerbaru',
            'storageUsed',
            'storageTotal',
            'storageFree',
            'pendingJobs'
        ));
    }

    private function adminPddDashboard()
    {
        $user = Auth::user();
        $albums = Album::where('id_user', $user->id_user)
                    ->withCount('media')
                    ->withSum('media', 'ukuran_byte')
                    ->latest()
                    ->get();

        return view('dashboard.admin-pdd', compact('albums'));
    }
}
