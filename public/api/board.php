<?php
// 1) No mostrar warnings/notices en la salida JSON
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 2) Cabeceras
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 3) Leer credenciales desde entorno
$host     = getenv('MYSQL_HOST')     ?: getenv('DB_HOST') ?: 'localhost';
$port     = getenv('MYSQL_PORT')     ?: '3306';
$dbname   = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'ecodata';
$user     = getenv('MYSQL_USER')     ?: getenv('DB_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASS') ?: '';

// 4) Conexión PDO y lectura
$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SELECT name, bottles FROM scores ORDER BY bottles DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB read failed']);
    exit;
}
