@extends('layouts.app')

@section('content')
<div class="container py-10">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!--  Tentang Kami Section -->
            <section class="why-choose-us py-4 py-lg-8 py-xl-10">
                <div class="row justify-content-between gap-5 gap-xl-0">
                    <div class="col-xl-4 col-xxl-4">
                        <div class="d-flex flex-column gap-7">
                            <div class="d-flex align-items-center gap-7 py-2" data-aos="fade-right" data-aos-delay="100" data-aos-duration="1000">
                                <span class="round-36 flex-shrink-0 text-white rounded-circle bg-primary hstack justify-content-center fw-medium">04</span>
                                <hr class="border-line">
                                <span class="badge text-bg-dark">Tentang Kami</span>
                            </div>
                            <h2 class="mb-0" data-aos="fade-right" data-aos-delay="200" data-aos-duration="1000">Mengapa memilih CineTix</h2>
                            <p class="mb-0 fs-5" data-aos="fade-right" data-aos-delay="300" data-aos-duration="1000">Kami menggabungkan kenyamanan fasilitas dengan teknologi sinema terkini untuk menghadirkan pengalaman menonton yang tak terlupakan bagi setiap pengunjung.</p>
                        </div>
                    </div>
                    <div class="col-xl-8 col-xxl-7">
                        <div class="row">
                            <div class="col-lg-6 mb-7 mb-lg-0">
                                <div class="card position-relative overflow-hidden bg-primary h-100 p-4" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex flex-column gap-3">
                                            <h4 class="text-white">Visi Kami</h4>
                                            <p class="mb-0 fs-6 text-white">Menjadi platform hiburan sinema terdepan yang memberikan akses hiburan berkualitas tinggi bagi seluruh masyarakat Indonesia.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-7 mb-lg-0">
                                <div class="card border h-100 position-relative overflow-hidden p-4" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1000">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div>
                                            <h2 class="mb-0">238+</h2>
                                            <p class="mb-0 text-dark">Bioskop Jaringan Kami</p>
                                        </div>
                                        <div class="d-flex flex-column gap-3 mt-4">
                                            <p class="mb-0 fs-5 text-dark">Jangkauan global kami memungkinkan kami untuk menghadirkan pengalaman sinema yang relevan secara budaya bagi penonton di berbagai lokasi.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
