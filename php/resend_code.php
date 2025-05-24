<?php
session_start();
require 'db.php';
require __DIR__ . '/../PHPMailer-6.10.0/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-6.10.0/src/SMTP.php';
require __DIR__ . '/../PHPMailer-6.10.0/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['correo_recuperar'])) {
    echo "No hay correo pendiente de verificación.";
    exit();
}

$correo = $_SESSION['correo_recuperar'];

$stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE correo = ?");
$stmt->execute([$correo]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}

$usuario_id = $usuario['id'];
$nombre = $usuario['nombre'];
$codigo = rand(100000, 999999);

$stmt = $pdo->prepare("SELECT intentos_reenvio, fecha_envio_codigo FROM verificacion_usuarios WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$verificacion = $stmt->fetch();

$intentos = 0;
$hoy = date('Y-m-d');

if ($verificacion) {
    $fecha_envio = substr($verificacion['fecha_envio_codigo'], 0, 10);
    $intentos = ($fecha_envio === $hoy) ? $verificacion['intentos_reenvio'] : 0;

    if ($intentos >= 5) {
        echo "Has alcanzado el límite de 5 reintentos por día. Intenta mañana.";
        exit();
    }

    $stmt = $pdo->prepare("UPDATE verificacion_usuarios SET codigo_verificacion = ?, fecha_envio_codigo = NOW(), intentos_reenvio = ?, codigo_usado = 0 WHERE usuario_id = ?");
    $stmt->execute([$codigo, $intentos + 1, $usuario_id]);
} else {
    $stmt = $pdo->prepare("INSERT INTO verificacion_usuarios (usuario_id, codigo_verificacion, intentos_reenvio) VALUES (?, ?, 1)");
    $stmt->execute([$usuario_id, $codigo]);
}


$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'alejoloaiza1998@gmail.com';
    $mail->Password = 'pfry fprq lbet xlkg';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom('alejoloaiza1998@gmail.com', 'Master of Food');
    $mail->addReplyTo('no-reply@masterfood.com', 'No Responder');
    $mail->addAddress($correo, $nombre);
    $mail->isHTML(true);
    $mail->Subject = 'Nuevo código de verificación';
    $mail->Body    = "Hola $nombre,<br>Tu nuevo código de verificación es: <b>$codigo</b>";

    $mail->send();
    echo "Se ha reenviado el código. Reintentos usados hoy: " . ($intentos + 1) . " de 5. <a href='reset.php'>Restablece tu contraseña: </a>";
} catch (Exception $e) {
    echo "Error al enviar el código: {$mail->ErrorInfo}";
}
