<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <script>
        (function() {
            try {
                var saved = localStorage.getItem('himti-theme');
                if (saved) {
                    document.documentElement.setAttribute('data-theme', saved);
                }
            } catch (e) {}
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Dashboard SIRDM HIMTI - Repositori Dokumentasi Multimedia">
    <title>@yield('title', 'Dashboard') — SIRDM HIMTI</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('logo-himti.png') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    {{-- CDN Preconnects --}}
    <link rel="preconnect" href="https://cdn.plyr.io">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen font-sans">

    {{-- Theme toggle (Mobile only) --}}
    <button onclick="toggleTheme()"
        class="fixed top-[18px] right-[22px] z-[999] w-[42px] h-[42px] rounded-full flex items-center justify-center cursor-pointer transition-all duration-200 hover:scale-[1.08] md:hidden"
        style="border: 1px solid var(--ink-line-2); background: var(--bg-panel); box-shadow: 0 4px 14px -4px rgba(0,0,0,0.25);"
        aria-label="Ganti tema gelap/terang">
        <svg class="w-5 h-5 icon-sun transition-transform duration-300 hover:rotate-[20deg]" style="color:var(--color-accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="4.5" />
            <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
        </svg>
        <svg class="w-5 h-5 icon-moon transition-transform duration-300 hover:rotate-[20deg] hidden" style="color:var(--color-accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
        </svg>
    </button>

    {{-- Toast container --}}
    <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-2.5 pointer-events-none"></div>

    {{-- App Shell: Sidebar + Main --}}
    <div class="grid min-h-screen" style="grid-template-columns: 220px 1fr;">

        {{-- ===== SIDEBAR ===== --}}
        <aside class="z-[100] flex flex-col p-[20px_14px] transition-colors duration-300 sticky top-0 h-screen overflow-y-auto shrink-0" style="background:var(--bg-sidebar); border-right: 1px solid var(--ink-line);">

            {{-- Brand --}}
            <div class="flex items-center gap-2.5 px-1.5 pb-5 mb-3.5" style="border-bottom: 1px solid var(--ink-line);">
                <img src="{{ asset('logo-himti.png') }}" alt="Logo HIMTI" class="w-8 h-8 object-contain">
                <div class="leading-tight">
                    <b class="text-[12.5px] block" style="color:var(--paper);">HIMTI Repo</b>
                    <span class="text-[10px] font-mono" style="color:var(--paper-dim);">
                        @if(auth()->user()->isSuperAdmin()) v1.0 · Monolithic
                        @elseif(auth()->user()->isAdminPdd()) Divisi PDD
                        @else Anggota Himpunan
                        @endif
                    </span>
                </div>
            </div>

            {{-- Notifications (Visible for Admin PDD) --}}
            @if(auth()->user()->role === 'admin_pdd')
            <div class="mb-5 px-1.5 relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="relative flex items-center gap-2.5 w-full px-2.5 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-[var(--ink-line)] active:scale-[0.98] group" 
                        style="color:var(--paper-dim);"
                        :style="open ? 'background:var(--ink-line); color:var(--paper);' : ''">
                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-[12deg]" 
                         :class="open ? 'text-[var(--color-accent)]' : 'text-current'"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="group-hover:text-[var(--paper)] transition-colors">Notifikasi</span>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="unread-badge absolute right-2.5 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-[var(--color-red)] text-[9px] font-bold text-white shadow-sm shadow-[var(--color-red)] animate-pulse">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
                
                {{-- Dropdown Notifications --}}
                <div x-show="open" @click.outside="open = false" x-cloak 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-[-15px] scale-[0.95]"
                     x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-x-[-15px] scale-[0.95]"
                     class="fixed left-[230px] top-[85px] w-[380px] rounded-2xl border shadow-2xl z-[999] flex flex-col overflow-hidden" 
                     style="background:var(--bg-panel); backdrop-filter: blur(16px); border-color: var(--ink-line-2); max-height: calc(100vh - 120px); display:none; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);">
                    
                    {{-- Header --}}
                    <div class="px-5 py-4 flex items-center justify-between border-b bg-black/5" style="border-color: var(--ink-line);">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm" style="background: linear-gradient(to top right, var(--color-accent), #1A7A35); color: white;">
                                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-[15px] font-semibold tracking-tight leading-tight" style="color: var(--paper);">Notifikasi</h4>
                                <p class="text-[11px]" style="color: var(--paper-dim);">Pemberitahuan aktivitas</p>
                            </div>
                        </div>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <form action="{{ route('pdd.notifications.read') }}" method="POST" x-data="{ marking: false }" @submit.prevent="
                                if(marking) return;
                                marking = true;
                                fetch($el.action, {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                                }).then(res => {
                                    if(res.ok) {
                                        $el.remove();
                                        document.querySelectorAll('.unread-badge').forEach(el => el.remove());
                                        document.querySelectorAll('.unread-indicator').forEach(el => el.remove());
                                        document.querySelectorAll('.unread-bg').forEach(el => el.style.background = 'transparent');
                                        document.querySelectorAll('.unread-icon').forEach(el => {
                                            el.style.background = 'transparent';
                                            el.style.color = 'var(--paper-dim)';
                                        });
                                        if (window.showToast) window.showToast('success', 'Berhasil', 'Semua notifikasi ditandai dibaca.');
                                    }
                                }).finally(() => marking = false);
                            ">
                                @csrf
                                <button type="submit" 
                                        class="flex items-center gap-1.5 text-[11px] font-medium px-3 py-1.5 rounded-full transition-all duration-200 hover:bg-[var(--ink-line)] active:scale-95 group"
                                        style="color: var(--text-muted);"
                                        :class="marking ? 'opacity-50 cursor-wait' : ''">
                                    <svg class="w-3.5 h-3.5 group-hover:text-[var(--color-accent)] transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6L9 17l-5-5"></path>
                                    </svg>
                                    <span class="group-hover:text-[var(--paper)] transition-colors" x-text="marking ? 'Memproses...' : 'Tandai Dibaca'"></span>
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- List --}}
                    <div class="flex-1 overflow-y-auto p-3 space-y-1.5 custom-scrollbar" style="max-height: 450px;">
                        @forelse(auth()->user()->notifications->take(10) as $notif)
                            @php
                                $isUnread = !$notif->read_at;
                            @endphp
                            <div class="relative p-4 rounded-xl transition-all duration-300 hover:bg-[var(--ink-line)]/50 group {{ $isUnread ? 'unread-bg' : '' }}" 
                                 style="{{ $isUnread ? 'background: var(--bg-input);' : '' }}">
                                
                                @if($isUnread)
                                    <div class="unread-indicator absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 rounded-r-full bg-[var(--color-red)] shadow-[0_0_8px_var(--color-red)]"></div>
                                @endif
                                
                                <div class="flex gap-3.5">
                                    {{-- Icon --}}
                                    <div class="mt-0.5 shrink-0">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center border transition-transform duration-300 group-hover:scale-110 {{ $isUnread ? 'unread-icon' : '' }}"
                                             style="border-color: var(--ink-line); background: {{ $isUnread ? 'var(--bg-panel)' : 'transparent' }}; color: {{ $isUnread ? 'var(--color-red)' : 'var(--paper-dim)' }};">
                                            @if(str_contains(strtolower($notif->data['judul'] ?? ''), 'tolak') || str_contains(strtolower($notif->data['judul'] ?? ''), 'gagal'))
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                            @elseif(str_contains(strtolower($notif->data['judul'] ?? ''), 'terima') || str_contains(strtolower($notif->data['judul'] ?? ''), 'berhasil'))
                                                <svg class="w-4 h-4 text-[var(--color-green)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                            @else
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-2 mb-1">
                                            <span class="text-[13px] font-semibold leading-tight" style="color: {{ $isUnread ? 'var(--paper)' : 'var(--paper-dim)' }};">
                                                {{ $notif->data['judul'] ?? 'Pemberitahuan' }}
                                            </span>
                                            <span class="text-[10px] font-mono shrink-0 pt-0.5" style="color: var(--text-muted);">
                                                {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                            </span>
                                        </div>

                                        <p class="text-[12px] leading-relaxed mb-2.5" style="color: var(--paper-dim);">
                                            {{ $notif->data['pesan'] ?? '' }}
                                        </p>

                                        @if(isset($notif->data['alasan']))
                                            <div class="p-2.5 rounded-lg text-[11.5px] relative" 
                                                 style="background: var(--ink-line); border-left: 2px solid var(--ink-line-2); color: var(--paper-dim);">
                                                <div class="flex gap-2">
                                                    <svg class="w-3.5 h-3.5 shrink-0 mt-0.5 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                    <span class="leading-relaxed italic">
                                                        "{{ $notif->data['alasan'] }}"
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-14 flex flex-col items-center justify-center text-center">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4 relative" 
                                     style="background: var(--bg-input); border: 1px solid var(--ink-line);">
                                    <div class="absolute inset-0 rounded-2xl animate-ping opacity-20" style="background: var(--ink-line-2);"></div>
                                    <svg class="w-7 h-7" style="color: var(--paper-dim);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                    </svg>
                                </div>
                                <h5 class="text-[13px] font-semibold mb-1" style="color: var(--paper);">Belum Ada Notifikasi</h5>
                                <p class="text-[11.5px] max-w-[200px]" style="color: var(--text-muted);">Aktivitas dan pemberitahuan penting akan muncul di sini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

            {{-- Navigation groups --}}
            @if(auth()->user()->role === 'super_admin')
            <div class="mb-5">
                <span class="block text-[10px] font-bold tracking-[0.05em] uppercase mb-1.5 px-2.5" style="color:var(--text-muted);">Manajemen</span>
                
                <a href="{{ route('admin.ringkasan') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('admin.ringkasan') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('admin.ringkasan') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('admin.ringkasan') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="13" r="8" /><path d="M12 13l4-4" /><path d="M8 3h8" /></svg>
                    Ringkasan
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('admin.users.*') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('admin.users.*') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('admin.users.*') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3.2" /><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6" /><circle cx="17" cy="8.5" r="2.6" /><path d="M15.5 14.2c2.8.3 5 2.4 5 5.8" /></svg>
                    Data Anggota
                </a>
                
                <a href="{{ route('admin.albums.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('admin.albums.*') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('admin.albums.*') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('admin.albums.*') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6.5A1.5 1.5 0 014.5 5h4.6l1.8 2H19.5A1.5 1.5 0 0121 8.5v9A1.5 1.5 0 0119.5 19h-15A1.5 1.5 0 013 17.5v-11z" /></svg>
                    Semua Album
                </a>
            </div>
            
            <div class="mb-5">
                <span class="block text-[10px] font-bold tracking-[0.05em] uppercase mb-1.5 px-2.5" style="color:var(--text-muted);">Sistem</span>
                
                <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('admin.logs.*') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('admin.logs.*') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('admin.logs.*') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6l7-3z" /><path d="M9 12l2 2 4-4" /></svg>
                    Log Aktivitas
                </a>
                
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('admin.settings.*') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('admin.settings.*') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('admin.settings.*') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009.09 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9.09a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" /></svg>
                    Pengaturan Sistem
                </a>
                
                <a href="{{ route('admin.storage.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('admin.storage.*') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('admin.storage.*') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('admin.storage.*') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="2" width="20" height="8" rx="2" ry="2" /><rect x="2" y="14" width="20" height="8" rx="2" ry="2" /><line x1="6" y1="6" x2="6.01" y2="6" /><line x1="6" y1="18" x2="6.01" y2="18" /></svg>
                    Penyimpanan
                </a>
            </div>
            @elseif(auth()->user()->isAdminPdd())
            {{-- Admin PDD Menu --}}
            <div class="mb-5">
                <span class="block text-[10px] font-bold tracking-[0.05em] uppercase mb-1.5 px-2.5" style="color:var(--text-muted);">Ruang Kerja</span>
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('dashboard') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('dashboard') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('dashboard') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6.5A1.5 1.5 0 014.5 5h4.6l1.8 2H19.5A1.5 1.5 0 0121 8.5v9A1.5 1.5 0 0119.5 19h-15A1.5 1.5 0 013 17.5v-11z" /></svg>
                    Album Saya
                </a>
                
                <a href="{{ route('pdd.album.create') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('pdd.album.create') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('pdd.album.create') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('pdd.album.create') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14" /></svg>
                    Buat Album Baru
                </a>
            </div>
            
            <div class="mb-5">
                <span class="block text-[10px] font-bold tracking-[0.05em] uppercase mb-1.5 px-2.5" style="color:var(--text-muted);">Jelajah</span>
                
                <a href="{{ route('katalog.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('katalog.*') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('katalog.*') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('katalog.*') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5" /><rect x="14" y="3" width="7" height="7" rx="1.5" /><rect x="3" y="14" width="7" height="7" rx="1.5" /><rect x="14" y="14" width="7" height="7" rx="1.5" /></svg>
                    Katalog Acara
                </a>
                
                <a href="{{ route('riwayat.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('riwayat.*') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('riwayat.*') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('riwayat.*') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Riwayat Unduhan
                </a>
            </div>
            @else
            {{-- Mahasiswa Menu --}}
            <div class="mb-5">
                <span class="block text-[10px] font-bold tracking-[0.05em] uppercase mb-1.5 px-2.5" style="color:var(--text-muted);">Jelajah</span>
                <a href="{{ route('katalog.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('katalog.*') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('katalog.*') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('katalog.*') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5" /><rect x="14" y="3" width="7" height="7" rx="1.5" /><rect x="3" y="14" width="7" height="7" rx="1.5" /><rect x="14" y="14" width="7" height="7" rx="1.5" /></svg>
                    Katalog Acara
                </a>
                
                <a href="{{ route('riwayat.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[13px] font-medium transition-all duration-150 mb-0.5 {{ request()->routeIs('riwayat.*') ? '' : 'hover:bg-[var(--ink-line)] hover:text-[var(--paper)]' }}" style="{{ request()->routeIs('riwayat.*') ? 'background:var(--ink-line); color:var(--paper);' : 'color:var(--paper-dim);' }}">
                    <svg class="w-4 h-4 shrink-0" style="{{ request()->routeIs('riwayat.*') ? 'color:var(--color-accent);' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Riwayat Unduhan
                </a>
            </div>
            @endif
            @yield('sidebar-nav')

            {{-- Footer: user info --}}
            <div class="mt-auto pt-3.5" style="border-top: 1px solid var(--ink-line);">
                <div class="flex items-center justify-between px-2 py-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[var(--color-accent)] to-[#1A7A35] flex items-center justify-center text-[11px] font-bold font-mono shrink-0" style="color:#0A2E12;">
                            {{ auth()->user()->initials }}
                        </div>
                        <div class="leading-none text-left">
                            <b class="text-xs block" style="color:var(--paper);">{{ auth()->user()->nama_lengkap }}</b>
                            <span class="text-[10px] block" style="color:var(--paper-dim);">{{ auth()->user()->role }}</span>
                        </div>
                    </div>
                    
                    {{-- Theme Toggle inside Sidebar --}}
                    <button onclick="toggleTheme()" class="w-7 h-7 rounded-lg flex items-center justify-center cursor-pointer transition-all hover:bg-[var(--ink-line)] active:scale-90" style="color:var(--paper-dim);" title="Ganti Tema">
                        <svg class="w-4 h-4 icon-sun" style="color:var(--color-accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="4.5" />
                            <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                        </svg>
                        <svg class="w-4 h-4 icon-moon hidden" style="color:var(--color-accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[12.5px] cursor-pointer transition-all duration-150 hover:bg-[var(--ink-line)]"
                        style="color:var(--paper-dim);">
                        <svg class="w-[15px] h-[15px] opacity-75" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="p-[22px_26px_34px] overflow-hidden">
            @yield('content')
        </div>
    </div>

    {{-- Mobile bottom nav --}}
    <div class="hidden fixed bottom-[18px] left-1/2 -translate-x-1/2 z-[100] px-[18px] py-2.5 rounded-[30px] items-center gap-4 backdrop-blur-[10px] md:hidden max-md:flex"
         style="background:var(--bg-panel); border:1px solid var(--ink-line-2); box-shadow:0 8px 24px -6px rgba(0,0,0,0.4);">
        @yield('mobile-nav')
    </div>

    @stack('modals')
    @stack('scripts')

    <style>
        [data-theme="dark"] .icon-moon { display: none; }
        [data-theme="dark"] .icon-sun { display: block; }
        [data-theme="light"] .icon-sun { display: none; }
        [data-theme="light"] .icon-moon { display: block; }
        
        /* Skeleton Loading Animation */
        .skeleton-bg {
            background-color: var(--ink-line);
            background-image: linear-gradient(90deg, var(--ink-line) 0px, var(--ink-line-2) 50%, var(--ink-line) 100%);
            background-size: 200% 100%;
            animation: skeletonShimmer 1.5s infinite linear;
        }
        @keyframes skeletonShimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        /* Media Viewer Dynamic Background */
        [data-theme="dark"] .mv-dynamic-bg { background-color: #000000 !important; }
        [data-theme="light"] .mv-dynamic-bg { background-color: #FFFFFF !important; }

        @media (max-width: 760px) {
            .grid { grid-template-columns: 1fr !important; }
            aside { display: none !important; }
        }
        /* Custom scrollbar for notifications dropdown */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--ink-line-2);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
        [x-cloak] { display: none !important; }
    </style>

    {{-- Custom Confirmation Modal --}}
    <div x-data="{ 
            show: false, 
            title: '', 
            message: '', 
            type: 'danger',
            confirmText: 'Hapus',
            confirmCallback: null,
            openConfirm(e) {
                this.title = e.detail.title;
                this.message = e.detail.message;
                this.type = e.detail.type || 'danger';
                this.confirmText = e.detail.confirmText || (this.type === 'danger' ? 'Hapus' : 'Ya, Lanjutkan');
                this.confirmCallback = e.detail.callback;
                this.show = true;
            },
            confirm() {
                if (this.confirmCallback) this.confirmCallback();
                this.show = false;
            }
         }"
         @confirm-dialog.window="openConfirm($event)"
         x-show="show" 
         x-cloak
         class="fixed inset-0 z-[10000] flex items-center justify-center p-4">
        
        {{-- Backdrop --}}
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="show = false"
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        {{-- Modal Content --}}
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-md rounded-xl p-6 shadow-2xl overflow-hidden"
             style="background:var(--bg-panel); border:1px solid var(--ink-line-2);">
            
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-serif font-semibold" :class="type === 'danger' ? 'text-red-500' : 'text-[var(--color-accent)]'" x-text="title"></h3>
                </div>
                <button @click="show = false" class="p-1 rounded-md transition-colors hover:bg-[var(--ink-line)]" style="color:var(--paper-dim);">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <p class="text-[13px] leading-relaxed mb-6" style="color:var(--paper-dim);" x-text="message"></p>
            
            <div class="flex justify-end gap-3">
                <button type="button" @click="show = false" 
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150 hover:bg-[var(--ink-line)]" 
                        style="background:var(--bg-input); color:var(--paper-dim);">
                    Batal
                </button>
                <button type="button" @click="confirm()" 
                        class="px-5 py-2 rounded-lg text-sm font-medium transition-all duration-150 shadow-lg text-white hover:opacity-90 hover:scale-[1.02] active:scale-[0.98]" 
                        :style="type === 'danger' ? 'background:var(--color-red); box-shadow:0 4px 12px rgba(220,38,38,0.3);' : 'background:var(--color-accent); box-shadow:0 4px 12px rgba(0,0,0,0.2);'"
                        x-text="confirmText">
                </button>
            </div>
        </div>
    </div>

    <script>
        window.showConfirm = function(title, message, callback, type = 'danger', confirmText = null) {
            window.dispatchEvent(new CustomEvent('confirm-dialog', {
                detail: { title, message, callback, type, confirmText }
            }));
        };
    </script>
    <script src="https://instant.page/5.2.0" type="module" integrity="sha384-jnZyxPjiipYXnSU0ygqeac2q7CVYMbh84q0uHVRRxm3jF1eB21e062bQ9G+XKXdO" crossorigin="anonymous"></script>
</body>
</html>
