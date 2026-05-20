# Penjelasan Penerapan Design Pattern di Cinetix

Dokumen ini menjelaskan bagaimana tiga design pattern utama diterapkan di aplikasi Cinetix:
- Chain of Responsibility (CoR)
- Strategy Pattern
- Observer Pattern

## 1. Chain of Responsibility (CoR)

### Tujuan
CoR digunakan untuk memisahkan logika validasi atau izin menjadi rangkaian langkah yang bisa diproses satu per satu.

### Area Penerapan
1. **Booking approval** sebelum booking dibuat
2. **Payment validation** sebelum payment dikonfirmasi
3. **Cancellation validation** sebelum booking dibatalkan

### Implementasi
- `app/Services/ChainOfResponsibility/BookingApprovalHandler.php`
- `app/Services/ChainOfResponsibility/BookingApprovalChain.php`
- `app/Services/ChainOfResponsibility/BookingApproval/UserAuthorizationHandler.php`
- `app/Services/ChainOfResponsibility/BookingApproval/SeatAvailabilityHandler.php`
- `app/Services/ChainOfResponsibility/BookingApproval/PromoValidationHandler.php`

- `app/Services/ChainOfResponsibility/PaymentValidationHandler.php`
- `app/Services/ChainOfResponsibility/PaymentValidationChain.php`
- `app/Services/ChainOfResponsibility/PaymentValidation/PaymentPendingValidationHandler.php`
- `app/Services/ChainOfResponsibility/PaymentValidation/PaymentTimeoutValidationHandler.php`
- `app/Services/ChainOfResponsibility/PaymentValidation/PaymentStatusValidationHandler.php`
- `app/Services/ChainOfResponsibility/PaymentValidation/BookingStatusValidationHandler.php`

- `app/Services/ChainOfResponsibility/CancellationHandler.php`
- `app/Services/ChainOfResponsibility/CancellationChain.php`
- `app/Services/ChainOfResponsibility/Cancellation/CancellationStatusHandler.php`
- `app/Services/ChainOfResponsibility/Cancellation/LockedSeatsHandler.php`
- `app/Services/ChainOfResponsibility/Cancellation/PaymentRefundHandler.php`

### Alur CoR di Booking
1. `UserAuthorizationHandler` — pastikan user login
2. `SeatAvailabilityHandler` — pastikan kursi tersedia
3. `PromoValidationHandler` — validasi promo jika ada

Jika semua handler menyetujui, booking dibuat.

### Alur CoR di Payment
1. `PaymentPendingValidationHandler` — cek apakah payment pending benar dan tidak ada payment pending lain
2. `PaymentTimeoutValidationHandler` — cek apakah payment belum expired
3. `PaymentStatusValidationHandler` — cek apakah payment masih `pending`
4. `BookingStatusValidationHandler` — cek apakah booking masih `pending`

Jika semua validasi valid, process payment dilanjutkan.

### Alur CoR di Cancellation
1. `CancellationStatusHandler` — hanya booking `pending` yang dapat dibatalkan
2. `LockedSeatsHandler` — memastikan ada kursi yang dikunci
3. `PaymentRefundHandler` — cek apakah ada payment pending yang harus di-mark failed

## 2. Strategy Pattern

### Tujuan
Strategy digunakan untuk memisahkan detail proses pembayaran berdasarkan metode yang dipilih.

### Area Penerapan
- pemilihan payment method
- pembuatan payment data
- tampilan data pembayaran khusus metode
- proses pembayaran ketika user klik "Selesaikan Pembayaran"

### Implementasi
- `app/Services/Payment/PaymentStrategyInterface.php`
- `app/Services/Payment/PaymentContext.php`
- `app/Services/Payment/QrisPaymentStrategy.php`
- `app/Services/Payment/VirtualAccountPaymentStrategy.php`

### Alur Strategy
1. User memilih metode pembayaran di halaman `booking.payment`
2. Controller `BookingController::initiatePayment()` memanggil `PaymentContext::resolve($method)`
3. Strategy yang sesuai membuat record payment dan menyiapkan data tampilan
4. Controller `BookingController::processPayment()` dan `BookingController::confirmPayment()` menggunakan strategy untuk:
   - menampilkan QR code atau nomor VA
   - memproses pembayaran sesuai metode

### Kenapa Strategy
- membuat kode terpisah per metode pembayaran
- memudahkan penambahan metode baru
- memisahkan logika UI dan proses payment

## 3. Observer Pattern

### Tujuan
Observer digunakan untuk me-refresh status kursi secara real-time ketika ada perubahan booking atau payment.

### Area Penerapan
- broadcast event ketika kursi berubah status
- kembalikan kursi ketika payment gagal

### Implementasi
- `app/Observers/SeatObserver.php`
- `app/Observers/PaymentObserver.php`
- `app/Events/SeatStatusUpdated.php`
- pendaftaran observer di `app/Providers/AppServiceProvider.php`

### Alur Observer
1. Saat kursi di-update (`status` berubah), `SeatObserver` mem-broadcast event `SeatStatusUpdated`
2. Ketika payment gagal, `PaymentObserver` mengembalikan kursi terkait ke `available`
3. Frontend bisa menerima event dan membuat seat UI kembali tersedia untuk user lain

### Kenapa Observer
- memisahkan side-effect dari model update
- menjaga agar perubahan kursi otomatis disebarkan ke user lain
- memastikan stock seats kembali dikembalikan ketika payment gagal

## Kombinasi Pola

### CoR + Strategy
- CoR menentukan apakah payment boleh diproses
- Strategy menentukan bagaimana payment diproses

### CoR + Observer
- CoR memutuskan kondisi validasi
- Observer menangani efek perubahan status (misal kursi dikembalikan)

### Strategy + Observer
- Strategy memproses pembayaran secara spesifik
- Observer men-trigger efek status kursi setelah payment gagal atau berhasil

## File Controller yang Terhubung
- `app/Http/Controllers/BookingController.php`

Perubahan penting di controller:
- `store()` memanggil `BookingApprovalChain`
- `confirmPayment()` memanggil `PaymentValidationChain`
- `BookingController::processPayment()` tetap menampilkan data Strategy
- `BookingController::initiatePayment()` membuat payment via Strategy

## Kesimpulan

Ketiga pattern ini bekerja bersama dengan tugas masing-masing:
- CoR untuk validasi berjenjang
- Strategy untuk proses payment berdasarkan metode
- Observer untuk efek samping dan update status real-time

Dengan struktur ini, aplikasi menjadi lebih modular, lebih mudah diuji, dan lebih mudah dikembangkan di kemudian hari.