# Penerapan Clean Code di CineTix

Dokumen ini merangkum prinsip-prinsip **Clean Code** yang diterapkan dalam pengembangan sistem manajemen bioskop CineTix, guna menjaga kode tetap rapi, mudah dibaca, mudah diuji, dan adaptif terhadap perubahan di masa mendatang.

---

## 1. YAGNI (You Aren't Gonna Need It)
*Jangan membuat fitur atau kode cadangan yang belum benar-benar dibutuhkan saat ini.*
* **Contoh di CineTix**: 
  * **Penghapusan Tabel FnB**: Seluruh skema database dan modul terkait Food & Beverage (FnB) dibersihkan sepenuhnya via migrasi database (`2026_05_13_000000_drop_fnb_tables.php`) ketika diputuskan fitur tersebut tidak lagi digunakan. Kode-kode "siapa tahu nanti dipakai" tidak dibiarkan mengotori basis kode.
  * **Fokus pada Core Flow**: Menghindari over-engineering pada struktur database awal dengan hanya membuat kolom-kolom yang esensial untuk pemesanan tiket, kursi, dan pembayaran.

---

## 2. KISS (Keep It Simple, Stupid)
*Jaga agar solusi dari setiap masalah sesederhana mungkin tanpa mengorbankan fungsionalitas.*
* **Contoh di CineTix**:
  * **Logika Bentrok Jadwal**: Dibandingkan menggunakan nested loops atau manipulasi waktu yang rumit di level aplikasi PHP, bentrok jadwal dideteksi menggunakan satu query SQL yang elegan dan lugas:
    ```sql
    (start_time < end_time_pembanding) AND (end_time > start_time_pembanding)
    ```
    Logika ini sederhana, sangat efisien di level database, dan mudah dipahami oleh developer lain.
  * **Pembatalan Transaksi**: Proses pembatalan pesanan yang belum dibayar diselesaikan melalui transaksi database sederhana (`DB::transaction`) di `BookingController::cancel()` yang langsung membebaskan status kursi dan mengubah status pembayaran menjadi `failed`.

---

## 3. DRY (Don't Repeat Yourself)
*Setiap potong pengetahuan atau logika harus memiliki representasi tunggal yang tidak ambigu di dalam sistem.*
* **Contoh di CineTix**:
  * **Enkapsulasi Ketersediaan Kursi**: Pengecekan ketersediaan kursi tidak ditulis ulang di setiap controller/handler. Logika ini dipusatkan di method `checkAvailability($scheduleId)` di model [Seat.php](file:///c:/Users/ASUS/Downloads/CineTix-Sistem-Manajemen-Bioskop/app/Models/Seat.php).
  * **Reusability Validasi Waktu**: Logika validasi tumpang tindih waktu di-share antara `Admin/ScheduleController` (untuk manajemen admin) dan `ScheduleController` frontend (jika ada input jadwal eksternal).

---

## 4. Prinsip SOLID

### S - Single Responsibility Principle (SRP)
*Setiap kelas atau modul hanya boleh memiliki satu alasan untuk berubah.*
* **Contoh di CineTix**:
  * **Pemisahan Validasi (Chain of Responsibility)**: Logika validasi sebelum memesan tiket dipisahkan dari controller ke dalam serangkaian handler khusus:
    * `UserAuthorizationHandler` (Hanya memvalidasi login)
    * `SeatAvailabilityHandler` (Hanya memvalidasi kursi kosong)
    * `PromoValidationHandler` (Hanya memvalidasi kode promo)
  * **Pemisahan Efek Samping (Observers)**: Pemicuan broadcast real-time status kursi dipisahkan dari model inti ke dalam `SeatObserver` dan `PaymentObserver`.

### O - Open/Closed Principle (OCP)
*Entitas perangkat lunak harus terbuka untuk ekstensi, tetapi tertutup untuk modifikasi.*
* **Contoh di CineTix**:
  * **Metode Pembayaran (Strategy Pattern)**: Jika CineTix ingin menambahkan metode pembayaran baru (misal: GoPay, Kartu Kredit), kita hanya perlu membuat kelas strategy baru (misal: `GopayPaymentStrategy.php`) yang mengimplementasikan `PaymentStrategyInterface`. Kita **tidak perlu mengubah** kode inti di `BookingController` atau `PaymentContext`.

### L - Liskov Substitution Principle (LSP)
*Sub-kelas harus dapat menggantikan kelas induknya tanpa merusak fungsionalitas program.*
* **Contoh di CineTix**:
  * Seluruh strategi pembayaran (`QrisPaymentStrategy`, `VirtualAccountPaymentStrategy`) mengimplementasikan `PaymentStrategyInterface` secara konsisten. Keduanya dapat saling menggantikan di dalam `PaymentContext` tanpa memicu error atau membutuhkan penanganan khusus (if-else) tambahan.

### I - Interface Segregation Principle (ISP)
*Lebih baik memiliki banyak interface khusus daripada satu interface umum yang besar.*
* **Contoh di CineTix**:
  * `PaymentStrategyInterface` dibuat ramping dan hanya berfokus pada method esensial untuk inisiasi dan konfirmasi pembayaran. Kelas yang mengimplementasikannya tidak dipaksa menulis method kosong yang tidak mereka butuhkan.

### D - Dependency Inversion Principle (DIP)
*Bergantunglah pada abstraksi, bukan pada konkretisasi (detail).*
* **Contoh di CineTix**:
  * `BookingController` memproses transaksi pembayaran melalui abstraksi `PaymentStrategyInterface` (diatur oleh `PaymentContext`), bukan langsung bergantung secara kaku pada kelas konkret `QrisPaymentStrategy` atau `VirtualAccountPaymentStrategy`.

---

## 5. Boy Scout Rule
*Tinggalkan basis kode dalam keadaan yang lebih bersih daripada saat Anda menemukannya.*
* **Contoh di CineTix**:
  * **Kompatibilitas Database Testing**: Saat mendapati test suite SQLite error akibat perintah migrasi ALTER TABLE MySQL mentah, migrasi di-refactor menggunakan kondisional driver:
    ```php
    if (Schema::getConnection()->getDriverName() === 'mysql') {
        DB::statement(...);
    }
    ```
    Hal ini membuat lingkungan pengujian tetap bersih, stabil, dan hijau (*green tests*) tanpa mengotori skema produksi.

---

## 6. Self-Documenting Code & Meaningful Naming
*Penulisan nama variabel, method, dan struktur kelas yang deskriptif sehingga kode dapat "menjelaskan dirinya sendiri" tanpa ketergantungan berlebih pada komentar.*
* **Contoh di CineTix**:
  * Penggunaan nama method yang ekspresif seperti `isAvailable($scheduleId)`, `BookingApprovalChain`, dan status tiket yang eksplisit (`confirmed`, `pending`, `cancelled`).
  * Membagi alur kompleks dengan helper method deskriptif untuk meningkatkan keterbacaan (readability).
