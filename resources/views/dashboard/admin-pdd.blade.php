@extends('layouts.app')

@section('title', 'Album Saya')


@section('mobile-nav')
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150" style="color:var(--color-accent);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M3 6.5A1.5 1.5 0 014.5 5h4.6l1.8 2H19.5A1.5 1.5 0 0121 8.5v9A1.5 1.5 0 0119.5 19h-15A1.5 1.5 0 013 17.5v-11z" />
        </svg>
        Album
    </a>
    <a href="{{ route('pdd.album.create') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150 hover:text-[var(--color-accent)]" style="color:var(--paper-dim);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14" />
        </svg>
        Buat
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
            <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Album Saya</h2>
            <p class="text-[13.5px]" style="color:var(--paper-dim);">Kelola dokumentasi yang telah Anda unggah</p>
        </div>
        <a href="{{ route('pdd.album.create') }}" 
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-transform duration-200 hover:-translate-y-0.5 text-white shadow-lg" 
           style="background:var(--color-navy);">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Buat Album Baru
        </a>
    </div>

    @if($albums->isEmpty())
        <div class="text-center py-12 px-5 mt-8 border-t border-dashed" style="border-color:var(--ink-line-2);">
            <div class="w-[72px] h-[72px] mx-auto mb-4 rounded-2xl flex items-center justify-center animate-[emptyPulse_2.5s_ease_infinite]" style="background:var(--accent-soft); border:1px solid rgba(46, 178, 83, 0.2);">
                <svg class="w-8 h-8" style="color:var(--color-accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M22 12l-3.6-8.4A2 2 0 0016.6 2H7.4a2 2 0 00-1.8 1.6L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6z" />
                    <path d="M2 12h6l2 3h4l2-3h6" />
                </svg>
            </div>
            <h4 class="text-base font-semibold mb-1.5" style="color:var(--paper);">Belum Ada Album</h4>
            <p class="text-[12.5px] max-w-[340px] mx-auto leading-[1.55]" style="color:var(--paper-dim);">Anda belum mengunggah dokumentasi apapun. Mulai dengan membuat album baru.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($albums as $album)
                <div class="album-card flex flex-col rounded-2xl overflow-hidden cursor-pointer h-full" style="background:var(--bg-panel); border:1px solid var(--ink-line-2);">
                    <div class="relative h-[160px] flex items-center justify-center p-5 transition-transform duration-500 group-hover:scale-105" 
                         style="{{ $album->id_media_cover ? 'background-image: url(' . route('media.stream', ['id' => $album->id_media_cover, 'thumb' => 1]) . '); background-size: cover; background-position: ' . ($album->cover_position ?? 'center center') . ';' : 'background: linear-gradient(145deg, var(--bg-input) 0%, rgba(0,0,0,0.2) 100%);' }}">
                         
                        @if(!$album->id_media_cover)
                            <img src="{{ asset('logo-himti.png') }}" class="w-16 h-16 opacity-[0.15] drop-shadow-md grayscale transition-all duration-500 group-hover:scale-110 group-hover:opacity-[0.25]" alt="HIMTI">
                        @endif

                        <span class="absolute top-3 right-3 px-2 py-[3px] rounded-md font-mono text-[10px] font-bold text-white shadow-sm" style="background:rgba(0,0,0,0.4); backdrop-filter:blur(4px);">
                            {{ $album->media_count }} media
                        </span>
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <h4 class="font-semibold text-[15px] mb-1.5 leading-tight" style="color:var(--paper);">{{ $album->nama_acara }}</h4>
                        <div class="text-[11.5px] mt-auto font-mono" style="color:var(--paper-dim);">
                            {{ $album->tanggal_formatted }} · {{ $album->total_ukuran_formatted }}
                        </div>
                        
                        <div class="mt-4 pt-3 flex items-center justify-between" style="border-top:1px solid var(--ink-line-2);">
                            <a href="{{ route('katalog.show', $album->slug) }}" class="text-[11.5px] font-medium hover:underline" style="color:var(--color-accent);">Lihat Galeri →</a>
                            <div class="flex gap-2">
                                <a href="{{ route('pdd.album.edit', $album->slug) }}" class="p-1.5 rounded transition-colors hover:bg-[var(--ink-line)] text-gray-400 hover:text-white" title="Edit">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                {{-- Delete button --}}
                                <form action="{{ route('pdd.album.destroy', $album->slug) }}" method="POST" id="delete-album-{{ $album->id_album }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            onclick="showConfirm('Hapus Album', 'Apakah Anda yakin ingin menghapus album &quot;{{ $album->nama_acara }}&quot; beserta seluruh file di dalamnya secara permanen?', () => document.getElementById('delete-album-{{ $album->id_album }}').submit());"
                                            class="p-1.5 rounded transition-colors hover:bg-[var(--ink-line)] text-gray-400 hover:text-red-400" 
                                            title="Hapus">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
