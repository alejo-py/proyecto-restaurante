<?php
$mensaje = '';
if (isset($_GET['enviado'])) {
    if ($_GET['enviado'] === 'ok') {
        $mensaje = 'Hemos enviado un código de recuperación a tu correo.';
    } elseif ($_GET['enviado'] === 'error') {
        $mensaje = 'No pudimos enviar el código. Intenta nuevamente.';
    }

}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="../assets/css/recovery.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>
<body>
    <div class="recovery-container">
        <form class="recovery-form" action="send_recovery_code.php" method="POST">
            <h2>Recuperar tu contraseña</h2>
            <p style="color: #ccc;">Ingresa tu correo y te enviaremos un código</p>
            
            <?php if ($mensaje !== ''): ?>
                <div class="error-message"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <input type="email" name="correo" id="correo" placeholder="Correo electrónico" required>
            <button type="submit">Enviar código</button>
            <a href="login.php">Volver al inicio de sesión</a>
        </form>
    </div>
</body>
</html>
