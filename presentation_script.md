# Naskah Presentasi CineTix (Format 3 Presenter)

*(Catatan: Naskah ini ditulis untuk kelompok beranggotakan 3 orang. Masing-masing orang mempresentasikan tepat 4 fitur berurutan. Bagian dalam kurung tebal **[Tindakan: ...]** adalah panduan untuk orang yang bertugas mengoperasikan laptop/mouse).*

---

## PRESENTER 1: (Silakan sebutkan nama Anda)
*(Fokus: Interaksi Awal Pengguna & Logika Pemilihan Kursi)*

**Presenter 1:**
"Selamat pagi/siang Bapak/Ibu Dosen dan teman-teman. Kami akan mendemonstrasikan **CineTix**, sebuah Sistem Manajemen Bioskop modern tingkat *Enterprise*. Kami telah meringkas sistem ini menjadi 12 fitur inti yang sarat akan implementasi *Design Pattern*. Saya akan mempresentasikan 4 fitur pertama."

### 1. Pencarian Film & Landing Page
**[Tindakan: Buka *browser*, tampilkan halaman awal (Landing Page) CineTix dan *scroll* perlahan]**
**Presenter 1:**
"Pertama adalah halaman *Landing Page*. Halaman ini secara murni mengimplementasikan arsitektur **MVC (Model-View-Controller)**. Melalui pola **Active Record** (Eloquent), kami memanggil query film yang sedang tayang dengan sangat efisien langsung dari *database*."

### 2. Autentikasi Fleksibel & Guest OTP
**[Tindakan: Tampilkan modal *Login*, lalu beralih tunjukkan *input Email Guest OTP*]**
**Presenter 1:**
"Kedua, sistem autentikasi. Kami melayani *Member Login*, *Google OAuth*, serta *Guest Booking* via OTP Email. Di balik ini, kami banyak menggunakan **Facade Pattern** khas Laravel seperti `Auth::` dan `Socialite::` yang membungkus kompleksitas enkripsi di belakang *interface* yang statis dan rapi."

### 3. Pemilihan Kursi Real-Time (Interactive Seating)
**[Tindakan: Klik salah satu film, pilih jadwal, lalu buka denah kursi. Di layar lain/HP, simulasikan kursi di-klik agar warna kursi di layar utama langsung berubah menjadi abu-abu]**
**Presenter 1:**
"Fitur ketiga adalah Pemilihan Kursi *Real-time*. Jika ada orang lain yang mengunci kursi, layar pengguna lain akan otomatis diperbarui detik itu juga. 
**[Tindakan: Buka VS Code, tampilkan file `app/Events/SeatStatusUpdated.php`]**
Ini bisa terjadi berkat **Observer Pattern (Pub-Sub)** menggunakan WebSockets/Pusher. *Event* ini otomatis memancarkan status terbaru ke seluruh penonton yang membuka jadwal yang sama, sehingga mencegah bentrok kursi ganda."

### 4. Validasi Pemesanan Khusus
**[Tindakan: Kembali ke *Browser*, klik kursi yang tersedia, lalu tekan tombol 'Pesan']**
**Presenter 1:**
"Keempat adalah Validasi Booking. Saat tombol ditekan, ia tidak langsung menyimpan data. 
**[Tindakan: Buka VS Code, tampilkan file `app/Services/ChainOfResponsibility/BookingApprovalChain.php`]**
Di sini kami mengimplementasikan **Chain of Responsibility (COR)**. Sebuah rantai *handler* berjejer untuk memverifikasi apakah pengguna diotorisasi, apakah jadwal belum ditutup, dan apakah kursi masih tersisa. Jika satu rantai gagal, seluruh proses diputus.

Selanjutnya, alur pembayaran akan dilanjutkan oleh rekan saya."

---

## PRESENTER 2: (Silakan sebutkan nama Anda)
*(Fokus: Checkout, Transaksi Gateway, & Pelayanan Pasca Bayar)*

**Presenter 2:**
"Terima kasih. Saya akan melanjutkan ke 4 fitur berikutnya yang berkaitan dengan pemrosesan transaksi dan layanan pasca-bayar."

### 5. Checkout & Penggunaan Promo
**[Tindakan: Kembali ke *Browser*, tampilkan Halaman Pembayaran. Ketik kode promo dan tunjukkan harga terpotong]**
**Presenter 2:**
"Fitur kelima adalah Halaman Checkout & Promo. Kami menahan sesi keranjang sementara pengguna memasukkan kode promo. Sistem mengecek *limit* pemakaian promo menggunakan logika **Active Record** secara langsung dari *database* untuk memastikan pelanggan tidak melebihi kuota."

### 6. Sistem Pembayaran Modular (Payment Gateway)
**[Tindakan: Tunjukkan opsi pilihan pembayaran QRIS dan Virtual Account di layar]**
**Presenter 2:**
"Keenam adalah Sistem Pembayaran. Kami punya QRIS dan Virtual Account. Di kode *backend*-nya, kami menggunakan gabungan dua *pattern* yang kuat.
**[Tindakan: Buka VS Code, tampilkan file `app/Services/Payment/PaymentContext.php`]**
Kami menggunakan **Strategy Pattern** dipadukan dengan **Factory Method Idiom**. Kelas *Context* ini akan memproduksi objek tipe pembayaran secara terpisah tergantung apa yang di-klik pengguna. Keunggulannya: jika besok kami mau tambah modul GoPay, kode utama pemesanan tiket tidak perlu diubah sama sekali."

