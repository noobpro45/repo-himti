@extends('layouts.app')

@section('title', 'Log Aktivitas')



@section('content')
<div class="animate-fade-in max-w-7xl mx-auto">
<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Log Aktivitas Sistem</h2>
            <p class="text-[13.5px]" style="color:var(--paper-dim);">Jejak rekam lengkap seluruh pergerakan pengguna di dalam aplikasi.</p>
        </div>
    </div>
        
    <form action="{{ route('admin.logs.index') }}" method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        {{-- Search Bar --}}
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg w-full md:w-[280px] transition-colors" style="background:var(--bg-input); border:1px solid var(--ink-line-2);">
            <svg class="w-4 h-4" style="color:var(--paper-dim)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7" />
                <path d="M21 21l-4.3-4.3" />
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau aksi..." class="bg-transparent border-none outline-none text-[13px] w-full" style="color:var(--paper);">
        </div>
        
        {{-- Date Range --}}
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors" style="background:var(--bg-input); border:1px solid var(--ink-line-2);">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-transparent border-none outline-none text-[13px] w-[110px]" style="color:var(--paper);">
            <span class="text-[13px]" style="color:var(--paper-dim)">-</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-transparent border-none outline-none text-[13px] w-[110px]" style="color:var(--paper);">
        </div>
        
        <button type="submit" class="px-5 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 shadow-lg text-white hover:opacity-90 hover:scale-[1.02] active:scale-[0.98]" style="background:var(--color-accent); box-shadow:0 4px 12px rgba(46,178,83,0.3);">
            Filter
        </button>
        
        @if(request()->anyFilled(['q', 'start_date', 'end_date']))
            <a href="{{ route('admin.logs.index') }}" class="px-4 py-2 rounded-lg text-[13px] font-medium transition-all duration-150 hover:bg-[var(--ink-line)] hover:text-[var(--paper)] hover:scale-[1.02] active:scale-[0.98]" style="background:var(--bg-input); color:var(--paper-dim);">
                Reset
            </a>
        @endif
    </form>

    <div class="rounded-2xl border overflow-hidden shadow-sm" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px] whitespace-nowrap">
                <thead style="background:var(--bg-panel);">
                    <tr>
                        <th class="py-3 px-4 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">WAKTU</th>
                        <th class="py-3 px-4 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">PENGGUNA</th>
                        <th class="py-3 px-4 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">AKSI / DESKRIPSI</th>
                        <th class="py-3 px-4 font-semibold text-xs border-b text-right" style="color:var(--text-muted); border-color:var(--ink-line);">ALAMAT IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="transition-colors hover:bg-[var(--ink-line)]">
                        <td class="py-3 px-4 border-b text-[11.5px] font-mono whitespace-nowrap" style="border-color:var(--ink-line-2); color:var(--paper-dim);">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}
                        </td>
                        <td class="py-3 px-4 border-b whitespace-nowrap" style="border-color:var(--ink-line-2); color:var(--paper);">
                            <div class="font-medium mb-0.5">{{ $log->user->nama_lengkap ?? 'Sistem/Anonim' }}</div>
                            <div class="text-[11px] font-mono" style="color:var(--paper-dim);">{{ $log->user->role ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 border-b" style="border-color:var(--ink-line-2); color:var(--paper-dim); white-space: normal; min-width: 300px; line-height:1.5;">
                            {{ $log->aktivitas }}
                        </td>
                        <td class="py-3 px-4 border-b text-right font-mono text-[11.5px] whitespace-nowrap" style="border-color:var(--ink-line-2); color:var(--paper-dim);">
                            {{ $log->alamat_ip }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-[13px]" style="color:var(--text-muted);">
                            Belum ada rekam jejak aktivitas yang sesuai kriteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-4 py-3 border-t" style="border-color:var(--ink-line-2); background:var(--bg-body);">
                {{ $logs->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>
</div>
@endsection
