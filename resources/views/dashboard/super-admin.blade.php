@extends('layouts.app')

@section('title', 'Ringkasan')


@section('mobile-nav')
    <a href="{{ route('admin.ringkasan') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150" style="color:var(--color-accent);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="13" r="8" />
            <path d="M12 13l4-4" />
            <path d="M8 3h8" />
        </svg>
        Admin
    </a>
    <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150 hover:text-[var(--color-accent)]" style="color:var(--paper-dim);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="9" cy="8" r="3.2" />
            <path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6" />
            <circle cx="17" cy="8.5" r="2.6" />
            <path d="M15.5 14.2c2.8.3 5 2.4 5 5.8" />
        </svg>
        Anggota
    </a>
    <a href="{{ route('katalog.index') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150 hover:text-[var(--color-accent)]" style="color:var(--paper-dim);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="3" width="7" height="7" rx="1.5" />
            <rect x="14" y="3" width="7" height="7" rx="1.5" />
            <rect x="3" y="14" width="7" height="7" rx="1.5" />
            <rect x="14" y="14" width="7" height="7" rx="1.5" />
        </svg>
        Katalog
    </a>
@endsection

@section('content')
<div class="animate-fade-in max-w-7xl mx-auto">
    {{-- Topbar --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Ringkasan Sistem</h2>
            <p class="text-[13.5px]" style="color:var(--paper-dim);">Metrik peladen dan aktivitas repositori secara langsung</p>
        </div>
    </div>

    {{-- Stat Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card p-5 rounded-2xl border" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <span class="block text-[10.5px] font-bold tracking-[0.06em] uppercase mb-2" style="color:var(--text-muted);">Total Akun Aktif</span>
            <div class="text-[26px] font-semibold tracking-tight leading-none mb-2 font-serif" style="color:var(--paper);">{{ number_format($totalAkun) }}</div>
            <div class="text-xs font-mono" style="color:var(--color-accent);">Akun sistem yang aktif</div>
        </div>
        <div class="stat-card p-5 rounded-2xl border" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <span class="block text-[10.5px] font-bold tracking-[0.06em] uppercase mb-2" style="color:var(--text-muted);">Total Album</span>
            <div class="text-[26px] font-semibold tracking-tight leading-none mb-2 font-serif" style="color:var(--paper);">{{ number_format($totalAlbum) }}</div>
            <div class="text-xs font-mono" style="color:var(--color-accent);">Keseluruhan acara/album</div>
        </div>
        <div class="stat-card p-5 rounded-2xl border" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <span class="block text-[10.5px] font-bold tracking-[0.06em] uppercase mb-2" style="color:var(--text-muted);">Total Media</span>
            <div class="text-[26px] font-semibold tracking-tight leading-none mb-2 font-serif" style="color:var(--paper);">{{ number_format($totalMedia) }}</div>
            <div class="text-xs font-mono" style="color:var(--paper-dim);">Foto dan video dalam storage</div>
        </div>
        <div class="stat-card p-5 rounded-2xl border" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <span class="block text-[10.5px] font-bold tracking-[0.06em] uppercase mb-2" style="color:var(--text-muted);">Antrean Worker</span>
            <div class="text-[26px] font-semibold tracking-tight leading-none mb-2 font-serif" style="color:var(--paper);">
                {{ number_format($pendingJobs) }}<span class="text-base ml-1" style="color:var(--text-subtle);">job</span>
            </div>
            @if($pendingJobs > 0)
                <div class="text-xs font-mono" style="color:var(--color-orange);">Sedang diproses...</div>
            @else
                <div class="text-xs font-mono" style="color:var(--color-accent);">Semua job selesai</div>
            @endif
        </div>
    </div>

    {{-- Panel Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-6">
        {{-- Activity Log Panel --}}
        <div class="p-6 rounded-2xl border flex flex-col" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <h3 class="text-base font-semibold mb-1" style="color:var(--paper);">Log Aktivitas Terbaru</h3>
            <p class="text-[11.5px] font-mono mb-5" style="color:var(--text-subtle);">tb_log_aktivitas — audit trail tindakan modifikatif/destruktif</p>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-[13px]">
                    <thead>
                        <tr>
                            <th class="py-2.5 px-3 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">Waktu</th>
                            <th class="py-2.5 px-3 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">Pengguna</th>
                            <th class="py-2.5 px-3 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">Aktivitas</th>
                            <th class="py-2.5 px-3 font-semibold text-xs border-b" style="color:var(--text-muted); border-color:var(--ink-line);">Alamat IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logTerbaru as $log)
                        <tr class="transition-colors hover:bg-[var(--ink-line)]">
                            <td class="py-3 px-3 border-b font-mono text-[11.5px] whitespace-nowrap" style="border-color:var(--ink-line-2); color:var(--paper-dim);">
                                {{ $log->created_at->format('H:i') }}
                            </td>
                            <td class="py-3 px-3 border-b font-medium whitespace-nowrap" style="border-color:var(--ink-line-2); color:var(--paper);">
                                {{ $log->user->nama_lengkap }}
                            </td>
                            <td class="py-3 px-3 border-b" style="border-color:var(--ink-line-2); color:var(--paper-dim);">
                                {{ $log->aktivitas }}
                            </td>
                            <td class="py-3 px-3 border-b font-mono text-[11.5px] whitespace-nowrap" style="border-color:var(--ink-line-2); color:var(--text-subtle);">
                                {{ $log->alamat_ip ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-[13px]" style="color:var(--text-muted);">Belum ada log aktivitas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Storage Panel --}}
        <div class="p-6 rounded-2xl border flex flex-col" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <h3 class="text-base font-semibold mb-1" style="color:var(--paper);">Kapasitas Penyimpanan</h3>
            <p class="text-[11.5px] font-mono mb-8" style="color:var(--text-subtle);" title="{{ storage_path('app/media') }}">Local Disk &middot; {{ str_replace('\\', '/', str_replace(base_path(), '', storage_path('app/media'))) }}</p>
            
            @php
                $diskError = ($storageTotal === null || $storageFree === null);
                
                $appUsedGb = number_format($storageUsed / (1024 * 1024 * 1024), 2);
                
                if (!$diskError) {
                    // Hitung persen kapasitas disk secara keseluruhan (Bukan cuma yang dipakai aplikasi, tapi sistem OS jg)
                    $diskUsed = $storageTotal - $storageFree;
                    $pct = $storageTotal > 0 ? ($diskUsed / $storageTotal) * 100 : 0;
                    $pctFormatted = number_format($pct, 1);
                    
                    // Kapasitas untuk ditampilkan dalam bentuk GB
                    $totalGb = number_format($storageTotal / (1024 * 1024 * 1024), 2);
                    $freeGb = number_format($storageFree / (1024 * 1024 * 1024), 2);
                }
            @endphp

            @if($diskError)
                <div class="mt-2 mb-8 p-4 rounded-xl text-[12px]" style="background:var(--bg-input); border:1px solid var(--ink-line-2); color:var(--color-orange);">
                    ⚠️ Sistem gagal membaca kapasitas hardisk secara fisik (Mungkin karena ekstensi PHP, Path, atau permission).
                    <br><br>
                    <span style="color:var(--paper-dim);">Aplikasi ini menggunakan: <b style="color:var(--paper);">{{ $appUsedGb }} GB</b> foto & video.</span>
                </div>
            @else
                <div class="flex items-center gap-6 mb-8">
                    <div class="gauge relative w-[110px] h-[110px] rounded-full shrink-0 flex items-center justify-center" style="--gauge-pct: {{ $pct / 100 }}turn;">
                        <div class="z-10 text-center leading-none mt-1">
                            <b class="text-[22px] font-serif block mb-0.5" style="color:var(--paper);">{{ $pctFormatted }}%</b>
                            <span class="text-[8.5px] font-bold tracking-[0.06em] uppercase" style="color:var(--text-muted);">Terpakai</span>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col gap-3 text-xs" style="color:var(--paper-dim);">
                        <div class="flex items-center gap-2" title="Terpakai secara khusus oleh Repositori Aplikasi ini"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:var(--color-accent);"></span>{{ $appUsedGb }} GB foto & video</div>
                        <div class="flex items-center gap-2" title="Sisa ruang hardisk asli di Server"><span class="w-2.5 h-2.5 rounded-full shrink-0 bg-white/15"></span>{{ $freeGb }} GB tersisa (Real-time)</div>
                        <div class="flex items-center gap-2" title="Kapasitas total partisi penyimpanan server"><span class="w-2.5 h-2.5 rounded-full border shrink-0" style="border-color:var(--ink-line-2);"></span>dari {{ $totalGb }} GB total</div>
                    </div>
                </div>
            @endif

            <div class="mt-auto pt-4 flex justify-between items-center text-[11px]" style="border-top:1px solid var(--ink-line); color:var(--paper-dim);">
                <span>Status Queue Worker</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background:var(--color-green-soft); color:var(--color-accent);">● Berjalan Normal</span>
            </div>
        </div>
    </div>
</div>
@endsection
