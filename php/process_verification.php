<?php
require 'db.php';

if (!isset($_POST['correo']) || !isset($_POST['codigo'])) {
    echo "Por favor, completa todos los campos.";
    exit();
}

$correo = trim($_POST['correo']);
$codigo = trim($_POST['codigo']);

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt->execute([$correo]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "Correo no registrado.";
    exit();
}

$usuario_id = $usuario['id'];

$stmt = $pdo->prepare("SELECT * FROM verificacion_usuarios 
                       WHERE usuario_id = ? AND codigo_verificacion = ? AND codigo_usado = 0");
$stmt->execute([$usuario_id, $codigo]);
$verificacion = $stmt->fetch();

if ($verificacion) {
    $pdo->prepare("UPDATE usuarios SET verificado = 1 WHERE id = ?")->execute([$usuario_id]);
    $pdo->prepare("UPDATE verificacion_usuarios SET codigo_usado = 1 WHERE usuario_id = ?")->execute([$usuario_id]);

    echo "✅ Cuenta verificada correctamente. Ya puedes <a href='login.php'>iniciar sesión</a>.";
} else {
    echo "❌ Código incorrecto o ya fue utilizado.";
}
