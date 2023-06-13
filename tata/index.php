<?php
include_once 'header.php';
include_once 'panier.php';

// Récupérer les critères de recherche
$taille = isset($_GET['taille']) ? $_GET['taille'] : [];
$marque = isset($_GET['marque']) ? $_GET['marque'] : [];
$etat = isset($_GET['etat']) ? $_GET['etat'] : [];

// Construire la requête SQL en fonction des critères de recherche
$sql = "SELECT produit.*, marque.nom_marque, taille.libelle AS libelle_taille, etat.libelle_etat AS libelle_etat 
        FROM produit 
        LEFT JOIN marque ON produit.id_marque = marque.id_marque 
        LEFT JOIN taille ON produit.id_taille = taille.id_taille 
        LEFT JOIN etat ON produit.id_etat = etat.id_etat 
        WHERE 1=1";

if (!empty($taille)) {
    $sql .= " AND produit.id_taille IN (" . implode(',', array_map('intval', $taille)) . ")";
}

if (!empty($marque)) {
    $sql .= " AND produit.id_marque IN (" . implode(',', array_map('intval', $marque)) . ")";
}

if (!empty($etat)) {
    $sql .= " AND produit.id_etat IN (" . implode(',', array_map('intval', $etat)) . ")";
}

// Récupérer la liste des produits
$pdo = connexion_bdd();
$stmt = $pdo->query($sql);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css"
          integrity="sha384-Hzwy5Jv0SUr41SBEtVgGb0XpY3aW4qzqgH5xZGzCYn0JT7rB3y5BxVXQnKII8bHj"
          crossorigin="anonymous">
    <title>Boutique</title>
</head>
<script>
    function afficherMasquerPanier() {
        var panier = document.getElementById("panier");
        if (panier.style.display === "none") {
            panier.style.display = "block";
        } else {
            panier.style.display = "none";
        }
    }
</script>
<body>
<div class="container">
    <h1>Boutique</h1>

    <!-- Formulaire de recherche -->
    <form method="get">
        <label for="taille">Taille :</label>
        <?php
        $tailleStmt = $pdo->query("SELECT * FROM taille");
        while ($row = $tailleStmt->fetch(PDO::FETCH_ASSOC)) {
            $checked = in_array($row['id_taille'], $taille) ? 'checked' : '';
            echo '<input type="checkbox" name="taille[]" value="' . $row['id_taille'] . '" ' . $checked . '>' . $row['libelle'] . '<br>';
        }
        ?>

        <label for="marque">Marque :</label>
        <?php
        $marqueStmt = $pdo->query("SELECT * FROM marque");
        while ($row = $marqueStmt->fetch(PDO::FETCH_ASSOC)) {
            $checked = in_array($row['id_marque'], $marque) ? 'checked' : '';
            echo '<input type="checkbox" name="marque[]" value="' . $row['id_marque'] . '" ' . $checked . '>' . $row['nom_marque'] . '<br>';
        }
        ?>

        <label for="etat">État :</label>
        <?php
        $etatStmt = $pdo->query("SELECT * FROM etat");
        while ($row = $etatStmt->fetch(PDO::FETCH_ASSOC)) {
            $checked = in_array($row['id_etat'], $etat) ? 'checked' : '';
            echo '<input type="checkbox" name="etat[]" value="' . $row['id_etat'] . '" ' . $checked . '>' . $row['libelle_etat'] . '<br>';
        }
        ?>

        <input type="submit" value="Rechercher">
    </form>

    <!-- Liste des produits -->
    <?php if (count($produits) == 0) : ?>
        <p>Aucun produit ne correspond à votre recherche.</p>
    <?php else : ?>
        <h2>Résultats de la recherche :</h2>
        <ul>
            <?php foreach ($produits as $produit) : ?>
                <li>
                    <h3><?php echo $produit['nom_produit']; ?></h3>
                    <img src="<?php echo $produit['classe_image']; ?>"
                         alt="<?php echo $produit['nom_produit']; ?>" width="250px">
                    <p>Taille : <?php echo $produit['libelle_taille']; ?></p>
                    <p>Prix : <?php echo number_format($produit['prix_vente_htva'], 2); ?> €</p>
                    <p>État : <?php echo $produit['libelle_etat']; ?></p>
                    <form method="post">
                        <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                        <input type="submit" name="ajouter_panier" value="Ajouter au panier">
                        <input type="hidden" name="action" value="ajouter">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Panier -->
    <div class="panier-container">
        <button class="panier-toggle" onclick="afficherMasquerPanier()">Afficher/Masquer le panier</button>
        <div id="panier" class="panier" style="display:none">
            <h2>Panier</h2>
            <?php afficherContenuPanier(); ?>
        </div>
    </div>
</div>
</body>
<?php
include_once('footer.php');
?>
</html>
