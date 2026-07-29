<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SeTiket — Temukan & Pesan Tiket Event')</title>
    <link rel="icon" type="image/webp" href="{{ asset('images/setiket.webp') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap"
        rel="stylesheet">
</head>

<body class="min-h-screen flex flex-col bg-canvas text-ink-900">

    {{-- ===== NAVBAR ===== --}}
    <header class="sticky top-0 z-50 bg-white border-b border-line" style="box-shadow: var(--shadow-nav);">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4 lg:gap-8 h-[72px]">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    <img src="{{ asset('images/setiketbg.webp') }}" alt="SeTiket" class="h-9 w-auto"
                        onerror="this.style.display='none'">
                    <span class="font-display font-bold text-lg tracking-tight text-ink-900 hidden sm:inline">SeTiket</span>
                </a>

                {{-- Pencarian di tengah --}}
                <div class="flex-1 max-w-xl mx-auto hidden md:block">
                    <div class="search-pill flex items-center gap-2 px-4 py-2">
                        <svg class="w-4 h-4 text-ink-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                        </svg>
                        <input type="text" id="navbarSearch" placeholder="Cari event, lokasi, atau kategori…"
                            class="w-full bg-transparent text-sm text-ink-900 placeholder-gray-400 focus:outline-none"
                            autocomplete="off">
                    </div>
                </div>

                {{-- Navigasi --}}
                <div class="flex items-center gap-1 lg:gap-2 ml-auto">
                    <a href="{{ route('home') }}"
                        class="hidden lg:inline-flex px-3 py-2 rounded-btn text-sm font-medium text-ink-500 hover:text-ink-900 hover:bg-gray-50 transition-colors">Beranda</a>
                    <a href="{{ route('home') }}#events"
                        class="hidden lg:inline-flex px-3 py-2 rounded-btn text-sm font-medium text-ink-500 hover:text-ink-900 hover:bg-gray-50 transition-colors">Event</a>
                    <a href="https://wa.me/6289681201941" target="_blank" rel="noopener"
                        class="hidden lg:inline-flex px-3 py-2 rounded-btn text-sm font-medium text-ink-500 hover:text-ink-900 hover:bg-gray-50 transition-colors">Pusat Bantuan</a>

                    @auth
                        @if(auth()->user()->isUser())
                            <a href="{{ route('tickets') }}"
                                class="px-3 py-2 rounded-btn text-sm font-semibold text-brand-600 hover:bg-brand-50 transition-colors whitespace-nowrap">Tiket Saya</a>
                        @else
                            <a href="{{ route('admin.dashboard') }}"
                                class="px-3 py-2 rounded-btn text-sm font-semibold text-brand-600 hover:bg-brand-50 transition-colors whitespace-nowrap">Panel Admin</a>
                        @endif

                        {{-- Menu profil --}}
                        <div class="relative ml-1" id="profileMenu">
                            <button type="button" id="profileTrigger"
                                class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-full border border-line hover:bg-gray-50 transition-colors"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="w-8 h-8 shrink-0 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <svg class="w-4 h-4 text-ink-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                </svg>
                            </button>

                            <div id="profileDropdown"
                                class="hidden absolute right-0 mt-2 w-60 card p-2 origin-top-right animate-fade-in">
                                <div class="px-3 py-2.5 border-b border-line mb-1">
                                    <p class="text-sm font-semibold text-ink-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-ink-500 truncate">{{ auth()->user()->email }}</p>
                                </div>

                                @if(auth()->user()->isUser())
                                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-btn text-sm text-ink-900 hover:bg-gray-50 transition-colors">Pesanan Saya</a>
                                    <a href="{{ route('tickets') }}" class="block px-3 py-2 rounded-btn text-sm text-ink-900 hover:bg-gray-50 transition-colors">Tiket Saya</a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-btn text-sm text-ink-900 hover:bg-gray-50 transition-colors">Profil</a>

                                <form action="{{ route('logout') }}" method="POST" class="border-t border-line mt-1 pt-1">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-3 py-2 rounded-btn text-sm text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="btn btn-ghost px-3 py-2 text-sm">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="btn btn-primary px-4 py-2 text-sm">Daftar</a>
                    @endauth
                </div>
            </div>

            {{-- Pencarian versi mobile --}}
            <div class="md:hidden pb-3">
                <div class="search-pill flex items-center gap-2 px-4 py-2">
                    <svg class="w-4 h-4 text-ink-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
                    </svg>
                    <input type="text" id="navbarSearchMobile" placeholder="Cari event…"
                        class="w-full bg-transparent text-sm text-ink-900 placeholder-gray-400 focus:outline-none" autocomplete="off">
                </div>
            </div>
        </nav>
    </header>

    {{-- ===== KONTEN ===== --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-ink-900 text-gray-400 mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-10">

                <div class="col-span-2 lg:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <img src="{{ asset('images/setiket.webp') }}" alt="SeTiket" class="h-10 w-auto"
                            onerror="this.style.display='none'">
                        <span class="font-display font-bold text-lg text-white">SeTiket</span>
                    </div>
                    <p class="text-sm leading-relaxed max-w-xs">
                        Temukan dan pesan tiket event favorit Anda — fun run, festival, seminar, dan pertunjukan —
                        dalam beberapa langkah saja.
                    </p>
                </div>

                <div>
                    <h4 class="text-white font-semibold text-sm mb-4">Jelajahi</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('home') }}#events" class="hover:text-white transition-colors">Semua Event</a></li>
                        <li><a href="{{ route('home') }}#featured" class="hover:text-white transition-colors">Event Pilihan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold text-sm mb-4">Akun</h4>
                    <ul class="space-y-2.5 text-sm">
                        @auth
                            @if(auth()->user()->isUser())
                                <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Pesanan Saya</a></li>
                                <li><a href="{{ route('tickets') }}" class="hover:text-white transition-colors">Tiket Saya</a></li>
                            @endif
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-white transition-colors">Profil</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar</a></li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold text-sm mb-4">Bantuan</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="https://wa.me/6289681201941" target="_blank" rel="noopener" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                        <li><a href="https://wa.me/6289681201941" target="_blank" rel="noopener" class="hover:text-white transition-colors">Pusat Bantuan</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-5">
                <p class="text-sm">&copy; {{ date('Y') }} SeTiket. Seluruh hak cipta dilindungi.</p>

                <div class="flex items-center gap-3">
                    @foreach([
                        ['label' => 'Instagram', 'path' => 'M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.2 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.2-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.2-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4C8.4 2.2 8.8 2.2 12 2.2Zm0 5.6a4.2 4.2 0 1 0 0 8.4 4.2 4.2 0 0 0 0-8.4Zm0 6.9a2.7 2.7 0 1 1 0-5.4 2.7 2.7 0 0 1 0 5.4Zm5.4-7.1a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z'],
                        ['label' => 'Twitter', 'path' => 'M18.9 3H22l-6.9 7.9L23 21h-6.4l-5-6.5L5.8 21H2.7l7.4-8.5L1.5 3H8l4.5 6 6.4-6Zm-1.1 16.1h1.7L7.3 4.8H5.5l12.3 14.3Z'],
                        ['label' => 'Facebook', 'path' => 'M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.7l-.4 2.9h-2.3v7A10 10 0 0 0 22 12Z'],
                    ] as $social)
                        <a href="#" aria-label="{{ $social['label'] }}"
                            class="w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="{{ $social['path'] }}" /></svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Menu profil
        (function () {
            const trigger = document.getElementById('profileTrigger');
            const dropdown = document.getElementById('profileDropdown');
            if (!trigger || !dropdown) return;

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                const open = !dropdown.classList.contains('hidden');
                dropdown.classList.toggle('hidden', open);
                trigger.setAttribute('aria-expanded', String(!open));
            });

            document.addEventListener('click', function () {
                dropdown.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') dropdown.classList.add('hidden');
            });
        })();

        // Pencarian event — menyaring kartu di beranda, atau melompat ke beranda
        // dengan kata kunci kalau sedang di halaman lain.
        (function () {
            const inputs = [document.getElementById('navbarSearch'), document.getElementById('navbarSearchMobile')].filter(Boolean);
            if (!inputs.length) return;

            function filter(query) {
                const q = query.toLowerCase().trim();
                let visible = 0;

                document.querySelectorAll('.event-card').forEach(card => {
                    const haystack = (card.getAttribute('data-search') || '').toLowerCase();
                    const match = haystack.includes(q);
                    card.style.display = match ? '' : 'none';
                    if (match) visible++;
                });

                const empty = document.getElementById('searchEmpty');
                if (empty) empty.classList.toggle('hidden', visible > 0 || q === '');
            }

            inputs.forEach(input => {
                input.addEventListener('input', function (e) {
                    if (document.querySelector('.event-card')) {
                        filter(e.target.value);
                        inputs.forEach(other => { if (other !== e.target) other.value = e.target.value; });
                    }
                });

                input.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter' && !document.querySelector('.event-card')) {
                        window.location.href = '{{ route('home') }}?q=' + encodeURIComponent(this.value);
                    }
                });
            });

            // Terapkan kata kunci dari URL saat beranda dimuat.
            const q = new URLSearchParams(window.location.search).get('q');
            if (q && document.querySelector('.event-card')) {
                inputs.forEach(i => { i.value = q; });
                filter(q);
                document.getElementById('events')?.scrollIntoView({ behavior: 'smooth' });
            }
        })();
    </script>

    @stack('scripts')
</body>

</html>
