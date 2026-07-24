@extends('layouts.app')

@section('title', $album->nama_acara)

@section('mobile-nav')
    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminPdd())
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150 hover:text-[var(--color-accent)]" style="color:var(--paper-dim);">
            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="13" r="8" />
                <path d="M12 13l4-4" />
                <path d="M8 3h8" />
            </svg>
            Dasbor
        </a>
    @endif
    <a href="{{ route('katalog.index') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150 hover:text-[var(--color-accent)]" style="color:var(--paper-dim);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="3" width="7" height="7" rx="1.5" />
            <rect x="14" y="3" width="7" height="7" rx="1.5" />
            <rect x="3" y="14" width="7" height="7" rx="1.5" />
            <rect x="14" y="14" width="7" height="7" rx="1.5" />
        </svg>
        Katalog
    </a>
    <a href="#" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150" style="color:var(--color-accent);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <circle cx="8.5" cy="9.5" r="1.5" />
            <path d="M21 15l-5-5-11 9" />
        </svg>
        Detail
    </a>
@endsection

@section('content')
<div class="animate-fade-in max-w-7xl mx-auto" x-data="{
    selectedType: '{{ request('tipe', 'all') }}',
    filterType(type) {
        this.selectedType = type;
        window.location.href = '?tipe=' + type;
    },
    viewerOpen: false,
    viewerInfoOpen: false,
    viewerType: '',
    viewerSrc: '',
    viewerTitle: '',
    viewerMetaDate: '',
    viewerMetaName: '',
    viewerMetaSize: '',
    viewerMetaUploader: '',
    viewerDownloadUrl: '',
    viewerMediaId: '',
    viewerThumbSrc: '',
    viewerLoaded: false,
    activeMediaId: null,
    scale: 1,
    panX: 0,
    panY: 0,
    isDragging: false,
    startX: 0,
    startY: 0,
    currentIndex: -1,
    plyrInstance: null,
    mediaItems: [
        @foreach($media as $item)
        {
            type: '{{ $item->is_video ? 'video' : 'image' }}',
            src: '{{ route('media.stream', $item->id_media) }}',
            thumbSrc: '{{ route('media.stream', ['id' => $item->id_media, 'thumb' => 1]) }}',
            title: '{{ addslashes($item->nama_file_asli) }}',
            date: '{{ $item->created_at->format('d M Y, H:i') }}',
            name: '{{ addslashes($item->nama_file_asli) }}',
            size: '{{ $item->ukuran_formatted }}',
            uploader: '{{ addslashes($album->user->nama_lengkap ?? 'Divisi PDD') }}',
            downloadUrl: '{{ route('media.download', $item->id_media) }}',
            id: '{{ $item->id_media }}'
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    openViewer(index) {
        if (index < 0 || index >= this.mediaItems.length) return;
        this.currentIndex = index;
        const item = this.mediaItems[index];
        this.activeMediaId = item.id;
        
        // Reset zoom state
        this.scale = 1;
        this.panX = 0;
        this.panY = 0;
        this.isDragging = false;
        
        this.viewerType = item.type;
        this.viewerSrc = item.src;
        this.viewerThumbSrc = item.thumbSrc;
        this.viewerLoaded = false;
        this.viewerTitle = item.title;
        this.viewerMetaDate = item.date;
        this.viewerMetaName = item.name;
        this.viewerMetaSize = item.size;
        this.viewerMetaUploader = item.uploader;
        this.viewerDownloadUrl = item.downloadUrl;
        this.viewerMediaId = item.id;
        this.viewerOpen = true;
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    },
    nextItem() {
        if (this.currentIndex < this.mediaItems.length - 1) {
            this.openViewer(this.currentIndex + 1);
        }
    },
    prevItem() {
        if (this.currentIndex > 0) {
            this.openViewer(this.currentIndex - 1);
        }
    },
    closeViewer() {
        this.viewerOpen = false;
        
        // Immediately pause and destroy video to prevent audio leaks
        if (this.plyrInstance) {
            this.plyrInstance.pause();
            this.destroyPlyr();
        }
        
        setTimeout(() => { 
            this.viewerSrc = ''; 
            this.viewerThumbSrc = ''; 
            this.activeMediaId = null; 
        }, 300);
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
    },
    initPlyr(el) {
        if (this.plyrInstance) {
            this.destroyPlyr();
        }
        if (window.Plyr) {
            this.plyrInstance = new Plyr(el, {
                controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'],
                settings: ['quality', 'speed'],
                speed: { selected: 1, options: [0.5, 0.75, 1, 1.25, 1.5, 2] },
                autoplay: true,
                keyboard: { focused: true, global: true }
            });
        }
    },
    destroyPlyr() {
        if (this.plyrInstance) {
            this.plyrInstance.destroy();
            this.plyrInstance = null;
        }
    },
    init() {
        this.$watch('viewerSrc', (newSrc) => {
            if (this.viewerType === 'video' && this.plyrInstance && newSrc) {
                // If navigating to another video, update Plyr source directly
                this.plyrInstance.source = {
                    type: 'video',
                    sources: [{ src: newSrc }]
                };
                this.plyrInstance.play();
            }
        });
    },
    zoom(e) {
        if (this.viewerType !== 'image') return;
        e.preventDefault();
        
        const zoomStep = 0.25;
        const delta = e.deltaY < 0 ? zoomStep : -zoomStep;
        const oldScale = this.scale;
        let newScale = oldScale * (1 + delta);
        
        if (newScale > 8) newScale = 8;
        
        const rect = e.currentTarget.getBoundingClientRect();
        const mouseX = e.clientX - (rect.left + rect.width / 2);
        const mouseY = e.clientY - (rect.top + rect.height / 2);
        
        let newPanX = mouseX - (mouseX - this.panX) * (newScale / oldScale);
        let newPanY = mouseY - (mouseY - this.panY) * (newScale / oldScale);
        
        if (newScale <= 1) {
            newScale = 1;
            newPanX = 0;
            newPanY = 0;
        }
        
        const bounds = this.getPanBounds(newScale);
        this.panX = Math.min(Math.max(newPanX, -bounds.x), bounds.x);
        this.panY = Math.min(Math.max(newPanY, -bounds.y), bounds.y);
        this.scale = newScale;
    },
    getPanBounds(scaleToUse) {
        if (scaleToUse <= 1) return { x: 0, y: 0 };
        const img = this.$refs.highResImg;
        if (!img) return { x: 0, y: 0 };
        
        const ratio = img.naturalWidth / img.naturalHeight;
        if (!ratio) return { x: 0, y: 0 };
        
        const containerRatio = img.clientWidth / img.clientHeight;
        let renderedWidth, renderedHeight;
        
        if (ratio > containerRatio) {
            renderedWidth = img.clientWidth;
            renderedHeight = img.clientWidth / ratio;
        } else {
            renderedHeight = img.clientHeight;
            renderedWidth = img.clientHeight * ratio;
        }
        
        const maxPanX = Math.max(0, (renderedWidth * scaleToUse - img.clientWidth) / 2);
        const maxPanY = Math.max(0, (renderedHeight * scaleToUse - img.clientHeight) / 2);
        
        return { x: maxPanX, y: maxPanY };
    },
    startPan(e) {
        if (this.scale > 1 && e.button === 0) { // Only left click
            e.preventDefault();
            this.isDragging = true;
            this.startX = e.clientX - this.panX;
            this.startY = e.clientY - this.panY;
        }
    },
    pan(e) {
        if (!this.isDragging) return;
        e.preventDefault();
        let newPanX = e.clientX - this.startX;
        let newPanY = e.clientY - this.startY;
        
        const bounds = this.getPanBounds(this.scale);
        this.panX = Math.min(Math.max(newPanX, -bounds.x), bounds.x);
        this.panY = Math.min(Math.max(newPanY, -bounds.y), bounds.y);
    },
    stopPan() {
        this.isDragging = false;
    },
    toggleZoom(e) {
        if (this.viewerType !== 'image') return;
        if (this.scale > 1) {
            this.scale = 1;
            this.panX = 0;
            this.panY = 0;
        } else {
            const oldScale = this.scale;
            const newScale = 3;
            
            const rect = e.currentTarget.getBoundingClientRect();
            const mouseX = e.clientX - (rect.left + rect.width / 2);
            const mouseY = e.clientY - (rect.top + rect.height / 2);
            
            let newPanX = mouseX - (mouseX - this.panX) * (newScale / oldScale);
            let newPanY = mouseY - (mouseY - this.panY) * (newScale / oldScale);
            
            const bounds = this.getPanBounds(newScale);
            this.panX = Math.min(Math.max(newPanX, -bounds.x), bounds.x);
            this.panY = Math.min(Math.max(newPanY, -bounds.y), bounds.y);
            this.scale = newScale;
        }
    }
}">
    {{-- Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
        <div>
            <div class="text-[10.5px] font-mono mb-2" style="color:var(--paper-dim);">
                <a href="{{ route('katalog.index') }}" class="hover:underline hover:text-[var(--paper)]">Katalog Album</a> 
                <svg class="inline-block w-2.5 h-2.5 -mt-0.5 mx-1" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 1l5 3.5L2 8" />
                </svg> 
                <b style="color:var(--color-accent); font-weight:500;">{{ $album->nama_acara }}</b>
            </div>
            <h2 class="text-[21px] font-serif font-semibold mb-1.5" style="color:var(--paper);">{{ $album->nama_acara }}</h2>
            
            <div class="flex flex-wrap gap-3.5 text-[11.5px]" style="color:var(--paper-dim);">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M12 7v5l3.5 2" />
                    </svg>
                    {{ $album->tanggal_formatted }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 11.5V4.5A1.5 1.5 0 014.5 3h7l9 9-8.5 8.5-9-9z" />
                        <circle cx="8" cy="8" r="1.4" />
                    </svg>
                    {{ $album->total_media }} media · {{ $album->total_ukuran_formatted }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="8" r="3.2" />
                        <path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6" />
                        <circle cx="17" cy="8.5" r="2.6" />
                        <path d="M15.5 14.2c2.8.3 5 2.4 5 5.8" />
                    </svg>
                    Diunggah oleh {{ $album->user->nama_lengkap ?? 'Divisi PDD' }}
                </span>
            </div>
            
            @if($album->deskripsi)
                <p class="text-[12.5px] mt-3.5 max-w-[680px] leading-relaxed" style="color:var(--paper-dim);">
                    {{ $album->deskripsi }}
                </p>
            @endif
        </div>
        
        <div class="flex flex-wrap gap-2.5">
            <button @click="filterType('all')" class="px-3.5 py-1.5 rounded-full text-xs font-medium transition-all"
                    :class="selectedType === 'all' ? 'bg-[var(--accent-soft)] text-[var(--color-accent)] border border-[rgba(46,178,83,0.5)]' : 'bg-[var(--bg-input)] border border-[var(--ink-line-2)] text-[var(--paper-dim)] hover:border-[var(--paper-dim)] hover:text-[var(--paper)]'">
                Semua
            </button>
            <button @click="filterType('foto')" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium transition-all"
                    :class="selectedType === 'foto' ? 'bg-[var(--accent-soft)] text-[var(--color-accent)] border border-[rgba(46,178,83,0.5)]' : 'bg-[var(--bg-input)] border border-[var(--ink-line-2)] text-[var(--paper-dim)] hover:border-[var(--paper-dim)] hover:text-[var(--paper)]'">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="4" width="18" height="16" rx="2" />
                    <circle cx="8.5" cy="9.5" r="1.5" />
                    <path d="M21 15l-5-5-11 9" />
                </svg> Foto
            </button>
            <button @click="filterType('video')" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-medium transition-all"
                    :class="selectedType === 'video' ? 'bg-[var(--accent-soft)] text-[var(--color-accent)] border border-[rgba(46,178,83,0.5)]' : 'bg-[var(--bg-input)] border border-[var(--ink-line-2)] text-[var(--paper-dim)] hover:border-[var(--paper-dim)] hover:text-[var(--paper)]'">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="2.5" y="6" width="13" height="12" rx="2" />
                    <path d="M15.5 10.5l6-3.5v10l-6-3.5" />
                </svg> Video
            </button>
        </div>
    </div>

    {{-- Masonry Gallery --}}
    @if($media->isEmpty())
        <div class="empty-state text-center pt-8 pb-12 mt-8 border-t border-dashed" style="border-color:var(--ink-line-2);">
            <div class="w-[72px] h-[72px] mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background:var(--bg-input); border:1px solid var(--ink-line-2);">
                <svg class="w-8 h-8" style="color:var(--paper-dim)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="4" width="18" height="16" rx="2" />
                    <circle cx="8.5" cy="9.5" r="1.5" />
                    <path d="M21 15l-5-5-11 9" />
                </svg>
            </div>
            <h4 class="text-base font-semibold mb-1.5" style="color:var(--paper);">Album Masih Kosong</h4>
            <p class="text-[12.5px] max-w-[340px] mx-auto leading-[1.55]" style="color:var(--paper-dim);">
                Belum ada media foto atau video yang selesai diproses dalam album ini.
            </p>
        </div>
    @else
        <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-2" style="column-gap: 5px;">
            @php
                $heights = ['h1t', 'h2t', 'h3t', 'h4t', 'h5t', 'h6t'];
            @endphp
            
            @foreach($media as $index => $item)
                @php
                    $h  = $heights[$index % count($heights)];
                    $thumbUrl = route('media.stream', ['id' => $item->id_media, 'thumb' => 1]);
                    $noThumb = $item->is_video && empty($item->path_thumbnail);
                @endphp
                <div class="tile cursor-pointer group relative overflow-hidden rounded-none" 
                     style="background: var(--bg-input);" 
                     @click="openViewer({{ $index }})">
                    
                    {{-- Thumbnail (hanya jika foto, atau video yang sudah memiliki thumbnail) --}}
                    @if(!$noThumb)
                        <img src="{{ $thumbUrl }}" alt="{{ $item->nama_file_asli }}" class="skeleton-bg w-full h-auto block relative z-0" loading="lazy" style="min-height: 100px;" onload="this.classList.remove('skeleton-bg')">
                    @else
                        {{-- Fallback: Placeholder khusus video tanpa load stream --}}
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
                        <button class="dl-icon" @click.stop="showToast('success', 'Mengunduh', 'Mengunduh {{ addslashes($item->nama_file_asli) }}'); window.location.href = '{{ route('media.download', $item->id_media) }}'" aria-label="Download">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 4v11M8 11l4 4 4-4" />
                                <path d="M4 19.5h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Media Viewer Modal (Baru) --}}
    <div class="media-viewer-wrap" :class="viewerOpen ? 'active' : ''" id="mediaViewer" style="display: none;" 
         x-show="viewerOpen" 
         x-transition.opacity.duration.300ms
         @keydown.escape.window="closeViewer()" 
         @keydown.right.window="viewerOpen && viewerType !== 'video' && nextItem()" 
         @keydown.left.window="viewerOpen && viewerType !== 'video' && prevItem()" 
         x-cloak>
        <div class="mv-main group">
            <div class="mv-topbar">
                <button class="mv-icon-btn" @click="closeViewer()" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="mv-top-actions">
                    @if(auth()->user()->isAdminPdd() || auth()->user()->isSuperAdmin())
                        <template x-if="viewerType === 'image'">
                            <button type="button" class="mv-icon-btn" aria-label="Jadikan Cover" title="Jadikan Cover Album" 
                                @click="window.dispatchEvent(new CustomEvent('open-cover-modal', { detail: { 
                                        url: '{{ route('pdd.album.set_cover', ['album' => $album->slug, 'media' => ':media_id']) }}'.replace(':media_id', viewerMediaId),
                                        imageSrc: '{{ route('media.stream', ':media_id') }}'.replace(':media_id', viewerMediaId)
                                    }}));">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                    <path d="M12 2v4m0 12v4m10-10h-4M6 12H2"></path>
                                </svg>
                            </button>
                        </template>
                    @endif
                    <button class="mv-icon-btn" aria-label="Download"
                        @click="showToast('success', 'Mengunduh', 'Mengunduh ' + viewerMetaName); window.location.href = viewerDownloadUrl">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                    </button>
                    <button class="mv-icon-btn" aria-label="Info" @click="viewerInfoOpen = !viewerInfoOpen" :class="viewerInfoOpen ? 'text-white' : 'text-white/60'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="16" x2="12" y2="12" />
                            <line x1="12" y1="8" x2="12.01" y2="8" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="media-viewer-frame relative group">
                
                {{-- Nav Prev --}}
                <button class="w-12 h-12 rounded-full bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/60"
                        style="position: absolute; left: 1.5rem; top: 50%; transform: translateY(-50%); z-index: 100;"
                        @click="prevItem()" x-show="currentIndex > 0" aria-label="Previous">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" /></svg>
                </button>

                {{-- Nav Next --}}
                <button class="w-12 h-12 rounded-full bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/60"
                        style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); z-index: 100;"
                        @click="nextItem()" x-show="currentIndex < mediaItems.length - 1" aria-label="Next">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" /></svg>
                </button>
                
                {{-- Image --}}
                <div x-show="viewerType === 'image' && (viewerSrc || viewerThumbSrc)"
                     class="w-full h-full relative overflow-hidden flex items-center justify-center"
                     :class="scale > 1 ? (isDragging ? 'cursor-grabbing' : 'cursor-grab') : 'cursor-default'"
                     @wheel.prevent="zoom"
                     @mousedown="startPan"
                     @mousemove.window="pan"
                     @mouseup.window="stopPan"
                     @dblclick="toggleZoom">
                     
                    <div class="relative w-full h-full flex items-center justify-center"
                         :style="`transform: translate(${panX}px, ${panY}px) scale(${scale}); transition: ${isDragging ? 'none' : 'transform 0.2s cubic-bezier(0.2, 0, 0, 1)'}; transform-origin: center center;`">
                         
                        {{-- Low-res placeholder --}}
                        <img x-show="viewerThumbSrc" :src="viewerThumbSrc" 
                             class="absolute inset-0 w-full h-full object-contain filter blur-xl transition-opacity duration-500 pointer-events-none" 
                             :class="viewerLoaded ? 'opacity-0' : 'opacity-60'"
                             draggable="false"
                             alt="Thumbnail Placeholder">
                             
                        {{-- High-res image --}}
                        <img x-show="viewerSrc" :src="viewerSrc" x-ref="highResImg"
                             class="absolute inset-0 w-full h-full object-contain transition-opacity duration-500 pointer-events-none"
                             :class="viewerLoaded ? 'opacity-100' : 'opacity-0'"
                             @load="viewerLoaded = true"
                             draggable="false"
                             alt="Media Viewer">
                    </div>
                </div>

                {{-- Video --}}
                <template x-if="viewerType === 'video' && viewerSrc">
                    <div class="w-full h-full relative flex items-center justify-center bg-black">
                        <video
                            x-ref="video"
                            :src="viewerSrc"
                            class="plyr-video bg-black"
                            crossorigin="anonymous"
                            playsinline
                            autoplay
                            x-init="$nextTick(() => initPlyr($refs.video))"
                        ></video>
                    </div>
                </template>
            </div>

        </div>

        <div class="mv-info-panel" :class="viewerInfoOpen ? 'active' : ''" id="mvInfo">
            <div class="mv-info-head">
                <h3>Info</h3>
                <button class="mv-icon-btn" @click="viewerInfoOpen = false">✕</button>
            </div>
            <div class="mv-info-body">
                <div class="info-group">
                    <div class="info-label">Detail</div>
                    <div class="info-val">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg> 
                        <span x-text="viewerMetaDate"></span>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-val">
                        <template x-if="viewerType === 'video'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2.5" y="6" width="13" height="12" rx="2" />
                                <path d="M15.5 10.5l6-3.5v10l-6-3.5" />
                            </svg>
                        </template>
                        <template x-if="viewerType !== 'video'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <polyline points="21 15 16 10 5 21" />
                            </svg>
                        </template>
                        <div>
                            <span x-text="viewerMetaName"></span><br>
                            <span style="color:var(--text-secondary);font-size:11px;" x-text="viewerMetaSize"></span>
                        </div>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-val">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <div>
                            <span x-text="viewerMetaUploader"></span><br>
                            <span style="color:var(--text-secondary);font-size:11px;">Disimpan ke server</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    @include('album.partials.cover_modal')

@push('styles')
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
<style>
    /* Styling to ensure Plyr fits correctly in the modal and matches the app UI */
    .plyr {
        width: 100%;
        height: 100%;
        
        /* General Layout */
        --plyr-border-radius: 0px;
        overflow: hidden;
    }
    
    .plyr--video {
        max-height: 100vh;
    }
    
    /* Make wrappers transparent so mv-dynamic-bg handles the color */
    .plyr__video-wrapper {
        background: transparent !important;
    }
    .plyr video {
        object-fit: contain;
    }
    
    /* Typography for time */
    .plyr__time {
        font-family: var(--font-mono);
        font-size: 11.5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
@endpush

@endsection
