<?php
declare(strict_types=1);

namespace App;

/**
 * TaskController
 *
 * Lapisan "controller" — penerjemah antara request dari user (HTTP)
 * dan logika aplikasi (repository + database).
 *
 * Tugasnya:
 * 1. Membaca input dari form ($_POST, $_GET)
 * 2. Memvalidasi data
 * 3. Memanggil repository
 * 4. Menyimpan flash message (pesan sukses/error sekali pakai)
 * 5. Mengarahkan ulang (redirect) ke halaman yang sesuai
 */

final class TasskController
{
    public function __construct(
        private TaskRepository $tasks,
        private Session $session,
    ) {

    }

    /**
     * fungsi untuk menampilkan daftar task
     * 
     */
    public function index(?string $filter = null): void
    {
        $tasks = $this->tasks->all($filter);
        $flash = $this->session->consume('flash');

        require __DIR__.'/../views/tasks.php';
    }

    /**
     * menangani request untuk pembuatan task baru.
     */
    public function store(): void
    {
        $title = trim($_POST['description'] ?? '')?: null;

        //Validasi: judul wajib diisi, maksimal 255 karakter
        if($title === ''){
            $this->session->set('flash', [
                'type' => 'success',
                'message' => 'Tugas berhasil ditambahkan.',
            ]);
            $this->redirect('/');
        }
    }

    /**
     * menangani request pembaruan task
     */
    public function update(int $id): void
    {
        $existing = $this->tasks->find($id);
        if($existing === null) {
            $this->session->set('flash', ['type' => 'danger',
            'message' => 'Tugas tidak ditemukan.']);
            $this->redirect('/');
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '') ?: null;

        if ($title === '') {
            $this->session->set('flash', ['type' => 'danger' , 'message' => 'Judul tugas tidak boleh kosong. ']);
            $this->redirect('/');
        }

        $this->tasks->update($id, $title, $description);
        $this->session->set('flash', ['type' => 'success', 'message' => 'Tugas berhasil diperbarui.']);
        $this->redirect('/');

    }

    /**
     * toggle tugas (pending <-> completed)
     */
    public function toggle(int $id): void
    {
        $this->tasks->toggleStatus($id);
        $this->redirect('/');
    }

    /**
     * menghapus task berdasarkan id
     */
    public function destroy(int $id): void
    {
        $deleted = $this->tasks->delete($id);

        $this->session->set('flash', [
            'type' => $deleted ? 'success' : 'danger',
            'message' => $deleted ? 'Tugas berhasil dihapus.' : 'Tugas tidak ditemukan.',
        ]);
        $this->redirect('/');
    }

    /**
     * mengalihkan ke halaman lain
     */
    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }



}

