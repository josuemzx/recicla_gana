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
if (!is_array($in) || !isset($in['name'],$in['bottles'],$in['pass']) || $in['pass']!=='eco123') {
    http_response_code(401);
    echo json_encode(['error'=>'unauthorized']);
    exit;
}

$name    = trim($in['name']);
$bottles = intval($in['bottles']);

try {
    $stmt = $pdo->prepare("
      INSERT INTO scores (name, bottles)
      VALUES (:name, :bottles)
      ON CONFLICT (name) DO UPDATE
        SET bottles = scores.bottles + EXCLUDED.bottles
    ");
    $stmt->execute([':name'=>$name, ':bottles'=>$bottles]);

    $stmt = $pdo->prepare("SELECT bottles FROM scores WHERE name = :name");
    $stmt->execute([':name'=>$name]);
    $total = (int)$stmt->fetchColumn();

    echo json_encode(['total'=>$total]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error'=>'DB write/read failed: '.$e->getMessage()]);
    exit;
}
