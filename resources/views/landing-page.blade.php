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
</head>

<body>

  <!-- Header -->
  <header class="header border-4 border-primary border-top position-fixed start-0 top-0 w-100">
    <div class="container">
      <div class="header-wrapper d-flex align-items-center justify-content-between">
        <div class="logo">
          <a href="index.html" class="logo-white">
            <img src="{{asset("assets/images/logos/logo-white.svg")}}" alt="logo" class="img-fluid">
          </a>
          <a href="index.html" class="logo-dark">
            <img src="{{asset("assets/images/logos/logo-dark.svg")}}" alt="logo" class="img-fluid">
          </a>
        </div>

        <div class="d-flex align-items-center gap-4">
          @if (auth()->check())
            <div class="d-flex align-items-center gap-2">
              <!-- Foto profil -->
              <img src="{{ auth()->user()->profile_photo_url ?? asset('assets/images/default-avatar.png') }}"
                alt="Profile" class="rounded-circle" width="32" height="32">

              <!-- Nama user -->
              <span class="fw-bold text-dark">
                {{ auth()->user()->name }}
              </span>
            </div>
          @else

            <div class="hstack gap-3">
              <a href="sign-in.html"
                class="btn btn-outline-light btn-md fs-6 bg-white px-3 py-1 text-dark hstack justify-content-center">Sign
                In</a>
              <a href="sign-up.html"
                class="btn btn-dark btn-md text-white fs-6 bg-dark px-3 py-1 hstack justify-content-center">Sign
                Up</a>
            </div>
          @endif

          <div class="btn-group">
            <button
              class="btn btn-secondary toggle-menu round-45 p-2 d-flex align-items-center justify-content-center bg-white rounded-circle"
              type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
              <iconify-icon icon="solar:hamburger-menu-line-duotone" class="menu-icon fs-8 text-dark"></iconify-icon>
            </button>
            <ul class="dropdown-menu dropdown-menu-end p-4">
              <div class="d-flex flex-column gap-6">
                <div class="hstack justify-content-between border-bottom pb-6">
                  <p class="mb-0 fs-5 text-dark">Menu</p>
                  <button type="button" class="btn-close opacity-75" aria-label="Close"></button>
                </div>
                <div class="d-flex flex-column gap-3">
                  <ul class="header-menu list-unstyled mb-0 d-flex flex-column gap-2">
                    <li class="header-item">
                      <a href="index.html" aria-current="true"
                        class="header-link active hstack gap-2 fs-7 fw-bold text-dark"><iconify-icon icon="lucide:popcorn" class="text-secondary fs-5"></iconify-icon>Beranda</a>
                    </li>
                    <li class="header-item">
                      <a href="about-us.html" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><iconify-icon icon="lucide:popcorn" class="text-secondary fs-5"></iconify-icon>Tentang</a>
                    </li>
                    <li class="header-item">
                      <a href="projects.html" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><iconify-icon icon="lucide:popcorn" class="text-secondary fs-5"></iconify-icon>Bioskop</a>
                    </li>
                    <li class="header-item">
                      <a href="blog.html" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><iconify-icon icon="lucide:popcorn" class="text-secondary fs-5"></iconify-icon>Berita</a>
                    </li>
                    <li class="header-item">
                      <a href="index.html" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><iconify-icon icon="lucide:popcorn" class="text-secondary fs-5"></iconify-icon>Layanan</a>
                    </li>
                    <li class="header-item">
                      <a href="contact.html" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><iconify-icon icon="lucide:popcorn" class="text-secondary fs-5"></iconify-icon>Kontak</a>
                    </li>
                    <li class="header-item">
                      <a href="index.html" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><iconify-icon icon="lucide:popcorn" class="text-secondary fs-5"></iconify-icon>Bantuan</a>
                    </li>
                  </ul>

                </div>
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
      <video class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" autoplay muted loop playsinline>
        <source src="{{asset("assets/images/backgrounds/banner-video.mp4")}}" type="video/mp4" />
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
            <a href="javascript:void(0)" class="p-1 ps-7 bg-primary rounded-pill">
              <span class="bg-white round-52 rounded-circle d-flex align-items-center justify-content-center">
                <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
              </span>
            </a>
          </div>
        </div>
      </div>
    </section>

