# Laporan Progress — CineTix

**Proyek:** Sistem Pemesanan Tiket Bioskop (Cinetix)  
**Periode update:** 28 Mei 2026  
**Stack:** Laravel, Blade, MySQL, Pattern (Chain of Responsibility, Strategy, State, Observer)

---

## 1. Ringkasan Eksekutif

Pengembangan terbaru berfokus pada **tiga area utama**:

| Area | Status | Keterangan |
|------|--------|------------|
| Sistem promo umum | ✅ Selesai | Kode promo satu untuk semua customer + limit per orang |
| Guest checkout | ✅ Selesai | Beli tiket tanpa login + email + konfirmasi popup |
| Perbaikan admin & stabilitas | ✅ Selesai | Halaman admin tidak error saat ada booking guest |

---

## 2. Fitur yang Telah Dikerjakan

### 2.1 Sistem Kode Promo (Umum)

**Konsep:** Satu kode promo dipakai banyak user, dibatasi lewat `max_usage_per_customer` (bukan kode unik per user).

| Komponen | Detail |
|----------|--------|
| Tabel `promos` | `max_usage`, `max_usage_per_customer`, `usage_count` |
| Tabel `promo_usages` | Tracking pemakaian per `promo_id` + `user_id` |
| Kode default | `WELCOME2026` — diskon Rp 20.000, **1x per akun** |
| Admin CRUD | `/admin/promos` — buat, edit, hapus, lihat riwayat pemakaian |
| Validasi booking | `PromoValidationHandler` (Chain of Responsibility) |
| Validasi AJAX | `POST /promo/validate` (hanya user login) |

**Aturan bisnis:**
- **Guest** tidak bisa memakai kode promo (hanya lihat info ajakan login/daftar).
- **Member** bisa memakai kode promo sesuai limit per customer.
- Setelah registrasi, user diarahkan memakai `WELCOME2026` (bukan generate kode personal).

---

### 2.2 Guest Checkout (Tanpa Login)

**Alur lengkap:**

```
Pilih film → Pilih kursi → Isi email
    → Popup: "Apakah email sudah benar?" [Kirim!] / [Cek kembali!]
    → Lock kursi (5 menit) → Pilih pembayaran (QRIS/VA)
    → Bayar → Konfirmasi → Tiket dikirim ke email + halaman tiket
```

| Fitur | Implementasi |
|-------|----------------|
| Email wajib | Kolom `guest_email` di `bookings` |
| Konfirmasi email | Modal Bootstrap di halaman pilih kursi |
| Akses pembayaran | Session + `access_token` (`GuestBookingAccess`) |
| Kirim tiket | `TicketConfirmationMail` setelah pembayaran sukses |
| Halaman tiket | `/booking/guest-ticket/{booking}?token=...` |
| Promo | Tidak tersedia untuk guest |

**Route publik (tanpa login):**
- `GET  /booking/schedule/{schedule}`
- `POST /booking/store`
- `GET  /booking/payment/{booking}`
- `POST /booking/initiate-payment/{booking}`
- `GET  /booking/process-payment/{booking}/{payment}`
- `POST /booking/confirm-payment/{booking}/{payment}`
- `GET  /booking/guest-ticket/{booking}`

---

### 2.3 Perbaikan Panel Admin

**Masalah:** Error `Attempt to read property "name" on null` karena booking guest tidak punya relasi `user`.

**Solusi:** Helper di model `Booking`:
- `customerName()`, `customerEmail()`, `customerPhone()`, `customerTypeLabel()`, `isGuest()`

**Halaman yang diperbaiki:**
- Manajemen Booking (`/admin/bookings`)
- Manajemen Tiket & Scan QR (`/admin/tickets`)
- Dashboard admin (booking pending)
- Detail promo (null-safe pada `user`)

**Lainnya:**
- Pencarian tiket admin mencakup `guest_email`
- Virtual Account guest memakai `booking_id` (bukan `user_id` null)
- Event `BookingConfirmed` tidak broadcast ke channel user jika guest

---

## 3. Arsitektur Teknis

### 3.1 Design Pattern yang Dipakai

| Pattern | Lokasi | Fungsi |
|---------|--------|--------|
| Chain of Responsibility | `BookingApprovalChain` | Validasi user/guest → kursi → promo |
| Chain of Responsibility | `PaymentValidationChain` | Validasi pembayaran |
| Strategy | `PaymentContext` | QRIS vs Virtual Account |
| State | Model `Seat` | available → pending → booked |
| Observer | `PaymentObserver` | Kembalikan kursi jika pembayaran gagal |

### 3.2 Model & Database (Perubahan Penting)

**Migration baru:**
- `2026_05_28_000002_refactor_promo_system_to_general.php` — promo umum + `promo_usages`
- `2026_05_28_100000_add_guest_fields_to_bookings_table.php` — `guest_email`, `access_token`, `user_id` nullable

