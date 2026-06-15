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
            z-index: 1030;
        }
        .footer {
            background-color: #1A1953 !important;
        }
        .header .logo img {
            max-height: 40px;
        }
        .header .fw-bold.text-white {
            color: #ffffff !important;
        }
        body {
            padding-top: 100px;
            background-color: #f5f7fb;
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
                        @php
                            $authRedirect = request()->routeIs('login', 'register') ? [] : ['redirect' => request()->fullUrl()];
                        @endphp
                        <div class="hstack gap-3">
                            <a href="{{ route('login', $authRedirect) }}" class="btn btn-outline-light btn-md fs-6 bg-white px-3 py-1 text-dark hstack justify-content-center">Sign In</a>
                            <a href="{{ route('register', $authRedirect) }}" class="btn btn-dark btn-md text-white fs-6 bg-dark px-3 py-1 hstack justify-content-center">Sign Up</a>
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
                                        <a href="{{ route('customer.promos') }}"
                                            class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                                            <iconify-icon icon="lucide:ticket-percent"
                                                class="text-secondary fs-5"></iconify-icon><span class="text-black">{{ auth()->check() ? 'Kode Promo Saya' : 'Kode Promo' }}</span>
                                        </a>
                                    </li>
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

    <style>
        .footer {
            background-color: #0b1426 !important;
            color: #ffffff;
            font-family: inherit;
        }
        .footer-bottom {
            background-color: #1a2235;
            padding: 16px 0;
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .footer-col-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        .footer-link {
            color: #aeb4c0 !important;
            font-size: 0.82rem;
            text-decoration: none;
            display: block;
            margin-bottom: 16px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: color 0.2s ease;
        }
        .footer-link:hover {
            color: #ffffff !important;
        }
        .footer-text {
            color: #ffffff;
            font-size: 0.85rem;
            line-height: 1.6;
            margin-top: 15px;
            font-weight: 500;
            max-width: 260px;
        }
        .footer-socials {
            display: flex;
            gap: 16px;
            margin-top: 10px;
        }
        .footer-socials a {
            color: #ffffff;
            font-size: 1.1rem;
            transition: opacity 0.2s;
        }
        .footer-socials a:hover {
            opacity: 0.7;
        }
        .footer-email {
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 28px;
        }
    </style>
    <footer class="footer pt-5">
        <div class="container pb-5">
            <div class="row">
              <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                  <a href="{{ route('landing-page') }}" class="footer-logo d-block mb-3">
                      <img src="{{asset("assets/images/logos/logo-white.svg")}}" alt="logo" height="36" style="max-height: 36px; object-fit: contain;">
                  </a>
                  <p class="footer-text">
                      Platform pemesanan tiket bioskop terbaik di Indonesia. Nikmati film favorit Anda dengan mudah dan nyaman bersama CineTix.
                  </p>
              </div>
              <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 pt-lg-2">
                  <a href="{{ route('landing-page') }}" class="footer-link">BERANDA</a>
                  <a href="{{ route('films.search') }}" class="footer-link">CARI FILM</a>
                  <a href="{{ route('customer.promos') }}" class="footer-link">KODE PROMO</a>
                  @if(auth()->check())
                  <a href="{{ route('booking.tickets') }}" class="footer-link">LIHAT TIKET</a>
                  @endif
              </div>
              <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 pt-lg-2">
                  <a href="{{ route('about') }}" class="footer-link">TENTANG KAMI</a>
                  <a href="{{ route('faq') }}" class="footer-link">FAQ & BANTUAN</a>
                  <a href="#!" class="footer-link">KEBIJAKAN PRIVASI</a>
                  <a href="#!" class="footer-link">SYARAT & KETENTUAN</a>
              </div>
              <div class="col-lg-3 col-md-6 pt-lg-2">
                  <div class="footer-col-title">DUKUNGAN CINETIX</div>
                  <div class="footer-email">E-MAIL: SUPPORT@CINETIX.COM</div>
                  
                  <div class="footer-col-title mb-2">HUBUNGI KAMI (WHATSAPP)</div>
                  <div>
                      <a href="https://wa.me/6289508101257" target="_blank" class="d-inline-flex align-items-center gap-2 text-white text-decoration-none mt-1" style="font-size: 0.95rem; font-weight: 500; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.7" onmouseout="this.style.opacity=1">
                          <iconify-icon icon="lucide:phone" style="font-size: 1.2rem;"></iconify-icon>
                          +62 895-0810-1257
                      </a>
                  </div>
              </div>
          </div>
        </div>
        <div class="footer-bottom">
            2026 CINETIX - PT NUSANTARA CINETIX. ALL RIGHTS RESERVED.
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