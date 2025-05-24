<?php
session_start();
require 'db.php'; 
require '../PHPMailer-6.10.0/src/Exception.php';
require '../PHPMailer-6.10.0/src/PHPMailer.php';
require '../PHPMailer-6.10.0/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['correo_verificacion'])) {
    echo "No se ha iniciado el proceso de verificación.";
    exit;
}

$correo = $_SESSION['correo_verificacion'];

if (isset($_POST['verificar'])) {
    $codigo_ingresado = $_POST['codigo'] ?? '';

    $stmt_usuario = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt_usuario->execute([$correo]);
    $usuario_data = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    if ($usuario_data && isset($usuario_data['id'])) {
        $usuario_id = $usuario_data['id'];

        $stmt_verificacion = $pdo->prepare("SELECT codigo_verificacion FROM verificacion_usuarios WHERE usuario_id = ?");
        $stmt_verificacion->execute([$usuario_id]);
        $verificacion = $stmt_verificacion->fetch(PDO::FETCH_ASSOC);

        if ($verificacion && $codigo_ingresado == $verificacion['codigo_verificacion']) {
            $pdo->prepare("UPDATE usuarios SET verificado = 1 WHERE id = ?")->execute([$usuario_id]);
            $pdo->prepare("UPDATE verificacion_usuarios SET codigo_usado = 1 WHERE usuario_id = ?")->execute([$usuario_id]);

            $stmt_datos = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE correo = ?");
            $stmt_datos->execute([$correo]);
            $datos = $stmt_datos->fetch(PDO::FETCH_ASSOC);

            if ($datos) {
                $_SESSION['usuario_id'] = $datos['id'];
                $_SESSION['usuario_nombre'] = $datos['nombre'];

                header("Location: login.php");
                exit;
            } else {
                $error = "Error al obtener los datos del usuario.";
            }
        } else {
            $error = "Código incorrecto.";
        }
    } else {
        $error = "No se encontró el usuario con ese correo.";
    }
} elseif (isset($_POST['reenviar'])) {
    $stmt_usuario_reenvio = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt_usuario_reenvio->execute([$correo]);
    $usuario_reenvio_data = $stmt_usuario_reenvio->fetch(PDO::FETCH_ASSOC);

    if ($usuario_reenvio_data && isset($usuario_reenvio_data['id'])) {
        $usuario_id_reenvio = $usuario_reenvio_data['id'];

        $stmt_reenvio_check = $pdo->prepare("SELECT intentos_reenvio, fecha_envio_codigo FROM verificacion_usuarios WHERE usuario_id = ?");
        $stmt_reenvio_check->execute([$usuario_id_reenvio]);
        $usuario_verificacion = $stmt_reenvio_check->fetch(PDO::FETCH_ASSOC);

        if (!$usuario_verificacion) {
            $error = "Información de verificación no encontrada.";
        } else {
            $reintentos = $usuario_verificacion['intentos_reenvio'] ?? 0;
            $ultimo_envio = strtotime($usuario_verificacion['fecha_envio_codigo'] ?? '1970-01-01 00:00:00');
            $hoy = date('Y-m-d');

            if (date('Y-m-d', $ultimo_envio) != $hoy) {
                $reintentos = 0;
            }

            if ($reintentos >= 5) {
                $error = "Has alcanzado el número máximo de intentos. Intenta más tarde.";
            } elseif (time() - $ultimo_envio < 30) {
                $error = "Debes esperar 30 segundos antes de reenviar.";
            } else {
                $nuevo_codigo = rand(100000, 999999);
                $ahora = date('Y-m-d H:i:s');

                $stmt_update_codigo = $pdo->prepare("UPDATE verificacion_usuarios SET codigo_verificacion = ?, intentos_reenvio = ?, fecha_envio_codigo = ? WHERE usuario_id = ?");
                $stmt_update_codigo->execute([$nuevo_codigo, $reintentos + 1, $ahora, $usuario_id_reenvio]);

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
                    $mail->addAddress($correo);
                    $mail->isHTML(true);
                    $mail->Subject = 'Reenvío de código de verificación';
                    $mail->Body = "Tu nuevo código de verificación es: <strong>$nuevo_codigo</strong>";

                    $mail->send();
                    $mensaje = "Código reenviado con éxito.";
                } catch (Exception $e) {
                    $error = "Error al reenviar el código: {$mail->ErrorInfo}";
                }
            }
        }
    } else {
        $error = "No se encontró el usuario con ese correo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Código</title>
    <link rel="stylesheet" href="../assets/css/recovery.css"> 
</head>
<body>
    <div class="recovery-container">
        <form class="recovery-form" method="POST" action="">
            <h2>Verifica tu cuenta</h2>
            <p style="color: #ccc;">Revisa tu correo (<?= htmlspecialchars($correo) ?>) y escribe el código que recibiste:</p>

            <?php if (isset($mensaje)): ?>
                <div class="error-message" style="background-color: #006400; color: white;"><?= $mensaje ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>

            <input type="text" name="codigo" placeholder="Código de verificación">
            <button type="submit" name="verificar">Verificar</button>
             <button type="submit" name="reenviar" value="1">Reenviar código</button>
            <a href="login.php">Volver al inicio de sesión</a>
        </form>
    </div>
</body>
</html>
