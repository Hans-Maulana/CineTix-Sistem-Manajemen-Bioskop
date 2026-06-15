# Rangkuman Design Pattern pada CineTix

Proyek CineTix ini dibangun menggunakan kerangka kerja (framework) **Laravel**, yang secara bawaan sangat kental dengan beberapa *Design Pattern* (pola desain) arsitektur perangkat lunak. Berikut adalah penjabaran dari pola desain yang digunakan beserta alasannya, dan perbandingannya dengan alternatif pola desain yang serupa.

---

## 1. MVC (Model-View-Controller)
**MVC** adalah fondasi arsitektur utama dari sistem CineTix.
- **Model**: Direpresentasikan oleh kelas-kelas di folder `app/Models` (seperti `Booking`, `Film`, `Seat`). Bertanggung jawab penuh terhadap pengolahan data dan aturan bisnis (business logic) tingkat database.
- **View**: Direpresentasikan oleh file `.blade.php` di `resources/views`. Menangani antarmuka pengguna akhir (UI).
- **Controller**: Direpresentasikan oleh kelas-kelas di `app/Http/Controllers` (seperti `BookingController`, `RefundController`). Bertindak sebagai perantara yang menerima *request* pengguna, meminta data dari Model, lalu memberikannya ke View.

**Alternatif yang mirip:** **MVP (Model-View-Presenter)** atau **MVVM (Model-View-ViewModel)**.
**Kenapa MVC yang dipakai?** MVC adalah pola bawaan yang paling optimal untuk aplikasi berbasis *Server-Side Rendering* (SSR) seperti Laravel dengan Blade. MVVM umumnya lebih cocok digunakan jika front-end dibangun terpisah menggunakan kerangka JavaScript reaktif murni (seperti Vue.js atau React Native), karena MVVM sangat bergantung pada konsep *two-way data binding*.

---

## 2. Active Record (via Eloquent ORM)
Semua model di sistem ini (contoh: `Booking::create()`, `$booking->save()`) menggunakan pola **Active Record**.
Dalam pola ini, setiap instance Model mewakili satu baris data di tabel database. Kelas model tersebut membungkus sekaligus logika akses data dan aturan bisnis tingkat dasar (contoh: *method* `$booking->refundNetAmount()`).

**Alternatif yang mirip:** **Data Mapper** (seperti yang digunakan oleh Doctrine di Symfony).
**Kenapa Active Record yang dipakai?** Pendekatan *Active Record* bawaan Eloquent Laravel membuat penulisan kode jauh lebih cepat, intuitif, dan ringkas. *Data Mapper* secara tegas memisahkan logika memori objek dari logika database, sehingga membutuhkan lebih banyak *boilerplate code* (kode yang panjang dan berulang). Untuk skala aplikasi manajemen tiket seperti CineTix, *Active Record* sangat memadai dan lebih pragmatis.

---

## 3. Dependency Injection (DI) & Inversion of Control (IoC)
Laravel otomatis menyuntikkan (inject) kelas ketergantungan langsung ke dalam *method*. Misalnya pada argumen `public function store(Request $request, Booking $booking)`. Kita tidak perlu melakukan instansiasi `new Request()` secara manual.

**Alternatif yang mirip:** **Service Locator** atau pola **Singleton Manual**.
**Kenapa Dependency Injection yang dipakai?** DI membuat pengujian (*testing*) aplikasi menjadi sangat mudah karena ketergantungan dapat digantikan sementara (*mocking*). Pola *Service Locator* seringkali menyembunyikan kelas apa saja yang dibutuhkan oleh sebuah *method*, yang akhirnya membuat kode lebih sulit dibaca dan rentan bocor memori (*memory leak*).

---

## 4. Chain of Responsibility (Pola Rantai Tanggung Jawab)
Sistem *Middleware* pada Laravel (contoh: perlindungan otentikasi `auth`, proteksi tipe admin di rute web) menggunakan pola ini. Permintaan (*request*) dari *browser* pengguna dilewatkan melalui serangkaian filter/rantai kelas secara berurutan sebelum mencapai *Controller*.

**Alternatif yang mirip:** **Decorator Pattern** atau *Inline Check* (Pengecekan langsung dengan IF).
**Kenapa Chain of Responsibility yang dipakai?** Bayangkan jika di setiap *method Controller* kita harus selalu menulis `if (!Auth::check()) return redirect('login');`. Itu sangat tidak efisien dan rentan lupa. *Middleware* (Chain of Responsibility) memusatkan logika pengamanan dalam satu lapisan, membuat kode *Controller* menjadi bersih dan hanya fokus pada tugas utamanya.

---

## 5. Observer & Event-Listener Pattern (Publish-Subscribe)
Fitur *real-time* seperti status kursi (terjual atau bebas) ditangani dengan Pusher menggunakan konsep *Event* (contoh: memanggil `broadcast(new SeatStatusUpdated(...))`). Saat status kursi berubah, sistem (Publisher) mengabarkan kejadian tersebut, lalu browser pelanggan (Subscriber/Listener) mendengarkan perubahan tersebut via websocket lalu mengubah warna UI kursi di layar tanpa perlu *refresh*.

**Alternatif yang mirip:** **Polling** (Client terus menerus memanggil API tiap detik).
**Kenapa Event-Listener yang dipakai?** *Polling* sangat membebani kinerja server (Overhead Tinggi) karena setiap browser menembak server secara terus-menerus meskipun tidak ada perubahan kursi. *Event-Listener / Pub-Sub* dengan WebSocket justru sebaliknya; diam dan bereaksi seketika **hanya jika** dikirimkan sinyal dari server. Ini menghemat penggunaan CPU dan Bandwidth server secara drastis.

---

## 6. Facade Pattern
Dalam program ini banyak sekali pemanggilan statis semu, misalnya `DB::transaction()`, `Log::error()`, atau `Auth::id()`. Pola *Facade* menyediakan antarmuka sederhana (*static*) untuk subsistem objek yang lebih besar dan kompleks yang tersembunyi di dalam *IoC container* aplikasi.

**Alternatif yang mirip:** **Singleton Pattern** konvensional.
**Kenapa Facade yang dipakai?** Penggunaan *Singleton* sering dianggap sebagai *anti-pattern* karena sulit di-*mock* dalam *testing* dan menciptakan *coupling* ketat antar kelas. *Facade* di Laravel menjembatani masalah ini dengan memberikan sintaks yang serapi singleton, namun sebenarnya mereka memanggil turunan instance yang tersimpan aman di dalam Wadah Injeksi (*DI Container*), sehingga mempertahankan kemudahan pengetesan.

---

## 7. Strategy Pattern (Terlihat pada Sistem Login Google)
Untuk fitur otentikasi (login biasa VS login Google menggunakan Laravel Socialite), aplikasi ini secara tidak langsung menyentuh pola *Strategy*. `Socialite::driver('google')` memilih algoritma pendorong (driver) secara dinamis di runtime berdasarkan "strategi" yang diberikan (bisa 'google', 'facebook', 'github').

**Alternatif yang mirip:** **Switch/Case Factory murni**.
**Kenapa Strategy yang dipakai?** Pola Strategy memungkinkan kita menambahkan dukungan sistem login pihak ketiga lainnya (misal: Twitter atau Apple) di masa mendatang tanpa perlu merusak dan menyentuh inti logika pengontrol (`Controller`) utama sama sekali (Prinsip Terbuka Tertutup / *Open-Closed Principle*).
