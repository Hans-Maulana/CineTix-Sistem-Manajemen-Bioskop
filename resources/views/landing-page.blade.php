<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CineTix</title>
  <link rel="shortcut icon" type="image/png" href="{{asset('assets/images/logos/favicon.svg')}}" />
  <link rel="stylesheet" href="{{asset('assets/libs/owl.carousel/dist/assets/owl.carousel.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/libs/aos-master/dist/aos.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/styles.css')}}" />
  @include('partials.customer_film_styles')
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
      background-color: #f5f7fb !important;
    }

    .banner-section {
      background-color: #0c0b24 !important;
    }

    .banner-video-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 0;
    }

    .banner-video-iframe {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 100vw;
      height: 56.25vw;
      min-height: 100vh;
      min-width: 177.77vh;
      transform: translate(-50%, -50%);
      pointer-events: none;
      border: none;
      opacity: 0;
      animation: videoFadeIn 1.2s ease-in forwards;
      animation-delay: 2.5s;
    }

    @keyframes videoFadeIn {
      to { opacity: 1; }
    }

    .banner-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, rgba(12, 11, 36, 0.1) 0%, rgba(12, 11, 36, 0.55) 100%);
      z-index: 1;
    }
  </style>
</head>

<body>

  <header class="header border-4 border-primary border-top position-fixed start-0 top-0 w-100">
    <div class="container">
      <div class="header-wrapper d-flex align-items-center justify-content-between">
        <div class="logo">
          <a href="{{ route('landing-page') }}">
            <img src="{{asset('assets/images/logos/logo-white.svg')}}" alt="logo" class="img-fluid"
              style="max-height: 40px;">
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
              <a href="{{ route('login') }}"
                class="btn btn-outline-light btn-md fs-6 bg-white px-3 py-1 text-dark hstack justify-content-center">Sign
                In</a>
              <a href="{{ route('register') }}"
                class="btn btn-dark btn-md text-white fs-6 bg-dark px-3 py-1 hstack justify-content-center">Sign Up</a>
            </div>
          @endif

          <div class="btn-group">
            <button
              class="btn btn-secondary toggle-menu round-45 p-2 d-flex align-items-center justify-content-center bg-white rounded-circle border shadow-sm"
              type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
              <iconify-icon icon="solar:hamburger-menu-line-duotone" class="menu-icon fs-8 text-dark"></iconify-icon>
            </button>
            <ul class="dropdown-menu dropdown-menu-end p-4">
              <div class="d-flex flex-column gap-3">
                <div class="hstack justify-content-between border-bottom pb-2">
                  <p class="mb-0 fs-5 text-dark fw-bold">Menu</p>
                  <button type="button" class="btn-close opacity-75" aria-label="Close"></button>
                </div>
                <ul class="header-menu list-unstyled mb-0 d-flex flex-column gap-2">
                  <li class="header-item">
                    <a href="{{ route('landing-page') }}" class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                      <iconify-icon icon="lucide:home" class="text-secondary fs-5"></iconify-icon>Beranda
                    </a>
                  </li>
                  @if(auth()->check())
                    <li class="header-item">
                      <a href="{{ route('booking.tickets') }}" class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                        <iconify-icon icon="lucide:ticket" class="text-secondary fs-5"></iconify-icon>Lihat Tiket
                      </a>
                    </li>
                    <li class="header-item">
                      <a href="{{ route('booking.history') }}" class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                        <iconify-icon icon="lucide:history" class="text-secondary fs-5"></iconify-icon>Daftar Transaksi
                      </a>
                    </li>
                  @endif
                  <li class="header-item">
                    <a href="{{ route('customer.promos') }}" class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                      <iconify-icon icon="lucide:ticket-percent" class="text-secondary fs-5"></iconify-icon>{{ auth()->check() ? 'Kode Promo Saya' : 'Kode Promo' }}
                    </a>
                  </li>
                  <li class="header-item">
                    <a href="{{ route('films.search') }}" class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                      <iconify-icon icon="lucide:film" class="text-secondary fs-5"></iconify-icon>Cari Film
                    </a>
                  </li>
                  <li class="header-item">
                    <a href="{{ route('faq') }}" class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                      <iconify-icon icon="lucide:help-circle" class="text-secondary fs-5"></iconify-icon>FAQ
                    </a>
                  </li>
                  <li class="header-item">
                    <a href="{{ route('about') }}" class="header-link hstack gap-2 fs-6 fw-bold text-dark">
                      <iconify-icon icon="lucide:info" class="text-secondary fs-5"></iconify-icon>Tentang Kami
                    </a>
                  </li>
                  @if(auth()->check())
                    <li class="header-item border-top pt-2 mt-2">
                      <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                          class="header-link hstack gap-2 fs-6 fw-bold text-danger border-0 bg-transparent w-100 text-start">
                          <iconify-icon icon="lucide:log-out" class="fs-5"></iconify-icon>Keluar
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

  <div class="page-wrapper overflow-hidden">

    <section class="banner-section position-relative d-flex align-items-end min-vh-100">
      <div class="banner-video-container">
        <iframe class="banner-video-iframe" 
                src="https://www.youtube.com/embed/yMqDgbZmBdk?autoplay=1&mute=1&loop=1&playlist=yMqDgbZmBdk&controls=0&showinfo=0&rel=0&iv_load_policy=3&playsinline=1&enablejsapi=1&vq=hd1080" 
                allow="autoplay; encrypted-media" 
                allowfullscreen>
        </iframe>
        <div class="banner-overlay"></div>
      </div>
      <div class="container">
        <div class="d-flex flex-column gap-4 pb-8 position-relative z-1">
          <div class="row align-items-center">
            <div class="col-xl-4">
              <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100"
                data-aos-duration="1000">
                <iconify-icon icon="lucide:film" class="text-primary" style="font-size: 2rem;"></iconify-icon>
                <p class="mb-0 text-white fs-5 text-opacity-70">Nikmati pengalaman sinema <span
                    class="text-secondary">terbaik</span>. Pesan tiket Anda dengan mudah dan cepat bersama CineTix.</p>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-end gap-3" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
            <h1 class="mb-0 fs-8 text-white lh-1">CineTix</h1>
            <a href="{{ route('films.search') }}" class="p-1 ps-7 bg-primary rounded-pill">
              <span class="bg-white round-52 rounded-circle d-flex align-items-center justify-content-center">
                <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
              </span>
            </a>
          </div>
        </div>
      </div>
    </section>

    {{-- Top 5 Film Populer --}}
    <section class="cx-section">
      <div class="container">
        <div class="cx-hero-panel" data-aos="fade-up">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
              <span class="cx-hero-eyebrow">
                <iconify-icon icon="lucide:flame"></iconify-icon> Top 5 Minggu Ini
              </span>
              <h2 class="mb-1">Film Paling Populer</h2>
              <p>Berdasarkan jumlah tiket terjual minggu ini — pilih favoritmu dan pesan sekarang.</p>
            </div>
            <a href="{{ route('films.search') }}" class="btn cx-hero-btn rounded-pill px-4">
              Lihat Semua Film
            </a>
          </div>
        </div>

        <div class="cx-film-grid cx-top-grid">
          @foreach($topFilms as $index => $film)
            @include('partials.customer_film_card', [
              'film' => $film,
              'rank' => $index + 1,
              'ticketsSold' => $film->tickets_sold ?? 0,
            ])
          @endforeach
        </div>
      </div>
    </section>

    {{-- Sedang Tayang --}}
    <section class="cx-section cx-section-alt">
      <div class="container">
        <div class="cx-section-header d-flex flex-wrap justify-content-between align-items-end gap-3" data-aos="fade-up">
          <div>
            <span class="cx-section-eyebrow">
              <iconify-icon icon="lucide:play-circle"></iconify-icon> Sedang Tayang
            </span>
            <h2 class="cx-section-title">Jadwal Film Hari Ini</h2>
            <p class="cx-section-desc">Geser ke samping untuk lihat film lainnya — tidak perlu scroll panjang ke bawah.</p>
          </div>
        </div>

        <div class="cx-filter-bar" data-aos="fade-up" data-aos-delay="100">
          <span class="cx-filter-label">
            <iconify-icon icon="lucide:sliders-horizontal"></iconify-icon> Filter:
          </span>
          <select id="filter-genre" class="cx-filter-select">
            <option value="">Semua Genre</option>
            @foreach($filterGenres as $genreName)
              <option value="{{ $genreName }}">{{ $genreName }}</option>
            @endforeach
          </select>
          <select id="filter-classification" class="cx-filter-select">
            <option value="">Semua Rating Umur</option>
            @foreach($filterClassifications as $value => $label)
              <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <p class="cx-rail-hint d-none d-md-flex">
          <iconify-icon icon="lucide:move-horizontal"></iconify-icon>
          Gunakan tombol panah atau geser track film ke kiri/kanan
        </p>
        <p class="cx-rail-hint d-md-none">
          <iconify-icon icon="lucide:move-horizontal"></iconify-icon>
          Geser ke kiri/kanan untuk melihat film lainnya
        </p>

        <div class="cx-rail-wrap" data-rail="now-playing">
          <button type="button" class="cx-rail-btn cx-rail-prev" aria-label="Film sebelumnya">
            <iconify-icon icon="lucide:chevron-left"></iconify-icon>
          </button>
          <div class="cx-film-rail" id="now-playing-rail">
            <div class="cx-film-rail-track" id="now-playing-grid">
              @foreach($nowPlayingFilms as $film)
                @include('partials.customer_film_card', ['film' => $film])
              @endforeach
            </div>
          </div>
          <button type="button" class="cx-rail-btn cx-rail-next" aria-label="Film berikutnya">
            <iconify-icon icon="lucide:chevron-right"></iconify-icon>
          </button>
        </div>

        <div class="cx-section-footer">
          <span class="cx-section-footer-meta" id="now-playing-meta">
            Menampilkan {{ $nowPlayingFilms->count() }} dari {{ $nowPlayingTotal }} film sedang tayang
          </span>
          @if($nowPlayingTotal > $nowPlayingFilms->count())
            <a href="{{ route('films.search') }}" class="cx-section-footer-link">
              Lihat semua {{ $nowPlayingTotal }} film
              <iconify-icon icon="lucide:arrow-right"></iconify-icon>
            </a>
          @endif
        </div>
      </div>
    </section>

    {{-- Segera Tayang --}}
    <section class="cx-section">
      <div class="container">
        <div class="cx-section-header" data-aos="fade-up">
          <span class="cx-section-eyebrow">
            <iconify-icon icon="lucide:clock"></iconify-icon> Segera Tayang
          </span>
          <h2 class="cx-section-title">Coming Soon</h2>
          <p class="cx-section-desc">Blockbuster yang paling dinantikan — geser untuk jelajahi lebih banyak.</p>
        </div>

        <div class="cx-rail-wrap" data-rail="coming-soon">
          <button type="button" class="cx-rail-btn cx-rail-prev" aria-label="Film sebelumnya">
            <iconify-icon icon="lucide:chevron-left"></iconify-icon>
          </button>
          <div class="cx-film-rail" id="coming-soon-rail">
            <div class="cx-film-rail-track">
              @foreach($comingSoonFilms as $film)
                @include('partials.customer_film_card', ['film' => $film])
              @endforeach
            </div>
          </div>
          <button type="button" class="cx-rail-btn cx-rail-next" aria-label="Film berikutnya">
            <iconify-icon icon="lucide:chevron-right"></iconify-icon>
          </button>
        </div>

        @if($comingSoonTotal > $comingSoonFilms->count())
          <div class="cx-section-footer">
            <span class="cx-section-footer-meta">
              Menampilkan {{ $comingSoonFilms->count() }} dari {{ $comingSoonTotal }} film segera tayang
            </span>
            <a href="{{ route('films.search') }}?q=coming" class="cx-section-footer-link">
              Lihat semua
              <iconify-icon icon="lucide:arrow-right"></iconify-icon>
            </a>
          </div>
        @endif
      </div>
    </section>

  </div>

  <footer class="footer bg-dark py-4 py-lg-8 py-xl-10">
    <div class="container">
      <div class="row">
        <div class="col-xl-5 mb-8 mb-xl-0">
          <div class="d-flex flex-column gap-8 pe-xl-5">
            <h2 class="mb-0 text-white">Siap untuk film berikutnya?</h2>
            <div class="d-flex flex-column gap-2">
              <a href="mailto:support@cinetix.com" target="_blank" class="link-hover hstack gap-3 text-white fs-5">
                <iconify-icon icon="lucide:arrow-up-right" class="fs-7 text-primary"></iconify-icon>
                support@cinetix.com
              </a>
              <a href="#" target="_blank" class="link-hover hstack gap-3 text-white fs-5">
                <iconify-icon icon="lucide:map-pin" class="fs-7 text-primary"></iconify-icon>
                support@cinetix.com
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4 col-xl-2 mb-8 mb-xl-0">
          <ul class="footer-menu list-unstyled mb-0 d-flex flex-column gap-2">
            <li><a class="link-hover fs-5 text-white" href="{{ route('landing-page') }}">Beranda</a></li>
            <li><a class="link-hover fs-5 text-white" href="{{ route('customer.promos') }}">Kode Promo</a></li>
            <li><a class="link-hover fs-5 text-white" href="{{ route('about') }}">Tentang</a></li>
            <li><a class="link-hover fs-5 text-white" id="services" href="#services">Layanan</a></li>
            <li><a class="link-hover fs-5 text-white" href="projects.html">Bioskop</a></li>
            <li><a class="link-hover fs-5 text-white" href="terms-and-conditions.html">Syarat & Ketentuan</a></li>
            <li><a class="link-hover fs-5 text-white" href="privacy-policy.html">Kebijakan Privasi</a></li>
            <li><a class="link-hover fs-5 text-white" href="404.html">Error 404</a></li>
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
    <p class="mb-0 text-white text-opacity-70 text-md-center mt-10">Dikembangkan dengan ❤️ oleh Tim CineTix</p>
  </footer>

  <div class="get-template hstack gap-2">
    <button class="btn bg-primary p-2 round-52 rounded-circle hstack justify-content-center flex-shrink-0"
      id="scrollToTopBtn">
      <iconify-icon icon="lucide:arrow-up" class="fs-7 text-dark"></iconify-icon>
    </button>
  </div>


  <script src="{{asset('assets/libs/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('assets/libs/owl.carousel/dist/owl.carousel.min.js')}}"></script>
  <script src="{{asset('assets/libs/aos-master/dist/aos.js')}}"></script>
  <script src="{{asset('assets/js/custom.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <script>
    $(document).ready(function() {
      const scrollStep = 240;

      document.addEventListener('click', function(e) {
        const prevBtn = e.target.closest('.cx-rail-prev');
        const nextBtn = e.target.closest('.cx-rail-next');
        if (!prevBtn && !nextBtn) return;

        const wrap = (prevBtn || nextBtn).closest('.cx-rail-wrap');
        const rail = wrap?.querySelector('.cx-film-rail');
        if (!rail) return;

        rail.scrollBy({
          left: prevBtn ? -scrollStep : scrollStep,
          behavior: 'smooth'
        });
      });

      function fetchFilteredFilms() {
        const genre = $('#filter-genre').val();
        const classification = $('#filter-classification').val();
        const $track = $('#now-playing-grid');

        $.ajax({
          url: "{{ route('films.filter') }}",
          type: "GET",
          data: { genre: genre, classification: classification },
          beforeSend: function() {
            $track.css('opacity', '0.5');
          },
          success: function(data) {
            $track.css('opacity', '1').html(data);
            const count = $track.find('.cx-film-card').length;
            $('#now-playing-meta').text(
              count > 0
                ? 'Menampilkan ' + count + ' film hasil filter'
                : 'Tidak ada film yang cocok dengan filter'
            );
            document.getElementById('now-playing-rail')?.scrollTo({ left: 0, behavior: 'smooth' });
            if (typeof AOS !== 'undefined') {
              AOS.refreshHard();
            }
          },
          error: function() {
            $track.css('opacity', '1');
          }
        });
      }

      $('#filter-genre, #filter-classification').on('change', fetchFilteredFilms);
    });
  </script>
</body>

</html>
