@extends('layouts.app')

@section('title', 'Penyimpanan Server')

@section('content')
<div class="animate-fade-in max-w-[900px] mx-auto">
    <div class="flex flex-wrap items-start justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Penyimpanan Server</h2>
            <p class="text-[13.5px] max-w-[600px]" style="color:var(--paper-dim);">Kelola cache upload yang tertunda, bersihkan file media fisik yang sudah tidak digunakan, dan pantau kapasitas disk server Anda.</p>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-2xl border flex gap-3 animate-fade-in items-start" style="background:var(--color-green-soft); border-color:rgba(46, 178, 83, 0.4); color:var(--color-accent);">
            <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <div class="text-[13px] flex-1">{{ session('success') }}</div>
            <button @click="show = false" type="button" class="text-current opacity-70 hover:opacity-100"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
    @endif

    {{-- ═══ Baris 1: Kapasitas Disk Server (Full-width) ═══ --}}
    <div class="mb-6 p-6 rounded-2xl border" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(99,102,241,0.1); color:#6366F1;">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold" style="color:var(--paper);">Kapasitas Disk Server</h3>
                <p class="text-[11.5px]" style="color:var(--paper-dim);">Ruang penyimpanan fisik tempat file media disimpan.</p>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full h-3 rounded-full overflow-hidden mb-4" style="background:var(--ink-line);">
            <div class="h-full rounded-full transition-all duration-500" style="width: {{ min($diskPercent, 100) }}%; background: {{ $diskPercent > 90 ? 'var(--color-red)' : ($diskPercent > 70 ? '#F59E0B' : 'var(--color-accent)') }};"></div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-3 rounded-xl" style="background:var(--bg-input);">
                <div class="text-[10.5px] font-mono uppercase tracking-wider mb-1" style="color:var(--paper-dim);">Total</div>
                <div class="text-lg font-bold font-mono" style="color:var(--paper);">{{ \Illuminate\Support\Number::fileSize($diskTotal) }}</div>
            </div>
            <div class="text-center p-3 rounded-xl" style="background:var(--bg-input);">
                <div class="text-[10.5px] font-mono uppercase tracking-wider mb-1" style="color:var(--paper-dim);">Terpakai</div>
                <div class="text-lg font-bold font-mono" style="color: {{ $diskPercent > 90 ? 'var(--color-red)' : ($diskPercent > 70 ? '#F59E0B' : 'var(--color-accent)') }};">{{ \Illuminate\Support\Number::fileSize($diskUsed) }}</div>
            </div>
            <div class="text-center p-3 rounded-xl" style="background:var(--bg-input);">
                <div class="text-[10.5px] font-mono uppercase tracking-wider mb-1" style="color:var(--paper-dim);">Tersedia</div>
                <div class="text-lg font-bold font-mono" style="color:var(--paper);">{{ \Illuminate\Support\Number::fileSize($diskFree) }}</div>
            </div>
        </div>

        <div class="text-right mt-3">
            <span class="text-[11px] font-mono" style="color:var(--paper-dim);">{{ $diskPercent }}% terpakai</span>
        </div>
    </div>

    {{-- ═══ Baris 2: Cache Upload + File Tak Terpakai ═══ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        
        {{-- Card: Cache Chunks --}}
        <div class="p-6 rounded-2xl border flex flex-col h-full relative" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(255,165,0,0.1); color:#FFA500;">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="21 8 21 21 3 21 3 8"></polyline>
                    <rect x="1" y="3" width="22" height="5"></rect>
                    <line x1="10" y1="12" x2="14" y2="12"></line>
                </svg>
            </div>
            
            <h3 class="text-lg font-bold mb-2" style="color:var(--paper);">Cache Upload</h3>
            <p class="text-[13px] leading-relaxed flex-1 mb-6" style="color:var(--paper-dim);">
                Ketika anggota mengunggah file besar, file tersebut dipotong (chunk). Jika unggahan terputus atau gagal, sisa potongan file ini akan tertinggal sebagai cache dan memakan ruang penyimpanan.
            </p>
            
            <div class="p-4 rounded-2xl mb-6 flex justify-between items-center" style="background:var(--bg-input); border:1px solid var(--ink-line-2);">
                <div>
                    <div class="text-[11px] font-mono mb-1" style="color:var(--paper-dim);">Total File Chunk</div>
                    <div class="text-2xl font-bold font-mono" style="color:var(--paper);">{{ $chunkCount }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[11px] font-mono mb-1" style="color:var(--paper-dim);">Ukuran Terpakai</div>
                    <div class="text-2xl font-bold font-mono" style="color:var(--color-accent);">{{ \Illuminate\Support\Number::fileSize($chunkSize ?? 0) }}</div>
                </div>
            </div>
            
            <form action="{{ route('admin.storage.clear_chunks') }}" method="POST">
                @csrf
                <button type="button" {{ $chunkCount == 0 ? 'disabled' : '' }} onclick="showConfirm('Bersihkan Cache', 'Apakah Anda yakin ingin menghapus semua sisa file unggahan (chunk) yang tertunda? File yang saat ini sedang diunggah mungkin akan gagal.', () => this.closest('form').submit())" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed" 
                        style="background:var(--color-navy); color:white; box-shadow:0 4px 14px -4px rgba(36, 56, 160, 0.4);">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                    Bersihkan Cache Upload
                </button>
            </form>
        </div>

        {{-- Card: Orphaned Files --}}
        <div class="p-6 rounded-2xl border flex flex-col h-full relative overflow-hidden" style="background:var(--bg-panel); border-color:var(--color-red-soft);">
            <div class="absolute w-32 h-32 rounded-full pointer-events-none" style="background:var(--color-red); filter:blur(50px); opacity:0.15; top:-1.5rem; right:-1.5rem;"></div>
            
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5 relative z-10" style="background:rgba(239, 68, 68, 0.1); color:var(--color-red);">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                </svg>
            </div>
            
            <h3 class="text-lg font-bold mb-2 relative z-10" style="color:var(--paper);">File Tak Terpakai</h3>
            <p class="text-[13px] leading-relaxed flex-1 mb-6 relative z-10" style="color:var(--paper-dim);">
                File fisik (raw, thumbnail, <strong>dan video terkonversi</strong>) yang sudah <strong>tidak digunakan oleh album manapun</strong> disebut Orphaned Files.
            </p>
            
            <div class="p-4 rounded-2xl mb-6 flex justify-between items-center relative z-10" style="background:rgba(239, 68, 68, 0.05); border:1px solid rgba(239, 68, 68, 0.2);">
                <div>
                    <div class="text-[11px] font-mono mb-1" style="color:var(--color-red); opacity:0.8;">Total Orphaned File</div>
                    <div class="text-2xl font-bold font-mono" style="color:var(--color-red);">{{ $orphanCount }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[11px] font-mono mb-1" style="color:var(--color-red); opacity:0.8;">Ukuran Dapat Dibebaskan</div>
                    <div class="text-2xl font-bold font-mono" style="color:var(--color-red);">{{ \Illuminate\Support\Number::fileSize($orphanSize ?? 0) }}</div>
                </div>
            </div>
            
            <form action="{{ route('admin.storage.clear_orphans') }}" method="POST" class="relative z-10">
                @csrf
                <button type="button" {{ $orphanCount == 0 ? 'disabled' : '' }} onclick="showConfirm('Bersihkan File Fisik', 'Apakah Anda yakin ingin menghapus permanen {{ $orphanCount }} file fisik yang sudah tidak terpakai? Tindakan ini tidak akan merusak album manapun karena file ini sudah yatim piatu.', () => this.closest('form').submit())" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[var(--color-red)] hover:text-white" 
                        style="background:rgba(239, 68, 68, 0.1); color:var(--color-red); border:1px solid rgba(239, 68, 68, 0.3);">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    Bersihkan File Tak Terpakai
                </button>
            </form>
        </div>

    </div>

    {{-- ═══ Baris 3: Video Gagal Diproses (Full-width) ═══ --}}
    <div class="mb-6 p-6 rounded-2xl border relative overflow-hidden" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
        @if($failedCount > 0)
            <div class="absolute w-24 h-24 rounded-full pointer-events-none" style="background:#F59E0B; filter:blur(40px); opacity:0.12; top:-1rem; left:-1rem;"></div>
        @endif
        
        <div class="flex items-center justify-between mb-5 relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:{{ $failedCount > 0 ? 'rgba(245,158,11,0.1)' : 'rgba(46,178,83,0.1)' }}; color:{{ $failedCount > 0 ? '#F59E0B' : 'var(--color-accent)' }};">
                    @if($failedCount > 0)
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    @else
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-base font-bold" style="color:var(--paper);">Video Gagal Diproses</h3>
                    <p class="text-[11.5px]" style="color:var(--paper-dim);">
                        @if($failedCount > 0)
                            {{ $failedCount }} video gagal saat proses konversi oleh server.
                        @else
                            Semua video telah berhasil diproses. Tidak ada kegagalan.
                        @endif
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.storage.retry_failed') }}" method="POST" class="relative z-10">
                @csrf
                <button type="button" 
                        {{ $failedCount == 0 ? 'disabled' : '' }}
                        @if($failedCount > 0)
                            onclick="showConfirm('Coba Ulang Video Gagal', 'Apakah Anda yakin ingin mencoba memproses ulang {{ $failedCount }} video yang gagal? Video akan masuk antrian proses kembali.', () => this.closest('form').submit(), 'warning', 'Coba Ulang')"
                        @endif
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-[13px] font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed" 
                        style="{{ $failedCount > 0 
                            ? 'background:rgba(245,158,11,0.1); color:#F59E0B; border:1px solid rgba(245,158,11,0.3);' 
                            : 'background:var(--bg-input); color:var(--paper-dim); border:1px solid var(--ink-line-2);' }}">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                    Coba Ulang Semua
                </button>
            </form>
        </div>

        @if($failedCount > 0)
            <div class="rounded-xl overflow-hidden border relative z-10" style="border-color:var(--ink-line-2);">
                <table class="w-full text-[13px]">
                    <thead>
                        <tr style="background:var(--bg-input);">
                            <th class="text-left px-4 py-2.5 font-semibold" style="color:var(--paper-dim);">Nama File</th>
                            <th class="text-left px-4 py-2.5 font-semibold" style="color:var(--paper-dim);">Album</th>
                            <th class="text-right px-4 py-2.5 font-semibold" style="color:var(--paper-dim);">Ukuran</th>
                            <th class="text-right px-4 py-2.5 font-semibold" style="color:var(--paper-dim);">Tanggal Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($failedVideos as $video)
                        <tr class="border-t" style="border-color:var(--ink-line-2);">
                            <td class="px-4 py-3" style="color:var(--paper);">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0" style="color:#F59E0B;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                    </svg>
                                    <span class="truncate max-w-[200px]">{{ $video->nama_file_asli }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3" style="color:var(--paper-dim);">
                                {{ $video->album->nama_acara ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-[12px]" style="color:var(--paper-dim);">
                                {{ \Illuminate\Support\Number::fileSize($video->ukuran_byte ?? 0) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-[12px]" style="color:var(--paper-dim);">
                                {{ $video->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══ Footer: Timestamp ═══ --}}
    <div class="text-center pb-6">
        <span class="text-[11.5px] font-mono" style="color:var(--paper-dim); opacity:0.6;">
            Terakhir dimuat: {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i:s') }} (WIB)
        </span>
    </div>

</div>
@endsection
