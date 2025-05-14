<?php
// --- 1) Suprimir warnings/notices y forzar JSON
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// --- 2) Credenciales de BD desde entorno o por defecto
$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'ecodata';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    // --- 3) Conexión PDO
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // --- 4) Consulta ordenada descendente
    $stmt = $pdo->query("SELECT name, bottles FROM scores ORDER BY bottles DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 5) Devolver array JSON
    echo json_encode($rows);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB read failed']);
    exit;
}