**Kolom `bookings` (relevan):**
| Kolom | Keterangan |
|-------|------------|
| `user_id` | Nullable — null = guest |
| `guest_email` | Email pengiriman tiket guest |
| `access_token` | Token akses halaman pembayaran/tiket guest |
| `promo_id` | Promo yang dipakai (jika ada) |
| `total_amount` | Total setelah diskon |

---

## 4. Perbandingan Alur: Member vs Guest

| Aspek | Member (Login) | Guest |
|-------|----------------|-------|
| Pilih kursi | ✅ | ✅ |
| Kode promo | ✅ | ❌ |
| Email | Dari akun | Input manual + popup konfirmasi |
| Pembayaran | ✅ | ✅ |
| Riwayat booking | `/booking/history` | ❌ (hanya via email/link token) |
| Tiket aktif | `/booking/tickets` | `/booking/guest-ticket` |
| Batalkan pesanan | ✅ | ❌ (tombol kembali saja) |

---

## 5. File Utama yang Diubah/Ditambah

### Backend
- `app/Models/Promo.php`, `PromoUsage.php`, `Booking.php`
- `app/Http/Controllers/PromoController.php`
- `app/Http/Controllers/BookingController.php`
- `app/Support/GuestBookingAccess.php`
- `app/Mail/TicketConfirmationMail.php`
- `app/Services/ChainOfResponsibility/BookingApproval/*`
- `app/Services/Payment/VirtualAccountPaymentStrategy.php`
- `database/seeders/PromoSeeder.php`

### Frontend (Views)
- `resources/views/booking/show.blade.php`
- `resources/views/booking/payment.blade.php`
- `resources/views/booking/process-payment.blade.php`
- `resources/views/booking/guest-ticket.blade.php`
- `resources/views/admin/promos/*`
- `resources/views/admin/bookings/index.blade.php`
- `resources/views/admin/tickets/index.blade.php`
- `resources/views/emails/ticket-confirmation.blade.php`

### Routes
- `routes/web.php` — pemisahan route guest vs auth

---

## 6. Cara Menjalankan & Testing

```bash
# Migrasi database
php artisan migrate

# Seed kode promo (termasuk WELCOME2026)
php artisan db:seed --class=PromoSeeder

# Jalankan server
php artisan serve
```

### Skenario uji disarankan

| No | Skenario | Hasil yang diharapkan |
|----|----------|------------------------|
| 1 | Guest: pilih kursi + email + popup Kirim | Redirect ke halaman pembayaran |
| 2 | Guest: selesaikan pembayaran | Email tiket terkirim (atau masuk log jika `MAIL_MAILER=log`) |
| 3 | Member: pakai WELCOME2026 pertama kali | Diskon Rp 20.000 terapply |
| 4 | Member: pakai WELCOME2026 kedua kali | Ditolak (limit 1x) |
| 5 | Admin: buka `/admin/bookings` | Tampil tanpa error, guest ada badge "Guest" |
| 6 | Admin: CRUD promo baru | Tersimpan dengan `max_usage_per_customer` |

### Konfigurasi email (`.env`) — Gmail App Password

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email@gmail.com
MAIL_PASSWORD="xxxx xxxx xxxx xxxx"   # App Password (16 karakter, tanpa spasi)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

> Setelah ubah `.env`, jalankan: `php artisan config:clear`

---

## 7. Progress per Modul (Estimasi)

```
Promo System        ████████████████████ 100%
Guest Checkout      ████████████████████ 100%
Admin Promo CRUD    ████████████████████ 100%
Admin Booking Fix   ████████████████████ 100%
Email Tiket         ███████████████████░  95%  (perlu konfigurasi SMTP production)
```

---

## 8. Catatan & Rekomendasi Lanjutan

### Sudah stabil
- Booking guest end-to-end
- Promo umum + limit per customer
- Admin panel aman untuk data guest

### Opsional untuk pengembangan berikutnya
1. **Halaman lacak tiket guest** — form cari booking by email + kode booking
2. **Notifikasi WhatsApp** — selain email
3. **Auto-expire booking pending** — command scheduler release kursi
4. **Unit test** — `Promo::canBeUsedBy()`, `GuestBookingAccess::canAccess()`
5. **Export laporan** — pisahkan revenue member vs guest di `/admin/reports`

---

## 9. Kesimpulan

Sistem CineTix kini mendukung:
- **Promo efisien** (satu kode, banyak user, limit per orang)
- **Penjualan tanpa hambatan login** (guest checkout dengan validasi email)
- **Operasional admin** yang tidak crash saat menampilkan transaksi guest

Semua fitur inti pada sprint ini **telah diimplementasikan dan diuji secara fungsional**. Penyesuaian terakhir yang disarankan adalah konfigurasi mail server production agar tiket guest benar-benar terkirim ke inbox pelanggan.

---

*Dokumen ini dibuat otomatis berdasarkan riwayat pengembangan proyek Cinetix.*