<!--  Featured Projects Section -->
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
            <div class="item">
              <div class="portfolio d-flex flex-column gap-6">
                <div class="portfolio-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="portfolio-overlay">
                    <a href="projects-detail.html"
                      class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                      <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                    </a>
                  </div>
                </div>
                <div class="portfolio-details d-flex flex-column gap-3">
                  <h3 class="mb-0">Inception</h3>
                  <div class="hstack gap-2">
                    <span class="badge text-dark border">Aksi</span>
                    <span class="badge text-dark border">Fiksi Ilmiah</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="item">
              <div class="portfolio d-flex flex-column gap-6">
                <div class="portfolio-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="portfolio-overlay">
                    <a href="projects-detail.html"
                      class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                      <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                    </a>
                  </div>
                </div>
                <div class="portfolio-details d-flex flex-column gap-3">
                  <h3 class="mb-0">Interstellar</h3>
                  <div class="hstack gap-2">
                    <span class="badge text-dark border">Premiere</span>
                    <span class="badge text-dark border">Petualangan</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="item">
              <div class="portfolio d-flex flex-column gap-6">
                <div class="portfolio-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="portfolio-overlay">
                    <a href="projects-detail.html"
                      class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                      <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                    </a>
                  </div>
                </div>
                <div class="portfolio-details d-flex flex-column gap-3">
                  <h3 class="mb-0">The Dark Knight</h3>
                  <div class="hstack gap-2">
                    <span class="badge text-dark border">Aksi</span>
                    <span class="badge text-dark border">Premiere</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="item">
              <div class="portfolio d-flex flex-column gap-6">
                <div class="portfolio-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="portfolio-overlay">
                    <a href="projects-detail.html"
                      class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                      <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                    </a>
                  </div>
                </div>
                <div class="portfolio-details d-flex flex-column gap-3">
                  <h3 class="mb-0">Oppenheimer</h3>
                  <div class="hstack gap-2">
                    <span class="badge text-dark border">IMAX</span>
                    <span class="badge text-dark border">Petualangan</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="item">
              <div class="portfolio d-flex flex-column gap-6">
                <div class="portfolio-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="portfolio-overlay">
                    <a href="projects-detail.html"
                      class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                      <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                    </a>
                  </div>
                </div>
                <div class="portfolio-details d-flex flex-column gap-3">
                  <h3 class="mb-0">Interstellar</h3>
                  <div class="hstack gap-2">
                    <span class="badge text-dark border">Fiksi Ilmiah</span>
                    <span class="badge text-dark border">Fantasi</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="item">
              <div class="portfolio d-flex flex-column gap-6">
                <div class="portfolio-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="portfolio-overlay">
                    <a href="projects-detail.html"
                      class="position-absolute top-50 start-50 translate-middle bg-primary round-64 rounded-circle hstack justify-content-center">
                      <iconify-icon icon="lucide:arrow-up-right" class="fs-8 text-dark"></iconify-icon>
                    </a>
                  </div>
                </div>
                <div class="portfolio-details d-flex flex-column gap-3">
                  <h3 class="mb-0">Avatar: The Way of Water</h3>
                  <div class="hstack gap-2">
                    <span class="badge text-dark border">Petualangan</span>
                    <span class="badge text-dark border">Premiere</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

