<?php
session_start();
require 'db.php'; 
require '../PHPMailer-6.10.0/src/Exception.php';
require '../PHPMailer-6.10.0/src/PHPMailer.php';
require '../PHPMailer-6.10.0/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generarCodigoVerificacion() {
    return rand(100000, 999999);
}

function enviarCorreoVerificacion($correo, $nombre, $codigo) {
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
        $mail->Subject = 'Código de verificación de cuenta';
        $mail->Body    = "Hola <strong>$nombre</strong>, tu código de verificación es: <strong>$codigo</strong>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['contrasena'];
    $confirmar = $_POST['confirmar_contrasena'];

    
    if ($password !== $confirmar) {
        $error = "Las contraseñas no coinciden.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo inválido.";

    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);

        if ($stmt->rowCount() > 0) {
            $error = "Este correo ya está registrado.";

        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $fecha = date('Y-m-d H:i:s');
           
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, fecha_registro) 
            VALUES (?, ?, ?, 0)");
            $stmt->execute([$nombre, $correo, $password_hash]);
            
            $usuario_id = $pdo->lastInsertId();

            $codigo = generarCodigoVerificacion();
            $stmt = $pdo->prepare("INSERT INTO verificacion_usuarios (usuario_id, codigo_verificacion, intentos_reenvio, fecha_envio_codigo) 
            VALUES (?, ?, 1, ?)");
            $stmt->execute([$usuario_id, $codigo, $fecha]);
            
            $resultado_correo = enviarCorreoVerificacion($correo, $nombre, $codigo);

            if ($resultado_correo === true) {
                $_SESSION['correo_verificacion'] = $correo;
                header("Location: verify.php");
                exit;
                
            } else {
                $error = $resultado_correo;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="../assets/css/login.css"> 
    <meta charset="UTF-8">
    <title>Registrarse</title>
</head>
<body>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="../assets/css/login.css"> 
    <meta charset="UTF-8">
    <title>Registrarse</title>
</head>
<body>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <div class="login-container">
  <form class="form login-form" method="POST" action="register.php">
    <h2>Crear cuenta</h2>
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="email" name="correo" placeholder="Correo electrónico" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <input type="password" name="confirmar_contrasena" placeholder="Confirmar contraseña" required>
    <button type="submit">Crear cuenta</button>
    <a href="login.php" class="login-link">¿Ya tienes cuenta? Inicia sesión aquí</a>
  </form>
</div>
</body>
</html>