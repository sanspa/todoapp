<?php
declare(strict_types=1);

namespace App\Tests;

use App\TaskRepository;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Test TaskRepository menggunakan SQLite in-memory.
 * Karena TaskRepository kini menerima PDO langsung (dependency injection),
 * test ini bisa berjalan tanpa menyentuh storage aplikasi.
 */

final class TaskRepositoryTest extends TestCase
{
    private PDO $pdo;
    private TaskRepository $repo;


    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Buat skema tabel yang sama dengan migrasi aplikasi
        $this->pdo->exec(
            "CREATE TABLE tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                status TEXT NOT NULL DEFAULT 'pending',
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )"
        );

        $this->repo = new TaskRepository($this->pdo);
    }

    public function testCreateReturnsBool(): void
    {
        $result = $this->repo->create('Belajar PHP', 'Fokus pada PDO dan dependency injection');
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    public function testCreatePersistsRow(): void
    {
        $this->repo->create('Belanja kebutuhan','Kebutuhan rumah tangga dan kantor');

        $row = $this->pdo->query('SELECT COUNT(*) AS total FROM tasks')->fetch();
        $this->assertEquals(1, (int)$row['total']);
    }

    public function testFindReturnsArrayWhenExists(): void
    {
        $this->repo->create('Tugas dengan deskripsi','DEskripsi tugas');
        $id = (int)$this->pdo->lastInsertId();

        $task = $this->repo->find($id);

        $this->assertIsArray($task);
        $this->assertSame('Tugas dengan deskripsi', $task['title']);
        $this->assertSame('pending', $task['status']);
    }

     public function testFindReturnsNullWhenMissing(): void
    {
        $task = $this->repo->find(999);
        $this->assertNull($task);
    }

    public function testAllReturnsAllTasks(): void
    {
        $this->repo->create('Tugas 1','desc 1');
        $this->repo->create('Tugas 2','descr 2');
        $this->repo->create('Tugas 3','descr 3');

        $tasks = $this->repo->all();
        $this->assertCount(3, $tasks);
    }

    public function testAllFiltersPending(): void
    {
        $this->repo->create('Aktif','task yang aktiv');
        $id = (int)$this->pdo->lastInsertId();
        $this->repo->create('Selesai','Task sudah selesai');

        // tandai tugas kedua selesai
        $this->pdo->exec("UPDATE tasks SET status='completed' WHERE id=" . ($id + 1));

        $tasks = $this->repo->all('pending');
        $this->assertCount(1, $tasks);
        $this->assertSame('Aktif', $tasks[0]['title']);
    }

    public function testUpdateReturnsBool(): void
    {
        $this->repo->create('Judul Lama','Task berjudul lama');
        $id = (int)$this->pdo->lastInsertId();

        $result = $this->repo->update($id, 'Judul Baru', 'deskripsi baru');
        $this->assertTrue($result);

        $task = $this->repo->find($id);
        $this->assertSame('Judul Baru', $task['title']);
    }

     public function testUpdateReturnsFalseForMissing(): void
    {
        $result = $this->repo->update(999, 'Tidak ada', null);
        $this->assertFalse($result);
    }

    public function testDeleteReturnsBool(): void
    {
        $this->repo->create('Akan dihapus','');
        $id = (int)$this->pdo->lastInsertId();

        $result = $this->repo->delete($id);
        $this->assertTrue($result);
        $this->assertNull($this->repo->find($id));
    }

    public function testToggleStatusChangesValue(): void
    {
        $this->repo->create('Toggle ini','');
        $id = (int)$this->pdo->lastInsertId();

        $this->repo->toggleStatus($id);
        $task = $this->repo->find($id);
        $this->assertSame('completed', $task['status']);
    }

}