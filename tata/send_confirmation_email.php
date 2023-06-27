<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_confirmation_email($userEmail, $adminEmail, $commandeDetails) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = 'ssl0.ovh.net';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'contact@yassineverriez.com';
        $mail->Password   = 'Cupra3474.';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('contact@yassineverriez.com', 'Confirmation de commande');
        $mail->addAddress($userEmail);
        $mail->addAddress($adminEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de votre commande';
        $mail->Body    = $commandeDetails;
        $mail->AltBody = $commandeDetails;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
