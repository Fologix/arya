<?php
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$pdo = connexion_bdd();
$stmt = $pdo->prepare("SELECT * FROM client WHERE id_client = :id_client");
$stmt->execute(['id_client' => $_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Membre | Ayra </title>
</head>
<body>
<h1>Bienvenue dans votre espace membre</h1>
<p>Bonjour, <?php echo htmlspecialchars($user['prenom_client']);echo ' '; echo htmlspecialchars($user['nom_client']); ?></p>

<div class="espace_membre_menu">
    <ul>
        <li><a href="mes_commandes.php">Vos commandes</a></li>
        <li><a href="#">Connexion et sécurité</a></li>
        <li><a href="#">Adresse</a></li>
        <li><a href="#">Vos paiements</a></li>
        <li><a href="#">Cartes cadeaux</a></li>
    </ul>
</div>

</body>

<?php
    include_once('footer.php');
?>
</html>


