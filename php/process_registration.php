<?php
require 'db.php';
require 'PHPMailer-6.10.0/src/PHPMailer.php';
require 'PHPMailer-6.10.0/src/SMTP.php';
require 'PHPMailer-6.10.0/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);
$contrasena = password_hash(trim($_POST['contrasena']), PASSWORD_DEFAULT);
$codigo = rand(100000, 999999);
$fecha = date("Y-m-d H:i:s");


$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
$stmt->execute([$correo]);

if ($stmt->fetch()) {
    echo "Ya existe una cuenta con ese correo.";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, verificado, codigo_verificacion, fecha_creacion)
VALUES (?, ?, ?, 0, ?, ?)");
$stmt->execute([$nombre, $correo, $contrasena, $codigo, $fecha]);

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
    $mail->Subject = 'Código de verificación';
    $mail->Body    = "Hola $nombre,<br>Tu código de verificación es: <b>$codigo</b>";

    $mail->send();
    echo "Registro exitoso. Revisa tu correo para verificar tu cuenta.";
    echo "<br><a href='verificar.php'>Verificar cuenta</a>";
} catch (Exception $e) {
    echo "Error al enviar correo: {$mail->ErrorInfo}";
}
