<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - CineTix Admin</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.svg') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #1A1953;
            --accent-color: #d4b06a;
            --bg-light: #f5f7fb;
            --card-bg: #fafafb;
            --text-color: #384252;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: var(--text-color);
            overflow: auto !important;
            pointer-events: auto !important;
        }

        /* Prevent modal backdrop from locking the screen */
        .modal-backdrop, .offcanvas-backdrop {
            display: none !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        .topbar {
            background-color: var(--primary-color);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .logo-text {
            color: var(--accent-color);
            font-size: 28px;
            font-weight: 800;
            text-decoration: none;
            letter-spacing: 1px;
        }

        .menu-bar {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e9f0;
            padding: 10px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .menu-link {
            text-decoration: none;
            color: #5d6778;
            font-size: 15px;
            font-weight: 600;
            margin-right: 25px;
            transition: 0.3s;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .menu-link:hover,
        .menu-link.active {
            color: var(--primary-color);
            background-color: rgba(26, 25, 83, 0.08);
        }

        .btn-teal {
            background-color: var(--primary-color);
            color: white;
            border-radius: 12px;
            padding: 10px 20px;
            border: none;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-teal:hover {
            background-color: #131240;
            color: white;
            box-shadow: 0 4px 15px rgba(26, 25, 83, 0.3);
        }

        .card-custom {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(26, 25, 83, 0.03);
            border: 1px solid rgba(26, 25, 83, 0.06);
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #dcdfe6;
            background-color: #ffffff;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(26, 25, 83, 0.08);
        }

        /* Modern Genre Tags Styles */
        .genre-tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 15px;
            background-color: #ffffff;
            border: 1px solid #dcdfe6;
            border-radius: 12px;
            max-height: 220px;
            overflow-y: auto;
        }

        .genre-tag-item {
            position: relative;
        }

        .genre-checkbox-hidden {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .genre-tag-label {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 50px;
            background-color: #f1f3f9;
            color: #495057;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #dcdfe6;
            user-select: none;
        }

        .genre-checkbox-hidden:checked + .genre-tag-label {
            background-color: var(--primary-color);
            color: #ffffff;
            border-color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(26, 25, 83, 0.2);
        }

        .genre-tag-label:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="topbar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.dashboard') }}" class="logo-text">
                CineTix Admin
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white small">Hi, {{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light rounded-pill px-3">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <div class="menu-bar">
        <div class="container">
            <nav class="d-flex align-items-center">
                <a href="{{ route('admin.dashboard') }}"
                    class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.films.index') }}"
                    class="menu-link {{ request()->segment(2) == 'films' ? 'active' : '' }}">Film</a>
                <a href="{{ route('admin.studios.index') }}"
                    class="menu-link {{ request()->segment(2) == 'studios' ? 'active' : '' }}">Studio</a>
                <a href="{{ route('admin.schedules.index') }}"
                    class="menu-link {{ request()->segment(2) == 'schedules' ? 'active' : '' }}">Schedule</a>
                <a href="{{ route('admin.promos.index') }}"
                    class="menu-link {{ request()->segment(2) == 'promos' ? 'active' : '' }}">Promo</a>
                <a href="{{ route('admin.bookings.index') }}"
                    class="menu-link {{ request()->segment(2) == 'bookings' ? 'active' : '' }}">Booking</a>
                <a href="{{ route('admin.customers.index') }}"
                    class="menu-link {{ request()->segment(2) == 'customers' ? 'active' : '' }}">Customer</a>
            </nav>
        </div>
    </div>

    <div class="container py-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function clearOverlays() {
                // Pastikan body dan html dapat di-scroll dan di-klik
                document.body.style.setProperty('pointer-events', 'auto', 'important');
                document.body.style.setProperty('overflow', 'auto', 'important');
                document.documentElement.style.setProperty('pointer-events', 'auto', 'important');
                document.documentElement.style.setProperty('overflow', 'auto', 'important');
                
                // Hapus class modal-open bawaan Bootstrap pada body jika ada
                document.body.classList.remove('modal-open');
                document.body.classList.remove('offcanvas-open');
                
                // Cari dan hapus semua elemen modal-backdrop atau offcanvas-backdrop
                const backdrops = document.querySelectorAll('.modal-backdrop, .offcanvas-backdrop');
                backdrops.forEach(el => el.remove());

                // Cari elemen fixed/absolute liar yang menutupi layar dan matikan pointer-eventsnya
                const allElements = document.getElementsByTagName('*');
                for (let el of allElements) {
                    const style = window.getComputedStyle(el);
                    if ((style.position === 'fixed' || style.position === 'absolute') && 
                        el.id !== 'app' && 
                        el.tagName !== 'BODY' && 
                        el.tagName !== 'HTML') {
                        
                        const zIndex = parseInt(style.zIndex, 10);
                        const width = el.offsetWidth;
                        const height = el.offsetHeight;
                        
                        // Jika elemen menutupi hampir seluruh layar (width/height > 90%) dan z-index tinggi, sembunyikan
                        if (width > window.innerWidth * 0.9 && height > window.innerHeight * 0.9 && zIndex > 5) {
                            el.style.setProperty('display', 'none', 'important');
                            el.style.setProperty('pointer-events', 'none', 'important');
                            el.style.setProperty('z-index', '-9999', 'important');
                        }
                    }
                }
            }

            // Jalankan beberapa kali untuk mengantisipasi load dinamis
            clearOverlays();
            setTimeout(clearOverlays, 300);
            setTimeout(clearOverlays, 800);
            setTimeout(clearOverlays, 1500);
            setInterval(clearOverlays, 2500);
        });
    </script>
</body>

</html>