# To-Do List App

Aplikasi manajemen tugas (CRUD) berbasis PHP 8 + SQLite sebagai portofolio
teknik untuk mendemonstrasikan praktik pengembangan web modern.

## ✨ Fitur
- ➕ Tambah tugas (validasi input)
- ✏️ Edit & hapus tugas
- ✅ Tandai selesai / batalkan
- 🔍 Filter berdasarkan status
- ⚠️ Proteksi CSRF & SQL injection safe (PDO prepared statements)

## 🚀 Cara Menjalankan
1. `composer install`  (saat dependensi sudah ada)
2. `cd public && php -S localhost:8000`
3. Buka `http://localhost:8000`

## 🛠️ Teknologi
PHP 8.2 · SQLite · Composer (PSR-4) · PDO

## 📁 Struktur
todo-list-app/
├── public/       # Front controller (satu-satunya entry point)
├── src/          # Logika aplikasi (Database, Repository, Controller)
├── views/        # Template tampilan
└── storage/      # Database & file runtime

## 📄 Lisensi
MIT