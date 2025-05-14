<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
$host=getenv('DB_HOST')?:'localhost';
$db=getenv('DB_NAME')?:'ecodata';
$user=getenv('DB_USER')?:'root';
$pass=getenv('DB_PASS')?:'';
$pdo=new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$res=$pdo->query('SELECT name, bottles FROM scores ORDER BY bottles DESC');
echo json_encode($res->fetchAll(PDO::FETCH_ASSOC));