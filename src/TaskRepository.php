<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Kelas TaskRepository
 * 
 * Akses data untuk tabel tasks.
 * Memisahkan logika database dari logika bisnis aplikasi.
 * 
 * Semua query database menggunakan prepared statements untuk mencegah SQL injection.
 * nilai dari user tidak pernah langsung dimasukkan ke query, melainkan melalui parameter binding
 * agar lebih aman dari SQL injection.
 *
 */

final class TaskRepository
{
    private PDO $pdo;

    public function __construct(Database $database)
    {
        $this->pdo = $database->getPDO();
    }

    /**
     * fungsi untuk mengambil seluruh tasks diurutkan dari yang terbaru
     * @return array<int, array<string,mixed>>
     */
    public function all(?string $filter = null):array
    {
        $sql = 'SELECT * FROM tasks';
        //Hanya teraapkan filter jika nilainya valid (bukan null dan bukan string kosong)
        if($filter === 'pending' || $filter === 'completed'){
            $stmt = $this->pdo->prepare($sql . ' WHERE status = :status ORDER BY id DESC');
            $stmt->execute(['status' => $filter]);
        } else {
            $stmt = $this->pdo->query($sql . ' ORDER BY id DESC');
        }
        return $stmt->fetchAll();
        
    }

    /**
     * fungsi untuk mengambil task berdasarkan id
     */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $task = $stmt->fetch();

        return $task !== false ? $task : null;
    }

    /**
     * fungsi untuk menambahkan task baru
     * @return int id task yang baru ditambahkan        
     */
    public function create(string $title, ?string $description = null):int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks (title, description) VALUES (:title, :description)'
        );
        $stmt->execute([
            'title' => $title,
            'description' => $description,
        ]);
        return (int) $this->pdo->lastInsertId();  
    }

    /**
     * fungsi untuk mengupdate task berdasarkan id
     * @return bool true jika berhasil, false jika gagal    
     */
    public function update(int $id, string $title, ?string $description = null): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE tasks
            SET title = :title, description = :description, update_at = datetime('now')
            WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * fungsi untuk mengubah status pending ke completed dan sebaliknya

     */

    public function toggleStatus(int $id): bool
    {
        $task = $this->find($id);
        if ($task === null) {
            return false;
            
        }
        $newStatus = $task['status'] === 'completed' ? 'pending' : 'completed';

        $stmt = $this->pdo->prepare(
            "UPDATE tasks SET status = :status, update_at = datetime('now') WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'status' => $newStatus,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * fungsi untuk menghapus task berdasrkan id
     * @return bool true jika berhasil, false jika gagal
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }


}
