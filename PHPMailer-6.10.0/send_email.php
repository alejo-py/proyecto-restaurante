<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP de Mailtrap
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Username = '490e5c24899cfd';  // ← cambia esto
    $mail->Password = '696f403bd99dfb'; // ← cambia esto
    $mail->Port = 2525;

    // Remitente y destinatario
    $mail->setFrom('prueba@masterfood.com', 'Master of Food');
    $mail->addAddress('destinatario@ejemplo.com', 'Usuario');

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Correo de prueba desde XAMPP';
    $mail->Body    = '¡Este es un <b>correo de prueba</b> enviado desde XAMPP usando PHPMailer y Mailtrap!';

    $mail->send();
    echo 'Correo enviado correctamente.';
} catch (Exception $e) {
    echo "Error al enviar el correo: {$mail->ErrorInfo}";
}
