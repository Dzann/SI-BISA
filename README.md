# ✋ SI-BISA (Sistem Isyarat Berbasis Suara)
> **Sistem Penerjemah Suara ke Bahasa Isyarat Berbasis Web Menggunakan Web Speech API dan Algoritma Stemming Sastrawi**

SI-BISA adalah aplikasi web inklusif yang dirancang untuk menjembatani komunikasi antara masyarakat umum dan **Teman Tuli** (penyandang tuna rungu) dengan menerjemahkan ucapan lisan Bahasa Indonesia secara langsung ke dalam bentuk visual peragaan **Bahasa Isyarat Indonesia (BISINDO)**.

---

## 👥 Tim Pengembang (Stakeholder Proyek)
* **Project Manager & Full Stack Developer**: Muhamad Gidzane Mustofa
* **System Analyst & Quality Assurance**: Ibnu Fariz Fauzan Hamzah
* **UI/UX Designer**: Rosya Fitriani Supyan
* **Media Specialist & Brand Designer**: Naufal Meitsamar Rizqyana
* **Technical Writer & Financial Specialist**: Mohammad Jafar Tella

*Institusi*: Universitas Nusa Putra, Sukabumi, Jawa Barat.

---

## 🚀 Fitur Unggulan

1. **Pengenalan Suara Real-time (Web Speech API)**
   * Merekam suara lisan pengguna dalam Bahasa Indonesia (`id-ID`) langsung melalui browser tanpa memerlukan perangkat keras khusus.
2. **Fallback Input Teks Manual (UX Recommendation)**
   * Opsi *textarea* responsif di Beranda jika mikrofon bermasalah atau jika pengguna mengakses melalui browser non-Chromium (seperti Firefox/Safari).
3. **Pemrosesan Teks & Algoritma Stemming Sastrawi (NLP)**
   * Pembersihan teks (*lowercasing*, sanitasi tanda baca) dan pemotongan imbuhan otomatis ke kata dasar dengan pustaka Sastrawi yang telah dioptimasi (*Singleton & Batch Query*).
4. **Visualisasi Gerakan BISINDO Berurutan**
   * Pemutar visual isyarat otomatis (*sequential player*) yang mendukung format GIF, Gambar (PNG/JPG), dan Video (MP4/WEBM).
   * Dilengkapi kendali navigasi (Mulai/Jeda, Gerakan Sebelumnya, Gerakan Selanjutnya, dan hitungan indeks).
5. **Peringatan Kata Tidak Ditemukan & Izin Mikrofon**
   * Notifikasi ramah saat kata dasar belum terdaftar dalam basis data kamus atau saat perizinan mikrofon ditolak.
6. **Riwayat Terjemahan & Privasi Sesi**
   * Riwayat percakapan tersimpan secara aman per sesi pengguna, dengan fitur pencarian teks instan.
7. **Pengaturan Aksesibilitas Lengkap**
   * Pilihan tema tampilan (Mode Terang / Mode Gelap).
   * Ukuran teks (Kecil, Sedang, Besar).
   * Kecepatan putar isyarat (0.5x, 0.75x, 1x), *autoplay*, dan *repeat*.
8. **Dashboard Pengelolaan Kamus Isyarat (Admin)**
   * Autentikasi sesi admin dengan proteksi *rate limiting*.
   * Manajemen lengkap (CRUD) aset BISINDO dengan pratinjau media dan filter pencarian cepat.

---

## 🏗️ Arsitektur Sistem

Aplikasi dibangun dengan arsitektur 3-tier:
* **Client Layer**: HTML5, Modern CSS (Responsive Custom Properties), Vanilla JavaScript, Web Speech API.
* **Application Layer**: Laravel 10 (PHP 8.1+), REST API, Sastrawi Stemmer Engine.
* **Data Storage Layer**: MySQL (Database 7 tabel sesuai ERD) & File Storage (`public/storage` untuk media isyarat).

---

## 📋 Prasyarat Sistem

* **PHP** >= 8.1 (dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `xml`, `ctype`)
* **Composer** >= 2.x
* **MySQL** / MariaDB
* **Browser Rekomendasi**: Google Chrome atau Microsoft Edge (untuk fitur suara Web Speech API)

---

## ⚙️ Panduan Instalasi Lokal

1. **Clone Repository & Masuk ke Direktori**:
   ```bash
   git clone https://github.com/Dzann/SI-BISA.git
   cd SI-BISA
   ```

2. **Instal Dependensi PHP (Composer)**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan sesuaikan pengaturan database:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=SIBISA
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Hubungkan Symlink Storage**:
   *Penting agar video dan gambar isyarat dapat diakses:*
   ```bash
   php artisan storage:link
   ```

6. **Migrasi Database & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi melalui browser di: `http://127.0.0.1:8000`

---

## 🔐 Kredensial Akun Bawaan (Default Admin)

* **URL Login**: `http://127.0.0.1:8000/login`
* **Email**: `admin@sibisa.test`
* **Password**: `123`
* **Peran (Role)**: `admin`

---

## 📡 Ringkasan Endpoint API Internal

| Metode | Endpoint | Hak Akses | Deskripsi |
| :--- | :--- | :---: | :--- |
| `POST` | `/api/login` | Publik (Throttle) | Otentikasi masuk akun (Admin/User) |
| `GET` | `/api/me` | Publik | Mendapatkan profil user yang sedang login |
| `POST` | `/api/logout` | Login | Menghapus sesi dan logout |
| `POST` | `/api/translate` | Publik (Throttle) | Pemrosesan NLP stemming & pencocokan BISINDO |
| `GET` | `/api/riwayat` | Publik | Mengambil riwayat terjemahan berdasarkan sesi/user |
| `GET` | `/api/riwayat/{id}` | Publik | Mengambil detail terjemahan kata per kata |
| `GET` | `/api/kamus` | Admin | Mengambil seluruh daftar kamus isyarat |
| `POST` | `/api/kamus` | Admin | Menambah aset gerakan isyarat baru |
| `PUT` | `/api/kamus/{id}` | Admin | Memperbarui data kata dasar / file isyarat |
| `DELETE`| `/api/kamus/{id}` | Admin | Menghapus entri isyarat dari database dan disk |
