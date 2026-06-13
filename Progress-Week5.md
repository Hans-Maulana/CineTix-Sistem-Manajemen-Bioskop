Laporan Pengembangan Website CineTix
week 5 – Progress perkiraan 75%
Link Repository week5 :
https://github.com/Hans-Maulana/CineTix-Sistem-Manajemen-Bioskop/tree/week5

1. 2472020 - Juan Alexander Nathaniel
Perubahan UI/UX
• Halaman Pencarian Film Tampilan dirombak menjadi grid card modern dengan poster
film, badge status (Now Playing/Coming Soon), klasifikasi usia, rating, durasi, serta
genre tag interaktif. Elemen visual lebih ringkas, konsisten, dan mudah dipindai oleh
pengguna.
• Halaman Laporan Admin Layout bergaya dashboard analitik: stat card ringkas di
bagian atas, filter chips aktif/nonaktif, tabel laporan dengan ringkasan total, serta grid
detail transaksi. Fokus pada keterbacaan data dan navigasi cepat antar level laporan.
Logika & Mekanisme Drill-down
• Tombol Detail pada laporan memungkinkan pengguna menelusuri data secara bertingkat.
• Parameter filter (misalnya bulan, hari, metode pembayaran) dikirim secara dinamis ke
backend.
• Hasil ditampilkan berlapis: mulai dari total pendapatan, jumlah tiket, hingga daftar
transaksi detail per periode.
• Mekanisme ini memudahkan analisis dari level makro (summary) ke mikro (detail
transaksi).
Preservasi Tab dengan JavaScript
• Sistem menambahkan logika untuk melacak tab aktif yang dipilih pengguna.
• Tab yang sedang aktif tetap dipertahankan meski pengguna melakukan pagination atau
reload halaman.
• Memberikan pengalaman konsisten, sehingga pengguna tidak perlu mengulang memilih
tab setiap kali berpindah halaman.
️Aturan Cetak Fisik (@media print)
• Layout khusus cetak disiapkan untuk kertas A4 portrait.
• Elemen dekoratif (sidebar, tombol, filter) disembunyikan agar hasil cetak bersih.
• Fokus hanya pada konten inti: tabel laporan, stat card, dan ringkasan.
• Hasil cetak rapi, profesional, dan siap digunakan sebagai dokumen fisik.

