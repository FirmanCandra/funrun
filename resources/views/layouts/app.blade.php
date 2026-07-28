<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SeTiket')</title>
    <link rel="icon" type="image/webp" href="{{ asset('images/setiket.webp') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }

        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .gradient-text {
            background: linear-gradient(to right, #38bdf8, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="antialiased min-h-screen flex flex-col relative overflow-x-hidden">
    <!-- Background Elements -->
    <div class="fixed inset-0 z-[-1] pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-600/20 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-purple-600/20 blur-[120px]">
        </div>
    </div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-panel border-b-0 border-white/5 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer"
                    onclick="window.location.href='{{ route('home') }}'">
                    <img src="{{ asset('images/setiket.webp') }}" alt="SeTiket Logo"
                        style="height: 125px; width: auto; display: block;" class="transition-all duration-300">
                </div>

                <!-- Search Panel -->
                <div class="flex-1 max-w-[140px] sm:max-w-xs mx-2 sm:mx-8 relative">
                    <input type="text" id="navbarSearch" placeholder="Cari event..."
                        class="w-full bg-slate-800/60 text-slate-200 placeholder-slate-400 text-xs sm:text-sm rounded-full pl-8 sm:pl-10 pr-3 py-1.5 sm:py-2 border border-white/10 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-transparent transition-all">
                    <div class="absolute left-2.5 sm:left-3.5 top-[9px] sm:top-[11px] text-slate-500 leading-none">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="flex items-center space-x-4 sm:space-x-8">
                    <a href="{{ route('home') }}"
                        class="text-gray-300 hover:text-white transition-colors font-medium text-xs sm:text-base">Home</a>
                    <a href="{{ route('home') }}#upcoming-events"
                        class="text-gray-300 hover:text-white transition-colors font-medium text-xs sm:text-base">Event</a>
                    <a href="https://wa.me/6289681201941" target="_blank"
                        class="text-gray-300 hover:text-white transition-colors font-medium text-xs sm:text-base">Helpdesk</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="glass-panel border-t border-white/10 mt-20 py-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center gap-2 mb-4 md:mb-0">
                    <img src="{{ asset('images/setiket.webp') }}" alt="SeTiket Logo"
                        style="height: 125px; width: auto; display: block;">
                </div>
                <div class="text-gray-400 text-sm">
                    &copy; 2026 SeTiket. All rights reserved.
                </div>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Instagram</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Twitter</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-lg', 'bg-slate-900/80');
            } else {
                nav.classList.remove('shadow-lg', 'bg-slate-900/80');
            }
        });

        // Dynamic Event Search & Filtering
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('navbarSearch');

            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const query = e.target.value.toLowerCase().trim();
                    const cards = document.querySelectorAll('.event-card');

                    cards.forEach(card => {
                        const title = card.getAttribute('data-nama') || '';
                        if (title.includes(query)) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });

                // If user is on a different page, pressing Enter redirects to homepage with search query
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        if (window.location.pathname !== '/') {
                            window.location.href = '/?q=' + encodeURIComponent(this.value);
                        }
                    }
                });

                // Read query param on homepage load
                if (window.location.pathname === '/') {
                    const urlParams = new URLSearchParams(window.location.search);
                    const q = urlParams.get('q');
                    if (q) {
                        searchInput.value = q;
                        // Trigger input event
                        setTimeout(() => {
                            searchInput.dispatchEvent(new Event('input'));
                        }, 150);
                    }
                }
            }
        });
    </script>
</body>

</html>