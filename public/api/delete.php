<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
$host=getenv('DB_HOST')?:'localhost';
$db=getenv('DB_NAME')?:'ecodata';
$user=getenv('DB_USER')?:'root';
$pass=getenv('DB_PASS')?:'';
$pdo=new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$in=json_decode(file_get_contents('php://input'),true);
if(!$in || $in['pass']!=='eco123'){http_response_code(401);echo json_encode(['error'=>'unauthorized']);exit;}
$stmt=$pdo->prepare('DELETE FROM scores WHERE name=?');$stmt->execute([$in['name']]);
echo json_encode(['ok'=>true]);