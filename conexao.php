<?php

$host = 'sql100.infinityfree.com';
$dbname = 'if0_41605484_pala'; 
$user = 'if0_41605484';
$pass = '0aHclODfo3txd';

try {
  
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>