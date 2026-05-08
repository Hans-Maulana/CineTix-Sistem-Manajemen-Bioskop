# resources/views/admin/dashboard.blade.php

```php
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Studiova</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.svg') }}" />

    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/dist/css/bootstrap.min.css') }}">

    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, Helvetica, sans-serif;
        }

        .topbar {
            background-color: #006b68;
            padding: 18px 0;
        }

        .logo-text {
            color: #d4b06a;
            font-size: 42px;
            font-weight: bold;
            text-decoration: none;
        }

        .menu-bar {
            background-color: #efefef;
            border-bottom: 1px solid #ddd;
            padding: 14px 0;
        }

        .menu-link {
            text-decoration: none;
            color: #006b68;
            font-size: 17px;
            font-weight: 500;
            margin-right: 28px;
        }

        .search-box {
            border-radius: 30px;
            border: none;
            padding: 12px 20px;
            width: 320px;
            font-size: 14px;
        }

        .dashboard-title {
            color: #006b68;
            font-weight: bold;
            font-size: 34px;
        }

        .movie-card {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            transition: 0.3s;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
            background: white;
        }

        .movie-card:hover {
            transform: translateY(-5px);
        }

        .movie-img {
            height: 420px;
            object-fit: cover;
        }

        .movie-title {
            font-size: 22px;
            font-weight: bold;
            color: #222;
        }

        .movie-category {
            color: #777;
            font-size: 14px;
        }

        .btn-teal {
            background-color: #006b68;
            color: white;
            border-radius: 10px;
            padding: 10px 18px;
            border: none;
            font-weight: 600;
        }

        .btn-teal:hover {
            background-color: #00514f;
            color: white;
        }

        .btn-danger-custom {
            background-color: #b42318;
            color: white;
            border-radius: 10px;
            padding: 10px 18px;
            border: none;
            font-weight: 600;
        }

        .stats-card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        .stats-title {
            color: #777;
            font-size: 16px;
        }

        .stats-number {
            font-size: 34px;
            font-weight: bold;
            color: #006b68;
        }
    </style>
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="container d-flex justify-content-between align-items-center">

            <a href="#" class="logo-text">
                Cinema XXI
            </a>

            <input type="text" class="search-box" placeholder="Search movie, theater...">

        </div>
    </div>

    <!-- MENU -->
    <div class="menu-bar">
        <div class="container d-flex align-items-center">
            <a href="#" class="menu-link">Dashboard</a>
            <a href="#" class="menu-link">Film</a>
            <a href="#" class="menu-link">Studio</a>
            <a href="#" class="menu-link">Schedule</a>
            <a href="#" class="menu-link">Booking</a>
            <a href="#" class="menu-link">Customer</a>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="dashboard-title">Dashboard Admin</h1>
                <p class="text-muted">Selamat datang Admin Studiova</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="btn-danger-custom">
                    Logout
                </button>
            </form>
        </div>

        <!-- STATS -->
        <div class="row mb-5">

            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-title">Total Film</div>
                    <div class="stats-number">24</div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-title">Total Booking</div>
                    <div class="stats-number">120</div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-title">Total Customer</div>
                    <div class="stats-number">85</div>
                </div>
            </div>

        </div>

        <!-- MOVIES -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Now Playing</h3>

            <button class="btn-teal">
                + Tambah Film
            </button>
        </div>

        <div class="row">

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="movie-card">
                    <img src="https://upload.wikimedia.org/wikipedia/en/7/7f/Minecraft_film_poster.jpg"
                        class="w-100 movie-img">

                    <div class="p-3">
                        <div class="movie-title">Minecraft</div>
                        <div class="movie-category">Adventure • Fantasy</div>

                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-warning w-100">
                                Edit
                            </button>

                            <button class="btn btn-danger w-100">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="movie-card">
                    <img src="https://upload.wikimedia.org/wikipedia/en/a/a2/How_to_Train_Your_Dragon_%282025_film%29_poster.jpg"
                        class="w-100 movie-img">

                    <div class="p-3">
                        <div class="movie-title">How To Train Dragon</div>
                        <div class="movie-category">Animation • Adventure</div>

                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-warning w-100">
                                Edit
                            </button>

                            <button class="btn btn-danger w-100">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="movie-card">
                    <img src="https://upload.wikimedia.org/wikipedia/en/8/8d/Lilo_%26_Stitch_2025_film_poster.jpg"
                        class="w-100 movie-img">

                    <div class="p-3">
                        <div class="movie-title">Lilo & Stitch</div>
                        <div class="movie-category">Family • Comedy</div>

                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-warning w-100">
                                Edit
                            </button>

                            <button class="btn btn-danger w-100">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="movie-card">
                    <img src="https://upload.wikimedia.org/wikipedia/en/f/fb/Final_Destination_Bloodlines_Poster.jpg"
                        class="w-100 movie-img">

                    <div class="p-3">
                        <div class="movie-title">Final Destination</div>
                        <div class="movie-category">Horror • Thriller</div>

                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-warning w-100">
                                Edit
                            </button>

                            <button class="btn btn-danger w-100">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
