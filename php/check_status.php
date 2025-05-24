<?php
session_start(); 
require 'db.php'; 

if (isset($_SESSION['usuario_id'])) {
    $userId = $_SESSION['usuario_id'];
    $userName = $_SESSION['usuario_nombre'];

    echo json_encode([
        'loggedIn' => true,
        'user' => [
            'id' => $userId,
            'name' => $userName
        ],
        'message' => 'Usuario logueado.'
    ]);

} else {
    echo json_encode([
        'loggedIn' => false,
        'user' => null, 
        'message' => 'Usuario no logueado.'
    ]);
}

exit;
?>