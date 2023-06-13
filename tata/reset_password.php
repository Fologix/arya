<?php
include_once 'connexion_BDD.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("SELECT * FROM client WHERE reset_token = :token AND reset_token_expiration > NOW()");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if ($user) {
        if (isset($_POST['password'], $_POST['password_confirm'])) {
            if ($_POST['password'] == $_POST['password_confirm']) {
                // Utilisez password_hash() pour sécuriser le mot de passe avant de le stocker
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE client SET password = :password, reset_token = NULL, reset_token_expiration = NULL WHERE mail = :email");
                $stmt->execute(['password' => $hashed_password, 'email' => $user['mail']]);
                $success = "Votre mot de passe a été mis à jour. Vous pouvez maintenant vous connecter.";
            } else {
                $error = "Les mots de passe ne correspondent pas.";
            }
        }
    } else {
        $error = "Le lien de réinitialisation du mot de passe est invalide ou a expiré.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>
<h1>Réinitialisation du mot de passe</h1>
<?php if (isset($error)) { echo "<p>$error</p>"; } ?>
<?php if (isset($success)) { echo "<p>$success</p>"; } ?>
<form method="post">
    <label for="password">Nouveau mot de passe :</label>
    <input type="password" name="password" id="password" required>
    <br>
    <label for="password_confirm">Confirmer le nouveau mot de passe :</label>
    <input type="password" name="password_confirm" id="password_confirm" required>
    <br>
    <input type="submit" value="Mettre à jour le mot de passe">
</form>
<p><a href="connexion.php">Retour à la connexion</a></p>
</body>
</html>

