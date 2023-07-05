<?php
session_start();
include_once 'connexion_BDD.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Ajouter la fonction de compression d'image
function compressImage($source, $destination, $quality) {
    $info = getimagesize($source);
    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source);

    // Enregistrer l'image compressée
    imagejpeg($image, $destination, $quality);
}

// Traiter le formulaire d'ajout de produit et stocker l'image
if (isset($_POST['ajouter_produit'])) {
    $nom_produit = $_POST['nom_produit'];
    $prix_vente_htva = $_POST['prix_vente_htva'];
    $marque_produit = $_POST['marque_produit'];
    $taille_produit = $_POST['taille_produit'];
    $sexe_produit = $_POST['sexe_produit'];
    $id_etat = $_POST['id_etat'];
    $categorie_produit = $_POST['categorie_produit'];

    $image = $_FILES['image']['name'];

    // Obtenir l'extension du fichier
    $ext = pathinfo($image, PATHINFO_EXTENSION);

    // Créer un nouveau nom de fichier unique
    $newName = uniqid() . '.' . $ext;

    $target = "img/" . $newName;

    // Compresser l'image avant de la déplacer
    compressImage($_FILES['image']['tmp_name'], $target, 60);

    $pdo = connexion_bdd();
    $stmt = $pdo->prepare("INSERT INTO produit (nom_produit, prix_vente_htva, classe_image, id_marque, id_taille, sexe_produit, id_etat, id_categorie) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom_produit, $prix_vente_htva, $target, $marque_produit, $taille_produit, $sexe_produit, $id_etat, $categorie_produit]);
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
    <label for="image">Image:</label>
    <input type="file" name="image" id="image" required>
    <br>
    <label for="marque_produit">Marque:</label>
    <select name="marque_produit" id="marque_produit" required>
        <?php
        $pdo = connexion_bdd();
        $marqueStmt = $pdo->query("SELECT * FROM marque");
        while ($row = $marqueStmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $row['id_marque'] . '">' . $row['nom_marque'] . '</option>';
        }
        ?>
    </select>
    <br>
    <label for="taille_produit">Taille:</label>
    <select name="taille_produit" id="taille_produit" required>
        <?php
        $tailleStmt = $pdo->query("SELECT * FROM taille");
        while ($row = $tailleStmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $row['id_taille'] . '">' . $row['libelle'] . '</option>';
        }
        ?>
    </select>
    <br>
    <label for="sexe_produit">Sexe:</label>
    <select name="sexe_produit" id="sexe_produit" required>
        <option value="homme">Homme</option>
        <option value="femme">Femme</option>
    </select>
    <br>
    <label for="id_etat">État:</label>
    <select name="id_etat" id="id_etat" required>
        <?php
        $etatStmt = $pdo->query("SELECT * FROM etat");
        while ($row = $etatStmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $row['id_etat'] . '">' . $row['libelle_etat'] . '</option>';
        }
        ?>
    </select>
    <br>
    <label for="categorie_produit">Categorie:</label>
    <select name="categorie_produit" id="categorie_produit" required>
        <?php
        $categorieStmt = $pdo->query("SELECT * FROM categorie");
        while ($row = $categorieStmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $row['id_categorie'] . '">' . $row['nom_categorie'] . '</option>';
        }
        ?>
    </select>
    <br>
    <input type="submit" name="ajouter_produit" value="Ajouter le produit">
</form>
<h1>Liste des produits disponibles</h1>
<?php
$stmt = $pdo->query("SELECT * FROM produit 
JOIN marque ON produit.id_marque = marque.id_marque 
JOIN etat ON produit.id_etat = etat.id_etat 
JOIN taille ON produit.id_taille = taille.id_taille 
LEFT JOIN categorie ON produit.id_categorie = categorie.id_categorie
WHERE etat_vente = 'disponible'");
echo '<table border="1">';
echo '<tr><th>ID</th><th>Nom</th><th>Prix HTVA</th><th>Image</th><th>Marque</th><th>Taille</th><th>Sexe</th><th>État</th><th>Categorie</th><th>Actions</th></tr>';
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
    echo '<td>' . $row['id_produit'] . '</td>';
    echo '<td>' . $row['nom_produit'] . '</td>';
    echo '<td>' . $row['prix_vente_htva'] . '</td>';
    echo '<td><img src="' . $row['classe_image'] . '" width="100"></td>';
    echo '<td>' . $row['nom_marque'] . '</td>';
    echo '<td>' . $row['libelle'] . '</td>';
    echo '<td>' . $row['sexe_produit'] . '</td>';
    echo '<td>' . $row['libelle_etat'] . '</td>';
    echo '<td>' . (isset($row['nom_categorie']) ? $row['nom_categorie'] : 'Non catégorisé') . '</td>';
    echo '<td><a href="modifier_produit.php?id=' . $row['id_produit'] . '">Modifier</a> | <a href="supprimer_produit.php?id=' . $row['id_produit'] . '">Supprimer</a></td>';
    echo '</tr>';
}
echo '</table>';
?>

<h1>Liste des autres produits vendu</h1>
<?php
$stmt = $pdo->query("SELECT * FROM produit 
JOIN marque ON produit.id_marque = marque.id_marque 
JOIN etat ON produit.id_etat = etat.id_etat 
JOIN taille ON produit.id_taille = taille.id_taille 
LEFT JOIN categorie ON produit.id_categorie = categorie.id_categorie
WHERE etat_vente <> 'disponible'");
echo '<table border="1">';
echo '<tr><th>ID</th><th>Nom</th><th>Prix HTVA</th><th>Image</th><th>Marque</th><th>Taille</th><th>Sexe</th><th>État</th><th>Categorie</th><th>Actions</th></tr>';
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo '<tr>';
    echo '<td>' . $row['id_produit'] . '</td>';
    echo '<td>' . $row['nom_produit'] . '</td>';
    echo '<td>' . $row['prix_vente_htva'] . '</td>';
    echo '<td><img src="' . $row['classe_image'] . '" width="100"></td>';
    echo '<td>' . $row['nom_marque'] . '</td>';
    echo '<td>' . $row['libelle'] . '</td>';
    echo '<td>' . $row['sexe_produit'] . '</td>';
    echo '<td>' . $row['libelle_etat'] . '</td>';
    echo '<td>' . (isset($row['nom_categorie']) ? $row['nom_categorie'] : 'Non catégorisé') . '</td>';
    echo '<td><a href="modifier_produit.php?id=' . $row['id_produit'] . '">Modifier</a> | <a href="supprimer_produit.php?id=' . $row['id_produit'] . '">Supprimer</a></td>';
    echo '</tr>';
}
echo '</table>';
?>
</body>
</html>
