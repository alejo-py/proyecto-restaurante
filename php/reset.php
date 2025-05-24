<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="../assets/css/recovery.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>
<body>
    <div class="recovery-container">
        <form class="recovery-form" action="process_reset.php" method="POST">
            <h2>Restablece tu contraseña</h2>
            <p style="color: #ccc;">Ingresa el código que te enviamos y tu nueva contraseña</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['mensaje'])): ?>
                <div class="error-message" style="background-color: #006400; color: white;">
                    <?= htmlspecialchars($_GET['mensaje']) ?>
                </div>
            <?php endif; ?>

            <input type="text" name="codigo" placeholder="Código de verificación">
            <input type="password" name="contrasena" placeholder="Nueva contraseña">
            <input type="password" name="confirmar_contrasena" placeholder="Confirmar contraseña">

            <button type="submit">Cambiar contraseña</button>
            <button type="submit" name="reenviar_codigo">Reenviar Código</button>
            <a href="login.php">Volver al inicio de sesión</a>
        </form>
    </div>
</body>
</html>
