<?php
// 1) Mostrar errores para depurar
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2) Cabeceras JSON + CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 3) Leer credenciales desde entorno
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'ecodata';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    // 4) Conexión PDO con puerto
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed: '.$e->getMessage()]);
    exit;
}

// 5) Leer JSON entrante y validar contraseña
$in = json_decode(file_get_contents('php://input'), true);
if (
    !is_array($in) ||
    !isset($in['name'], $in['bottles'], $in['pass']) ||
    $in['pass'] !== 'eco123'
) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$name    = trim($in['name']);
$bottles = intval($in['bottles']);

try {
    // 6) INSERT / UPDATE
    $stmt = $pdo->prepare("
        INSERT INTO scores (`name`, `bottles`)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE
          bottles = bottles + VALUES(bottles)
    ");
    $stmt->execute([$name, $bottles]);

    // 7) Leer total actualizado
    $stmt  = $pdo->prepare("SELECT bottles FROM scores WHERE `name` = ?");
    $stmt->execute([$name]);
    $total = (int)$stmt->fetchColumn();

    echo json_encode(['total' => $total]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB write/read failed: '.$e->getMessage()]);
    exit;
}
