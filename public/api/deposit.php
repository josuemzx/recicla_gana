<?php
// 1) No mostrar warnings/notices en la salida JSON
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 2) Cabeceras
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 3) Leer credenciales desde variables de entorno (o define aquí tus valores)
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'ecodata';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

// 4) Conectar con PDO
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

// 5) Leer y decodificar JSON de la petición
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

// 6) Sanitizar y preparar datos
$name    = trim($in['name']);
$bottles = intval($in['bottles']);

// 7) Hacer INSERT … ON DUPLICATE KEY UPDATE
try {
    $stmt = $pdo->prepare("
        INSERT INTO scores (`name`, `bottles`)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE
          bottles = bottles + VALUES(bottles)
    ");
    $stmt->execute([$name, $bottles]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB write failed']);
    exit;
}

// 8) Leer el total actualizado
try {
    $stmt  = $pdo->prepare("SELECT bottles FROM scores WHERE `name` = ?");
    $stmt->execute([$name]);
    $total = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB read failed']);
    exit;
}

// 9) Devolver resultado limpio en JSON
echo json_encode(['total' => $total]);
