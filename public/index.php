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
