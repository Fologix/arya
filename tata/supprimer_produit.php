<?php
session_start();
include_once 'connexion_BDD.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Vérifier si l'ID du produit à supprimer a été fourni en paramètre d'URL
if (!isset($_GET['id'])) {
    header('Location: gerer_articles.php');
    exit;
}

// Supprimer le produit de la base de données
$pdo = connexion_bdd();

// Récupérer le chemin de l'image avant de supprimer le produit
$stmt = $pdo->prepare("SELECT classe_image FROM produit WHERE id_produit = ?");
$stmt->execute([$_GET['id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$imagePath = $row['classe_image'];

// Supprimer le produit
$stmt = $pdo->prepare("DELETE FROM produit WHERE id_produit = ?");
$stmt->execute([$_GET['id']]);

// Vérifier si le fichier existe avant de le supprimer
if (file_exists($imagePath)) {
    unlink($imagePath);
}

// Rediriger vers la page de gestion des articles
header('Location: gerer_articles.php');
exit;
?>
