# Sistem Recruitment & Order Karyawan Outsourcing

Dokumen ini adalah rancangan teknis (Implementation Plan) untuk membangun Sistem Recruitment & Order Karyawan Outsourcing berdasarkan SRS yang telah diberikan. Teknologi utama yang akan digunakan adalah **Laravel** dan **Filament Panel**, beserta **Spatie Laravel Permission** untuk manajemen hak akses (Role & Permission).

## User Review Required

> [!IMPORTANT]
> Mohon direview rencana implementasi di bawah ini. Jika ada penambahan, pengurangan, atau penyesuaian khusus (terutama terkait struktur database, alur, atau desain UI untuk form publik), silakan sampaikan sebelum saya mulai melakukan instalasi dan menulis kode.

## Open Questions

> [!WARNING]
> Sebelum memulai eksekusi, ada beberapa hal yang perlu dipastikan:
> 1. **Database:** Saat ini di file `.env` aplikasi, database yang digunakan bernama `ats`. Apakah kamu sudah membuat database bernama `ats` di MySQL/MariaDB Laragon kamu? Jika belum, pastikan untuk membuatnya di HeidiSQL/phpMyAdmin sebelum proses migrasi dijalankan.
> 2. **Library Role & Permission:** Untuk manajemen Role, saya berencana menggunakan plugin **Filament Shield** (yang membungkus Spatie Permission dan otomatis membuat UI manajemen role di Filament). Apakah kamu setuju menggunakan ini?
> 3. **Public Form:** Untuk form kandidat publik tanpa login, apakah kamu lebih memilih menggunakan **Laravel Livewire** (agar terasa lebih reaktif/modern seperti Filament) atau **Blade Form** biasa?

## Proposed Changes

### 1. Instalasi dan Setup Dasar
- Menginstall package Filament Panel ke dalam project Laravel.
- Menginstall package `spatie/laravel-permission` dan `bezhansalleh/filament-shield` untuk Role-Based Access Control (RBAC).
- Konfigurasi `User` model agar memiliki fitur hak akses (Role).

### 2. Struktur Database (Migrations & Models)
Akan dibuat beberapa tabel beserta relasinya sesuai SRS:
- **Master Data**: `clients`, `divisions`, `positions`
- **Order Management**: `orders` (menyimpan info Klien, PIC, HRD, kuota, status), `order_slots` (opsional jika detail slot dibutuhkan, atau langsung disatukan ke orders tergantung kompleksitas).
- **Kandidat**: `candidates` (menyimpan bio data), `candidate_documents` (file upload).
- **Proses Seleksi**: `hrd_interviews`, `pic_reviews`, `client_interviews` (bisa juga disatukan dalam tabel kandidat atau dipisah untuk history).
- **Karyawan**: `employees` (tabel karyawan hasil hired).
- **Tracking**: `status_histories` (mencatat setiap perubahan status order/kandidat).

### 3. Modul Filament Admin Panel
Saya akan membuat **Filament Resources** berikut:
- **User Resource** & **Role Resource**: Manajemen pengguna dan hak akses.
- **Client Resource**, **Division Resource**, **Position Resource**: Master data CRUD.
- **Order Resource**: Manajemen order bagi PIC dan HRD (lengkap dengan filter & status).
- **Candidate Resource**: Tempat mengelola kandidat, termasuk *Action* untuk memproses status kandidat (Interview HRD, Review PIC, dll).
- **Employee Resource**: Database karyawan (read-only atau editable bagi PIC).
- **Dashboard & Widgets**: Statistik order, kandidat masuk, kandidat hired, bottleneck info.

### 4. Modul Form Publik Kandidat (Frontend)
- Membuat route khusus (misalnya `suksesindo.test/apply/{order_id}`).
- Membuat halaman form pendaftaran (Upload CV, KTP, Ijazah, Input Data Diri) menggunakan **Livewire** atau **Blade**.
- Menyimpan data kandidat baru dengan status awal: `Menunggu Interview HRD`.

### 5. Workflow & Logika Bisnis
- **Otomatisasi Status**: Saat order penuh, otomatis ubah status ke `Closed`.
- **Copy Data Karyawan**: Saat kandidat disetujui (`Hired`), data bio secara otomatis di-*copy* ke tabel `employees`.
- **Tracking**: Membuat observer/event listener pada model `Candidate` dan `Order` untuk setiap kali field `status` berubah, sistem akan mencatat (log) ke tabel `status_histories`.

## Verification Plan

### Automated Tests
- Menjalankan migrasi database (`php artisan migrate:fresh --seed`) beserta seeder untuk Superadmin, Role, dan beberapa Dummy Master Data untuk keperluan testing awal.

### Manual Verification
- Login sebagai Superadmin untuk mencoba membuat master data dan order.
- Mengakses halaman publik (tanpa login) untuk mencoba input kandidat.
- Login sebagai HRD dan PIC untuk mencoba memproses alur seleksi kandidat (dari masuk hingga hired).
- Memastikan tracking tercatat saat status berubah.
