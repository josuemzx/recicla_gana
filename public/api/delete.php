<?php
// --- 1) Suprimir warnings/notices y forzar JSON
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// --- 2) Credenciales de BD
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'ecodata';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

// --- 3) Conexión PDO
try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// --- 4) Leer JSON entrante
$in = json_decode(file_get_contents('php://input'), true);
if (
    !is_array($in) ||
    !isset($in['name'], $in['pass']) ||
    $in['pass'] !== 'eco123'
) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$name = trim($in['name']);

try {
    // --- 5) Borrar registro
    $stmt = $pdo->prepare("DELETE FROM scores WHERE name = ?");
    $stmt->execute([$name]);

    echo json_encode(['ok' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB delete failed']);
    exit;
}
