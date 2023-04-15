<?php
include_once 'connexion_BDD.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("SELECT * FROM password_reset WHERE token = :token AND expires_at > NOW()");
    $stmt->execute(['token' => $token]);
    $reset_request = $stmt->fetch();

    if ($reset_request) {
        if (isset($_POST['submit'])) {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if (!empty($password) && !empty($confirm_password) && ($password === $confirm_password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE client SET password = :password WHERE id = :id");
                $stmt->execute(['password' => $hashed_password, 'id' => $reset_request['client_id']]);
                $stmt = $pdo->prepare("DELETE FROM password_reset WHERE token = :token");
                $stmt->execute(['token' => $token]);
                $message = "Votre mot de passe a été réinitialisé.";
            } else {
                $error = "Les mots de passe ne correspondent pas.";
            }
        }
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Réinitialisation du mot de passe</title>
        </head>
        <body>
        <h1>Réinitialisation du mot de passe</h1>
        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
        <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
        <form method="post">
            <label for="password">Nouveau mot de passe :</label>
            <input type="password" name="password" id="password" required>
            <br>
            <label for="confirm_password">Confirmez le nouveau mot de passe :</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
            <br>
            <input type="submit" name="submit" value="Réinitialiser le mot de passe">
        </form>
        <p><a href="connexion.php">Retour à la connexion</a></p>
        </body>
        </html>
        <?php
    } else {
        // Si le token est invalide ou expiré
        $error = "Votre demande de réinitialisation de mot de passe est invalide ou a expiré.";
        echo "<p>$error</p>";
    }
} else {
    // Si le token n'est pas présent dans l'URL
    header("Location: mot_de_passe_oublie.php");
    exit();
}
?>
