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

// Récupérer les informations du produit à modifier
$pdo = connexion_bdd();
$stmt = $pdo->prepare("SELECT * FROM produit WHERE id_produit = ?");
$stmt->execute([$_GET['id']]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

// Traiter le formulaire de modification de produit et mettre à jour l'image si nécessaire
if (isset($_POST['modifier_produit'])) {
    $nom_produit = $_POST['nom_produit'];
    $prix_vente_htva = $_POST['prix_vente_htva'];
    $taux_tva = $_POST['taux_tva'];
    $prix_vente_tvac = $prix_vente_htva * (1 + $taux_tva / 100);
    $code_barre = $_POST['code_barre'];
    $marque_produit = $_POST['marque_produit'];
    $taille_produit = $_POST['taille_produit'];
    $sexe_produit = $_POST['sexe_produit'];

    // Mettre à jour l'image si un nouveau fichier a été uploadé
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "img/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $classe_image = $target;
    } else {
        $classe_image = $produit['classe_image'];
    }

    $stmt = $pdo->prepare("UPDATE produit SET nom_produit = ?, prix_vente_htva = ?, prix_vente_tvac = ?, taux_tva = ?, code_barre = ?, classe_image = ?, marque_produit = ?, taille_produit = ?, sexe_produit = ? WHERE id_produit = ?");
    $stmt->execute([$nom_produit, $prix_vente_htva, $prix_vente_tvac, $taux_tva, $code_barre, $classe_image, $marque_produit, $taille_produit, $sexe_produit, $_GET['id']]);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un produit</title>
</head>
<body>
<h1>Modifier un produit</h1>
<form action="modifier_produit.php?id=<?php echo $_GET['id']; ?>" method="post" enctype="multipart/form-data">
    <label for="nom_produit">Nom du produit:</label>
    <input type="text" name="nom_produit" id="nom_produit" value="<?php echo $produit['nom_produit']; ?>" required>
    <br>
    <label for="prix_vente_htva">Prix HTVA:</label>
    <input type="number" step="0.01" name="prix_vente_htva" id="prix_vente_htva" value="<?php echo $produit['prix_vente_htva']; ?>" required>
    <br>
    <label for="taux_tva">Taux TVA:</label>
    <input type="number" step="0.01" name="taux_tva" id="taux_tva" value="<?php echo $produit['taux_tva']; ?>" required>
    <br>
    <label for="code_barre">Code barre:</label>
    <input type="text" name="code_barre" id="code_barre" value="<?php echo $produit['code_barre']; ?>" required>
    <br>
    <label for="image">Image:</label>
    <input type="file" name="image" id="image">
    <br>
    <?php if (!empty($produit['classe_image'])) : ?>
        <img src="<?php echo $produit['classe_image']; ?>" width="100">
    <?php endif; ?>
    <br>
    <label for="marque_produit">Marque:</label>
    <input type="text" name="marque_produit" id="marque_produit" value="<?php echo $produit['marque_produit']; ?>" required>
    <br>
    <label for="taille_produit">Taille:</label>
    <input type="text" name="taille_produit" id="taille_produit" value="<?php echo $produit['taille_produit']; ?>" required>
    <br>
    <label for="sexe_produit">Sexe:</label>
    <select name="sexe_produit" id="sexe_produit" required>
        <option value="homme" <?php if ($produit['sexe_produit'] == 'homme') echo 'selected'; ?>>Homme</option>
        <option value="femme" <?php if ($produit['sexe_produit'] == 'femme') echo 'selected'; ?>>Femme</option>
    </select>
    <br>
    <input type="submit" name="modifier_produit" value="Modifier le produit">

</form>
<p><a href="gerer_articles.php">Retour à la liste des produits</a></p>
</body>
</html>