<!--  Services Section -->
    <section class="services py-4 py-lg-8 py-xl-10 bg-dark" id="services">
      <div class="container">
        <div class="d-flex flex-column gap-5 gap-xl-10">
          <div class="row gap-7 gap-xl-0">
            <div class="col-xl-4 col-xxl-4">
              <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
                data-aos-duration="1000">
                <span
                  class="round-36 flex-shrink-0 text-white rounded-circle bg-primary hstack justify-content-center fw-medium">03</span>
                <hr class="border-line bg-white">
                <span class="badge text-dark bg-white">Promo Spesial</span>
              </div>
            </div>
            <div class="col-xl-8 col-xxl-7">
              <div class="row">
                <div class="col-xxl-8">
                  <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100"
                    data-aos-duration="1000">
                    <h2 class="mb-0 text-white">Promo Spesial Minggu Ini</h2>
                    <p class="fs-5 mb-0 text-white text-opacity-70">Jangan lewatkan penawaran menarik dari CineTix. Nikmati menonton film favoritmu dengan harga lebih hemat dan berbagai bonus eksklusif.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="services-tab">
            <div class="row gap-5 gap-xl-0">
              <div class="col-xl-4">
                <div class="tab-content" data-aos="zoom-in" data-aos-delay="100" data-aos-duration="1000">
                  <div class="tab-pane active" id="one" role="tabpanel" aria-labelledby="one-tab" tabindex="0">
                    <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  </div>
                  <div class="tab-pane" id="two" role="tabpanel" aria-labelledby="two-tab" tabindex="0">
                    <img src="https://images.unsplash.com/photo-1505686994434-e3cc5abf1330?q=80&w=800&auto=format&fit=crop" alt="promo" class="img-fluid w-100 object-fit-cover rounded-4" style="aspect-ratio: 16/9;">
                  </div>
                  <div class="tab-pane" id="three" role="tabpanel" aria-labelledby="three-tab" tabindex="0">
                    <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?q=80&w=800&auto=format&fit=crop" alt="promo" class="img-fluid w-100 object-fit-cover rounded-4" style="aspect-ratio: 16/9;">
                  </div>
                  <div class="tab-pane" id="four" role="tabpanel" aria-labelledby="four-tab" tabindex="0">
                    <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  </div>
                </div>
              </div>
              <div class="col-xl-8">
                <div class="d-flex flex-column gap-5">
                  <ul class="nav nav-tabs" id="myTab" role="tablist" data-aos="fade-up" data-aos-delay="200"
                    data-aos-duration="1000">
                    <li
                      class="nav-item py-4 py-lg-8 border-top border-white border-opacity-10 d-flex align-items-center w-100"
                      role="presentation">
                      <div class="row w-100 align-items-center gx-3">
                        <div class="col-lg-6 col-xxl-5">
                          <button class="nav-link fs-8 fw-bold py-1 px-0 border-0 rounded-0 flex-shrink-0 active"
                            id="one-tab" data-bs-toggle="tab" data-bs-target="#one" type="button" role="tab"
                            aria-controls="one" aria-selected="true">Beli 1 Gratis 1</button>
                        </div>
                        <div class="col-lg-6 col-xxl-7">
                          <p class="text-white text-opacity-70 mb-0">
                            Nikmati promo Beli 1 Gratis 1 untuk semua film tayang setiap hari Selasa menggunakan kartu kredit partner CineTix.
                          </p>
                        </div>
                      </div>
                    </li>
                    <li
                      class="nav-item py-4 py-lg-8 border-top border-white border-opacity-10 d-flex align-items-center w-100"
                      role="presentation">
                      <div class="row w-100 align-items-center gx-3">
                        <div class="col-lg-6 col-xxl-5">
                          <button class="nav-link fs-8 fw-bold py-1 px-0 border-0 rounded-0 flex-shrink-0" id="two-tab"
                            data-bs-toggle="tab" data-bs-target="#two" type="button" role="tab" aria-controls="two"
                            aria-selected="false">Diskon Pelajar 50%</button>
                        </div>
                        <div class="col-lg-6 col-xxl-7">
                          <p class="text-white text-opacity-70 mb-0">
                            Tunjukkan kartu pelajar atau mahasiswa kamu dan dapatkan potongan 50% untuk tiket nonton khusus di hari kerja.
                          </p>
                        </div>
                      </div>
                    </li>
                    <li
                      class="nav-item py-4 py-lg-8 border-top border-white border-opacity-10 d-flex align-items-center w-100"
                      role="presentation">
                      <div class="row w-100 align-items-center gx-3">
                        <div class="col-lg-6 col-xxl-5">
                          <button class="nav-link fs-8 fw-bold py-1 px-0 border-0 rounded-0 flex-shrink-0"
                            id="three-tab" data-bs-toggle="tab" data-bs-target="#three" type="button" role="tab"
                            aria-controls="three" aria-selected="false">Cashback OVO 30%</button>
                        </div>
                        <div class="col-lg-6 col-xxl-7">
                          <p class="text-white text-opacity-70 mb-0">
                            Gunakan OVO untuk pembayaran tiket atau makanan dan nikmati cashback hingga 30% tanpa minimum transaksi.
                          </p>
                        </div>
                      </div>
                    </li>
                    <li
                      class="nav-item py-4 py-lg-8 border-top border-white border-opacity-10 d-flex align-items-center w-100"
                      role="presentation">
                      <div class="row w-100 align-items-center gx-3">
                        <div class="col-lg-6 col-xxl-5">
                          <button class="nav-link fs-8 fw-bold py-1 px-0 border-0 rounded-0 flex-shrink-0"
                            id="four-tab" data-bs-toggle="tab" data-bs-target="#four" type="button" role="tab"
                            aria-controls="four" aria-selected="false">Paket Keluarga</button>
                        </div>
                        <div class="col-lg-6 col-xxl-7">
                          <p class="text-white text-opacity-70 mb-0">
                            Lebih hemat dengan Paket Keluarga: 4 tiket + 2 popcorn ukuran besar + 4 minuman hanya dengan Rp 200.000.
                          </p>
                        </div>
                      </div>
                    </li>
                  </ul>
                  <a href="projects.html" class="btn border border-white border-opacity-25" data-aos="fade-up"
                    data-aos-delay="300" data-aos-duration="1000">
                    <span class="btn-text">Lihat Semua Promo</span>
                    <iconify-icon icon="lucide:arrow-up-right"
                      class="btn-icon bg-white text-dark round-52 rounded-circle hstack justify-content-center fs-7 shadow-sm"></iconify-icon>
                  </a>
                </div>
              </div>
            </div>
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
                    <p class="fs-5 mb-0 text-opacity-70">Bersiaplah untuk film-film blockbuster yang sangat dinantikan ini, segera tayang di layar lebar.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
              <div class="poster-film d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="100"
                data-aos-duration="1000">
                <div class="poster-film-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="team-img" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="poster-film-overlay p-7 d-flex flex-column justify-content-end">
                    <ul class="social list-unstyled mb-0 hstack gap-2 justify-content-end">
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-twitter.svg')}}" alt="twitter"></a></li>
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-be.svg')}}" alt="be"></a></li>
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-linkedin.svg')}}" alt="linkedin"></a></li>
                    </ul>
                  </div>
                </div>
                <div class="poster-film-details">
                  <h4 class="mb-0">Deadpool & Wolverine</h4>
                  <p class="mb-0">Aksi / Komedi</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
              <div class="poster-film d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="200"
                data-aos-duration="1000">
                <div class="poster-film-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="team-img" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="poster-film-overlay p-7 d-flex flex-column justify-content-end">
                    <ul class="social list-unstyled mb-0 hstack gap-2 justify-content-end">
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-twitter.svg')}}" alt="twitter"></a></li>
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-be.svg')}}" alt="be"></a></li>
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-linkedin.svg')}}" alt="linkedin"></a></li>
                    </ul>
                  </div>
                </div>
                <div class="poster-film-details">
                  <h4 class="mb-0">Furiosa: A Mad Max Saga</h4>
                  <p class="mb-0">Aksi / Petualangan</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
              <div class="poster-film d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="300"
                data-aos-duration="1000">
                <div class="poster-film-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="team-img" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="poster-film-overlay p-7 d-flex flex-column justify-content-end">
                    <ul class="social list-unstyled mb-0 hstack gap-2 justify-content-end">
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-twitter.svg')}}" alt="twitter"></a></li>
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-be.svg')}}" alt="be"></a></li>
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-linkedin.svg')}}" alt="linkedin"></a></li>
                    </ul>
                  </div>
                </div>
                <div class="poster-film-details">
                  <h4 class="mb-0">Joker: Folie à Deux</h4>
                  <p class="mb-0">Premiere / Musikal</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
              <div class="poster-film d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="400"
                data-aos-duration="1000">
                <div class="poster-film-img position-relative overflow-hidden">
                  <img src="{{asset("assets/images/backgrounds/blog-detail-banner.jpg")}}" alt="team-img" class="img-fluid w-100 object-fit-cover shadow-sm rounded-3" style="aspect-ratio: 2/3;">
                  <div class="poster-film-overlay p-7 d-flex flex-column justify-content-end">
                    <ul class="social list-unstyled mb-0 hstack gap-2 justify-content-end">
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-twitter.svg')}}" alt="twitter"></a></li>
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-be.svg')}}" alt="be"></a></li>
                      <li><a href="#!"
                          class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><img
                            src="{{asset('assets/images/svgs/icon-linkedin.svg')}}" alt="linkedin"></a></li>
                    </ul>
                  </div>
                </div>
                <div class="poster-film-details">
                  <h4 class="mb-0">Gladiator II</h4>
                  <p class="mb-0">Aksi / Sejarah</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>



