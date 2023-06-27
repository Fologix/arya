<?php
session_start();
include_once 'connexion_BDD.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

if (!isset($_GET['id_commande']) || !isset($_GET['id_produit'])) {
    header("Location: gerer_commandes.php");
    exit;
}

$pdo = connexion_bdd();
$query = "UPDATE produit SET etat_vente = 'en_livraison' WHERE id_produit = :id_produit";
$stmt = $pdo->prepare($query);
$stmt->execute(['id_produit' => $_GET['id_produit']]);

header("Location: gerer_commandes.php");
?>
