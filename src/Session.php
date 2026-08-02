<?php
declare(strict_types=1);

namespace App;

/**
 * Session
 *
 * Helper untuk mengelola session PHP dan flash messages.
 *
 * Flash message = pesan (sukses/error) yang hanya muncul SEKALI
 * setelah redirect, lalu hilang. Ini standar UX di aplikasi web.
 *
 * Keamanan:
 * - Session di-setup hanya jika belum aktif
 * - Menggunakan settings cookie yang lebih aman (HttpOnly, SameSite)
 * - Input user selalu di-escape saat dirender di view (XSS protection)
 */

final class Session
{
    /**
     * Konstruktor - memulai session jika belum aktif   
     */
    public function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => true, // set to true if using HTTPS
            'httponly' => true,
            'samesite' => 'Lax',   
        ]);
        session_start();
    }

    /**
     * Menyimpan data ke session
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Mengambil nilai dari session tanpa menghapusnya (untuk dibaca berulang).
     */
    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }


    /**
     * Menghapus data dari session
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Mengambil dan menghapus data dari session
     */
    public function consume(string $key): mixed
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }


     /**
     * Membuat (atau mengambil yang sudah ada) token CSRF untuk session ini,
     * lalu menyimpannya di session.
     */
    public function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

     /**
     * Memvalidasi token CSRF yang dikirim dari form POST.
     * Menggunakan hash_equals agar aman dari timing attack.
     */
    public function validateCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }
    

}
