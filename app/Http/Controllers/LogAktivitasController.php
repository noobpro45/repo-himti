<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')->latest('created_at');

        if ($request->filled('q')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->q . '%')
                  ->orWhere('username', 'like', '%' . $request->q . '%');
            })->orWhere('aktivitas', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->start_date));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->end_date));
        }

        $logs = $query->paginate(10)->withQueryString();

        return view('admin.log.index', compact('logs'));
    }
}
