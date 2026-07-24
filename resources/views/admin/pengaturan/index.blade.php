@extends('layouts.app')

@section('title', 'Pengaturan')



@section('content')
<div class="animate-fade-in max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-serif font-semibold mb-1 tracking-tight" style="color:var(--paper);">Pengaturan Sistem (Variabel Global)</h1>
        <p class="text-[13.5px]" style="color:var(--paper-dim);">Ubah aturan dan batas operasi repositori secara dinamis tanpa membongkar kode.</p>
    </div>

    @if(session('success'))
        <div class="p-3 mb-6 rounded-lg text-sm border font-medium flex items-center gap-2" style="background:var(--color-green-soft); color:var(--color-green); border-color:rgba(46, 178, 83, 0.2);">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Grup: Penyimpanan & Upload --}}
        <div class="mb-6 p-6 rounded-2xl border flex flex-col gap-6" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <div>
                <h3 class="text-base font-semibold mb-1" style="color:var(--paper);">Sistem Penyimpanan & Kuota Upload</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col">
                    <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Batas Maksimal Ukuran 1 File (MB)</label>
                    <input type="number" name="max_upload_size_mb" value="{{ old('max_upload_size_mb', $settings['max_upload_size_mb'] ?? 500) }}" required
                           class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]"
                           style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    <p class="text-[11px] mt-1.5 leading-relaxed" style="color:var(--text-subtle);">Menentukan seberapa besar 1 foto/video yang boleh dimasukkan. Contoh: Isi 500 jika tidak boleh upload video di atas 500 MB.</p>
                </div>
                <div class="flex flex-col">
                    <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Ukuran Potongan Upload (MB)</label>
                    <input type="number" name="chunk_size_mb" value="{{ old('chunk_size_mb', $settings['chunk_size_mb'] ?? 10) }}" required
                           class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]"
                           style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    <p class="text-[11px] mt-1.5 leading-relaxed" style="color:var(--text-subtle);">Besar potongan video saat proses unggah (Chunking). Biarkan 10 MB agar koneksi internet tidak mudah terputus saat mengunggah.</p>
                </div>
                <div class="flex flex-col md:col-span-2">
                    <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Jenis File Boleh Diunggah</label>
                    <input type="text" name="allowed_mime_types" value="{{ old('allowed_mime_types', $settings['allowed_mime_types'] ?? 'image/*,video/*') }}" required
                           class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]"
                           style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    <p class="text-[11px] mt-1.5 leading-relaxed" style="color:var(--text-subtle);">Format jenis dokumen. Jangan diubah kecuali Anda tahu format MIME-type (Misal: <code>image/jpeg, video/mp4</code>).</p>
                </div>
            </div>
        </div>

        {{-- Grup: Multimedia --}}
        <div class="mb-6 p-6 rounded-2xl border flex flex-col gap-6" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <div>
                <h3 class="text-base font-semibold mb-1" style="color:var(--paper);">Kualitas Konversi Video (FFmpeg)</h3>
            </div>
            <div class="flex flex-col">
                <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Tingkat Kecepatan vs Ukuran Video</label>
                <select name="ffmpeg_preset" required class="custom-select px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]" style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    <option value="veryfast" {{ ($settings['ffmpeg_preset'] ?? '') == 'veryfast' ? 'selected' : '' }}>Sangat Cepat (Ukuran file jadi agak besar)</option>
                    <option value="fast" {{ ($settings['ffmpeg_preset'] ?? '') == 'fast' ? 'selected' : '' }}>Cepat</option>
                    <option value="medium" {{ ($settings['ffmpeg_preset'] ?? 'medium') == 'medium' ? 'selected' : '' }}>Standar (Direkomendasikan)</option>
                    <option value="slow" {{ ($settings['ffmpeg_preset'] ?? '') == 'slow' ? 'selected' : '' }}>Lambat (Ukuran file jadi kecil / hemat penyimpanan)</option>
                    <option value="veryslow" {{ ($settings['ffmpeg_preset'] ?? '') == 'veryslow' ? 'selected' : '' }}>Sangat Lambat (Sangat hemat penyimpanan, tapi server bekerja keras)</option>
                </select>
                <p class="text-[11px] mt-1.5 leading-relaxed" style="color:var(--text-subtle);">Mengatur bagaimana video dari kamera diproses oleh server. Jika Anda punya banyak memori server, pilih Cepat. Jika memori server terbatas, pilih Lambat.</p>
            </div>
        </div>

        {{-- Grup: Identitas & Lokasi --}}
        <div class="mb-6 p-6 rounded-2xl border flex flex-col gap-6" style="background:var(--bg-panel); border-color:var(--ink-line-2);">
            <div>
                <h3 class="text-base font-semibold mb-1" style="color:var(--paper);">Umum & Lokasi Folder</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col">
                    <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Nama Organisasi/Institusi</label>
                    <input type="text" name="nama_organisasi" value="{{ old('nama_organisasi', $settings['nama_organisasi'] ?? 'HIMTI') }}" required
                           class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]"
                           style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    <p class="text-[11px] mt-1.5 leading-relaxed" style="color:var(--text-subtle);">Teks institusi yang akan muncul pada laporan (Jika ada).</p>
                </div>
                <div class="flex flex-col">
                    <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Lokasi Folder Rahasia (Path)</label>
                    <input type="text" name="storage_path" value="{{ old('storage_path', $settings['storage_path'] ?? 'storage/app/media') }}" required
                           class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]"
                           style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    <p class="text-[11px] mt-1.5 leading-relaxed" style="color:var(--text-subtle);">Lokasi bersembunyinya file asli foto/video di dalam komputer *server* agar tidak bisa didownload secara publik tanpa login.</p>
                </div>
                <div class="flex flex-col md:col-span-2">
                    <label class="text-[11.5px] font-medium mb-1.5" style="color:var(--paper-dim);">Nomor WhatsApp Admin</label>
                    <input type="text" name="wa_admin" value="{{ old('wa_admin', $settings['wa_admin'] ?? '') }}" required
                           class="px-3 py-2.5 rounded-lg border outline-none text-[13px] transition-colors focus:border-[var(--color-accent)]"
                           style="background:var(--bg-input); border-color:var(--ink-line-2); color:var(--paper);">
                    <p class="text-[11px] mt-1.5 leading-relaxed" style="color:var(--text-subtle);">Gunakan kode negara tanpa simbol + (Contoh: 628123...). Digunakan untuk tombol bantuan Lupa Password.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2 rounded-lg text-sm font-medium text-white transition-colors" style="background:var(--color-accent); box-shadow:0 4px 12px rgba(46,178,83,0.25);">
                Simpan Perubahan Global
            </button>
        </div>
    </form>
</div>
@endsection
