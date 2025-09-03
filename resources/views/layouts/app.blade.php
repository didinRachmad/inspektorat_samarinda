<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">

    <!-- DNS-prefetch untuk font -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Preconnect & preload fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- preload Noto Sans -->
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap"
        onload="this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap">
    </noscript>

    <!-- preload Material Icons -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined"
        onload="this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined">
    </noscript>

    <!-- INLINE: sembunyikan dulu semua konten -->
    <style>
        html {
            visibility: hidden;
        }

        /* custom spinner alternatif */
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeMap = {
                BlueTheme: "blue-theme",
                LightTheme: "light",
                DarkTheme: "dark",
                SemiDarkTheme: "semi-dark",
                BoderedTheme: "bodered-theme",
            };
            const saved = localStorage.getItem('selectedTheme') || 'BlueTheme';
            document.documentElement.setAttribute('data-bs-theme', themeMap[saved]);
            document.documentElement.style.visibility = 'visible';
        });
        window.addEventListener('load', () => {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                setTimeout(() => {
                    overlay.classList.add('fade-out');
                    setTimeout(() => overlay.style.display = 'none', 500); // tunggu animasi selesai
                }, 300);
            }
        });
    </script>

    <!-- Vite otomatis inject CSS & JS hasil build -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>




<body data-page="{{ $page ?? 'default' }}" data-action="{{ $action ?? 'index' }}">
    <div id="loading-overlay"
        style="
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: var(--bs-body-bg);
">
        <div class="spinner text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    @yield('content')
</body>

@if (session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            showToast("{{ session('success') }}", "success");
        });
    </script>
@endif

@if (session('info'))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            showToast("{{ session('info') }}", "info");
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            showToast("{{ session('error') }}", "error");
        });
    </script>
@endif

@if ($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            @foreach ($errors->all() as $error)
                showToast("{{ $error }}", "error");
            @endforeach
        });
    </script>
@endif

</html>
