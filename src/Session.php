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
     * Mengambil data dari session
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
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
}