<!--  Mengapa memilih CineTix Section -->
    <section class="why-choose-us py-4 py-lg-8 py-xl-10">
      <div class="container">
        <div class="row justify-content-between gap-5 gap-xl-0">
          <div class="col-xl-3 col-xxl-3">
            <div class="d-flex flex-column gap-7">
              <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
                data-aos-duration="1000">
                <span
                  class="round-36 flex-shrink-0 text-white rounded-circle bg-primary hstack justify-content-center fw-medium">04</span>
                <hr class="border-line">
                <span class="badge text-bg-dark">Tentang Kami</span>
              </div>
              <h2 class="mb-0" data-aos="fade-right" data-aos-delay="200" data-aos-duration="1000">Mengapa memilih CineTix</h2>
              <p class="mb-0 fs-5" data-aos="fade-right" data-aos-delay="300" data-aos-duration="1000">Kami menggabungkan kenyamanan fasilitas dengan teknologi sinema terkini untuk menghadirkan pengalaman menonton yang tak terlupakan bagi setiap pengunjung.</p>
            </div>
          </div>
          <div class="col-xl-9 col-xxl-8">
            <div class="row">
              <div class="col-lg-4 mb-7 mb-lg-0">
                <div class="card position-relative overflow-hidden bg-primary h-100" data-aos="fade-up"
                  data-aos-delay="100" data-aos-duration="1000">
                  <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex flex-column gap-3 position-relative z-1">
                      <ul class="list-unstyled mb-0 hstack gap-1">
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-bold"
                              class="fs-6 text-dark"></iconify-icon></a></li>
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-bold"
                              class="fs-6 text-dark"></iconify-icon></a></li>
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-bold"
                              class="fs-6 text-dark"></iconify-icon></a></li>
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-bold"
                              class="fs-6 text-dark"></iconify-icon></a></li>
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-line-duotone"
                              class="fs-6 text-dark"></iconify-icon></a></li>
                      </ul>
                      <p class="mb-0 fs-6 text-dark">CineTix membuat pemesanan tiket jadi sangat mudah! Bioskopnya bersih dan layarnya luar biasa.
                      </p>
                    </div>
                    <div class="position-relative z-1">
                      <div class="pb-6 border-bottom">
                        <h2 class="mb-0">98.6%</h2>
                        <p class="mb-0">Kepuasan Pelanggan</p>
                      </div>
                      <div class="hstack gap-6 pt-6">
                        <img src="{{asset("assets/images/profile/avatar-1.png")}}" alt=""
                          class="img-fluid rounded-circle overflow-hidden flex-shrink-0" width="64" height="64">
                        <div>
                          <h5 class="mb-0">Budi Santoso</h5>
                          <p class="mb-0">Penggemar Film</p>
                        </div>
                      </div>
                    </div>
                    <div class="position-absolute bottom-0 end-0">
                      <img src="{{asset("assets/images/backgrounds/customer-satisfaction-bg.svg")}}" alt=""
                        class="img-fluid">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 mb-7 mb-lg-0">
                <div class="d-flex flex-column gap-7" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                  <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?q=80&w=800&auto=format&fit=crop" alt="promo" class="img-fluid w-100 object-fit-cover rounded-4" style="aspect-ratio: 16/9;">
                  </div>

                  <div class="card bg-dark">
                    <div class="card-body d-flex flex-column gap-7">
                      <div>
                        <h2 class="mb-0 text-white">500+</h2>
                        <p class="mb-0 text-white text-opacity-70">Film Shown</p>
                      </div>
                      <ul class="d-flex align-items-center mb-0">
                        <li>
                          <a href="javascript:void(0)">
                            <img src="{{asset("assets/images/profile/user-1.jpg")}}" width="44" height="44"
                              class="rounded-circle border border-2 border-dark" alt="user-1">
                          </a>
                        </li>
                        <li class="ms-n2">
                          <a href="javascript:void(0)">
                            <img src="{{asset("assets/images/profile/user-2.jpg")}}" width="44" height="44"
                              class="rounded-circle border border-2 border-dark" alt="user-2">
                          </a>
                        </li>
                        <li class="ms-n2">
                          <a href="javascript:void(0)">
                            <img src="{{asset("assets/images/profile/user-3.jpg")}}" width="44" height="44"
                              class="rounded-circle border border-2 border-dark" alt="user-3">
                          </a>
                        </li>
                        <li class="ms-n2">
                          <a href="javascript:void(0)">
                            <img src="{{asset("assets/images/profile/user-4.jpg")}}" width="44" height="44"
                              class="rounded-circle border border-2 border-dark" alt="user-4">
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 mb-7 mb-lg-0">
                <div class="card border h-100 position-relative overflow-hidden" data-aos="fade-up" data-aos-delay="300"
                  data-aos-duration="1000">
                  <span
                    class="border rounded-circle round-490 d-block position-absolute top-0 start-50 translate-middle"></span>
                  <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                      <h2 class="mb-0">238+</h2>
                      <p class="mb-0 text-dark">Bioskop di Seluruh Dunia</p>
                    </div>
                    <div class="d-flex flex-column gap-3">
                      <a href="index.html" class="logo-dark">
                        <img src="{{asset("assets/images/logos/logo-dark.svg")}}" alt="logo" class="img-fluid">
                      </a>
                      <p class="mb-0 fs-5 text-dark">Our global reach allows us to create unique, culturally relevant
                        designs for businesses across different industries.</p>
                    </div>
                  </div>
                  <span
                    class="border rounded-circle round-490 d-block position-absolute top-100 start-50 translate-middle"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

