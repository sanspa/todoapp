<?php
declare(strict_types=1);

namespace App;

// ============================================================
// Front Controller — satu-satunya pintu masuk aplikasi.
// Semua request (GET/POST) masuk ke file ini, lalu dirutekan
// ke controller yang sesuai berdasarkan metode + URL.
// ============================================================

require __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Session;
use App\TaskController;
use App\TaskRepository;

// =====================================================================
// 1. Bootstrap: mulai session & setup database
// =====================================================================
$session = new Session();
$session->start();



$db = Database::getInstance();
$db->migrate();

$repo = new TaskRepository($db);
$controller = new TaskController($repo, $session);

// ============================================================
// 2. Routing — tentukan aksi berdasarkan metode + jalur URL
// ============================================================
$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path   = rtrim($path, '/') ?: '/';

// ============================================================
// 3. Proteksi CSRF — semua form POST wajib membawa token valid
// ============================================================
if ($method === 'POST') {
    $token = $_POST['_token'] ?? '';
    echo $token;

    if (!$session->validateCsrfToken($token)) {
        http_response_code(419);
        exit('Sesi berakhir atau token tidak valid. Silakan kembali ke halaman awal.');
    }
}


// ============================================================
// 4. Penanganan rute
// ============================================================
if ($method === 'GET' && ($path === '/' || $path === '/index.php')) {
    // Filter status: ?status=pending | completed (opsional)
    $filter = $_GET['status'] ?? null;
    $controller->index($filter);

} elseif ($method === 'POST' && $path === '/tasks') {
    $controller->store();

} elseif ($method === 'POST' && $path === '/tasks/update') {
    $controller->update((int) ($_POST['id'] ?? 0));

} elseif ($method === 'POST' && $path === '/tasks/toggle') {
    $controller->toggle((int) ($_POST['id'] ?? 0));

} elseif ($method === 'POST' && $path === '/tasks/delete') {
    $controller->destroy((int) ($_POST['id'] ?? 0));

} else {
    http_response_code(404);
    exit('404 — Halaman tidak ditemukan.');
}

