<?php
session_start();
include_once 'connexion_BDD.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Traiter le formulaire d'ajout de produit et stocker l'image
if (isset($_POST['ajouter_produit'])) {
    $nom_produit = $_POST['nom_produit'];
    $prix_vente_htva = $_POST['prix_vente_htva'];
    $taux_tva = $_POST['taux_tva'];
    $prix_vente_tvac = $prix_vente_htva * (1 + $taux_tva / 100);
    $code_barre = $_POST['code_barre'];
    $marque_produit = $_POST['marque_produit'];
    $taille_produit = $_POST['taille_produit'];
    $sexe_produit = $_POST['sexe_produit'];

    $image = $_FILES['image']['name'];
    $target = "img/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("INSERT INTO produit (nom_produit, prix_vente_htva, prix_vente_tvac, taux_tva, code_barre, classe_image, marque_produit, taille_produit, sexe_produit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom_produit, $prix_vente_htva, $prix_vente_tvac, $taux_tva, $code_barre, $target, $marque_produit, $taille_produit, $sexe_produit]);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les articles | AYR</title>
</head>
<body>
<button onclick="window.location.href='panel_admin.php'">Retourner au panel admin</button>
<h1>Ajouter un produit</h1>
<form action="gerer_articles.php" method="post" enctype="multipart/form-data">
    <label for="nom_produit">Nom du produit:</label>
    <input type="text" name="nom_produit" id="nom_produit" required>
    <br>
    <label for="prix_vente_htva">Prix HTVA:</label>
    <input type="number" step="0.01" name="prix_vente_htva" id="prix_vente_htva" required>
    <br>
    <label for="taux_tva">Taux TVA:</label>
    <input type="number" step="0.01" name="taux_tva" id="taux_tva" required>
    <br>
    <label for="code_barre">Code barre:</label>
    <input type="text" name="code_barre" id="code_barre" required>
    <br>
    <label for="image">Image:</label>
    <input type="file" name="image" id="image" required>
    <br>
    <label for="marque_produit">Marque:</label>
    <input type="text" name="marque_produit" id="marque_produit" required>
    <br>
    <label for="taille_produit">Taille:</label>
    <input type="text" name="taille_produit" id="taille_produit" required>
    <br>
    <label for="sexe_produit">Sexe:</label>
    <select name="sexe_produit" id="sexe_produit" required>
        <option value="homme">Homme</option>
        <option value="femme">Femme</option>
    </select>
    <br>
    <input type="submit" name="ajouter_produit" value="Ajouter le produit">
</form>
<h1>Liste des produits</h1>
<?php
$pdo = connexion_bdd();
$stmt = $pdo->query("SELECT * FROM produit");
echo '<table border="1">';
echo '<tr><th>ID</th><th>Nom</th><th>Prix HTVA</th><th>Prix TVAC</th><th>Taux TVA</th><th>Code barre</th><th>Image</th><th>Marque</th><th>Taille</th><th>Sexe</th><th>Actions</th></tr>';
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
    echo '<td>' . $row['id_produit'] . '</td>';
    echo '<td>' . $row['nom_produit'] . '</td>';
    echo '<td>' . $row['prix_vente_htva'] . '</td>';
    echo '<td>' . $row['prix_vente_tvac'] . '</td>';
    echo '<td>' . $row['taux_tva'] . '</td>';
    echo '<td>' . $row['code_barre'] . '</td>';
    echo '<td><img src="' . $row['classe_image'] . '" width="100"></td>';
    echo '<td>' . $row['marque_produit'] . '</td>';
    echo '<td>' . $row['taille_produit'] . '</td>';
    echo '<td>' . $row['sexe_produit'] . '</td>';
    echo '<td><a href="modifier_produit.php?id=' . $row['id_produit'] . '">Modifier</a> | <a href="supprimer_produit.php?id=' . $row['id_produit'] . '">Supprimer</a></td>';
    echo '</tr>';
}
echo '</table>';
?>
</body>
</html>
