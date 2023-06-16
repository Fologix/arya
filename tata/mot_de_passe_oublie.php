<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

session_start();
include_once 'connexion_BDD.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    if (!empty($email)) {
        $pdo = connexion_bdd();
        $stmt = $pdo->prepare("SELECT * FROM client WHERE mail = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(50)); // génère un token unique
            $expiration = date('Y-m-d H:i:s', time() + 100000); // le token expirera dans 1 heure
            $stmt = $pdo->prepare("UPDATE client SET reset_token = :token, reset_token_expiration = :expiration WHERE mail = :email");
            $stmt->execute(['token' => $token, 'expiration' => $expiration, 'email' => $email]);

            // Envoie un e-mail avec le lien de réinitialisation
            $url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";

            $mail = new PHPMailer(true);
            try {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->isSMTP();
                $mail->Host       = 'ssl0.ovh.net'; // Remplacer par votre serveur SMTP OVH
                $mail->SMTPAuth   = true;
                $mail->Username   = 'contact@yassineverriez.com';
                $mail->Password   = 'Cupra3474.';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->setFrom('arya@gmail.com', 'Mail de modification de mot passe');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                $mail->Body    = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe : $url";
                $mail->AltBody = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe : $url";
                $mail->send();
                $success = "Vérifiez votre e-mail pour un lien de réinitialisation du mot de passe.";
            } catch (Exception $e) {
                $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        } else {
            $error = "Aucun compte trouvé avec cet e-mail.";
        }
    } else {
        $error = "Veuillez entrer votre adresse e-mail.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="css/connexion.css">
</head>
<body>
<h1>Réinitialisation du mot de passe</h1>
<?php if (isset($error)) { echo "<p>$error</p>"; } ?>
<?php if (isset($success)) { echo "<p>$success</p>"; } ?>
<form method="post">
    <label for="email">Email :</label>
    <input type="email" name="email" id="email" required>
    <br>
    <input type="submit" name="submit" value="Réinitialiser le mot de passe">
</form>
<p><a href="connexion.php">Retour à la connexion</a></p>
</body>
</html>
