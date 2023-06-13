<?php
session_start();
include_once 'connexion_BDD.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Traiter le formulaire d'ajout de marque, d'état et de taille
if (isset($_POST['ajouter_marque'])) {
    $nom_marque = $_POST['nom_marque'];

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("INSERT INTO marque (nom_marque) VALUES (?)");
    $stmt->execute([$nom_marque]);
}

if (isset($_POST['ajouter_etat'])) {
    $libelle_etat = $_POST['libelle_etat'];

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("INSERT INTO etat (libelle) VALUES (?)");
    $stmt->execute([$libelle_etat]);
}

if (isset($_POST['ajouter_taille'])) {
    $nom_taille = $_POST['nom_taille'];

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("INSERT INTO taille (libelle) VALUES (?)");
    $stmt->execute([$nom_taille]);
}

// Traitement de la suppression de marque
if (isset($_GET['supprimer_marque'])) {
    $id_marque = $_GET['supprimer_marque'];

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("DELETE FROM marque WHERE id_marque = ?");
    $stmt->execute([$id_marque]);
}

// Traitement de la suppression d'état
if (isset($_GET['supprimer_etat'])) {
    $id_etat = $_GET['supprimer_etat'];

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("DELETE FROM etat WHERE id_etat = ?");
    $stmt->execute([$id_etat]);
}

// Traitement de la suppression de taille
if (isset($_GET['supprimer_taille'])) {
    $id_taille = $_GET['supprimer_taille'];

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("DELETE FROM taille WHERE id_taille = ?");
    $stmt->execute([$id_taille]);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter des marques, états et tailles</title>
</head>
<body>
<button onclick="window.location.href='panel_admin.php'">Retourner au panel admin</button>
<h1>Ajouter une marque</h1>
<form action="" method="post">
    <label for="nom_marque">Nom de la marque:</label>
    <input type="text" name="nom_marque" id="nom_marque" required>
    <input type="submit" name="ajouter_marque" value="Ajouter la marque">
</form>

<h1>Ajouter un état</h1>
<form action="" method="post">
    <label for="libelle_etat">Libellé de l'état:</label>
    <input type="text" name="libelle_etat" id="libelle_etat" required>
    <input type="submit" name="ajouter_etat" value="Ajouter l'état">
</form>

<h1>Ajouter une taille</h1>
<form action="" method="post">
    <label for="nom_taille">Nom de la taille:</label>
    <input type="text" name="nom_taille" id="nom_taille" required>
    <input type="submit" name="ajouter_taille" value="Ajouter la taille">
</form>

<h1>Liste des marques</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Nom de la marque</th>
        <th>Action</th>
    </tr>
    <?php
    $pdo = connexion_bdd();
    $stmt = $pdo->query("SELECT * FROM marque");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . $row['id_marque'] . '</td>';
        echo '<td>' . $row['nom_marque'] . '</td>';
        echo '<td><a href="?supprimer_marque=' . $row['id_marque'] . '">Supprimer</a></td>';
        echo '</tr>';
    }
    ?>
</table>

<h1>Liste des états</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Libellé de l'état</th>
        <th>Action</th>
    </tr>
    <?php
    $stmt = $pdo->query("SELECT * FROM etat");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . $row['id_etat'] . '</td>';
        echo '<td>' . $row['libelle'] . '</td>';
        echo '<td><a href="?supprimer_etat=' . $row['id_etat'] . '">Supprimer</a></td>';
        echo '</tr>';
    }
    ?>
</table>

<h1>Liste des tailles</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Nom de la taille</th>
        <th>Action</th>
    </tr>
    <?php
    $stmt = $pdo->query("SELECT id_taille, libelle FROM taille");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        echo '<td>' . $row['id_taille'] . '</td>';
        echo '<td>' . $row['libelle'] . '</td>';
        echo '<td><a href="?supprimer_taille=' . $row['id_taille'] . '">Supprimer</a></td>';
        echo '</tr>';
    }
    ?>
</table>

</body>
</html>
