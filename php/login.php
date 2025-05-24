<?php

if (isset($_GET['verificado']) && $_GET['verificado'] == 1) {
    $success_message = "<p style='color: green;'>Tu cuenta ha sido verificada con éxito. Ya puedes iniciar sesión.</p>";

    echo "<p style='color: green; text-align: center;'>Tu cuenta ha sido verificada con éxito. Ya puedes iniciar sesión.</p>";
}

session_start();
require 'db.php';

$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    if (empty($correo) || empty($contrasena)) {
        $error_message = "Por favor, ingresa tu correo y contraseña.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            if ($usuario['verificado'] == 0) {
                $_SESSION['correo_no_verificado'] = $correo;
                $error_message = "Tu cuenta aún no ha sido verificada. <a href=/resend_code.php'>Reenviar código de verificación</a>";
            } elseif (password_verify($contrasena, $usuario['contrasena'])) {
            
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_correo'] = $usuario['correo'];
                header('Location: ../pages/index.html');
                exit();
            } else {
                $error_message = "Contraseña incorrecta.";
            }
        } else {
            $error_message = "No existe una cuenta con ese correo.";
        }
    }
}
?>

<link rel="stylesheet" href="../assets/css/login.css">

<div class="login-container">
  <form class="form login-form" method="POST" action="login.php">
    <h2>Iniciar sesión</h2>

    <?php
    if (!empty($error_message)) {
        echo '<p class="error-message">' . $error_message . '</p>';
    }
    ?>

    <input type="email" name="correo" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($correo ?? ''); ?>" required> <input type="password" name="contrasena" placeholder="Contraseña" required>
    <button type="submit">Iniciar sesión</button>
    <a href="recovery.php" class="recovery-link">¿Olvidaste tu contraseña?</a>
    <a href="register.php" class="register-link">¿No tienes cuenta? Regístrate aquí</a>
  </form>
</div>