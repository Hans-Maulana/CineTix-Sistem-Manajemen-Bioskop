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
      <video class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" autoplay muted loop playsinline>
        <source src="{{asset('assets/images/backgrounds/banner-video.mp4')}}" type="video/mp4" />
      </video>
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

          <div class="row mt-4 mb-4">
            <div class="col-12 d-flex gap-3 justify-content-start flex-wrap">
              <select id="filter-genre" class="form-select w-auto shadow-sm border-secondary text-dark fw-semibold">
                <option value="">Semua Genre</option>
                <option value="Action">Action</option>
                <option value="Comedy">Comedy</option>
                <option value="Drama">Drama</option>
                <option value="Horror">Horror</option>
                <option value="Romance">Romance</option>
              </select>

              <select id="filter-classification" class="form-select w-auto shadow-sm border-secondary text-dark fw-semibold">
                <option value="">Semua Rating Umur</option>
                <option value="SU">SU</option>
                <option value="13+">13+</option>
                <option value="17+">17+</option>
              </select>
            </div>
          </div>

        </div>
        <div class="featured-projects-slider px-3">
          <div class="owl-carousel owl-theme" id="owl-now-playing">
            @include('partials.film_items', ['nowPlayingFilms' => $nowPlayingFilms])
          </div>
        </div>
      </div>
    </section>

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
                <div class="poster-film d-flex flex-column gap-4" data-aos="fade-up"
                  data-aos-delay="{{ $loop->iteration * 100 }}" data-aos-duration="1000">
                  <div class="poster-film-img position-relative overflow-hidden">
                    <img src="{{ $film->cover_url }}" alt="{{ $film->title }}"
                      class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                    <div class="poster-film-overlay p-7 d-flex flex-column justify-content-end">
                      <ul class="social list-unstyled mb-0 hstack gap-2 justify-content-end">
                        <li><a href="{{ route('films.detail', $film) }}"
                            class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center">
                            <iconify-icon icon="lucide:eye" class="text-dark fs-5"></iconify-icon>
                          </a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="poster-film-details">
                    <h4 class="mb-0">{{ $film->title }}</h4>
                    <p class="mb-0">
                      @foreach($film->genres as $genre)
                        {{ $genre->genre_name }}{{ !$loop->last ? ' / ' : '' }}
                      @endforeach
                    </p>
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
            <li><a class="link-hover fs-5 text-white" href="index.html">Beranda</a></li>
            <li><a class="link-hover fs-5 text-white" href="about-us.html">Tentang</a></li>
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
      // Encapsulation: Mengisolasi logika request filter ke dalam fungsi mandiri
      function fetchFilteredFilms() {
        let genre = $('#filter-genre').val();
        let classification = $('#filter-classification').val();
        let $carousel = $('#owl-now-playing');

        $.ajax({
          url: "{{ route('films.filter') }}",
          type: "GET",
          data: { genre: genre, classification: classification },
          beforeSend: function() {
            $carousel.css('opacity', '0.5'); 
          },
          success: function(data) {
            $carousel.css('opacity', '1');

            // State Reset: Menghancurkan instance Owl Carousel lama agar DOM HTML baru tidak mengalami freeze/bug
            $carousel.trigger('destroy.owl.carousel');
            $carousel.find('.owl-stage-outer').children().unwrap();
            $carousel.removeClass('owl-loaded');

            $carousel.html(data);

            // Re-initialization: Menyalakan kembali plugin slider carousel dengan data baru
            $carousel.owlCarousel({
              loop: false, margin: 20, nav: false, dots: true,
              responsive: { 0: { items: 1 }, 576: { items: 2 }, 992: { items: 3 }, 1200: { items: 4 } }
            });
          }
        });
      }

      // Event Binding: Mengikat event listener 'change' pada elemen dropdown ke fungsi AJAX
      $('#filter-genre, #filter-classification').on('change', function() {
        fetchFilteredFilms();
      });
    });
  </script>
</body>

</html>
