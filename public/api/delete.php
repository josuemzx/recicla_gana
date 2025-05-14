<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

try {
    $pdo = new PDO(
        "pgsql:host={$host};port={$port};dbname={$db}",
        $user, $pass,
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error'=>'DB connection failed: '.$e->getMessage()]);
    exit;
}

$in = json_decode(file_get_contents('php://input'), true);
if (!is_array($in) || !isset($in['name'],$in['pass']) || $in['pass']!=='eco123') {
    http_response_code(401);
    echo json_encode(['error'=>'unauthorized']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM scores WHERE name = :name");
    $stmt->execute([':name'=>trim($in['name'])]);
    echo json_encode(['ok'=>true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error'=>'DB delete failed: '.$e->getMessage()]);
    exit;
}
