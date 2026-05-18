Laporan Perkembangan Website CineTix (Ticketing Bioskop)
Week 2

Link GitHub :
https://github.com/Hans-Maulana/CineTix-Sistem-Manajemen-Bioskop/branches 

1. Juan Alexander Nathaniel - 2472020
Backend & Database
	- Tambah kolom baru (total_amount, qr_redeem, countdown_seconds, method) di tabel bookings & payments.
	- Fix bug schedule_id di BookingController + kursi Baris D di StudioSeeder.
	- generate QR Code
	- Pusher dibungkus try-catch biar website tetap jalan kalau koneksi real-time gagal.
	- Strategy Pattern dipakai untuk handle metode pembayaran (QRIS & VA).

UI/UX
	- Tampilan Dashboard Admin  diperbaiki grafik, warna, dan lain-lain.
	- History transaksi diubah jadi Grid 2 Kolom dengan poster film.
	- E-Ticket didesain ulang mirip tiket fisik (poster, QR Code, perforasi).
	- Payment diperbagus

- Perbaikan Google OAuth lagi pada redirect URI dan validasi kredensial di GoogleController.
- CRUD & Infrastruktur Film, Studio, Jadwal , Booking.
- Fix error 419 (Page Expired), jalankan storage:link, dan pastikan semua route aktif.


2. Hans Maulana - 2472052
Frontend
	    - Blade sudah sampai tahap payment, tapi route & controller baru tersambung sampai pilih kursi.
	    - Navbar diperbaiki ukurannya agar lebih proporsional.
	    - Landing page sudah terhubung ke database sehingga konten tampil dinamis.

Backend & Database
	    - Banyak atribut kolom di migration sebelumnya salah, sudah diperbaiki agar struktur tabel konsisten.



3. Yoel Kristianto - 2472057

Fitur Booking Kursi & Real-Time Update
- Membuat UI denah kursi dinamis dari database (termasuk fitur lorong kosong) dengan indikator warna interaktif (Tersedia, Dipilih, Dipesan).

Fitur Real-Time:
- Mengintegrasikan Laravel Broadcasting & Pusher (WebSocket) sehingga status/warna kursi yang dipesan orang lain langsung update otomatis di layar tanpa perlu refresh.

Arsitektur & Keamanan Backend:  
- Menerapkan Pessimistic Locking di database untuk mencegah rebutan kursi (double-booking) di detik yang sama.
- Menggunakan Strategy Pattern pada sistem pembayaran (QRIS/VA) agar mudah ditambahkan metode baru.
- Menggunakan State Pattern untuk alur status kursi (available -> pending -> booked).