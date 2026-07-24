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
    <meta name="description" content="Sistem Informasi Repositori Dokumentasi Multimedia HIMTI UIN Jakarta">
    <title>@yield('title', 'Login') — SIRDM HIMTI</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('logo-himti.png') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans">

    {{-- Theme toggle --}}
    <button onclick="toggleTheme()"
        class="fixed top-[18px] right-[22px] z-[999] w-[42px] h-[42px] rounded-full flex items-center justify-center cursor-pointer transition-all duration-200 hover:scale-[1.08]"
        style="border: 1px solid var(--ink-line-2); background: var(--bg-panel); box-shadow: 0 4px 14px -4px rgba(0,0,0,0.25);"
        aria-label="Ganti tema gelap/terang">
        {{-- Sun icon (dark mode) --}}
        <svg class="w-5 h-5 icon-sun transition-transform duration-300 hover:rotate-[20deg]" style="color:var(--color-accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="4.5" />
            <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
        </svg>
        {{-- Moon icon (light mode) --}}
        <svg class="w-5 h-5 icon-moon transition-transform duration-300 hover:rotate-[20deg] hidden" style="color:var(--color-accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
        </svg>
    </button>

    {{-- Toast container --}}
    <div id="toastContainer" class="fixed bottom-6 right-6 z-[9999] flex flex-col-reverse gap-2.5 pointer-events-none">
    </div>

    @yield('content')

    <style>
        [data-theme="dark"] .icon-moon { display: none; }
        [data-theme="dark"] .icon-sun { display: block; }
        [data-theme="light"] .icon-sun { display: none; }
        [data-theme="light"] .icon-moon { display: block; }
    </style>
</body>
</html>
