<?php
include_once 'header.php';
include_once 'connexion_BDD.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$pdo = connexion_bdd();
$stmt = $pdo->prepare("SELECT * FROM client WHERE id_client = :id_client");
$stmt->execute(['id_client' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if (isset($_POST['submit'])) {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($prenom) && !empty($nom) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            if (check_password_strength($password)) {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE client SET prenom_client = :prenom_client, nom_client = :nom_client, mail = :mail, password = :password WHERE id_client = :id_client";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'prenom_client' => $prenom,
                    'nom_client' => $nom,
                    'mail' => $email,
                    'password' => $password,
                    'id_client' => $_SESSION['user_id']
                ]);
                $message = "Informations mises à jour avec succès!";
            } else {
                $error = "Le mot de passe doit contenir au moins une majuscule et un caractère spécial.";
            }
        } else {
            $error = "Les mots de passe ne correspondent pas.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

function check_password_strength($password) {
    $lowercase = preg_match('@[a-z]@', $password);
    $uppercase = preg_match('@[A-Z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $special_chars = preg_match('@[^\w]@', $password);
    return ($lowercase && $uppercase && $number && $special_chars);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion et sécurité</title>
</head>
<body>
<h1>Modifier mes informations</h1>
<?php if (isset($error)) { echo "<p>$error</p>"; } ?>
<?php if (isset($message)) { echo "<p>$message</p>"; } ?>
<form method="POST" action="">
    <input type="text" name="prenom" placeholder="Prénom" value="<?php echo htmlspecialchars($user['prenom_client']); ?>" required><br>
    <input type="text" name="nom" placeholder="Nom" value="<?php echo htmlspecialchars($user['nom_client']); ?>" required><br>
    <input type="email" name="email" placeholder="Adresse e-mail" value="<?php echo htmlspecialchars($user['mail']); ?>" required><br>
    <input type="password" name="password" placeholder="Mot de passe" required><br>
    <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required><br>
    <input type="submit" name="submit" value="Mettre à jour">
</form>
</body>
</html>
<?php
include_once('footer.php');
?>