<!--  Testimonial Section -->
    <section class="testimonial py-4 py-lg-8 py-xl-10 bg-light-gray">
      <div class="container">
        <div class="d-flex flex-column gap-5 gap-xl-11">
          <div class="row gap-7 gap-xl-0">
            <div class="col-xl-4 col-xxl-4">
              <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
                data-aos-duration="1000">
                <span
                  class="round-36 flex-shrink-0 text-white rounded-circle bg-primary hstack justify-content-center fw-medium">05</span>
                <hr class="border-line bg-white">
                <span class="badge text-bg-dark">Ulasan</span>
              </div>
            </div>
            <div class="col-xl-8 col-xxl-7">
              <div class="row">
                <div class="col-xxl-8">
                  <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100"
                    data-aos-duration="1000">
                    <h2 class="mb-0">Apa Kata Penonton</h2>
                    <p class="fs-5 mb-0 text-opacity-70">Real experiences, genuine feedback—discover how our creative
                      solutions have transformed brands and elevated businesses.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row gap-7 gap-lg-0">
            <div class="col-lg-4 col-xl-3 d-flex align-items-stretch">
              <div class="card bg-primary w-100" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                <div class="card-body d-flex flex-column gap-5 gap-xl-11 justify-content-between">
                  <div class="d-flex flex-column gap-4">
                    <p class="mb-0">Suara Penonton</p>
                    <h4 class="mb-0">Memesan tiket di CineTix sangat mudah. Saya suka bisa memilih kursi yang saya mau!</h4>
                  </div>
                  <div class="hstack gap-3">
                    <img src="{{asset('assets/images/testimonial/testimonial-1.jpg')}}" alt=""
                      class="img-fluid rounded-circle overflow-hidden flex-shrink-0" width="60" height="60">
                    <div>
                      <h5 class="mb-1 fw-normal">Gladiator II</h5>
                      <p class="mb-0">Penonton Setia</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-xl-6 d-flex align-items-stretch">
              <div class="card bg-dark w-100" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                <div class="card-body d-flex flex-column gap-5 gap-xl-11 justify-content-between">
                  <div class="d-flex flex-column gap-4">
                    <p class="mb-0 text-white text-opacity-70">Suara Penonton</p>
                    <h4 class="mb-0 text-white pe-xl-2">From concept to execution, they delivered outstanding results.
                      Highly recommend their expertise!</h4>
                    <div class="hstack gap-2">
                      <ul class="list-unstyled mb-0 hstack gap-1">
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-bold"
                              class="fs-6 text-white"></iconify-icon></a></li>
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-bold"
                              class="fs-6 text-white"></iconify-icon></a></li>
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-bold"
                              class="fs-6 text-white"></iconify-icon></a></li>
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-bold"
                              class="fs-6 text-white"></iconify-icon></a></li>
                        <li><a class="hstack" href="javascript:void(0)"><iconify-icon icon="solar:star-line-duotone"
                              class="fs-6 text-white"></iconify-icon></a></li>
                      </ul>
                      <h6 class="mb-0 text-white fw-medium">4.0</h6>
                    </div>
                  </div>
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="hstack gap-3">
                      <img src="{{asset('assets/images/testimonial/testimonial-2.jpg')}}" alt=""
                        class="img-fluid rounded-circle overflow-hidden flex-shrink-0" width="60" height="60">
                      <div>
                        <h5 class="mb-1 fw-normal text-white">Indra Wijaya</h5>
                        <p class="mb-0 text-white text-opacity-70">Kritikus Film</p>
                      </div>
                    </div>
                    <span><img src="{{asset('assets/images/testimonial/quete.svg')}}" alt="quete"
                        class="img-fluid flex-shrink-0"></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-xl-3 d-flex align-items-stretch">
              <div class="card w-100" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1000">
                <div class="card-body d-flex flex-column gap-5 gap-xl-11 justify-content-between">
                  <div class="d-flex flex-column gap-4">
                    <p class="mb-0">Suara Penonton</p>
                    <h4 class="mb-0">Camilan enak, kursi nyaman, dan film fantastis. Apa lagi yang Anda inginkan?</h4>
                  </div>
                  <div class="hstack gap-3">
                    <img src="{{asset('assets/images/testimonial/testimonial-3.jpg')}}" alt=""
                      class="img-fluid rounded-circle overflow-hidden flex-shrink-0" width="60" height="60">
                    <div>
                      <h5 class="mb-1 fw-normal">Siti Aminah</h5>
                      <p class="mb-0">Penonton Biasa</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>



