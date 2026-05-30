Laporan Pengembangan Website CineTix 
week 4 – Progress perkiraan 60% 

Link Repository week4 : 
https://github.com/Hans-Maulana/CineTix-Sistem-Manajemen-Bioskop/tree/week4 
 
1. 2472020 - Juan Alexander Nathaniel 
Halaman Login (Sign-In) 

Tema Sinematik: background diganti gradasi biru malam (linear-gradient #0c0b24 → #1A1953). 
Input & Tombol: field input jadi garis bawah minimalis, tombol login & Google Auth diberi animasi hover premium. 
Logo: path logo CineTix diperbaiki, warna fill text di logo-dark.svg dibuat kontras agar jelas di form login. 

Genre di Panel Admin 
Bug Fix: teks genre hilang diperbaiki ($genre->name → $genre->genre_name). 
UI Baru: daftar genre diubah dari scroll-checkbox menjadi badge/pill interaktif. 
   Terpilih → biru tua CineTix + shadow lembut. 
   Tidak terpilih → abu-abu muda bersih. 

Penyelarasan Warna Lembut 
Admin Panel: background jadi soft grayish-blue (#f5f7fb), form card jadi off-white (#fafafb) dengan border tipis. 
Halaman Pelanggan (Landing Page & Global Layout): putih kontras diganti #f5f7fb, seksi film jadi #ebedf3. 

Video Banner Utama  
Integrasi YouTube: video lokal diganti embed Jumbo Official Trailer, resolusi 1080p HD. 
Autoplay Muted + Loop: video otomatis jalan tanpa suara, berulang tanpa henti. 
Fade-in 2.5 detik: sembunyikan kontrol & judul YouTube saat pertama kali dimuat. 
Overlay Gradient Transparan-ke-Gelap: video lebih cerah di tengah, teks putih tetap kontras. 
Responsif Fullscreen: tampil penuh di desktop & mobile tanpa black bars. 

 

2. 2472052 – Hans Maulana 
Sistem Promo 
Promo umum selesai: berlaku untuk semua customer, limit per orang. 
Guest checkout selesai: bisa beli tiket tanpa login, isi email + popup konfirmasi. 
App Mail selesai: tiket otomatis dikirim ke email (member & guest). 
Promo untuk guest: diarahkan login/daftar agar bisa pakai promo. 
Validasi promo via PromoValidationHandler (CoR)  

Guest Checkout Flow 
Alur lengkap: pilih film → kursi → isi email → konfirmasi popup → kursi terkunci 5 menit → pilih pembayaran (QRIS/VA) → tiket dikirim via email. 
Data guest disimpan di bookings.guest_email. 

Admin Panel Fixes 
Helper di model Booking: customerName, customerEmail, customerPhone, customerTypeLabel, isGuest. 
Perbaikan di: Manajemen Booking, Manajemen Tiket & Scan QR, Dashboard booking pending, Detail promo null-safe. 
Pencarian tiket admin mencakup guest_email. 
VA guest pakai booking_id. 
Event BookingConfirmed tidak broadcast ke channel user jika guest. 

Database & Arsitektur 
Tabel promos: max_usage, max_usage_per_customer, usage_count. 

Tabel promo_usages: melacak pemakaian per promo_id & user_id. 

Migration add guest fields: guest_email, access_token, user_id nullable. 

Design pattern: 

CoR: BookingApprovalChain & PaymentValidationChain. 

Strategy: PaymentContext (QRIS/VA). 

State: Seat status (available → pending → booked). 

Observer: PaymentObserver (kursi kembali jika gagal), MailObserver (kirim tiket sukses). 
 

3. 2472057 – Yoel Kristianto 

Database & Model 

Migration baru: tambah kolom guest_name & guest_email di tabel bookings. 

Model Booking.php: update $fillable agar data guest tersimpan. 

BookingController 

Fix duplikasi user_id saat create booking. 

Transaksi user login & guest sekarang ditangani terpisah. 

Validasi session khusus guest dengan guest_booking_id → payment & status tetap aman. 

Admin Dashboard 

Perbaikan error di index.blade.php saat booking tidak punya relasi user_id. 

Implementasi fitur baru: 

Validasi bentrok jadwal (mencegah overlapping film di studio). 

Auto End Time: hitung otomatis end_time dari start_time + durasi film. 

Pengembangan visual dashboard untuk monitoring operasional. 

Admin Promo 

Perbaikan icon tombol aksi (Detail, Edit, Hapus) → pakai Bootstrap Icons. 

Tipografi: teks kode promo jadi fw-bold text-dark. 

Standardisasi layout: container & header konsisten dengan modul admin lain. 

Promo Customer (UI) 

Redesain total card promo → clean, minimalis, profesional. 

Header: judul “Kode Promo Saya” lebih besar & modern. 

Styling kupon: header solid gelap + kode promo dalam rounded-pill gelap dengan teks putih. 

Tombol interaksi: semua jadi rounded-pill navy blue (Beranda, Salin Kode, Pesan Tiket). 

 