<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatUnduhan;
use Illuminate\Support\Facades\Auth;

class RiwayatUnduhanController extends Controller
{
    public function index()
    {
        $riwayat = RiwayatUnduhan::with(['media.album'])
            ->where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(24);

        return view('riwayat.index', compact('riwayat'));
    }
}
