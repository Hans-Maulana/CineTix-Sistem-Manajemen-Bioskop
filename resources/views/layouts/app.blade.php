<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CineTix') }}</title>

    <link rel="shortcut icon" type="image/png" href="{{asset("assets/images/logos/favicon.svg")}}" />
    <link rel="stylesheet" href="{{asset("assets/libs/owl.carousel/dist/assets/owl.carousel.min.css")}}">
    <link rel="stylesheet" href="{{asset("assets/libs/aos-master/dist/aos.css")}}">
    <link rel="stylesheet" href="{{asset("assets/css/styles.css")}}" />

    <!-- Custom Styles for Detail Pages -->
    <style>
        .header {
            background: #1A1953 !important;
            padding: 15px 0 !important;
            transition: none !important;
        }
        .header .logo img {
            max-height: 40px;
        }
        .header .fw-bold.text-white {
            color: #ffffff !important;
        }
        body {
            padding-top: 100px;
            background-color: #f8f9fa;
        }
        .main-content {
            min-height: 80vh;
        }
        .header-link {
            color: #ffffff !important;
        }
        .header-link:hover {
            color: #ffffff !important;
            opacity: 0.8;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Header -->
    <header class="header border-4 border-primary border-top position-fixed start-0 top-0 w-100">
        <div class="container">
            <div class="header-wrapper d-flex align-items-center justify-content-between">
                <div class="logo">
                    <a href="{{ route('landing-page') }}">
                        <img src="{{asset("assets/images/logos/logo-white.svg")}}" alt="logo" class="img-fluid">
                    </a>
                </div>

                <div class="d-flex align-items-center gap-4">
                    @if (auth()->check())
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ auth()->user()->profile_photo_url ?? asset('assets/images/profile/avatar-1.png') }}"
                                alt="Profile" class="rounded-circle" width="32" height="32">
                            <span class="fw-bold text-white">{{ auth()->user()->name }}</span>
                        </div>
                    @else
                        <div class="hstack gap-3">
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-md fs-6 bg-white px-3 py-1 text-dark hstack justify-content-center">Sign In</a>
                            <a href="{{ route('register') }}" class="btn btn-dark btn-md text-white fs-6 bg-dark px-3 py-1 hstack justify-content-center">Sign Up</a>
                        </div>
                    @endif

                    <div class="btn-group">
                        <button
                            class="btn btn-secondary toggle-menu round-45 p-2 d-flex align-items-center justify-content-center bg-white rounded-circle border shadow-sm"
                            type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                            <iconify-icon icon="solar:hamburger-menu-line-duotone"
                                class="menu-icon fs-8 text-dark"></iconify-icon>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-4">
                            <div class="d-flex flex-column gap-3">
                                <div class="hstack justify-content-between border-bottom pb-2">
                                    <p class="mb-0 fs-5 text-dark fw-bold">Menu</p>
                                    <button type="button" class="btn-close opacity-75" aria-label="Close"></button>
                                </div>
                                <ul class="header-menu list-unstyled mb-0 d-flex flex-column gap-2">
                                    <li class="header-item">
                                        <a href="{{ route('landing-page') }}"
                                            class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                                            <iconify-icon icon="lucide:home"
                                                class="text-secondary fs-5"></iconify-icon> <span class="text-black">Beranda</span>
                                        </a>
                                    </li>
                                    
                                    @if(auth()->check())
                                        <li class="header-item">
                                            <a href="{{ route('booking.tickets') }}"
                                                class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                                                <iconify-icon icon="lucide:ticket"
                                                    class="text-secondary fs-5"></iconify-icon><span class="text-black">Lihat Tiket</span>
                                            </a>
                                        </li>
                                        <li class="header-item">
                                            <a href="{{ route('booking.history') }}"
                                                class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                                                <iconify-icon icon="lucide:history"
                                                    class="text-secondary fs-5"></iconify-icon><span class="text-black">Daftar Transaksi</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="header-item">
                                        <a href="{{ route('films.search') }}"
                                            class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                                            <iconify-icon icon="lucide:film"
                                                class="text-secondary fs-5"></iconify-icon><span class="text-black">Cari Film</span>
                                        </a>
                                    </li>
                                    <li class="header-item">
                                        <a href="{{ route('faq') }}"
                                            class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                                            <iconify-icon icon="lucide:help-circle"
                                                class="text-secondary fs-5"></iconify-icon><span class="text-black">FAQ </span>
                                        </a>
                                    </li>
                                    <li class="header-item">
                                        <a href="{{ route('about') }}"
                                            class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                                            <iconify-icon icon="lucide:info"
                                                class="text-secondary fs-5"></iconify-icon><span class="text-black">Tentang Kami</span>  
                                        </a>
                                    </li>
                                    @if(auth()->check())
                                        <li class="header-item border-top pt-2 mt-2">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit"
                                                    class="header-link hstack gap-2 fs-6 fw-bold text-danger border-0 bg-transparent w-100 text-start">
                                                    <iconify-icon icon="lucide:log-out" class="fs-5 text-dark"></iconify-icon><span class="text-black">Keluar</span>
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        @yield('content')
    </main>

    <footer class="footer bg-dark py-10">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-4 mb-8 mb-xl-0">
                    <a href="{{ route('landing-page') }}" class="footer-logo">
                        <img src="{{asset("assets/images/logos/logo-white.svg")}}" alt="logo" class="img-fluid">
                    </a>
                    <p class="text-white text-opacity-70 mt-4">Platform pemesanan tiket bioskop terbaik di Indonesia.
                        Nikmati film favorit Anda dengan mudah dan nyaman.</p>
                </div>
                <div class="col-md-4 col-xl-2 offset-xl-1 mb-8 mb-xl-0">
                    <ul class="footer-menu list-unstyled mb-0 d-flex flex-column gap-2">
                        <li><a class="link-hover fs-5 text-white" href="{{ route('landing-page') }}">Beranda</a></li>
                        <li><a class="link-hover fs-5 text-white" href="{{ route('films.search') }}">Film</a></li>
                        <li><a class="link-hover fs-5 text-white" href="#!">Promo</a></li>
                    </ul>
                </div>
                <div class="col-md-4 col-xl-2 mb-8 mb-xl-0">
                    <ul class="footer-menu list-unstyled mb-0 d-flex flex-column gap-2">
                        <li><a class="link-hover fs-5 text-white" href="#!">Facebook</a></li>
                        <li><a class="link-hover fs-5 text-white" href="#!">Instagram</a></li>
                        <li><a class="link-hover fs-5 text-white" href="#!">Twitter</a></li>
                    </ul>
                </div>
                <div class="col-md-4 col-xl-3 mb-8 mb-xl-0">
                    <p class="mb-0 text-white text-opacity-70 text-md-end">© CineTix copyright 2025</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{asset("assets/libs/jquery/dist/jquery.min.js")}}"></script>
    <script src="{{asset("assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js")}}"></script>
    <script src="{{asset("assets/libs/owl.carousel/dist/owl.carousel.min.js")}}"></script>
    <script src="{{asset("assets/libs/aos-master/dist/aos.js")}}"></script>
    <script src="{{asset("assets/js/custom.js")}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true
                });
            }
        });
    </script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    @stack('scripts')
</body>

</html>