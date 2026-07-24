@extends('layouts.app')

@section('title', 'Edit Album')



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
@endsection

@section('content')
<div class="animate-fade-in max-w-[1180px] mx-auto" x-data="{ activeTab: '{{ request('tab') === 'upload' ? 'upload' : (request('tab') === 'media' ? 'media' : 'metadata') }}' }">
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Buat Album & Unggah Dokumentasi</h2>
            <p class="text-[13.5px] max-w-[760px]" style="color:var(--paper-dim);">Satu halaman untuk metadata, unggah media chunked, dan pengelolaan file album.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit" form="album-form" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-transform duration-200 hover:-translate-y-0.5 text-white shadow-lg" style="background:var(--color-navy);">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Simpan Metadata
            </button>
            <a href="{{ route('katalog.show', $album->slug) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors border" style="background:var(--bg-panel); border-color:var(--ink-line-2); color:var(--paper-dim);">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
                Katalog
            </a>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl border flex gap-3 animate-fade-in items-start" style="background:var(--color-green-soft); border-color:rgba(46, 178, 83, 0.4); color:var(--color-accent);">
            <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <div class="text-[13px] flex-1">{{ session('success') }}</div>
            <button @click="show = false" type="button" class="text-current opacity-70 hover:opacity-100"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl border flex gap-3 animate-fade-in" style="background:var(--color-red-soft); border-color:rgba(185, 80, 63, 0.4); color:var(--text-error);">
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

    <div class="mb-6 flex flex-wrap gap-2 rounded-2xl border p-2" style="background:var(--bg-panel); border-color:var(--ink-line-2); box-shadow:0 6px 18px rgba(0,0,0,0.04);">
        <button type="button" @click="activeTab = 'metadata'" :style="activeTab === 'metadata' ? 'background:var(--color-navy); color:#fff; box-shadow:0 10px 24px rgba(27,45,107,0.22);' : 'color:var(--paper-dim);'" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors">Metadata Album</button>
        <button type="button" @click="activeTab = 'upload'" :style="activeTab === 'upload' ? 'background:var(--color-navy); color:#fff; box-shadow:0 10px 24px rgba(27,45,107,0.22);' : 'color:var(--paper-dim);'" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors">Unggah Media</button>
        <button type="button" @click="activeTab = 'media'" :style="activeTab === 'media' ? 'background:var(--color-navy); color:#fff; box-shadow:0 10px 24px rgba(27,45,107,0.22);' : 'color:var(--paper-dim);'" class="px-4 py-2 rounded-xl text-sm font-medium transition-colors">Media dalam Album</button>
    </div>

    <section x-show="activeTab === 'metadata'" x-cloak x-transition.opacity class="mb-6">
        <form id="album-form" action="{{ route('pdd.album.update', $album->slug) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 rounded-2xl border flex flex-col gap-6" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <h3 class="text-base font-semibold mb-1" style="color:var(--paper);">Metadata Album</h3>
                        <p class="text-[11.5px] font-mono" style="color:var(--text-subtle);">tb_album — nama_acara, tanggal_acara, deskripsi</p>
                    </div>
                    <div class="text-[11px] font-mono px-3 py-1.5 rounded-full border" style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper-dim);">Album: {{ $album->slug }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="flex flex-col">
                        <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Nama Acara <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_acara" value="{{ old('nama_acara', $album->nama_acara) }}" required class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]" style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Tanggal Acara <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_acara" value="{{ old('tanggal_acara', $album->tanggal_acara->format('Y-m-d')) }}" required class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]" style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    </div>

                    <div class="flex flex-col md:col-span-2">
                        <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="4" class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors resize-y focus:border-[var(--color-accent)]" style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">{{ old('deskripsi', $album->deskripsi) }}</textarea>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <section x-show="activeTab === 'upload'" x-cloak x-transition.opacity class="mb-6">
        <div class="p-6 rounded-2xl border flex flex-col gap-5" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h3 class="text-base font-semibold mb-1" style="color:var(--paper);">Unggah Berkas Mentah</h3>
                    <p class="text-[11.5px] font-mono" style="color:var(--text-subtle);">Chunk 10 MB per bagian · mendukung gambar dan video</p>
                </div>
                <div class="text-[11px] font-mono px-3 py-1.5 rounded-full border" style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper-dim);">Album aktif: {{ $album->nama_acara }}</div>
            </div>

            <div id="album-upload-zone" class="relative rounded-2xl border-2 border-dashed overflow-hidden transition-colors" role="button" tabindex="0" style="border-color:var(--ink-line-2); background:var(--bg-input);">
                <input id="album-upload-input" type="file" multiple accept="image/*,video/*" class="hidden">
                <div class="flex min-h-[300px] flex-col items-center justify-center text-center p-8">
                    <svg class="w-11 h-11 mb-4" style="color:var(--paper-dim)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <div class="text-[15px] font-semibold mb-2" style="color:var(--paper);">Seret file ke sini atau klik untuk memilih</div>
                    <div class="text-[11px] font-mono max-w-[420px] leading-6" style="color:var(--paper-dim);">File dipotong otomatis menjadi chunk 10 MB, lalu digabung di server. Jika Anda menaruh video besar, tunggu sampai seluruh file selesai diproses sebelum menutup tab.</div>
                    <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                        <button type="button" id="album-upload-trigger" class="px-4 py-2 rounded-lg text-sm font-medium text-white shadow-lg" style="background:var(--color-navy);">Pilih File</button>
                        <span class="px-4 py-2 rounded-lg text-[11px] font-mono border" style="background:var(--bg-panel); border-color:var(--ink-line-2); color:var(--paper-dim);">image/*, video/*</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Upload Widget -->
    <div id="floating-uploader" class="fixed bottom-6 right-6 w-[28rem] flex flex-col rounded-xl shadow-2xl z-50 transition-all duration-500 overflow-hidden" style="background:var(--bg-panel); border:1px solid var(--ink-line-2); display:none; max-height: 85vh; transform: translateY(0); opacity: 1;">
        <div class="px-4 py-3 flex items-center justify-between border-b cursor-pointer select-none relative" style="background:var(--bg-input); border-color:var(--ink-line-2);" onclick="document.getElementById('upload-queue-container').classList.toggle('hidden'); document.getElementById('upload-action-bar').classList.toggle('hidden');">
            <!-- Global Progress Bar -->
            <div id="global-progress-bar" class="absolute bottom-0 left-0 h-[2px] transition-all duration-300 rounded-r-full" style="width: 0%; background: var(--color-navy); box-shadow: 0 0 8px var(--color-navy);"></div>
            
            <h4 class="text-[13px] font-bold shrink-0 mr-4" style="color:var(--paper);">Proses Unggah</h4>
            <div class="flex items-center gap-2 min-w-0">
                <span id="upload-speed-badge" class="text-[10px] font-mono px-2 py-0.5 rounded-full truncate hidden" style="background:var(--bg-panel); border:1px solid var(--ink-line-2); color:var(--paper-dim);">0 MB/s</span>
                <span id="upload-count-badge" class="text-[10px] font-mono px-2 py-0.5 rounded-full truncate" style="background:var(--ink-line); color:var(--paper-dim);">0 file</span>
                <svg class="w-4 h-4" style="color:var(--paper-dim);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
        </div>
        <!-- Action Bar -->
        <div id="upload-action-bar" class="px-4 py-2 flex items-center justify-between border-b text-[11px] font-medium" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <div id="upload-eta" class="text-gray-500 font-mono text-[10px] truncate max-w-[150px]">Menghitung...</div>
            <div class="flex gap-2">
                <button type="button" id="btn-cancel-all" class="px-2.5 py-1 rounded-md text-[10px] font-bold transition-all hidden" style="background:rgba(239, 68, 68, 0.1); color:#ef4444; border:1px solid rgba(239, 68, 68, 0.2);" onmouseover="this.style.background='rgba(239, 68, 68, 0.2)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'">Batalkan Semua</button>
                <button type="button" id="btn-clear-completed" class="px-2.5 py-1 rounded-md text-[10px] font-bold transition-all" style="background:var(--ink-line); color:var(--paper-dim); border:1px solid var(--ink-line-2);" onmouseover="this.style.color='var(--paper)'; this.style.background='var(--ink-line-2)'" onmouseout="this.style.color='var(--paper-dim)'; this.style.background='var(--ink-line)'">Bersihkan Selesai</button>
            </div>
        </div>
        <div id="upload-queue-container" class="overflow-y-auto p-4 flex-1 custom-scrollbar relative" style="max-height: 280px;">

            <div class="space-y-3" id="upload-queue"></div>
        </div>
    </div>

    <section x-show="activeTab === 'media'" x-cloak x-transition.opacity>
        <div class="p-6 rounded-2xl border flex flex-col" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <div class="flex items-start justify-between mb-6 gap-4 flex-wrap">
                <div>
                    <h3 class="text-[17px] font-bold" style="color:var(--paper);"><span id="album-media-count">{{ $media->count() }}</span> Item di Dalam Album</h3>
                    <p class="text-[13px] mt-1" style="color:var(--paper-dim);">Menampilkan semua file yang sudah tersimpan di database.</p>
                </div>
                
                <div class="flex items-center gap-4 shrink-0">
                    <a href="{{ route('katalog.show', $album->slug) }}" target="_blank" class="text-[12px] font-medium transition-colors flex items-center gap-1.5 hover:underline" style="color:var(--color-accent);">
                        Lihat di Katalog
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </a>

                    @if($media->count() > 0)
                    <form action="{{ route('media.clear', $album->id_album) }}" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="showConfirm('Hapus Semua Media', 'Apakah Anda yakin ingin menghapus SEMUA media ({{ $media->count() }} file) di album ini? Tindakan ini akan menghapus file secara permanen dari server dan tidak dapat dibatalkan.', () => this.closest('form').submit());" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-[12px] font-bold transition-all rounded-md" style="background:var(--color-red-soft); color:var(--color-red); border:1px solid rgba(239,68,68,0.2);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Kosongkan Album
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            @if($media->isEmpty())
                <div id="empty-media-state" class="text-center py-12 rounded-2xl border" style="background:var(--bg-input); border-color:var(--ink-line-2); border-style:dashed;">
                    <div class="text-[12.5px]" style="color:var(--paper-dim);">Belum ada media di album ini.</div>
                </div>
                <div id="media-gallery-container" class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-2 hidden" style="column-gap: 5px;">
                </div>
            @else
                <div id="empty-media-state" class="text-center py-12 rounded-2xl border hidden" style="background:var(--bg-input); border-color:var(--ink-line-2); border-style:dashed;">
                    <div class="text-[12.5px]" style="color:var(--paper-dim);">Belum ada media di album ini.</div>
                </div>
                <div id="media-gallery-container" class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-2" style="column-gap: 5px;">
                    @foreach($media as $index => $item)
                        @include('album.partials.media_tile', ['item' => $item, 'album' => $album])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @include('album.partials.cover_modal')

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadUrl = "{{ route('pdd.upload.chunk') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const albumId = "{{ $album->id_album }}";
        const chunkSize = 10 * 1024 * 1024;
        const uploadZone = document.getElementById('album-upload-zone');
        const uploadInput = document.getElementById('album-upload-input');
        const uploadTrigger = document.getElementById('album-upload-trigger');
        const uploadQueue = document.getElementById('upload-queue');

        if (!uploadZone || !uploadInput || !uploadTrigger || !uploadQueue || !csrfToken) {
            return;
        }
        
        window.uploadGlobalState = {
            total: 0,
            completed: 0,
            failed: 0,
            totalBytes: 0,
            uploadedBytes: 0,
            networkBytes: 0,
            activeControllers: [],
            speedInterval: null,
            autoHideTimeout: null,
            lastBytes: 0
        };

        window.addEventListener('beforeunload', function (e) {
            if (window.uploadGlobalState && window.uploadGlobalState.activeControllers.length > 0) {
                e.preventDefault();
                e.returnValue = ''; // Standard behavior for beforeunload prompt
            }
        });

        const preventNativeFileOpen = function (event) {
            event.preventDefault();
            event.stopPropagation();
        };

        const highlightZone = function (isActive) {
            uploadZone.style.borderColor = isActive ? 'var(--color-accent)' : 'var(--ink-line-2)';
            uploadZone.style.background = isActive ? 'var(--color-green-soft)' : 'var(--bg-input)';
        };

        const hashFile = async function(file) {
            const chunkSize = 1024 * 1024; // 1MB
            let buffer;
            if (file.size <= chunkSize * 2) {
                buffer = await file.arrayBuffer();
            } else {
                const start = await file.slice(0, chunkSize).arrayBuffer();
                const end = await file.slice(file.size - chunkSize).arrayBuffer();
                buffer = new Uint8Array(start.byteLength + end.byteLength);
                buffer.set(new Uint8Array(start), 0);
                buffer.set(new Uint8Array(end), start.byteLength);
            }
            const hashBuffer = await crypto.subtle.digest('SHA-256', buffer);
            const hashArray = Array.from(new Uint8Array(hashBuffer));
            return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        };

        const createUploadCard = function (file, abortController) {
            const card = document.createElement('div');
            card.className = 'upload-card flex items-center justify-between gap-3 py-2 border-b last:border-0 relative';
            card.style.borderColor = 'var(--ink-line-2)';

            let thumbnailHtml = `<div class="w-9 h-9 rounded-md shrink-0 flex items-center justify-center border" style="border-color:var(--ink-line-2); background:var(--bg-input);">
                                    <svg class="w-4 h-4" style="color:var(--paper-dim);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                 </div>`;
            
            if (file.type.startsWith('image/') || file.type.startsWith('video/')) {
                const objectUrl = URL.createObjectURL(file);
                card.dataset.objectUrl = objectUrl; // Simpan untuk direvoke nanti
                
                if (file.type.startsWith('image/')) {
                    thumbnailHtml = `<div class="relative w-9 h-9 shrink-0 rounded-md overflow-hidden border" style="border-color:var(--ink-line-2); background:var(--bg-input);">
                                        <img src="${objectUrl}" loading="lazy" class="w-full h-full object-cover" style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                                     </div>`;
                } else {
                    thumbnailHtml = `<div class="relative w-9 h-9 shrink-0 rounded-md overflow-hidden border" style="border-color:var(--ink-line-2); background:var(--bg-input);">
                                        <video src="${objectUrl}#t=0.1" class="w-full h-full object-cover" preload="metadata" style="width: 100%; height: 100%; object-fit: cover; object-position: center;"></video>
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/20"><svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></div>
                                     </div>`;
                }
            }

            card.innerHTML = `
                ${thumbnailHtml}
                <div class="min-w-0 flex-1 flex flex-col">
                    <div class="text-[12px] font-medium truncate" style="color:var(--paper);">${file.name}</div>
                    <div class="text-[10px] font-mono mt-0.5 truncate upload-detail" style="color:var(--text-subtle);">Menunggu...</div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <div class="upload-status text-[10px] font-bold hidden px-1.5 py-0.5 rounded-sm" style="background:var(--ink-line); color:var(--paper-dim);"></div>
                    <div class="relative w-5 h-5 flex items-center justify-center upload-circle-container">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" class="stroke-current opacity-20 text-gray-500" stroke-width="4"></circle>
                            <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-blue-500 upload-bar transition-all duration-300" stroke-width="4" stroke-dasharray="100.5" stroke-dashoffset="100.5"></circle>
                        </svg>
                    </div>
                    <button type="button" class="abort-btn p-1 -mr-1 rounded hover:bg-red-500/10 text-gray-400 hover:text-red-500 transition-colors" title="Batalkan">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            `;

            const abortBtn = card.querySelector('.abort-btn');
            abortBtn.addEventListener('click', () => {
                abortController.abort();
                abortBtn.remove();
            });
            
            // Tampilkan floating widget (jangan paksa buka queue jika user sudah minimize)
            const floatingUploader = document.getElementById('floating-uploader');
            if (floatingUploader.style.display === 'none') {
                floatingUploader.style.display = 'flex';
                document.getElementById('upload-queue-container').classList.remove('hidden');
            }

            return {
                card,
                status: card.querySelector('.upload-status'),
                bar: card.querySelector('.upload-bar'),
                detail: card.querySelector('.upload-detail'),
                abortBtn
            };
        };

        const formatBytes = function (bytes) {
            if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(1) + ' GB';
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return bytes + ' B';
        };

        const startGlobalInterval = function() {
            if (window.uploadGlobalState.speedInterval) return;
            window.uploadGlobalState.startTime = Date.now();
            
            document.getElementById('btn-cancel-all').classList.remove('hidden');
            
            window.uploadGlobalState.speedInterval = setInterval(() => {
                const state = window.uploadGlobalState;
                if (state.completed + state.failed >= state.total || state.total === 0) {
                    clearInterval(state.speedInterval);
                    state.speedInterval = null;
                    document.getElementById('btn-cancel-all').classList.add('hidden');
                    document.getElementById('upload-eta').textContent = 'Selesai';
                    document.getElementById('upload-speed-badge').classList.add('hidden');
                    document.getElementById('global-progress-bar').style.width = '100%';
                    return;
                }
                
                const now = Date.now();
                const totalTimeDiff = (now - state.startTime) / 1000;
                
                if (totalTimeDiff >= 1 && state.uploadedBytes > 0) {
                    const speedBps = state.networkBytes / totalTimeDiff;
                    const speedMbps = speedBps / (1024 * 1024);
                    
                    const speedBadge = document.getElementById('upload-speed-badge');
                    if (speedBadge) {
                        speedBadge.classList.remove('hidden');
                        speedBadge.textContent = speedMbps.toFixed(1) + ' MB/s';
                    }

                    const remainingBytes = state.totalBytes - state.uploadedBytes;
                    const etaSeconds = speedBps > 0 ? Math.ceil(remainingBytes / speedBps) : 0;
                    const etaElement = document.getElementById('upload-eta');
                    if (etaElement) {
                        if (etaSeconds > 0) {
                            const mins = Math.floor(etaSeconds / 60);
                            const secs = etaSeconds % 60;
                            etaElement.textContent = `Sisa waktu: ${mins > 0 ? mins + 'm ' : ''}${secs}d`;
                        } else {
                            etaElement.textContent = 'Menyelesaikan...';
                        }
                    }
                }
                
                const progress = state.totalBytes > 0 ? (state.uploadedBytes / state.totalBytes) * 100 : 0;
                document.getElementById('global-progress-bar').style.width = Math.min(progress, 100) + '%';
            }, 1000);
        };

        document.getElementById('btn-cancel-all').addEventListener('click', () => {
            window.uploadGlobalState.activeControllers.forEach(c => c.abort());
            window.uploadGlobalState.activeControllers = [];
        });

        document.getElementById('btn-clear-completed').addEventListener('click', () => {
            const queue = document.getElementById('upload-queue');
            const cards = queue.querySelectorAll('.upload-card.completed, .upload-card.failed');
            cards.forEach(c => {
                if(c.dataset.objectUrl) URL.revokeObjectURL(c.dataset.objectUrl);
                c.remove();
            });
            if (queue.children.length === 0) {
                document.getElementById('floating-uploader').style.display = 'none';
            }
        });

        const setUploadState = function (elements, percent, statusText, detailText, tone) {
            const offset = Math.max(0, 100.5 - (percent / 100) * 100.5);
            elements.bar.style.strokeDashoffset = offset;
            elements.detail.textContent = detailText;

            if (tone === 'success') {
                elements.card.classList.add('completed');
                elements.bar.classList.remove('text-blue-500');
                elements.bar.style.color = 'var(--color-green)';
                elements.bar.style.strokeDashoffset = 0;
                elements.status.textContent = 'OK';
                elements.status.style.color = 'var(--color-green)';
                elements.status.classList.remove('hidden');
                elements.card.querySelector('.upload-circle-container')?.classList.add('hidden');
            } else if (tone === 'error') {
                elements.card.classList.add('failed');
                elements.bar.classList.remove('text-blue-500');
                elements.bar.style.color = 'var(--color-red)';
                elements.status.textContent = 'Gagal';
                elements.status.style.color = 'var(--color-red)';
                elements.status.classList.remove('hidden');
                elements.card.querySelector('.upload-circle-container')?.classList.add('hidden');
            }
        };

        const asyncPool = async (poolLimit, array, iteratorFn) => {
            const ret = [];
            const executing = [];
            for (const item of array) {
                const p = Promise.resolve().then(() => iteratorFn(item, array));
                ret.push(p);
                if (poolLimit <= array.length) {
                    const e = p.then(() => executing.splice(executing.indexOf(e), 1));
                    executing.push(e);
                    if (executing.length >= poolLimit) {
                        await Promise.race(executing);
                    }
                }
            }
            return Promise.all(ret);
        };

        const uploadChunkWithRetry = async function (file, chunk, meta, retries = 3, abortSignal) {
            const formData = new FormData();
            formData.append('album_id', albumId);
            formData.append('file', chunk, file.name);
            formData.append('dzuuid', meta.uuid);
            formData.append('dzchunkindex', String(meta.index));
            formData.append('dztotalchunkcount', String(meta.totalChunks));
            formData.append('dzchunksize', String(chunkSize));
            formData.append('dztotalfilesize', String(file.size));

            for (let i = 0; i < retries; i++) {
                try {
                    const response = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                        credentials: 'same-origin',
                        signal: abortSignal
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(payload.error || payload.message || 'Upload gagal.');
                    }
                    return payload;
                } catch (err) {
                    if (err.name === 'AbortError') throw err;
                    if (i === retries - 1) throw err;
                    await new Promise(res => setTimeout(res, 1000 * Math.pow(2, i))); // Exponential backoff
                }
            }
        };

        const mergeChunks = async function (file, meta, abortSignal) {
            const formData = new FormData();
            formData.append('album_id', albumId);
            formData.append('dzuuid', meta.uuid);
            formData.append('dztotalchunkcount', String(meta.totalChunks));
            formData.append('dztotalfilesize', String(file.size));
            formData.append('file_name', file.name);
            formData.append('file_hash', meta.hash);

            const response = await fetch("{{ route('pdd.upload.merge') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
                credentials: 'same-origin',
                signal: abortSignal
            });

            const payload = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(payload.error || payload.message || 'Gagal memproses file.');
            }
            return payload;
        };

        const appendHtmlToGallery = function (html) {
            if (!html) return;
            const gallery = document.getElementById('media-gallery-container');
            const emptyState = document.getElementById('empty-media-state');
            
            if (emptyState) emptyState.classList.add('hidden');
            if (gallery) {
                gallery.classList.remove('hidden');
                gallery.insertAdjacentHTML('afterbegin', html);
                
                const countEl = document.getElementById('album-media-count');
                if (countEl) {
                    countEl.textContent = parseInt(countEl.textContent) + 1;
                }
            }
        };

        const uploadSingleFile = async function (file, abortController, elements) {
            const totalChunks = Math.max(1, Math.ceil(file.size / chunkSize));
            
            const cleanup = () => {
                const idx = window.uploadGlobalState.activeControllers.indexOf(abortController);
                if (idx > -1) window.uploadGlobalState.activeControllers.splice(idx, 1);
            };
            
            try {
                if (abortController.signal.aborted) {
                    throw new DOMException('Aborted', 'AbortError');
                }

                // 1. Generate Deterministic UUID & Hash
                setUploadState(elements, 0, 'Memeriksa', 'Mengecek duplikasi...', 'loading');
                const fileHash = await hashFile(file);
                // uuid based on hash so we can resume even if page reloads
                const fileUuid = fileHash.substring(0, 32); 

                // 2. Check for Duplicates & Resume Status (Combined into 1 Request)
                const checkData = new FormData();
                checkData.append('album_id', albumId);
                checkData.append('file_hash', fileHash);
                checkData.append('file_name', file.name);
                checkData.append('dzuuid', fileUuid); // Send UUID to check chunks in one request!

                const checkResponse = await fetch("{{ route('pdd.upload.check_hash') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: checkData,
                    signal: abortController.signal
                });
                const checkPayload = await checkResponse.json().catch(() => ({}));

                if (checkPayload.exists) {
                    elements.abortBtn?.remove();
                    setUploadState(elements, 100, 'Selesai', 'File sudah ada (Instan).', 'success');
                    window.uploadGlobalState.uploadedBytes += file.size;
                    cleanup();
                    return true;
                }

                // 3. Extract Resumable Chunks from Payload
                const uploadedChunks = checkPayload.uploaded_chunks || [];

                // 4. Build Chunk Queue
                const chunks = [];
                for (let index = 0; index < totalChunks; index++) {
                    if (uploadedChunks.includes(index)) continue; // Skip already uploaded
                    const start = index * chunkSize;
                    const end = Math.min(file.size, start + chunkSize);
                    chunks.push({ index, blob: file.slice(start, end) });
                }

                let completedChunks = uploadedChunks.length;
                if (chunks.length > 0) {
                    setUploadState(elements, Math.round((completedChunks / totalChunks) * 90), 'Mengunggah', 'Melanjutkan upload...', 'loading');
                    
                    // Concurrent upload (up to 3 chunks at a time)
                    await asyncPool(3, chunks, async (chunkData) => {
                        await uploadChunkWithRetry(file, chunkData.blob, {
                            uuid: fileUuid,
                            index: chunkData.index,
                            totalChunks,
                        }, 3, abortController.signal);
                        completedChunks++;
                        window.uploadGlobalState.uploadedBytes += chunkData.blob.size;
                        window.uploadGlobalState.networkBytes += chunkData.blob.size;
                        setUploadState(
                            elements,
                            Math.round((completedChunks / totalChunks) * 90), // progress up to 90% during chunks
                            'Mengunggah',
                            `${completedChunks} dari ${totalChunks} bagian terkirim`,
                            'loading'
                        );
                    });
                }

                // 5. Merge Chunks
                setUploadState(elements, 95, 'Memproses', 'Menyatukan file di server...', 'loading');
                const mergePayload = await mergeChunks(file, { uuid: fileUuid, totalChunks, hash: fileHash }, abortController.signal);

                elements.abortBtn?.remove();
                setUploadState(elements, 100, 'Selesai', 'File tersimpan di album.', 'success');
                if (mergePayload.html) appendHtmlToGallery(mergePayload.html);
                cleanup();
                return true;
            } catch (error) {
                elements.abortBtn?.remove();
                window.uploadGlobalState.uploadedBytes += file.size; // force add so progress bar isn't stuck
                cleanup();
                if (error.name === 'AbortError') {
                    setUploadState(elements, 0, 'Dibatalkan', 'Upload dibatalkan pengguna.', 'error');
                    return false;
                }
                console.error(error);
                setUploadState(elements, 100, 'Gagal', error.message || 'Terjadi kesalahan saat mengunggah.', 'error');
                return false;
            }
        };

        const uploadFiles = async function (files) {
            const acceptedExtensions = ['.heic', '.heif', '.raw', '.cr2', '.cr3', '.nef', '.arw', '.dng', '.raf', '.orf', '.sr2', '.pef', '.rw2', '.mov', '.mp4', '.m4v', '.hevc', '.jpg', '.jpeg', '.png', '.webp', '.gif'];
            
            const isAccepted = (file) => {
                if (file.type.startsWith('image/') || file.type.startsWith('video/')) return true;
                const extMatch = file.name.match(/\.[0-9a-z]+$/i);
                const ext = extMatch ? extMatch[0].toLowerCase() : '';
                return acceptedExtensions.includes(ext);
            };

            const acceptedFiles = Array.from(files || []).filter(isAccepted);

            if (!acceptedFiles.length) {
                showToast('warning', 'Tidak didukung', 'Pilih file gambar atau video.');
                return;
            }

            // Update floating counter
            const badge = document.getElementById('upload-count-badge');
            window.uploadGlobalState = {
                ...window.uploadGlobalState,
                total: window.uploadGlobalState.total + acceptedFiles.length,
                totalBytes: window.uploadGlobalState.totalBytes + acceptedFiles.reduce((acc, f) => acc + f.size, 0)
            };
            
            if (window.uploadGlobalState.autoHideTimeout) {
                clearTimeout(window.uploadGlobalState.autoHideTimeout);
                window.uploadGlobalState.autoHideTimeout = null;
            }
            
            const updateGlobalBadge = () => {
                if(badge) {
                    badge.textContent = `Mengunggah ${window.uploadGlobalState.completed} dari ${window.uploadGlobalState.total}`;
                }
            };
            updateGlobalBadge();
            startGlobalInterval();

            // Map files to tasks upfront and use DocumentFragment to batch DOM inserts (massive performance boost)
            const fragment = document.createDocumentFragment();
            const fileTasks = acceptedFiles.map(file => {
                const abortController = new AbortController();
                window.uploadGlobalState.activeControllers.push(abortController);
                const elements = createUploadCard(file, abortController);
                fragment.appendChild(elements.card);
                return { file, abortController, elements };
            });
            document.getElementById('upload-queue').appendChild(fragment);

            // Concurrent file processing (up to 2 files at a time)
            await asyncPool(2, fileTasks, async (task) => {
                const result = await uploadSingleFile(task.file, task.abortController, task.elements);
                if (result) {
                    window.uploadGlobalState.completed += 1;
                } else {
                    window.uploadGlobalState.failed += 1;
                }
                updateGlobalBadge();
            });

            if (window.uploadGlobalState.completed + window.uploadGlobalState.failed >= window.uploadGlobalState.total) {
                if(badge) badge.textContent = 'Semua selesai';
                if(window.uploadGlobalState.completed > 0) showToast('success', 'Upload Selesai', `${window.uploadGlobalState.completed} dari ${window.uploadGlobalState.total} file berhasil diunggah.`);
                
                window.uploadGlobalState.autoHideTimeout = setTimeout(() => {
                    const uploader = document.getElementById('floating-uploader');
                    if (uploader) {
                        uploader.style.transform = 'translateY(20px)';
                        uploader.style.opacity = '0';
                        setTimeout(() => { uploader.style.display = 'none'; uploader.style.transform = 'translateY(0)'; uploader.style.opacity = '1'; }, 500);
                    }
                }, 5000);
            }
        };

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
            uploadZone.addEventListener(eventName, preventNativeFileOpen);
        });

        window.addEventListener('dragover', preventNativeFileOpen);
        window.addEventListener('drop', preventNativeFileOpen);

        uploadZone.addEventListener('dragenter', function () {
            highlightZone(true);
        });

        uploadZone.addEventListener('dragover', function () {
            highlightZone(true);
        });

        uploadZone.addEventListener('dragleave', function (event) {
            if (!uploadZone.contains(event.relatedTarget)) {
                highlightZone(false);
            }
        });

        const getAllFilesFromDataTransfer = async (dataTransfer) => {
            const files = [];
            const entries = [];
            
            // 1. Collect all entries synchronously because dataTransfer items expire if accessed asynchronously
            if (dataTransfer.items) {
                for (let i = 0; i < dataTransfer.items.length; i++) {
                    const item = dataTransfer.items[i];
                    if (item.webkitGetAsEntry) {
                        const entry = item.webkitGetAsEntry();
                        if (entry) entries.push(entry);
                    } else if (item.kind === 'file') {
                        files.push(item.getAsFile());
                    }
                }
            } else {
                for (let i = 0; i < dataTransfer.files.length; i++) {
                    files.push(dataTransfer.files[i]);
                }
            }

            // 2. Process entries asynchronously
            const readEntry = async (entry) => {
                if (entry.isFile) {
                    const file = await new Promise((resolve) => entry.file(resolve));
                    files.push(file);
                } else if (entry.isDirectory) {
                    const reader = entry.createReader();
                    const readEntries = () => new Promise((resolve, reject) => reader.readEntries(resolve, reject));
                    
                    let dirEntries;
                    do {
                        dirEntries = await readEntries();
                        for (let child of dirEntries) {
                            await readEntry(child);
                        }
                    } while (dirEntries.length > 0);
                }
            };

            for (const entry of entries) {
                await readEntry(entry);
            }

            return files;
        };

        uploadZone.addEventListener('drop', async function (event) {
            highlightZone(false);
            
            // Tampilkan toast bahwa sedang memindai folder jika banyak file
            if (event.dataTransfer.items && event.dataTransfer.items.length > 0) {
                showToast('info', 'Memindai...', 'Sedang membaca file dari folder, mohon tunggu.');
            }

            const files = await getAllFilesFromDataTransfer(event.dataTransfer);
            uploadFiles(files);
        });

        uploadZone.addEventListener('click', function (event) {
            if (event.target.closest('button, a, input, label')) {
                return;
            }
            uploadInput.click();
        });

        uploadZone.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                uploadInput.click();
            }
        });

        uploadTrigger.addEventListener('click', function () {
            uploadInput.click();
        });

        uploadInput.addEventListener('change', function () {
            uploadFiles(this.files);
            this.value = '';
        });
    });
</script>
@endpush

