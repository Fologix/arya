<?php
include_once 'connexion_BDD.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    if (!empty($email)) {
        $pdo = connexion_bdd();
        $stmt = $pdo->prepare("SELECT * FROM client WHERE mail = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Par exemple, vous pouvez générer un token unique, le stocker dans la base de données avec une date d'expiration, et envoyer un lien de réinitialisation par e-mail.
            $message = "Un e-mail contenant les instructions pour réinitialiser votre mot de passe a été envoyé.";
        } else {
            $error = "Aucun compte associé à cette adresse e-mail.";
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
</head>
<body>
<h1>Mot de passe oublié</h1>
<?php if (isset($message)) { echo "<p>$message</p>"; } ?>
<?php if (isset($error)) { echo "<p>$error</p>"; } ?>
<form method="post">
    <label for="email">Email :</label>
    <input type="email" name="email" id="email" required>
    <br>
    <input type="submit" name="submit" value="Envoyer">
</form>
<p><a href="connexion.php">Retour à la connexion</a></p>
</body>
</html>
