@extends('layouts.app')

@section('title', 'Semua Album')



@section('content')
<div class="animate-fade-in max-w-7xl mx-auto">
<div x-data="{ 
    showDeleteModal: false, 
    albumIdToDelete: null, 
    albumNameToDelete: '',
    confirmDelete(id, name) {
        this.albumIdToDelete = id;
        this.albumNameToDelete = name;
        this.showDeleteModal = true;
    }
}">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Semua Album</h2>
            <p class="text-[13.5px]" style="color:var(--paper-dim);">Pantau dan tindak lanjuti seluruh album yang ada di repositori.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form action="{{ route('admin.albums.index') }}" method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg w-full md:w-[280px] transition-colors" style="background:var(--bg-input); border:1px solid var(--ink-line-2);">
            <svg class="w-4 h-4" style="color:var(--paper-dim)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7" />
                <path d="M21 21l-4.3-4.3" />
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama acara..." class="bg-transparent border-none outline-none text-[13px] w-full" style="color:var(--paper);">
        </div>
        <button type="submit" class="hidden"></button>
    </form>

    @if(session('success'))
        <div class="p-3 mb-6 rounded-lg text-sm border font-medium flex items-center gap-2" style="background:var(--color-green-soft); color:var(--color-green); border-color:rgba(46, 178, 83, 0.2);">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Grid --}}
    @if($albums->isEmpty())
        <div class="empty-state text-center pt-8 pb-12 mt-8 border-t border-dashed" style="border-color:var(--ink-line-2);">
            <div class="w-[72px] h-[72px] mx-auto mb-4 rounded-2xl flex items-center justify-center animate-[emptyPulse_2.5s_ease_infinite]" style="background:var(--color-accent-soft); border:1px solid rgba(46, 178, 83, 0.2);">
                <svg class="w-8 h-8" style="color:var(--color-accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M22 12l-3.6-8.4A2 2 0 0016.6 2H7.4a2 2 0 00-1.8 1.6L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6z" />
                    <path d="M2 12h6l2 3h4l2-3h6" />
                </svg>
            </div>
            <h4 class="text-base font-semibold mb-1.5" style="color:var(--paper);">Tidak Ada Album Ditemukan</h4>
            <p class="text-[12.5px] max-w-[340px] mx-auto leading-[1.55]" style="color:var(--paper-dim);">
                @if(request('q'))
                    Pencarian "<b>{{ request('q') }}</b>" tidak menemukan album.
                @else
                    Belum ada album dokumentasi yang tersedia.
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($albums as $album)
                <div class="album-card flex flex-col rounded-2xl overflow-hidden h-full group relative" style="background:var(--bg-panel); border:1px solid var(--ink-line-2);">
                    <div class="relative h-[160px] flex items-center justify-center p-5 transition-transform duration-500 group-hover:scale-[1.02]" 
                         style="{{ $album->id_media_cover ? 'background-image: url(' . route('media.stream', ['id' => $album->id_media_cover, 'thumb' => 1]) . '); background-size: cover; background-position: ' . ($album->cover_position ?? 'center center') . ';' : 'background: linear-gradient(145deg, var(--bg-input) 0%, rgba(0,0,0,0.2) 100%);' }}">
                        
                        @if(!$album->id_media_cover)
                            <img src="{{ asset('logo-himti.png') }}" class="w-16 h-16 opacity-[0.15] drop-shadow-md grayscale transition-all duration-500 group-hover:scale-[1.05] group-hover:opacity-[0.25]" alt="HIMTI">
                        @endif
                    </div>

                    <span class="absolute top-3 right-3 px-2 py-[3px] rounded-md font-mono text-[10px] font-bold text-white shadow-sm z-20" style="background:rgba(0,0,0,0.4); backdrop-filter:blur(4px);">
                        {{ $album->media->count() }} media
                    </span>
                    <div class="p-4 flex flex-col flex-1 relative z-10" style="background:var(--bg-panel);">
                        <h4 class="font-semibold text-[15px] mb-1.5 leading-tight" style="color:var(--paper);">{{ $album->nama_acara }}</h4>
                        <div class="text-[11.5px] mt-auto mb-4 font-mono" style="color:var(--paper-dim);">
                            {{ \Carbon\Carbon::parse($album->created_at)->format('d M Y') }}
                        </div>
                        <div class="flex items-center justify-between mt-auto pt-3 border-t" style="border-color:var(--ink-line-2);">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background:var(--color-green-soft); color:var(--color-accent);">
                                {{ $album->user->nama_lengkap ?? 'Tidak Diketahui' }}
                            </span>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('katalog.show', $album->slug) }}" target="_blank" class="p-1.5 rounded transition-colors hover:bg-[var(--ink-line)] text-gray-400 hover:text-white" title="Lihat di Katalog">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </a>
                                <button @click="confirmDelete('{{ $album->id_album }}', '{{ addslashes($album->nama_acara) }}')" class="p-1.5 rounded transition-colors hover:bg-[var(--ink-line)] text-gray-400 hover:text-red-400" title="Hapus Paksa">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($albums->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $albums->links('pagination::tailwind') }}
            </div>
        @endif
    @endif

    {{-- Modal Hapus Paksa & Alasan --}}
    <div x-show="showDeleteModal" style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showDeleteModal = false"
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
             
        {{-- Modal Content --}}
        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-md rounded-2xl p-6 shadow-2xl overflow-hidden"
             style="background:var(--bg-panel); border:1px solid var(--ink-line-2);">
            
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-serif font-semibold text-red-500">Hapus Paksa Album</h3>
                    <p class="text-[12.5px] mt-1" style="color:var(--paper-dim);">Anda akan menghapus album <strong x-text="albumNameToDelete" style="color:var(--paper);"></strong>.</p>
                </div>
                <button @click="showDeleteModal = false" class="p-1 rounded-md transition-colors hover:bg-[var(--ink-line)]" style="color:var(--paper-dim);">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <form :action="'{{ url('/admin/albums') }}/' + albumIdToDelete" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Alasan Penghapusan (Wajib) <span class="text-red-500">*</span></label>
                        <textarea name="alasan" required rows="3" placeholder="Contoh: Melanggar kode etik..." 
                                  class="px-3 py-2 rounded-lg border outline-none text-[13px] transition-colors focus:border-red-500" 
                                  style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);"></textarea>
                        <p class="text-[10.5px] mt-1.5" style="color:var(--text-subtle);">Pesan ini akan dikirimkan otomatis sebagai Notifikasi Lonceng kepada Admin PDD pembuat album.</p>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--bg-input); color:var(--paper-dim);">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-medium transition-colors shadow-lg text-white" style="background:var(--color-red); box-shadow:0 4px 12px rgba(220,38,38,0.3);">
                        Hapus Paksa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