### 7. Pengiriman E-Ticket (QR Code)
**[Tindakan: Kembali ke *Browser*, simulasikan pembayaran sukses. Buka halaman *inbox email* yang menampilkan E-Ticket ber-QR Code]**
**Presenter 2:**
"Ketujuh, Pengiriman Tiket. Setelah berhasil, kami men-*generate* QR Code tiket. Sama seperti autentikasi tadi, fungsionalitas pengiriman tiket ini sangat rumit di belakang layar, namun kami membungkusnya menggunakan **Facade Pattern** (`Mail::to()`) yang rapi di sisi *Controller*."

### 8. Pengembalian Dana Otomatis (Auto-Refund)
**[Tindakan: Buka menu 'Riwayat Transaksi', lalu klik tombol 'Ajukan Refund' pada salah satu tiket]**
**Presenter 2:**
"Kedelapan adalah fitur Pengembalian Dana (Auto-Refund) untuk pesanan yang dibatalkan maksimal 2 jam sebelum tayang.
**[Tindakan: Buka VS Code, tampilkan file `app/Http/Controllers/RefundController.php`]**
Alih-alih membuat sistem manual bagi admin, kami menggunakan **Active Record** untuk merombak nilai `status` menjadi `refunded` secara *real-time*. Fungsi ini otomatis memanggil **Observer** milik kursi agar status kursi tersebut dikembalikan ke pasar detik itu juga.

Untuk sisa fitur dari sisi interaksi lain dan dasbor manajemen, akan dijelaskan oleh rekan saya."

---

## PRESENTER 3: (Silakan sebutkan nama Anda)
*(Fokus: Interaksi Ulasan, Validasi Fisik Tiket, & Dasbor Pelaporan Admin)*

**Presenter 3:**
"Terima kasih. Bagian saya akan berfokus pada fitur operasional dan *backend management*."

### 9. Sistem Review & Rating Film
**[Tindakan: Buka *Browser*, masuk ke Halaman Detail Film yang jadwalnya sudah lewat, tulis *review* bintang 5]**
**Presenter 3:**
"Fitur kesembilan adalah Ulasan Film. Pengguna yang divalidasi 'sudah menonton' bisa memberikan ulasan. Ini menggunakan pondasi standar **MVC** dan **Active Record** untuk menyimpan ulasan dan merekapitulasi rata-rata (*average*) peringkat bintang pada film terkait."

### 10. Manajemen Master Data (Film & Jadwal)
**[Tindakan: *Log out* dari pelanggan, *Login* sebagai Admin. Tunjukkan halaman tabel Daftar Film dan Jadwal Tayang]**
**Presenter 3:**
"Kesepuluh adalah *Dashboard* Admin. Di sini pengelola bioskop dapat mengontrol penuh data film, poster, serta menambah jadwal tayang lengkap dengan modifikasi harganya. Ini adalah *CRUD (Create, Read, Update, Delete)* komprehensif berstandar **MVC** yang kami desain agar ramah guna bagi petugas operasional."

### 11. Pemindai Tiket Fisik (QR Scanner)
**[Tindakan: Buka menu 'Scan Tiket' di Dasbor Admin, tampilkan antarmuka kamera (atau simulasikan *scan* tiket)]**
**Presenter 3:**
"Kesebelas adalah *QR Scanner*. Penjaga bioskop memindai tiket pelanggan saat di pintu masuk. Logika ini kembali menggunakan prinsip **Chain of Responsibility** di mana saat *QR text* dikirim, *backend* memvalidasi *hash*-nya, mengecek apakah tiket kedaluwarsa, atau apakah tiket tersebut merupakan tiket yang di-*refund*. Jika sah, status *redeem* diubah lewat **Active Record**."

### 12. Laporan Penjualan (Export PDF)
**[Tindakan: Masuk ke menu 'Laporan', pilih rentang tanggal, klik 'Export PDF', lalu buka file PDF yang terunduh]**
**Presenter 3:**
"Terakhir, fitur Keduabelas adalah Pelaporan Eksekutif. Admin dapat mengunduh laporan keuangan berfilter.
**[Tindakan: Buka VS Code, tampilkan `app/Http/Controllers/Admin/ReportController.php`]**
Perhatikan pada bagian parameter fungsi laporan ini. Kami mengimplementasikan **Dependency Injection**. Laravel secara ajaib menyuntikkan (meng-*inject*) layanan pembuatan PDF langsung ke dalam kelas ini tanpa harus kami *hardcode* pengaturannya secara manual.

Dengan 12 fitur terintegrasi dan belasan pola perancangan *software* ini, CineTix menjadi platform yang anti-rentan dan siap dipasarkan skala besar. Sekian demonstrasi dari kelompok kami, atas perhatiannya kami ucapkan terima kasih."
