<?php
$db = 'proyecto_restaurante';
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'proyecto_restaurante';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Error de conexión a la base de datos', 'error' => $e->getMessage()]);
    exit();
}
?>