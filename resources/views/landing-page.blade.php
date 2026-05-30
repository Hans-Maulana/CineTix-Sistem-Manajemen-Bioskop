<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CineTix</title>
  <link rel="shortcut icon" type="image/png" href="{{asset("assets/images/logos/favicon.svg")}}" />
  <link rel="stylesheet" href="{{asset("assets/libs/owl.carousel/dist/assets/owl.carousel.min.css")}}">
  <link rel="stylesheet" href="{{asset("assets/libs/aos-master/dist/aos.css")}}">
  <link rel="stylesheet" href="{{asset("assets/css/styles.css")}}" />
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

    .bg-light-gray {
      background-color: #ebedf3 !important;
    }
    .banner-section {
      background-color: #0c0b24 !important;
    }

    /* Fullscreen YouTube Background Video */
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
      to {
        opacity: 1;
      }
    }

    .banner-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, rgba(12, 11, 36, 0.1) 0%, rgba(12, 11, 36, 0.5) 100%);
      z-index: 1;
    }

    /* Coming Soon Card Styles */
    .poster-film {
      transition: transform 0.3s ease;
    }
    .poster-film:hover {
      transform: translateY(-5px);
    }
    .poster-film-img {
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .poster-film-img::before {
      content: "";
      display: block;
      padding-top: 72% !important; /* Matches Now Playing landscape aspect ratio */
    }
    .poster-film-img img {
      position: absolute !important;
      top: 0 !important;
      left: 0 !important;
      width: 100% !important;
      height: 100% !important;
      object-fit: fill !important; /* Stretches the image to fill the box completely without cropping or black bars */
      transition: transform 0.5s ease;
    }
    .poster-film-img:hover img {
      transform: scale(1.05);
    }
    .poster-film-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(26, 25, 83, 0.85) !important; /* CineTix deep purple-blue with transparency */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
      z-index: 2;
    }
    .poster-film-img:hover .poster-film-overlay {
      opacity: 1;
      visibility: visible;
    }
    .poster-film-overlay .btn-detail {
      background: #ffffff;
      color: #1A1953;
      font-weight: 700;
      padding: 10px 20px;
      border-radius: 50px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
      transform: translateY(15px);
    }
    .poster-film-img:hover .poster-film-overlay .btn-detail {
      transform: translateY(0);
    }
    .poster-film-overlay .btn-detail:hover {
      background: #1A1953;
      color: #ffffff;
      transform: scale(1.05);
    }

    /* Override Now Playing images to fit without cropping */
    .portfolio-img > img {
      object-fit: fill !important; /* Stretches the image to fill the box completely without cropping or black bars */
    }

    /* Now Playing (Sedang Tayang) Hover Overlay Styling */
    .portfolio-img .portfolio-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(26, 25, 83, 0.85) !important; /* CineTix deep purple-blue with transparency */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
      z-index: 2;
      transform: none !important; /* Overrides default template slide transition */
    }
    .portfolio-img:hover .portfolio-overlay {
      opacity: 1 !important;
      visibility: visible !important;
    }
    .portfolio-overlay .btn-detail-playing {
      background: #ffffff;
      color: #1A1953;
      font-weight: 700;
      padding: 10px 20px;
      border-radius: 50px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
      transform: translateY(15px);
    }
    .portfolio-img:hover .portfolio-overlay .btn-detail-playing {
      transform: translateY(0);
    }
    .portfolio-overlay .btn-detail-playing:hover {
      background: #1A1953;
      color: #ffffff;
      transform: scale(1.05);
    }

    /* Style for static 'Pesan' button inside now playing cards */
    .btn-pesan {
      background-color: #1A1953;
      color: #ffffff;
      font-weight: 700;
      font-size: 0.8rem;
      padding: 6px 14px;
      border-radius: 50px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
      border: 1px solid #1A1953;
    }
    .btn-pesan:hover {
      background-color: #ffffff;
      color: #1A1953;
      border-color: #1A1953;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(26, 25, 83, 0.2);
    }
    .btn-pesan iconify-icon {
      font-size: 0.95rem;
    }

    /* Style for Coming Soon film details to match Now Playing details */
    .poster-film-details {
      padding: 10px 5px;
      text-align: left;
    }
    .poster-film-details h3 {
      font-size: 1.15rem !important;
      font-weight: 700 !important;
      margin-bottom: 8px !important;
      color: #1F2A2E !important;
    }
  </style>
</head>

