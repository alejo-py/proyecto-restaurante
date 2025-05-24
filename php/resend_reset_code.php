<?php
session_start();
require 'db.php';
require '../PHPMailer-6.10.0/src/Exception.php';
require '../PHPMailer-6.10.0/src/PHPMailer.php';
require '../PHPMailer-6.10.0/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['reenviar_codigo'])) {
    $correo = $_SESSION['correo_recuperar'] ?? '';

    if (!$correo) {
        header("Location: reset.php?error=Sesión expirada. Intenta nuevamente.");
        exit;
    }

    $stmt_usuario = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt_usuario->execute([$correo]);
    $usuario_data = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    if ($usuario_data && isset($usuario_data['id'])) {
        $usuario_id = $usuario_data['id'];

        $nuevo_codigo = rand(100000, 999999);
        $ahora = date('Y-m-d H:i:s');

        $stmt_update_codigo = $pdo->prepare("UPDATE verificacion_usuarios SET codigo_verificacion = ?, fecha_envio_codigo = ?, codigo_usado = 0 WHERE usuario_id = ?");
        $stmt_update_codigo->execute([$nuevo_codigo, $ahora, $usuario_id]);

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
            $mail->Subject = 'Nuevo código para restablecer tu contraseña';
            $mail->Body    = "Tu nuevo código de verificación para restablecer tu contraseña es: <strong>$nuevo_codigo</strong>. Por favor, ingresa este código en el formulario de restablecimiento.";

            $mail->send();
            header("Location: reset.php?mensaje=Se ha enviado un nuevo código a tu correo electrónico.");
            exit;
        } catch (Exception $e) {
            header("Location: reset.php?error=Error al enviar el correo electrónico: " . $mail->ErrorInfo);
            exit;
        }
    } else {
        header("Location: reset.php?error=Error al reenviar el código. Intenta nuevamente.");
        exit;
    }
} else {
    header("Location: reset.php"); 
    exit;
}
?>