<!--  FAQ Section -->
    <section class="faq py-4 py-lg-8 py-xl-10">
      <div class="container">
        <div class="d-flex flex-column gap-5 gap-xl-11">
          <div class="row gap-7 gap-xl-0">
            <div class="col-xl-4 col-xxl-4">
              <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100"
                data-aos-duration="1000">
                <span
                  class="round-36 flex-shrink-0 text-white rounded-circle bg-primary hstack justify-content-center fw-medium">08</span>
                <hr class="border-line bg-white">
                <span class="badge text-bg-dark">FAQ</span>
              </div>
            </div>
            <div class="col-xl-8 col-xxl-7">
              <div class="row">
                <div class="col-xxl-9">
                  <div class="d-flex flex-column gap-6" data-aos="fade-up" data-aos-delay="100"
                    data-aos-duration="1000">
                    <h2 class="mb-0">Pertanyaan yang sering diajukan</h2>
                    <p class="fs-5 mb-0 text-opacity-70">Temukan jawaban untuk pertanyaan umum tentang pemesanan tiket, metode pembayaran, dan kebijakan bioskop.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row justify-content-end">
            <div class="col-xl-8">
              <div class="accordion accordion-flush" id="accordionFlushExample" data-aos="fade-up" data-aos-delay="200"
                data-aos-duration="1000">
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-8 fw-bold" type="button" data-bs-toggle="collapse"
                      data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                      Bagaimana cara memesan tiket secara online?
                    </button>
                  </h2>
                  <div id="flush-collapseOne" class="accordion-collapse collapse"
                    data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body pt-0 fs-5 text-dark">Anda dapat menemukan semua detail di halaman bantuan khusus kami atau hubungi dukungan pelanggan kami untuk pertanyaan spesifik.</div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-8 fw-bold" type="button" data-bs-toggle="collapse"
                      data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                      Metode pembayaran apa saja yang diterima?
                    </button>
                  </h2>
                  <div id="flush-collapseTwo" class="accordion-collapse collapse"
                    data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body pt-0 fs-5 text-dark">Anda dapat menemukan semua detail di halaman bantuan khusus kami atau hubungi dukungan pelanggan kami untuk pertanyaan spesifik.</div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-8 fw-bold" type="button" data-bs-toggle="collapse"
                      data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                      Bisakah saya membatalkan atau me-refund tiket saya?
                    </button>
                  </h2>
                  <div id="flush-collapseThree" class="accordion-collapse collapse"
                    data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body pt-0 fs-5 text-dark">Anda dapat menemukan semua detail di halaman bantuan khusus kami atau hubungi dukungan pelanggan kami untuk pertanyaan spesifik.</div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-8 fw-bold" type="button" data-bs-toggle="collapse"
                      data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                      Apakah ada diskon untuk siswa atau lansia?
                    </button>
                  </h2>
                  <div id="flush-collapseFour" class="accordion-collapse collapse"
                    data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body pt-0 fs-5 text-dark">Anda dapat menemukan semua detail di halaman bantuan khusus kami atau hubungi dukungan pelanggan kami untuk pertanyaan spesifik.</div>
                  </div>
                </div>
                <div class="accordion-item border-bottom">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-8 fw-bold" type="button" data-bs-toggle="collapse"
                      data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                      Berapa lama saya harus datang sebelum film dimulai?
                    </button>
                  </h2>
                  <div id="flush-collapseFive" class="accordion-collapse collapse"
                    data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body pt-0 fs-5 text-dark">Anda dapat menemukan semua detail di halaman bantuan khusus kami atau hubungi dukungan pelanggan kami untuk pertanyaan spesifik.</div>
                  </div>
                </div>
              </div>
            </div>
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
              <a href="#" target="_blank"
                class="link-hover hstack gap-3 text-white fs-5">
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


  <script src="{{asset("assets/libs/jquery/dist/jquery.min.js")}}"></script>
  <script src="{{asset("assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js")}}"></script>
  <script src="{{asset("assets/libs/owl.carousel/dist/owl.carousel.min.js")}}"></script>
  <script src="{{asset("assets/libs/aos-master/dist/aos.js")}}"></script>
  <script src="{{asset("assets/js/custom.js")}}"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>