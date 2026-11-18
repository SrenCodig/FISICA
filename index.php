<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($path === '/api/simulate') {
    require __DIR__ . '/Backend/Routes/simulate.php';
    exit;
}
if ($path === '/health') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'app' => 'Física en juego']);
    exit;
}
require __DIR__ . '/Frontend/Pages/index.php';
