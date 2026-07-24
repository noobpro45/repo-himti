@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="relative min-h-screen flex items-center justify-center px-5 py-[60px]"
     style="background:
        radial-gradient(circle at 50% 30%, rgba(46, 178, 83, 0.10), transparent 55%),
        repeating-linear-gradient(0deg, var(--ink-line) 0 1px, transparent 1px 34px),
        repeating-linear-gradient(90deg, var(--ink-line) 0 1px, transparent 1px 34px),
        var(--bg-body);
     transition: background 0.35s ease;">

    <div class="w-[380px] max-w-full rounded-2xl p-[34px_30px_28px] text-center shadow-xl transition-all duration-300"
         style="background: var(--bg-panel); border: 1px solid var(--ink-line-2); box-shadow: var(--shadow);">

        {{-- Logo --}}
        <div class="w-32 h-32 mx-auto mb-4 flex items-center justify-center">
            <img src="{{ asset('logo-himti.png') }}" alt="Logo HIMTI" class="w-24 h-24 object-contain transition-transform duration-300 hover:scale-[1.06]" style="filter: drop-shadow(0 8px 16px rgba(46, 178, 83, 0.25));">
        </div>

        <h1 class="text-[17px] font-serif font-semibold mb-1" style="color:var(--paper);">
            Repositori Dokumentasi Multimedia
        </h1>
        <p class="text-[12.5px] mb-6" style="color:var(--paper-dim);">
            Masuk menggunakan akun internal Himpunan Mahasiswa Teknik Informatika
        </p>

        {{-- Error message --}}
        @if($errors->any())
        <div class="flex gap-2 items-start text-left rounded-lg p-[10px_12px] mb-4 text-xs"
             style="background:var(--color-red-soft); border:1px solid rgba(185,80,63,0.4); color:var(--text-error); animation: shake 0.4s ease;">
            <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="7" cy="7" r="6.2" /><path d="M7 4v4M7 10h.01" />
            </svg>
            <span><b>{{ $errors->first() }}</b></span>
        </div>
        @endif

        {{-- Login form --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Username/NIM --}}
            <div class="text-left mb-3.5">
                <label class="block font-mono text-[10.5px] tracking-[0.06em] uppercase mb-1.5" style="color:var(--paper-dim);">
                    Username (NIM)
                </label>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus
                    class="w-full px-3.5 py-[11px] rounded-lg text-[13.5px] font-sans outline-none transition-all duration-200"
                    style="border:1px solid var(--ink-line-2); background:var(--bg-input); color:var(--paper);"
                    onfocus="this.style.borderColor='var(--color-accent)'; this.style.boxShadow='0 0 0 3px var(--color-accent-soft)';"
                    onblur="this.style.borderColor='var(--ink-line-2)'; this.style.boxShadow='none';"
                    placeholder="Contoh: 11230610001">
            </div>

            {{-- Password --}}
            <div class="text-left mb-3.5 relative">
                <label class="block font-mono text-[10.5px] tracking-[0.06em] uppercase mb-1.5" style="color:var(--paper-dim);">
                    Password
                </label>
                <input type="password" name="password" required id="loginPassword"
                    class="w-full px-3.5 py-[11px] rounded-lg text-[13.5px] font-sans outline-none transition-all duration-200"
                    style="border:1px solid var(--ink-line-2); background:var(--bg-input); color:var(--paper);"
                    onfocus="this.style.borderColor='var(--color-accent)'; this.style.boxShadow='0 0 0 3px var(--color-accent-soft)';"
                    onblur="this.style.borderColor='var(--ink-line-2)'; this.style.boxShadow='none';"
                    placeholder="Masukkan kata sandi">
                <span class="absolute right-3 top-[34px] font-mono text-[11px] cursor-pointer transition-colors duration-150 hover:text-[var(--color-accent)]"
                      style="color:var(--paper-dim);"
                      onclick="const p=document.getElementById('loginPassword'); p.type=p.type==='password'?'text':'password'; this.textContent=p.type==='password'?'tampilkan':'sembunyikan';">
                    tampilkan
                </span>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3 rounded-lg font-semibold text-[13.5px] font-sans cursor-pointer transition-all duration-150 mt-1.5 text-white hover:-translate-y-0.5"
                style="background:linear-gradient(180deg,#2438A0,var(--color-navy)); box-shadow:0 10px 22px -8px rgba(27,45,107,0.55);">
                Masuk ke Sistem
            </button>
        </form>

        {{-- Helper row --}}
        <div class="flex justify-between items-center mt-3.5 text-[11.5px]" style="color:var(--paper-dim);">
            <span>Lupa kata sandi?</span>
            @php
                $waAdmin = \App\Models\Pengaturan::ambil('wa_admin', null);
            @endphp
            @if($waAdmin)
                @php $waText = urlencode('Halo Admin, saya lupa password untuk login ke SIRDM. Mohon bantuannya untuk reset.'); @endphp
                <a href="https://wa.me/{{ $waAdmin }}?text={{ $waText }}" target="_blank" class="transition-colors duration-150 hover:text-[var(--paper)]" style="color:var(--color-accent); text-decoration:none;">
                    Hubungi Super Admin →
                </a>
            @else
                <span class="transition-colors duration-150" style="color:var(--color-orange);" title="Mohon beritahu admin secara langsung karena nomor belum diatur">
                    Belum diatur, harap lapor admin langsung
                </span>
            @endif
        </div>

        {{-- Footer --}}
        <div class="mt-6 pt-4 font-mono text-[10.5px] tracking-[0.03em]" style="border-top:1px solid var(--ink-line); color:var(--text-subtle);">
            HIMTI × PRODI TEKNIK INFORMATIKA<br>
            FAKULTAS SAINS DAN TEKNOLOGI — UIN SYARIF HIDAYATULLAH JAKARTA
        </div>
    </div>
</div>
@endsection
