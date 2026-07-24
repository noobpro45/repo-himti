@php
    $thumbUrl = route('media.stream', ['id' => $item->id_media, 'thumb' => 1]);
    $noThumb = $item->is_video && empty($item->path_thumbnail);
@endphp
<div class="tile group relative overflow-hidden rounded-none mb-2 inline-block w-full animate-fade-in" style="background:var(--bg-input);">
    
    @if(!$noThumb)
        <img src="{{ $thumbUrl }}" alt="{{ $item->nama_file_asli }}" class="skeleton-bg w-full h-auto block relative z-0" loading="lazy" style="min-height: 100px;" onload="this.classList.remove('skeleton-bg')" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="hidden absolute inset-0 items-center justify-center flex-col z-0 pointer-events-none">
            <div class="text-[28px] font-black tracking-widest text-white/20 uppercase">{{ pathinfo($item->nama_file_asli, PATHINFO_EXTENSION) }}</div>
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
        @if($item->is_video)
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
    
    @if($item->is_video && $item->durasi)
        <div class="duration">{{ $item->durasi }}</div>
    @endif
    
    <div class="overlay">
        <div class="top-info">
            <div class="fname" title="{{ $item->nama_file_asli }}">{{ $item->nama_file_asli }}</div>
            <div class="fmeta">{{ $item->ukuran_formatted }}</div>
        </div>
        @if(!$item->is_video)
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-cover-modal', { detail: { 
                    url: '{{ route('pdd.album.set_cover', ['album' => $album->slug ?? $album->id_album, 'media' => $item->id_media]) }}', 
                    imageSrc: '{{ route('media.stream', $item->id_media) }}'
                }}));" class="dl-icon" style="left:12px; right:auto; background:rgba(59, 130, 246, 0.2); color:#60a5fa; border:1px solid rgba(59,130,246,0.3);" aria-label="Set Cover" title="Jadikan Cover Album">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                    <path d="M12 2v4m0 12v4m10-10h-4M6 12H2"></path>
                </svg>
            </button>
        @endif

        <form action="{{ route('media.destroy', $item->id_media) }}" method="POST" id="delete-media-{{ $item->id_media }}">
            @csrf
            @method('DELETE')
            <button type="button" onclick="showConfirm('Hapus Media', 'Apakah Anda yakin ingin menghapus file &quot;{{ $item->nama_file_asli }}&quot; secara permanen dari server?', () => document.getElementById('delete-media-{{ $item->id_media }}').submit());" class="dl-icon" style="background:rgba(239, 68, 68, 0.2); color:#ef4444; border:1px solid rgba(239,68,68,0.3);" aria-label="Delete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </button>
        </form>
    </div>
</div>
