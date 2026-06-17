# Daftar *Design Pattern* di CineTix

Kalian sudah menyadari penerapan 3 *Design Pattern* utama yang memang diimplementasikan secara eksplisit (sebagai *behavioral logic*), yaitu:
1. **Chain of Responsibility (COR)**: Digunakan dalam proses persetujuan *booking* (`BookingApprovalChain`) dan validasi *payment* (`PaymentValidationChain`).
2. **Observer / Pub-Sub (Event-Listener)**: Digunakan saat memperbarui status kursi secara *real-time* dengan WebSockets/Pusher (`SeatStatusUpdated` Event).
3. **Strategy Pattern**: Digunakan dalam sistem metode pembayaran (`PaymentContext`, `QrisStrategy`, `VirtualAccountStrategy`), sehingga logika pembayaran bisa ditukar dengan mudah tanpa mengubah alur utamanya.

---

Namun selain ketiga pola di atas, program ini juga menerapkan banyak pola lain (sebagian besar memang bawaan standar *framework* Laravel yang kalian manfaatkan). Berikut adalah daftarnya:

### 4. MVC (Model-View-Controller) - *Architectural Pattern*
Ini adalah fondasi utama program kalian.
- **Model**: Kelas-kelas di `app/Models` seperti `Booking`, `Film`, `Seat` mengatur struktur dan interaksi data.
- **View**: File-file `.blade.php` seperti `history.blade.php`, `tickets.blade.php` menangani tampilan (*user interface*).
- **Controller**: Kelas di `app/Http/Controllers` seperti `BookingController`, `RefundController` yang menghubungkan rute/tindakan dari UI ke logika bisnis dan basis data.

### 5. Active Record Pattern
Ini adalah cara kerja Eloquent ORM dari Laravel yang kalian pakai. Dalam pola ini, satu baris di tabel *database* direpresentasikan oleh satu *instance object* (misal objek `Booking`), dan objek tersebut punya metode langsung untuk melakukan *save*, *update*, atau *delete*.
- **Contoh di kode:** `$booking->update(['status' => 'refunded']);`

### 6. Facade Pattern
*Facade* berfungsi memberikan *interface* statis (yang terlihat sederhana) kepada susunan sistem di baliknya yang sangat kompleks. Di program ini, kalian sangat bergantung pada kelas *Facade* Laravel.
- **Contoh di kode:** 
  - `Auth::user()` (Menyederhanakan panggilan ke layanan *Authentication*).
  - `DB::transaction(...)` (Menyederhanakan panggilan ke layanan *Database Connection*).
  - `Log::info(...)` dan `Mail::to(...)`.

### 7. Factory Method Pattern
Digunakan secara ekstensif untuk dua hal:
1. **Database Seeding**: Pembuatan data *dummy* menggunakan *Factory* (`FilmFactory`, `StudioFactory`).
2. **Context Pembayaran**: Di dalam `PaymentContext::resolve($method)`, kalian menggunakan pola mirip *Factory* untuk membuat atau memanggil *Strategy Object* yang tepat (QRIS atau VA) berdasarkan *string input*.

### 8. Dependency Injection (DI)
Kalian sangat sering menggunakan pola ini ketika meminta objek di dalam parameter fungsi (terutama di kelas *Controller*), dan Laravel secara otomatis memberikan objek yang dimaksud *(IoC / Inversion of Control)*.
- **Contoh di kode:** `public function show(Schedule $schedule)` di mana Laravel otomatis mencari ID Schedule di *database* dan menyuntikkan (meng-*inject*) *instance* `Schedule` yang sudah jadi ke dalam parameter fungsi.

### 9. Singleton Pattern
Secara tidak langsung, *services* bawaan Laravel yang kalian gunakan (seperti koneksi ke Redis, sistem Cache, atau koneksi Database) diload menggunakan *Singleton Pattern*, yang berarti aplikasi hanya membuat satu *instance object* di *memory* yang digunakan berulang-ulang selama siklus satu kali *request* halaman web.
