Laporan Pengembangan Website CineTix
week 6 – Progress perkiraan 90%
Link Repository week6 :
https://github.com/Hans-Maulana/CineTix-Sistem-Manajemen-Bioskop/tree/week6

1. 2472020 - Juan Alexander Nathaniel
Perubahan UI/UX & Mekanisme Teknis
Halaman Pencarian Film
• Tampilan sekarang pakai grid card modern dengan poster film.
• Ada tambahan badge status (Now Playing/Coming Soon), klasifikasi usia, rating,
durasi, dan genre tag interaktif.
• Desain lebih ringkas, konsisten, dan gampang dipindai sama user.
Halaman Laporan Admin
• Layout diubah jadi gaya dashboard analitik.
• Ada stat card singkat di bagian atas, filter chips aktif/nonaktif.
• Tabel laporan dilengkapi ringkasan total plus grid detail transaksi.
• Fokus ke keterbacaan data dan navigasi cepat antar level laporan.
Logika & Mekanisme Drill-down
• Tombol Detail bikin user bisa ngulik data secara bertingkat.
• Parameter filter (bulan, hari, metode pembayaran) dikirim dinamis ke backend.
• Hasilnya muncul berlapis: Total pendapatan , Jumlah tiket , Daftar transaksi detail
per periode
Jadi analisis bisa lebih fleksibel, dari level makro (summary) sampai mikro (detail
transaksi).
Preservasi Tab dengan JavaScript
• Ada script buat melacak tab aktif yang dipilih user.
• Tab aktif tetap ke-save walau user pindah halaman (pagination) atau reload.
• Pengalaman jadi lebih konsisten, nggak perlu repot pilih ulang tab tiap kali pindah
halaman.
Aturan Cetak Fisik (@media print)
• Layout khusus cetak disiapin buat kertas A4 portrait.
• Elemen dekoratif (sidebar, tombol, filter) otomatis disembunyiin biar hasil cetak
bersih.
• Fokus ke konten inti: tabel laporan, stat card, dan ringkasan.
• Output cetak jadi rapi, profesional, dan siap dipakai sebagai dokumen fisik.

2. 2472052 - Hans Maulana Budiputra
Perbaikan Alur Booking
• Form Booking
– Submit sering gagal untuk guest maupun user login → alur submit diperbaiki supaya
booking berjalan normal.
• OTP Guest
– Validasi OTP sebelumnya hanya di frontend → sekarang dipindah ke backend lewat
BookingApprovalChain dan UserAuthorizationHandler.
• Email Guest
– Guest dulu bisa booking tanpa email terverifikasi → sekarang email wajib cocok dengan
session verified_guest_email.
• Validasi Kursi
– Pengecekan kursi tidak konsisten antar jadwal → bug di SeatAvailabilityHandler sudah
diperbaiki.
• Booking Window
– Jadwal di luar window penjualan masih bisa dibooking → ditambahkan
BOOKING_WINDOW_DAYS = 7 dan method isWithinBookingWindow() di HomeController,
FilmController, dan BookingController.
Perbaikan UI/UX & Flow
• Filter Genre/Klasifikasi
– Data di landing page tidak sesuai database → diperbaiki dengan mapping alias (contoh: G
→ SU, PG-13 → 13+, R → 17+).
• Redirect Setelah Auth
– Guest login/register di tengah booking malah diarahkan ke homepage → sekarang pakai
RedirectAfterAuth + parameter redirect agar kembali ke halaman booking.
• Preservasi Kursi
– Pilihan kursi hilang setelah login → disimpan sementara ke session server lewat endpoint
remember-seats + backup sessionStorage, lalu dipulihkan otomatis.
Mekanisme Payment
• Duplicate Pending Payment
– Tiap ganti metode pembayaran bikin record pending baru → sekarang payment lama
ditutup otomatis, payment aktif direuse kalau metodenya sama.
• Expired Payment
– Payment yang sudah expired dibersihkan otomatis sebelum proses lanjut.
• Release Kursi
– Kursi ikut dilepas saat payment pending lama di-mark failed → PaymentObserver
diperbaiki supaya kursi tetap aman selama masih ada payment pending aktif.
Pengiriman Email Tiket
• Kejelasan Status Email
– Email tiket tidak jelas terkirim atau gagal → ditambahkan method sendTicketEmail
dengan logging + flash ticket_email_sent.
• Selesaikan Pembayaran
– Jika user klik “Selesaikan Pembayaran” padahal payment sudah sukses → sistem kirim
ulang email, bukan error.
• Resend Ticket
– Ditambahkan endpoint resend-ticket dengan rate limit 3x/jam.
• Flash Status Email
– Halaman tiket dulu selalu menampilkan pesan email terkirim → logika flash diperbaiki
agar sesuai status sebenarnya.
• SMTP Gmail
– Pengiriman email via SMTP Gmail sudah terverifikasi di log → kalau tidak masuk inbox
kemungkinan karena spam filter/kebijakan email kampus.