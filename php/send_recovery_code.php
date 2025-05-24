<?php
session_start();
require 'db.php';
require '../PHPMailer-6.10.0/src/Exception.php';
require '../PHPMailer-6.10.0/src/PHPMailer.php';
require '../PHPMailer-6.10.0/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["correo"]);

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "Correo inválido.";
        exit;
    }

    try {
        // Obtener el ID del usuario
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ? AND verificado = 1");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && isset($usuario['id'])) {
            $usuario_id = $usuario['id'];
            $codigo = rand(100000, 999999);

            // Verifica si ya existe una fila en verificacion_usuarios
            $check = $pdo->prepare("SELECT 1 FROM verificacion_usuarios WHERE usuario_id = ?");
            $check->execute([$usuario_id]);

            if ($check->fetch()) {
                // Si ya existe, actualiza
                $stmt2 = $pdo->prepare("UPDATE verificacion_usuarios SET codigo_verificacion = ?, fecha_envio_codigo = NOW(), codigo_usado = 0 WHERE usuario_id = ?");
                $stmt2->execute([$codigo, $usuario_id]);
            } else {
                // Si no existe, inserta
                $stmt2 = $pdo->prepare("INSERT INTO verificacion_usuarios (usuario_id, codigo_verificacion, fecha_envio_codigo, intentos_reenvio, codigo_usado) VALUES (?, ?, NOW(), 0, 0)");
                $stmt2->execute([$usuario_id, $codigo]);
            }

            // Enviar el correo
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
                $mail->Subject = 'Código de recuperación de contraseña';
                $mail->Body = "Tu código de recuperación es: <strong>$codigo</strong>";

                $mail->send();

                $_SESSION['correo_recuperar'] = $correo;
                header("Location: reset.php");
                exit();
            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
            }

        } else {
            echo "El correo no está registrado o no ha sido verificado.";
        }
    } catch (PDOException $e) {
        echo "Error al ejecutar la consulta: " . $e->getMessage();
    }
}
?>
