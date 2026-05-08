# 📊 Laporan Perkembangan Website CineTix (Ticketing Bioskop)  
**Week 1**

---

## 👤 1. Juan Alexander - 2472020  
### Sistem Autentikasi dan Pondasi Awal Database
- **Google Login (Socialite)**  
  - Berhasil mengintegrasikan fitur login menggunakan akun Google.  
  - Menambah konfigurasi pada file `.env` (Client ID & Secret).  
  - Mengimplementasikan logic pada `GoogleController`.  
  - Mengatur routing khusus untuk proses callback dari Google.  

- **Database Foundation**  
  - Merancang dan membuat file migrasi (migrations) awal untuk tabel-tabel inti.  
  - Menyiapkan seeder awal untuk memastikan database memiliki data dasar saat proses development.  

---

## 👤 2. Yoel Kristianto - 2472057  
### Struktur Pengguna, Model Data, dan Perbaikan Antarmuka
- **Sistem Role (3 role: Admin, Resepsionis, Customer)**  
  - Menambahkan logika cek role pada model `User`.  
  - Redirect user diarahkan ke tujuan yang sesuai setelah login.  

- **Model & Seeder**  
  - Membuat model penting: `Booking`, `Film`, `Studio`, `Seat`, dll.  
  - Membuat seeder spesifik untuk tiap role (`AdminSeeder`, `ResepsionisSeeder`, `CustomerSeeder`).  

- **UI/UX Improvement**  
  - Memperbaiki tampilan sign in dan sign up yang masih berantakan.  

---

## 👤 3. Hans Maulana Budiputra - 2472052  
### Tampilan Utama dan Branding Visual Website
- **Landing Page**  
  - Membangun halaman utama (Landing Page) dengan desain modern dan responsif.  

- **Foundation UI/UX**  
  - Menentukan pondasi tampilan website (tema warna, layouting).  
  - Memastikan integrasi aset visual (gambar, ikon, dll) berjalan dengan baik.  

- **Scaffolding Breeze Login**  
  - Menggunakan Laravel Breeze untuk sistem login/register default.  
  - Menyediakan struktur autentikasi dasar terintegrasi dengan UI bawaan Breeze.  
  - Menjadi pondasi sebelum integrasi lebih lanjut dengan Socialite (Google Login).  

---
