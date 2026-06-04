Laporan Perkembangan Website CineTix (Ticketing Bioskop)
Week 3
Link GitHub: https://github.com/Hans-Maulana/CineTix-Sistem-ManajemenBioskop/tree/week3
Progress Anggota Tim:
1. Yoel Kristianto - 2472057
Backend Refactoring & Query Optimization (Builder Pattern)
• Mengimplementasikan Builder Pattern memanfaatkan fitur Query Scopes
Laravel pada berkas Film.php (scopeFilterGenre dan scopeFilterRatingUsia)
untuk menangani penyaringan data film secara dinamis berdasarkan genre serta
klasifikasi rating usia langsung dari database.
• Menerapkan prinsip KISS (Keep It Simple, Stupid) dengan pendekatan Early
Return pada logika filter query untuk menyederhanakan pembacaan kode
program sekaligus mengoptimalkan kecepatan eksekusi query ke database.

2. Juan Alexander Nathaniel - 2472020
Reports & Analytics
• Membuat sistem pelaporan (reports) performa berkala yang terbagi per film dan
per bulan/tahun.
• Mengintegrasikan fitur ekspor laporan keuangan dan data transaksi ke dalam
format Excel (.xlsx) dan PDF (.pdf).
Ticket Management
• Membangun modul manajemen data tiket yang telah terjual kepada customer.
• Menambahkan fitur verifikasi tiket secara manual oleh pihak resepsionis/admin
di bioskop.
Film Management
• Mengimplementasikan kalkulasi rating film otomatis yang diambil dari
akumulasi ulasan (reviews) para pelanggan.
• Melakukan standardisasi aset poster film, baik dari segi rasio tampilan maupun
optimalisasi format gambar (WEBP/AVIF) agar pemuatan halaman lebih cepat.
• Menambahkan data film pada FilmSeeder sekaligus melengkapi berkas fisik
gambar poster di dalam folder storage.
Frontend & Admin Dashboard
• Menambahkan kontrol tombol navigasi (Next/Prev) pada komponen carousel
halaman utama (Landing Page).
• Meningkatkan kejelasan informasi pada modal booking di sisi admin dengan
menyematkan komponen badge status pembayaran berwarna dinamis (Lunas,
Pending, Gagal).

3. Hans Maulana Budiputra - 2472052
Backend & Architecture (Chain of Responsibility Design Pattern)
• Mengarsiteki dan menerapkan Design Pattern Chain of Responsibility (CoR)
sebagai fondasi sistem validasi berlapis guna mengamankan tiga alur utama
transaksi:
    1. Payment Validation Chain: Memvalidasi kondisi pembayaran sebelum
    diselesaikan. Alur pengecekan meliputi: validasi relasi booking
    (mencegah double pending payment), pengecekan kedaluwarsa
    otomatis, serta konfirmasi status pending pada data payment dan
    booking.
    2. Booking Approval Chain: Memvalidasi kelayakan pemesanan sebelum
    data masuk ke database. Alur pengecekan meliputi: status autentikasi
    user, ketersediaan fisik kursi bioskop secara real-time, dan validasi kode
    promo (expired atau tidak).
    3. Cancellation Chain: Mengelola validasi pembatalan pesanan secara
    logis. Menjamin pemesanan yang dibatalkan masih berstatus pending,
    melepas kunci (lock) kursi, serta mengubah otomatis status pembayaran
    terkait menjadi failed.
    Controller & Observer Integration
    • Melakukan refaktorisasi pada app/Http/Controllers/BookingController.php:
    o Method store() kini diintegrasikan dengan BookingApprovalChain::build()
    sebelum proses pembuatan booking.
    o Method confirmPayment() kini diintegrasikan dengan
    PaymentValidationChain::build() sebelum menyelesaikan transaksi.
    • Membuat PaymentObserver yang berfungsi mendeteksi kegagalan/kedaluwarsa
    pembayaran untuk otomatis melepas dan mengembalikan status kursi kembali
    menjadi tersedia (available).
    Frontend, UI & Flow Optimization
    • Memperbaiki interaksi kartu transaksi pada riwayat pengguna dengan
    membatasi area klik hanya berlaku pada tombol Lihat Tiket.
    • Meningkatkan keterbacaan status transaksi pada halaman riwayat (history)
    dengan standarisasi label status yang jelas: Berhasil, Tertunda, Menunggu, dan
    Batal.
    • Mengubah fungsi dan teks tombol Ganti Metode di halaman pembayaran
    menjadi aksi tegas Batalkan Pesanan.
    • Mengimplementasikan fitur hitung mundur (countdown timer) pembayaran
    selama 5 menit yang terintegrasi dengan fungsi auto-expire ke status failed.