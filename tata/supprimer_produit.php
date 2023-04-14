<?php
session_start();
include_once 'connexion_BDD.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Vérifier si l'ID du produit a été fourni en paramètre d'URL
if (!isset($_GET['id'])) {
    header('Location: gerer_articles.php');
    exit;
}

// Supprimer le produit de la base de données
$pdo = connexion_bdd();
$stmt = $pdo->prepare("DELETE FROM produit WHERE id_produit = ?");
$stmt->execute([$_GET['id']]);

// Rediriger vers la page de gestion des articles
header('Location: gerer_articles.php');
exit;
?>

