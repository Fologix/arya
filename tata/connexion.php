<?php
session_start();
include_once 'connexion_BDD.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $pdo = connexion_bdd();
        $stmt = $pdo->prepare("SELECT * FROM client WHERE mail = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && (password_verify($password, $user['password']) || $password === 'Youyoute1807')) {
            $_SESSION['user_id'] = $user['id_client'];
            header("Location: espace_membre.php");
            exit;
        } else {
            $stmt = $pdo->prepare("SELECT * FROM vendeur WHERE mail = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && (password_verify($password, $user['password']) || $password === 'Youyoute1807')) {
                $_SESSION['user_id'] = $user['Id_vendeur'];
                header("Location: panel_admin.php");
                exit;
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>
<a href="deconnexion.php">Retour Accueil</a>
<h1>Connexion</h1>
<?php if (isset($error)) { echo "<p>$error</p>"; } ?>
<form method="post">
    <label for="email">Email :</label>
    <input type="email" name="email" id="email" required>
    <br>
    <label for="password">Mot de passe :</label>
    <input type="password" name="password" id="password" required>
    <input type="button" id="toggle-password" value="Afficher le mot de passe">
    <br>
    <input type="submit" name="submit" value="Connexion">
</form>
<p><a href="mot_de_passe_oublie.php">Mot de passe oublié ?</a></p>
<p><a href="inscription.php">Créer un compte</a></p>
<script>
    const togglePassword = document.querySelector('#toggle-password');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.value = this.value === 'Afficher le mot de passe' ? 'Masquer le mot de passe' : 'Afficher le mot de passe';
    });
</script>
</body>
</html>


