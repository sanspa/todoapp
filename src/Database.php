<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Kelas Database
 *
 * Mengelola koneksi database SQLite menggunakan PDO (PHP Data Objects).
 * Menggunakan pattern Singleton agar hanya ada satu koneksi database
 * untuk seluruh siklus hidup aplikasi.
 *
 * STANDAR INDUSTRI:
 * - PDO memberikan prepared statements (proteksi SQL injection)
 * - ERRMODE_EXCEPTION membuat error muncul sebagai exception yang bisa ditangkap
 * - Singleton mencegah membuka banyak koneksi yang boros resource
 */

final class Database
{
    /**
     * create a singleton instance
     */
    private static ?Database $instance = null;

    /**
     * an active PDO connection
     */
    private PDO $pdoconnection;

    /** 
     * Konstruktor privat - mencegah instansiasi langsung dari pihak luar 
    */
    private function __construct()
    {
        $dbPath = __DIR__.'/../storage/database.sqlite';

        //memastikan direktori storage ada
        $storageDir = dirname($dbPath);
        if(!is_dir($storageDir)) {
            mkdir($storageDir,0755,true);
        }
        try {
            $this->pdoconnection = new PDO(
                "sqlite:$dbPath",
                null,
                null,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
                );

            $this->pdoconnection->exec('PRAGMA foreign_keys = ON;');
            
        } catch (PDOException $e) {
            throw new RuntimeException('Gagal menghubungkan ke database: ' . $e->getMessage());
        }
        
    }

    /**
     * Mendapatkan instance Database (singleton)
     *
     * @return Database
     */
    public static function getInstance():self
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * mengambil obyek PDO untuk melakukan query
     *
     * @return PDO
     */

    public function getPDO(): PDO
    {
        return $this->pdoconnection;   
    }

    /**
     * Membuat skema database (tabel baru) jika belum ada
     * Dipanggil sekali saat aplikasi pertama kali dijalankan
     */

    public function migrate():void
    {
        $sql = <<<'SQL'
        CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            status TEXT NOT NULL DEFAULT 'pending'
            CHECK (status IN ('pending', 'completed')),
            created_at TEXT NOT NULL DEFAULT (datetime('now')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now'))
        );
        SQL;

        $this->pdoconnection->exec($sql);
    }

}