2. 2472052 - Hans Maulana Budiputra
Dashboard Admin
• Panel Perlu Konfirmasi diganti menjadi Pembayaran Masuk (menampilkan 10 transaksi
sukses terbaru).
• Card Booking Pending langsung routing ke /admin/bookings?status=pending.
• Item Pembayaran Masuk + tombol Lihat Semua routing ke booking dengan filter
status=success.
Booking Management
• Sorting default: updated_at desc, dengan opsi tambahan (amount, status, dll).
• Filter baru: booking_status, method, per_page.
• UI dirombak ala opus: hero, stat card sebagai filter, toolbar + active filter chips, list →
grid card.
• Hilangkan email & ID pelajar (NRP) dari nama, avatar hanya 1 inisial.
Film CRUD
• Eager load: genres + count schedules + avg rating.
• Search ditambah: actors.
• Filter baru: genre, status (now playing/coming soon), classification.
• UI list jadi grid card modern: poster, badge status & klasifikasi, rating, durasi, genre tag,
aksi.
Studio CRUD
• Form pakai interactive seat layout builder: grid kotak klik untuk toggle kursi/lorong,
tambah/hapus baris-kolom, fill all.
• Tombol Kosongkan dihapus.
• Kapasitas dihitung otomatis dari layout, disimpan sebagai JSON.
• syncSeatsFromLayout aman — tidak regenerate seat jika studio sudah ada booking.
• Index list studio jadi grid card dengan mini-preview layout.
Schedule CRUD
• Schedule::autoUpdateStatuses() jalan tiap akses.
• Filter lengkap: search, film, studio, status, range, date range. Sort 4 opsi.
• Stat card: total, hari ini, akan datang, realized revenue (sum confirmed bookings).
• Schedule card detail: terjual/kapasitas, hadir/terjual, pendapatan, bar okupansi.
• Edit dikunci untuk schedule complete (tombol jadi Terkunci).
• Hapus hanya untuk on schedule tanpa booking, sisanya disabled + tooltip alasan.
• Form schedule: layout 2-panel (input + live preview film & jam).
Ticket Scan
• UI dirombak total: hero + jam digital live, scanner card kiri (QR illustration animasi
pulse), panel aktivitas scan 5 terakhir sticky kanan.
• Scanner via AJAX (kamera + manual), pop-up modal hasil scan dengan animasi & detail
customer/film/kursi.
• Monitor jadwal: tab filter (Hari Ini / Sedang Tayang / Akan Datang / Semua), card jadwal
dengan badge LIVE, mini-stat & bar okupansi.
• Tombol Lihat Pengunjung → halaman detail terpisah (bukan modal).
Halaman Detail Pengunjung Jadwal (baru)
• Route baru admin.tickets.schedule + view tickets/schedule.blade.php.
• Hero film + 4 stat card (total/hadir/belum/status).
• Search + filter (Semua/Hadir/Belum).
• List dipisah: Sudah Hadir (urut waktu scan terbaru) & Belum Scan (urut nama).
• Filter & search client-side instan.
• Activity item di sidebar scan tiket → link langsung ke halaman detail ini.
Promo CRUD
• Filter: status (active/upcoming/expired), tipe diskon, sort 5 opsi + active chips.
• Stat card: total / aktif / expired / total pemakaian.
• List jadi grid kupon card: gradient header sesuai status, notch potongan, kode dalam
dashed box, progress bar, countdown "X hari lagi".
• Form create/edit: layout 2-panel (form 4 section bernomor + live preview kupon sticky
real-time).
• Tipe diskon: radio card, prefix input otomatis (Rp / %).
• Show page: kupon preview + info card + periode/stat + riwayat customer pemakai.
Customer
• Stats card simple: 3 icon polos (Total / Sudah Booking / Belum Booking).
• Filter aktivitas (Sudah/Belum Booking) + 6 opsi sorting (terbaru, terlama, nama A-Z/ZA, paling aktif, top spender).
• List jadi grid card: avatar gradient, nama, tanggal join, email + kontak, mini-stat booking
& total belanja (auto format rb/jt).
• Total revenue member tampil di hero kanan atas.

3. 2472057 - Yoel Kristianto
• Pembuatan Sistem Mailer OTP
o Membuat class GuestOtpMail beserta template email
emails.guest_otp.blade.php.
o Email berisi kode OTP 6 digit yang dikirim ke pelanggan.
• Logika Generate & Verifikasi OTP
o Menambahkan metode sendOtp dan verifyOtp pada BookingController.
o OTP dikirim saat checkout dan diverifikasi sebelum melanjutkan ke pembayaran.
• Implementasi Cache Laravel
o OTP disimpan sementara menggunakan Cache dengan masa berlaku 5 menit.
o Mencegah penyalahgunaan dan memastikan OTP hanya valid dalam periode
singkat.
• API Routes Baru
o Endpoint /guest/send-otp untuk mengirim OTP.
o Endpoint /guest/verify-otp untuk verifikasi OTP.
o Mendukung komunikasi frontend-backend via AJAX.
• Modifikasi Pop-up/Modal Checkout
o Modal checkout di halaman booking/show.blade.php diubah menjadi TwoStep Verification:
▪ Tahap konfirmasi alamat email.
▪ Tahap input kode OTP.
• Implementasi AJAX / Fetch API
o JavaScript dirombak agar proses pengiriman email & verifikasi OTP berjalan
real-time.
o Tidak perlu reload halaman, lebih interaktif dan cepat.
• Keamanan Alur Penguncian Kursi
o Submit form pemesanan ditahan hingga OTP valid.
o Kursi baru dikunci dan diarahkan ke pembayaran setelah verifikasi OTP sukses.
o Mencegah booking palsu atau bypass sistem verifikasi