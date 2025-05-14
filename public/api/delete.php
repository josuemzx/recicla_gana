<?php
// 1) No mostrar warnings/notices en la salida JSON
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 2) Cabeceras y CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// 3) Leer credenciales desde entorno
$host     = getenv('MYSQL_HOST')     ?: getenv('DB_HOST') ?: 'localhost';
$port     = getenv('MYSQL_PORT')     ?: '3306';
$dbname   = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'ecodata';
$user     = getenv('MYSQL_USER')     ?: getenv('DB_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASS') ?: '';

// 4) Conectar PDO
$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// 5) Leer JSON entrante y validar
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

// 6) Borrar del score
try {
    $stmt = $pdo->prepare("DELETE FROM scores WHERE name = ?");
    $stmt->execute([ trim($in['name']) ]);
    echo json_encode(['ok' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB delete failed']);
    exit;
}
