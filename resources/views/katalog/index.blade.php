@extends('layouts.app')

@section('title', 'Katalog')

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
    <a href="{{ route('katalog.index') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150" style="color:var(--color-accent);">
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
<div class="animate-fade-in max-w-7xl mx-auto" x-data="{
    searchQ: '{{ request('q') }}',
    selectedYear: '{{ request('tahun', 'all') }}',
    submitForm() {
        this.$nextTick(() => {
            this.$refs.filterForm.submit();
        });
    }
}">
    <form x-ref="filterForm" action="{{ route('katalog.index') }}" method="GET">
        {{-- Topbar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Katalog Dokumentasi HIMTI</h2>
                <p class="text-[13.5px]" style="color:var(--paper-dim);">Telusuri dan unduh arsip kegiatan yang pernah kamu ikuti</p>
            </div>
            
            <div class="flex items-center gap-2 px-3 py-2 rounded-lg w-full md:w-[280px] transition-colors" style="background:var(--bg-input); border:1px solid var(--ink-line-2);">
                <svg class="w-4 h-4" style="color:var(--paper-dim)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" />
                    <path d="M21 21l-4.3-4.3" />
                </svg>
                <input type="text" name="q" x-model="searchQ" @keydown.enter="submitForm()" placeholder="Cari nama acara…" class="bg-transparent border-none outline-none text-[13px] w-full" style="color:var(--paper);">
            </div>
        </div>

        {{-- Filter Row --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <input type="hidden" name="tahun" x-model="selectedYear">
            
            <button type="button" @click="selectedYear = 'all'; submitForm()" 
                    class="px-3.5 py-1.5 rounded-full text-xs font-medium transition-all"
                    :class="selectedYear === 'all' ? 'bg-[var(--paper)] text-[var(--bg-body)]' : 'border border-[var(--ink-line-2)] text-[var(--paper-dim)] hover:bg-[var(--ink-line)] hover:text-[var(--paper)]'">
                Semua Tahun
            </button>
            
            @foreach($availableYears as $tahun)
                <button type="button" @click="selectedYear = '{{ $tahun }}'; submitForm()" 
                        class="px-3.5 py-1.5 rounded-full text-xs font-medium transition-all"
                        :class="selectedYear === '{{ $tahun }}' ? 'bg-[var(--paper)] text-[var(--bg-body)]' : 'border border-[var(--ink-line-2)] text-[var(--paper-dim)] hover:bg-[var(--ink-line)] hover:text-[var(--paper)]'">
                    {{ $tahun }}
                </button>
            @endforeach
        </div>
    </form>

    {{-- Grid --}}
    @if($albums->isEmpty())
        <div class="empty-state text-center pt-8 pb-12 mt-8 border-t border-dashed" style="border-color:var(--ink-line-2);">
            <div class="w-[72px] h-[72px] mx-auto mb-4 rounded-2xl flex items-center justify-center animate-[emptyPulse_2.5s_ease_infinite]" style="background:var(--accent-soft); border:1px solid rgba(46, 178, 83, 0.2);">
                <svg class="w-8 h-8" style="color:var(--color-accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M22 12l-3.6-8.4A2 2 0 0016.6 2H7.4a2 2 0 00-1.8 1.6L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6z" />
                    <path d="M2 12h6l2 3h4l2-3h6" />
                </svg>
            </div>
            <h4 class="text-base font-semibold mb-1.5" style="color:var(--paper);">Tidak Ada Hasil Ditemukan</h4>
            <p class="text-[12.5px] max-w-[340px] mx-auto leading-[1.55]" style="color:var(--paper-dim);">
                @if(request('q'))
                    Pencarian "<b>{{ request('q') }}</b>" tidak menemukan album. Coba gunakan kata kunci lain atau hapus filter tahun.
                @else
                    Belum ada album dokumentasi yang tersedia.
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($albums as $album)
                <a href="{{ route('katalog.show', $album->slug) }}" class="album-card flex flex-col rounded-xl overflow-hidden cursor-pointer h-full group relative" style="background:var(--bg-panel); border:1px solid var(--ink-line-2);">
                    <div class="relative h-[160px] flex items-center justify-center p-5 transition-transform duration-500 group-hover:scale-[1.02]" 
                         style="{{ $album->id_media_cover ? 'background-image: url(' . route('media.stream', ['id' => $album->id_media_cover, 'thumb' => 1]) . '); background-size: cover; background-position: ' . ($album->cover_position ?? 'center center') . ';' : 'background: linear-gradient(145deg, var(--bg-input) 0%, rgba(0,0,0,0.2) 100%);' }}">
                         
                        @if(!$album->id_media_cover)
                            <img src="{{ asset('logo-himti.png') }}" class="w-16 h-16 opacity-[0.15] drop-shadow-md grayscale transition-all duration-500 group-hover:scale-[1.05] group-hover:opacity-[0.25]" alt="HIMTI">
                        @endif
                    </div>
                    
                    <span class="absolute top-3 right-3 px-2 py-[3px] rounded-md font-mono text-[10px] font-bold text-white shadow-sm z-20" style="background:rgba(0,0,0,0.4); backdrop-filter:blur(4px);">
                        {{ $album->media_count }} media
                    </span>

                    <div class="p-4 flex flex-col flex-1 relative z-10" style="background:var(--bg-panel);">
                        <h4 class="font-semibold text-[15px] mb-1.5 leading-tight group-hover:text-[var(--color-accent)] transition-colors" style="color:var(--paper);">{{ $album->nama_acara }}</h4>
                        <div class="text-[11.5px] mt-auto font-mono" style="color:var(--paper-dim);">
                            {{ $album->tanggal_formatted }} · {{ $album->total_ukuran_formatted }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $albums->links() }}
        </div>
    @endif
</div>
@endsection
