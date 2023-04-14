<?php
session_start();
include_once 'connexion_BDD.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$pdo = connexion_bdd();
$stmt = $pdo->prepare("SELECT prenom_vendeur FROM vendeur WHERE Id_vendeur = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();
$prenom = $user['prenom_vendeur'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panel admin</title>
</head>
<body>
<h1>Bienvenue, <?php echo $prenom; ?></h1>
<ul>
    <li><a href="gerer_articles.php">Gérer les articles</a></li>
    <li><a href="gerer_clients.php">Gérer les clients</a></li>
    <li><a href="gerer_commandes.php">Gérer les commandes</a></li>
    <li><a href="gerer_stock.php">Gérer le stock</a></li>
    <li><a href="gerer_ventes.php">Gérer les ventes</a></li>
    <li><a href="gerer_clients.php">Gérer les clients</a></li>
    <li><a href="gerer_carte_fidelite.php">Gérer les cartes de fidélité</a></li>
    <li><a href="deconnexion.php">Déconnexion</a></li>
</ul>
</body>
</html>
