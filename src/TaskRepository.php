<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Repository untuk operasi CRUD tabel "tasks".
 *
 * Bergantung pada PDO (bukan tipe konkret Database) sehingga mudah
 * diuji dengan database in-memory dan berpegang pada prinsip
 * dependency injection.
 */


class TaskRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function pdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * fungsi untuk mengambil seluruh tasks diurutkan dari yang terbaru
     * @return array<int, array<string,mixed>>
     */
    public function all(?string $filter = null): array
    {
        $sql   = 'SELECT * FROM tasks';
        $where = '';

        if (in_array($filter, ['pending', 'completed'], true)) {
            $where = ' WHERE status = ?';
        }

        $stmt = $this->pdo->prepare($sql . $where . ' ORDER BY created_at DESC, id DESC');
        if ($where !== '') {
            $stmt->execute([$filter]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * fungsi untuk mengambil task berdasarkan id
     */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    /**
     * fungsi untuk menambahkan task baru    
     */
    public function create(string $title, ?string $description): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks (title, description, status, created_at)
             VALUES (:title, :description, :status, :created_at)'
        );

        return $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':status'      => 'pending',
            ':created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * fungsi untuk mengupdate task berdasarkan id
     * @return bool true jika berhasil, false jika gagal    
     */
    public function update(int $id, string $title, ?string $description = null): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tasks SET title = :title, description = :description WHERE id = :id'
        );
        $stmt->execute([
            'id'          => $id,
            'title'       => $title,
            'description' => $description,
        ]);

        // true hanya jika ada baris yang benar-benar diperbarui
        return $stmt->rowCount() > 0;
    }
    /**
     * fungsi untuk mengubah status pending ke completed dan sebaliknya

     */

    public function toggleStatus(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE tasks SET status = 
            CASE WHEN status = 'pending' THEN 'completed' ELSE 'pending' END
            WHERE id = :id"
        );
        return $stmt->execute([$id]);
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
