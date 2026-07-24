@extends('layouts.app')

@section('title', 'Buat Album')



@section('mobile-nav')
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150 hover:text-[var(--color-accent)]" style="color:var(--paper-dim);">
        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M3 6.5A1.5 1.5 0 014.5 5h4.6l1.8 2H19.5A1.5 1.5 0 0121 8.5v9A1.5 1.5 0 0119.5 19h-15A1.5 1.5 0 013 17.5v-11z" />
        </svg>
        Album
    </a>
    <a href="{{ route('pdd.album.create') }}" class="flex flex-col items-center gap-[3px] text-[9px] font-mono p-0 transition-colors duration-150" style="color:var(--color-accent);">
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
<div class="animate-fade-in max-w-[800px] mx-auto" x-data="{ 
    dateType: '{{ old('tipe_tanggal', 'single') }}',
    submitForm() {
        if (this.$refs.form.reportValidity()) {
            this.$refs.form.submit();
        }
    }
}">
    <form x-ref="form" action="{{ route('pdd.album.store') }}" method="POST">
        @csrf
        
        {{-- Topbar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Buat Album Baru</h2>
                <p class="text-[13.5px]" style="color:var(--paper-dim);">Lengkapi metadata acara sebelum mengunggah dokumentasi</p>
            </div>
            <button type="button" @click="submitForm()" 
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-transform duration-200 hover:-translate-y-0.5 text-white shadow-lg" 
                    style="background:var(--color-navy);">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan & Lanjut Upload
            </button>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl border flex gap-3 animate-fade-in" style="background:var(--color-red-soft); border-color:rgba(185, 80, 63, 0.4); color:var(--text-error);">
                <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div class="text-[13px]">
                    <div class="font-semibold mb-1">Terdapat kesalahan pada input:</div>
                    <ul class="list-disc list-inside space-y-0.5 ml-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Form Panel --}}
        <div class="p-6 rounded-2xl border flex flex-col mb-6" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <h3 class="text-base font-semibold mb-1" style="color:var(--paper);">Metadata Album</h3>
            <p class="text-[11.5px] font-mono mb-6" style="color:var(--text-subtle);">tb_album — nama_acara, tanggal_acara, deskripsi</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col">
                    <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Nama Acara <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_acara" value="{{ old('nama_acara') }}" required
                           class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]" 
                           style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);"
                           placeholder="Contoh: Seminar Nasional TI 2026">
                </div>
                
                <div class="flex flex-col">
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-[11.5px] font-medium" style="color:var(--paper-dim);">Tanggal Acara <span class="text-red-500">*</span></label>
                        <select name="tipe_tanggal" x-model="dateType"
                                class="bg-transparent border outline-none text-[9.5px] font-mono rounded px-1.5 py-0.5 cursor-pointer" 
                                style="border-color:var(--ink-line-2); color:var(--paper-dim);">
                            <option value="single" style="background:var(--bg-panel); color:var(--paper);">Tanggal Spesifik</option>
                            <option value="range" style="background:var(--bg-panel); color:var(--paper);">Periode Waktu</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="date" name="tanggal_acara" value="{{ old('tanggal_acara') }}" required
                               class="flex-1 px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]" 
                               style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                        
                        <span x-show="dateType === 'range'" x-cloak class="text-[11px] font-mono shrink-0" style="color:var(--paper-dim);">s.d.</span>
                        
                        <input x-show="dateType === 'range'" x-cloak type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                               class="flex-1 px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]" 
                               style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    </div>
                </div>
                
                <div class="flex flex-col md:col-span-2">
                    <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="3"
                              class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors resize-y focus:border-[var(--color-accent)]" 
                              style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);"
                              placeholder="Tuliskan deskripsi singkat mengenai kegiatan atau acara ini...">{{ old('deskripsi') }}</textarea>
                </div>
            </div>
        </div>
        
        <div class="p-6 rounded-2xl border flex flex-col items-center justify-center text-center" style="background:var(--bg-panel); border-color:var(--ink-line-2); border-style:dashed;">
            <svg class="w-10 h-10 mb-3" style="color:var(--paper-dim)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="17 8 12 3 7 8" />
                <line x1="12" y1="3" x2="12" y2="15" />
            </svg>
            <div class="text-[13.5px] font-medium mb-1" style="color:var(--paper);">Tab unggah akan terbuka otomatis setelah metadata disimpan</div>
            <div class="text-[11px] font-mono max-w-[420px]" style="color:var(--paper-dim);">Setelah klik Simpan & Lanjut Upload, album akan dibuat dan Anda diarahkan ke tab Unggah Media di halaman edit untuk mengirim foto atau video secara chunked.</div>
        </div>
    </form>
</div>
@endsection
