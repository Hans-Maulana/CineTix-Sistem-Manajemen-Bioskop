<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - CineTix Admin</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.svg') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #1A1953;
            --accent-color: #d4b06a;
            --bg-light: #f4f7f6;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            color: #333;
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
            background-color: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 10px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .menu-link {
            text-decoration: none;
            color: #555;
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
            background-color: rgba(26, 25, 83, 0.1);
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
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e0e0e0;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(26, 25, 83, 0.1);
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
                <a href="{{ route('admin.tickets.index') }}"
                    class="menu-link {{ request()->segment(2) == 'tickets' ? 'active' : '' }}">Tiket & Scan</a>
                <a href="{{ route('admin.reports.index') }}"
                    class="menu-link {{ request()->segment(2) == 'reports' ? 'active' : '' }}">Laporan</a>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>

</html>