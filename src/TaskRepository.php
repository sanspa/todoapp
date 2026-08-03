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
    

    public function __construct(private Database $database)
    {
        
    }

    private function pdo(): PDO
    {
        return $this->database->getPDO();
    }

    /**
     * fungsi untuk mengambil seluruh tasks diurutkan dari yang terbaru
     * @return array<int, array<string,mixed>>
     */
    public function all(?string $filter = null):array
    {
        if(!in_array($filter,['pending','completed'],true)){
            $filter = null;
        }

        if($filter === null){
            $stmt = $this->pdo()->query(
                'SELECT * FROM tasks ORDER BY 
                CASE WHEN status = "pending" THEN 0 ELSE 1 END, 
                id DESC'
            );
        } else {
            $stmt = $this->pdo()->prepare(
                'SELECT * FROM tasks WHERE status = :status ORDER BY id DESC'
            );
            $stmt->execute(['status' => $filter]);
        }
        return $stmt->fetchAll();     
    
        
    }

    /**
     * fungsi untuk mengambil task berdasarkan id
     */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM tasks WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * fungsi untuk menambahkan task baru    
     */
    public function create(string $title, ?string $description):bool
    {
        $stmt = $this->pdo()->prepare(
            'INSERT INTO tasks (title, description) VALUES (:title, :description)'
        );
        return $stmt->execute([
            'title' => $title,
            'description' => $description,
        ]);
        
    }

    /**
     * fungsi untuk mengupdate task berdasarkan id
     * @return bool true jika berhasil, false jika gagal    
     */
    public function update(int $id, string $title, ?string $description): bool
    {
        $stmt = $this->pdo()->prepare(
            "UPDATE tasks
            SET title = :title, description = :description, updated_at = datetime('now')
            WHERE id = :id"
        );
        return $stmt->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
        ]);

    }

    /**
     * fungsi untuk mengubah status pending ke completed dan sebaliknya

     */

    public function toggleStatus(int $id): void
    {
       $stmt = $this->pdo()->prepare(
            "UPDATE tasks SET status = 
            CASE WHEN status = 'pending' THEN 'completed' ELSE 'pending' END
            WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
    }

    /**
     * fungsi untuk menghapus task berdasrkan id
     * @return bool true jika berhasil, false jika gagal
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo()->prepare('DELETE FROM tasks WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }


}
