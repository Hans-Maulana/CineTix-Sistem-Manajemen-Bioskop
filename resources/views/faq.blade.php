@extends('layouts.app')

@section('content')
<div class="container py-10">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-8" data-aos="fade-up">
                <span class="badge text-bg-dark mb-3">FAQ</span>
                <h2 class="mb-3">Pertanyaan yang sering diajukan</h2>
                <p class="fs-5 text-muted">Temukan jawaban untuk pertanyaan umum tentang pemesanan tiket, metode pembayaran, dan kebijakan bioskop.</p>
            </div>

            <div class="accordion accordion-flush shadow-sm rounded-4 overflow-hidden border" id="accordionFlushExample" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-5 fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                      Bagaimana cara memesan tiket secara online?
                    </button>
                  </h2>
                  <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body py-4 fs-6 text-dark">Pilih film yang ingin Anda tonton di halaman Beranda atau Cari Film, pilih jadwal tayang, lalu klik "Pilih Kursi". Setelah memilih kursi, Anda dapat melanjutkan ke pembayaran.</div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-5 fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                      Metode pembayaran apa saja yang diterima?
                    </button>
                  </h2>
                  <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body py-4 fs-6 text-dark">Kami menerima berbagai metode pembayaran digital termasuk Transfer Bank (VA), E-Wallet (OVO, GoPay, Dana), dan Kartu Kredit.</div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-5 fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                      Bisakah saya membatalkan atau me-refund tiket saya?
                    </button>
                  </h2>
                  <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body py-4 fs-6 text-dark">Kebijakan pembatalan tiket bergantung pada ketentuan masing-masing bioskop. Umumnya, pembatalan dapat dilakukan minimal 2 jam sebelum film dimulai dengan biaya admin tertentu.</div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-5 fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                      Apakah ada diskon untuk siswa atau lansia?
                    </button>
                  </h2>
                  <div id="flush-collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body py-4 fs-6 text-dark">Diskon khusus seringkali tersedia pada hari-hari tertentu atau melalui promo partner kami. Pastikan Anda mengecek bagian "Promo" sebelum melakukan pemesanan.</div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header">
                    <button class="accordion-button collapsed fs-5 fw-bold py-4" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                      Berapa lama saya harus datang sebelum film dimulai?
                    </button>
                  </h2>
                  <div id="flush-collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body py-4 fs-6 text-dark">Kami merekomendasikan untuk tiba di bioskop minimal 15-30 menit sebelum jadwal tayang untuk proses scan tiket dan pembelian popcorn atau minuman.</div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
