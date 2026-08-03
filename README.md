# ✅ Semantic To-Do List — PHP Case Study

Aplikasi *to-do list* berbasis PHP murni (tanpa framework) yang dibangun dengan standar industri: **arquitetur berlapis, keamanan tingkat produksi, dan kode yang mudah diuji**.

Proyek ini sengaja dibangun **tanpa framework eksternal** untuk membuktikan pemahaman mendalam terhadap inti PHP — sehingga menjadi portofolio yang menunjukkan kualitas engineering, bukan sekadar kemampuan memakai alat.

---

## 🎯 Masalah yang Diselesaikan

Aplikasi pengelola tugas yang sering ditemukan di dunia nyata harus menyelesaikan pertanyaan:

- Bagaimana memisahkan logika bisnis (controller), akses data (repository), dan tampilan (view)?
- Bagaimana mencegah **SQL Injection**, **XSS**, dan **CSRF** tanpa mengandalkan framework?
- Bagaimana menjaga navigasi tetap aman dan rapi lewat *front controller*?
- Bagaimana membuat kode yang **bisa diuji** dan **mudah dipelihara**?

Proyek ini menjawab semuanya dari nol.

---

## 🧱 Arsitektur

```
todo-list-app/
├── public/
│   └── index.php          # Front controller — satu-satunya entry point
├── src/
│   ├── Database.php       # Koneksi PDO (SQLite), pattern Singleton
│   ├── TaskController.php # Logika bisnis & validasi
│   ├── TaskRepository.php # Akses data via prepared statements
│   └── Session.php        # Flash messages & proteksi CSRF
├── views/
│   └── tasks.php          # Tampilan (aman XSS, support filter)
├── storage/               # File database SQLite (tidak ikut commit)
├── composer.json          # PSR-4 autoloading (App\ → src/)
└── README.md
```

**Alur request:**

```
Browser → public/index.php (front controller)
        → routing → TaskController::method()
        → TaskRepository::method() → PDO → SQLite
        → kembali → render views/tasks.php
```

---

## 🔒 Keamanan yang Diterapkan

| Ancaman | Mitigasi |
|---|---|
| SQL Injection | Prepared statements PDO di semua query |
| XSS (Cross-Site Scripting) | `htmlspecialchars()` pada setiap output |
| CSRF (Cross-Site Request Forgery) | Token CSRF per-sesi + validasi `hash_equals` |
| Akses file internal | Folder `public/` sebagai satu-satunya entry point |
| Tipe data tidak aman | `declare(strict_types=1)` di seluruh kode |

---

## ⚙️ Cara Menjalankan

**Prasyarat:** PHP 8.2+, Composer.

```bash
# 1. Instal dependency (menghasilkan autoload)
composer install

# 2. Jalankan server development
php -S localhost:8000 -t public
```

Buka `http://localhost:8000` di browser.

> Dengan **Laragon + Nginx**: arahkan *root* virtual host ke folder `public/`, lalu akses lewat `http://todo-app.test`.

---

## 🧪 Menjalankan Test

```bash
composer test
```

---

## ✅ Fitur

- Tambah tugas
- Tandai selesai / belum selesai
- Hapus tugas
- Filter berdasarkan status (`all` / `pending` / `completed`)
- Pesan flash setelah aksi
- Validasi input + proteksi CSRF & XSS

---

## 🛠️ Teknologi

- PHP 8.2+
- PDO + SQLite
- Composer (PSR-4 autoload)
- Arsitektur Controller–Repository–View (CRV)
- PHPUnit (mendatang langkah berikutnya)

---

## 📌 Tentang Proyek Ini

Dibangun sebagai bagian dari perjalanan pengembangan diri menjadi *software engineer* profesional. Kode sengaja ditulis eksplisit dan didokumentasikan agar mudah dibaca, diuji, dan dikembangkan — sesuai standar industri.
