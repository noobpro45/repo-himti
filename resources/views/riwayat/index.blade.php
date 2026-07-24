@extends('layouts.app')

@section('title', 'Riwayat Unduhan')

@section('mobile-nav')
    <a href="{{ route('katalog.index') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150 hover:text-[var(--color-accent)]" style="color:var(--paper-dim);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="3" width="7" height="7" rx="1.5" />
            <rect x="14" y="3" width="7" height="7" rx="1.5" />
            <rect x="3" y="14" width="7" height="7" rx="1.5" />
            <rect x="14" y="14" width="7" height="7" rx="1.5" />
        </svg>
        Katalog
    </a>
    <a href="{{ route('riwayat.index') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150" style="color:var(--color-accent);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        Riwayat
    </a>
@endsection

@section('content')
<div class="animate-fade-in max-w-7xl mx-auto">
    {{-- Topbar --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Riwayat Unduhan</h2>
            <p class="text-[13.5px]" style="color:var(--paper-dim);">Berkas dokumentasi yang pernah Anda unduh sebelumnya.</p>
        </div>
    </div>

    @if($riwayat->isEmpty())
        <div class="text-center py-12 px-5 mt-8 border-t border-dashed" style="border-color:var(--ink-line-2);">
            <div class="w-[72px] h-[72px] mx-auto mb-4 rounded-2xl flex items-center justify-center animate-[emptyPulse_2.5s_ease_infinite]" style="background:var(--bg-input); border:1px solid var(--ink-line-2);">
                <svg class="w-8 h-8" style="color:var(--paper-dim)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
            </div>
            <h4 class="text-base font-semibold mb-1.5" style="color:var(--paper);">Belum Ada Riwayat</h4>
            <p class="text-[12.5px] max-w-[340px] mx-auto leading-[1.55]" style="color:var(--paper-dim);">Anda belum mengunduh berkas dokumentasi apapun dari katalog.</p>
        </div>
    @else
        <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-2" style="column-gap: 5px;">
            @foreach($riwayat as $index => $item)
                @php
                    $media = $item->media;
                @endphp
                
                @if($media)
                    @php
                        $thumbUrl = route('media.stream', ['id' => $media->id_media, 'thumb' => 1]);
                        $noThumb = $media->is_video && empty($media->path_thumbnail);
                    @endphp
                    <div class="tile group relative overflow-hidden rounded-none mb-2 inline-block w-full animate-fade-in" style="background:var(--bg-input);">
                        @if(!$noThumb)
                            <img src="{{ $thumbUrl }}" alt="{{ $media->nama_file_asli }}" class="skeleton-bg w-full h-auto block relative z-0" loading="lazy" style="min-height: 100px;" onload="this.classList.remove('skeleton-bg')" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden absolute inset-0 items-center justify-center flex-col z-0 pointer-events-none">
                                <div class="text-[28px] font-black tracking-widest text-white/20 uppercase">{{ pathinfo($media->nama_file_asli, PATHINFO_EXTENSION) }}</div>
                                <div class="text-[10px] font-mono text-white/40 mt-1 uppercase">Format tidak didukung</div>
                            </div>
                        @else
                            <div class="w-full h-full min-h-[100px] flex items-center justify-center relative z-0" style="background:var(--bg-panel); color:var(--text-muted);">
                                <svg class="w-12 h-12 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14v-4z" />
                                    <rect x="3" y="6" width="12" height="12" rx="2" ry="2" />
                                </svg>
                            </div>
                        @endif
                        
                        <div class="badge-type">
                            @if($media->is_video)
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="2.5" y="6" width="13" height="12" rx="2" />
                                    <path d="M15.5 10.5l6-3.5v10l-6-3.5" />
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="4" width="18" height="16" rx="2" />
                                    <circle cx="8.5" cy="9.5" r="1.5" />
                                    <path d="M21 15l-5-5-11 9" />
                                </svg>
                            @endif
                        </div>
                        
                        @if($media->is_video && $media->durasi)
                            <div class="duration">{{ $media->durasi }}</div>
                        @endif
                        
                        <div class="overlay">
                            <div class="top-info">
                                <div class="fname" title="{{ $media->nama_file_asli }}">{{ $media->nama_file_asli }}</div>
                                <div class="fmeta">Diunduh: {{ $item->created_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('media.download', $media->id_media) }}" class="dl-icon" style="background:rgba(46, 178, 83, 0.2); color:#3cc964; border:1px solid rgba(46,178,83,0.3);" title="Unduh Ulang">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="tile rounded-lg mb-2 inline-block w-full flex items-center justify-center p-4 border border-dashed text-center" style="background:var(--bg-panel); border-color:var(--ink-line-2); min-height:100px;">
                        <div>
                            <svg class="w-6 h-6 mx-auto mb-1 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                            <div class="text-[11px] font-mono opacity-60">File Telah Dihapus</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        
        @if($riwayat->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $riwayat->links('pagination::tailwind') }}
            </div>
        @endif
    @endif
</div>
@endsection