<body>

  <!-- Header -->
  <header class="header border-4 border-primary border-top position-fixed start-0 top-0 w-100">
    <div class="container">
      <div class="header-wrapper d-flex align-items-center justify-content-between">
        <div class="logo">
          <a href="{{ route('landing-page') }}">
            <img src="{{asset("assets/images/logos/logo-white.svg")}}" alt="logo" class="img-fluid"
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

  <!--  Page Wrapper -->
  <div class="page-wrapper overflow-hidden">

    <!--  Banner Section -->
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

    <!--  Sedang Tayang Section -->
    <section class="featured-projects py-4 py-lg-8 py-xl-10 bg-light-gray">
      <div class="d-flex flex-column gap-5 gap-xl-11">
        <div class="container">
          <div class="row gap-7 gap-xl-0">
            <div class="col-xl-4 col-xxl-4">
              <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
                data-aos-duration="1000">
                <span
                  class="round-36 flex-shrink-0 text-white rounded-circle bg-primary hstack justify-content-center fw-medium">02</span>
                <hr class="border-line">
                <span class="badge text-bg-dark">Film</span>
              </div>
            </div>
            <div class="col-xl-8 col-xxl-7">
              <div class="row">
                <div class="col-xxl-8">
                  <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100"
                    data-aos-duration="1000">
                    <h2 class="mb-0">Sedang Tayang</h2>
                    <p class="fs-5 mb-0">A glimpse into our creativity—exploring innovative designs, successful
                      collaborations, and transformative digital experiences.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="featured-projects-slider px-3">
          <div class="owl-carousel owl-theme">
            @foreach($nowPlayingFilms as $film)
              <div class="item">
                <div class="portfolio d-flex flex-column gap-6">
                  <div class="portfolio-img position-relative overflow-hidden">
                    <img src="{{ $film->cover_url }}" alt="{{ $film->title }}"
                      class="img-fluid w-100 object-fit-cover shadow-sm rounded-3">
                    <div class="portfolio-overlay">
                      <a href="{{ route('films.detail', $film) }}" class="btn-detail-playing">
                        <iconify-icon icon="lucide:ticket" class="fs-5"></iconify-icon>
                        <span>Pesan Tiket</span>
                      </a>
                    </div>
                  </div>
                  <div class="portfolio-details d-flex flex-column gap-3">
                    <h3 class="mb-0">{{ $film->title }}</h3>
                    <div class="d-flex align-items-center justify-content-between gap-2 mt-1">
                      <div class="hstack gap-2 flex-wrap">
                        @foreach($film->genres as $genre)
                          <span class="badge text-dark border">{{ $genre->genre_name }}</span>
                        @endforeach
                      </div>
                      <a href="{{ route('films.detail', $film) }}" class="btn-pesan flex-shrink-0">
                        <iconify-icon icon="lucide:ticket" class="fs-5"></iconify-icon>
                        <span>Pesan Sekarang</span>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </section>



    <!--  Segera Tayang Section -->
    <section class="meet-our-team py-4 py-lg-8 py-xl-10">
      <div class="container">
        <div class="d-flex flex-column gap-5 gap-xl-11">
          <div class="row gap-7 gap-xl-0">
            <div class="col-xl-4 col-xxl-4">
              <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
                data-aos-duration="1000">
                <span
                  class="round-36 flex-shrink-0 text-white rounded-circle bg-primary hstack justify-content-center fw-medium">06</span>
                <hr class="border-line bg-white">
                <span class="badge text-bg-dark">Segera Tayang</span>
              </div>
            </div>
            <div class="col-xl-8 col-xxl-7">
              <div class="row">
                <div class="col-xxl-8">
                  <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100"
                    data-aos-duration="1000">
                    <h2 class="mb-0">Segera Tayang</h2>
                    <p class="fs-5 mb-0 text-opacity-70">Bersiaplah untuk film-film blockbuster yang sangat dinantikan
                      ini, segera tayang di layar lebar.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            @foreach($comingSoonFilms as $film)
              <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
                <div class="poster-film d-flex flex-column gap-6" data-aos="fade-up"
                  data-aos-delay="{{ $loop->iteration * 100 }}" data-aos-duration="1000">
                  <div class="poster-film-img position-relative overflow-hidden">
                    <img src="{{ $film->cover_url }}" alt="{{ $film->title }}"
                      class="img-fluid w-100 object-fit-cover shadow-sm rounded-3">
                    <div class="poster-film-overlay">
                      <a href="{{ route('films.detail', $film) }}" class="btn-detail">
                        <iconify-icon icon="lucide:eye" class="fs-5"></iconify-icon>
                        <span>Detail Film</span>
                      </a>
                    </div>
                  </div>
                  <div class="poster-film-details d-flex flex-column gap-3">
                    <h3 class="mb-0">{{ $film->title }}</h3>
                    <div class="hstack gap-2 flex-wrap">
                      @foreach($film->genres as $genre)
                        <span class="badge text-dark border">{{ $genre->genre_name }}</span>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
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


  <script src="{{asset("assets/libs/jquery/dist/jquery.min.js")}}"></script>
  <script src="{{asset("assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js")}}"></script>
  <script src="{{asset("assets/libs/owl.carousel/dist/owl.carousel.min.js")}}"></script>
  <script src="{{asset("assets/libs/aos-master/dist/aos.js")}}"></script>
  <script src="{{asset("assets/js/custom.js")}}"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>