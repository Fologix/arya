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
$stmt = $pdo->prepare("SELECT * FROM produit JOIN marque ON produit.id_marque = marque.id_marque JOIN etat ON produit.id_etat = etat.id_etat JOIN taille ON produit.id_taille = taille.id_taille WHERE id_produit = ?");
$stmt->execute([$_GET['id']]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si le produit existe
if (!$produit) {
    echo "Le produit spécifié n'existe pas.";
    exit;
}

// Traiter le formulaire de modification de produit et mettre à jour l'image si nécessaire
if (isset($_POST['modifier_produit'])) {
    $nom_produit = $_POST['nom_produit'];
    $prix_vente_htva = $_POST['prix_vente_htva'];
    $taille_produit = $_POST['taille_produit'];
    $sexe_produit = $_POST['sexe_produit'];
    $id_etat = $_POST['id_etat'];

    // Mettre à jour l'image si un nouveau fichier a été uploadé
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "img/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $classe_image = $target;
    } else {
        $classe_image = $produit['classe_image'];
    }

    $stmt = $pdo->prepare("UPDATE produit SET nom_produit = ?, prix_vente_htva = ?, classe_image = ?, taille_produit = ?, sexe_produit = ?, id_etat = ? WHERE id_produit = ?");
    $stmt->execute([$nom_produit, $prix_vente_htva, $classe_image, $taille_produit, $sexe_produit, $id_etat, $_GET['id']]);

    // Rediriger vers la page de gestion des articles
    header('Location: gerer_articles.php');
    exit;
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
    <label for="image">Image:</label>
    <input type="file" name="image" id="image">
    <br>
    <?php if (!empty($produit['classe_image'])) : ?>
        <img src="<?php echo $produit['classe_image']; ?>" width="100">
    <?php endif; ?>
    <br>
    <label for="taille_produit">Taille:</label>
    <select name="taille_produit" id="taille_produit" required>
        <?php
        $tailleStmt = $pdo->query("SELECT * FROM taille");
        while ($row = $tailleStmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = ($produit['id_taille'] == $row['id_taille']) ? 'selected' : '';
            echo '<option value="' . $row['id_taille'] . '" ' . $selected . '>' . $row['libelle'] . '</option>';
        }
        ?>
    </select>
    <br>
    <label for="sexe_produit">Sexe:</label>
    <input type="text" name="sexe_produit" id="sexe_produit" value="<?php echo $produit['sexe_produit']; ?>" required>
    <br>
    <label for="id_etat">État:</label>
    <select name="id_etat" id="id_etat" required>
        <?php
        $etatStmt = $pdo->query("SELECT * FROM etat");
        while ($row = $etatStmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = ($produit['id_etat'] == $row['id_etat']) ? 'selected' : '';
            echo '<option value="' . $row['id_etat'] . '" ' . $selected . '>' . $row['libelle_etat'] . '</option>';
        }
        ?>
    </select>
    <br>
    <input type="submit" name="modifier_produit" value="Modifier le produit">
</form>
<p><a href="gerer_articles.php">Retour à la liste des produits</a></p>
</body>
</html>
