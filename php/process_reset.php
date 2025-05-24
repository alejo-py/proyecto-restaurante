<?php
session_start();
require 'db.php'; 

if(isset($_POST['reenviar_codigo'])) {
    header("Location: resend_code.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_SESSION['correo_recuperar'] ?? '';
    $codigo = trim($_POST["codigo"]);
    $nueva_contrasena = trim($_POST["contrasena"]);

    if (!$correo) {
        header("Location: reset.php?error=Sesión expirada. Intenta nuevamente.");
        exit;
    }

    $stmt_usuario = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt_usuario->execute([$correo]);
    $usuario_data = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

    if ($usuario_data && isset($usuario_data['id'])) {
        $usuario_id = $usuario_data['id'];

        
        $stmt_verificacion = $pdo->prepare("SELECT codigo_verificacion FROM verificacion_usuarios WHERE usuario_id = ? AND codigo_verificacion = ? AND codigo_usado = 0");
        $stmt_verificacion->execute([$usuario_id, $codigo]);
        $verificacion = $stmt_verificacion->fetch(PDO::FETCH_ASSOC);

        if ($verificacion) {
           
            $hash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);

          
            $update_usuario = $pdo->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
            $update_usuario->execute([$hash, $usuario_id]);

           
            $update_verificacion = $pdo->prepare("UPDATE verificacion_usuarios SET codigo_usado = 1 WHERE usuario_id = ? AND codigo_verificacion = ?");
            $update_verificacion->execute([$usuario_id, $codigo]);

            
            unset($_SESSION['correo_recuperar']);
            echo "Contraseña actualizada correctamente. <a href='login.php'>Iniciar sesión</a>";
        } else {
            header("Location: reset.php?error=Código incorrecto.");
            exit;
        }
    } else {
        header("Location: reset.php?error=Correo electrónico no encontrado.");
        exit;
    }
}
?>