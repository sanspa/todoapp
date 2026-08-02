<?php
// views/tasks.php — View untuk menampilkan daftar tugas
// Data tersedia: $tasks (array), $filter (string), $flash (array|null)
declare(strict_types=1);

namespace App;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-2xl mx-auto py-10 px-4">

        <!-- ===== Judul ===== -->
        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800">📝 To-Do List</h1>
            <p class="text-gray-500 mt-1">Kelola tugas harianmu dengan mudah</p>
        </header>

        <!-- ===== Flash message ===== -->
        <?php if (!empty($flash)): ?>
            <div class="<?= $flash['type'] === 'danger' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700' ?> border px-4 py-3 rounded-lg mb-4">
                <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- ===== Form Tambah Tugas ===== -->
        <form method="POST" action="/tasks" class="bg-white rounded-xl shadow p-5 mb-6">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="title" placeholder="Apa yang perlu dikerjakan?"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Tambah
                </button>
            </div>

            <p class="text-gray-500 text-xs mt-2">Deskripsi opsional:</p>
            <input type="text" name="description" placeholder="Deskripsi (opsional)"
                   class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
        </form>

        <!-- ===== Filter status ===== -->
        <div class="flex gap-2 mb-6 justify-center">
            <a href="/?status=all"
               class="px-4 py-1.5 rounded-full text-sm <?= $filter !== 'pending' && $filter !== 'completed' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' ?>">Semua</a>
            <a href="/?status=pending"
               class="px-4 py-1.5 rounded-full text-sm <?= $filter === 'pending' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' ?>">Belum selesai</a>
            <a href="/?status=completed"
               class="px-4 py-1.5 rounded-full text-sm <?= $filter === 'completed' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' ?>">Selesai</a>
        </div>

        <!-- ===== Daftar Tugas ===== -->
        <?php if (empty($tasks)): ?>
            <div class="text-center text-gray-400 bg-white rounded-xl shadow p-10">
                <p class="text-5xl mb-3">🗒️</p>
                <p>Belum ada tugas di kategori ini.</p>
            </div>
        <?php else: ?>
            <ul class="space-y-3">
                <?php foreach ($tasks as $task): ?>
                    <?php $isDone = $task['status'] === 'completed'; ?>
                    <li class="bg-white rounded-xl shadow p-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 flex-1">
                            <!-- Toggle selesai -->
                            <form method="POST" action="/tasks/toggle">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                                <button type="submit"
                                        class="w-6 h-6 rounded-full border-2 border-gray-400 hover:border-green-500 transition <?= $isDone ? 'bg-green-500 border-green-500' : '' ?>"
                                        title="Tandai selesai/batal"></button>
                            </form>

                            <!-- Judul + deskripsi -->
                            <div class="flex-1">
                                <span class="block <?= $isDone ? 'line-through text-gray-400' : 'text-gray-800' ?>">
                                    <?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <?php if (!empty($task['description'])): ?>
                                    <span class="block text-sm text-gray-400"><?= htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Aksi edit -->
                            <form method="POST" action="/tasks/update" class="flex items-center gap-2">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                                <input type="text" name="title" value="<?= htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') ?>"
                                       class="w-44 px-2 py-1 border rounded focus:outline-none text-sm">
                                <button type="submit" class="px-3 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600" title="Simpan">Simpan</button>
                            </form>

                            <!-- Aksi hapus -->
                            <form method="POST" action="/tasks/delete"
                                  onsubmit="return confirm('Hapus tugas ini?')">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                                <button type="submit" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">Hapus</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <footer class="text-center text-gray-400 text-sm mt-8">
            Dibangun dengan PHP 8 & SQLite · Portofolio
        </footer>
    </div>
</body>
</html>
