<?php
include_once 'header.php';
include_once 'panier.php';

// Récupérer les critères de recherche
if (isset($_GET['taille'])) {
    $taille = $_GET['taille'];
} else {
    $taille = null;
}

if (isset($_GET['marque'])) {
    $marque = $_GET['marque'];
} else {
    $marque = null;
}

if (isset($_GET['etat'])) {
    $etat = $_GET['etat'];
} else {
    $etat = null;
}

// Construire la requête SQL en fonction des critères de recherche
$sql = "SELECT produit.*, marque.nom_marque, taille.libelle AS libelle_taille, etat.libelle_etat AS libelle_etat 
        FROM produit 
        LEFT JOIN marque ON produit.id_marque = marque.id_marque 
        LEFT JOIN taille ON produit.id_taille = taille.id_taille 
        LEFT JOIN etat ON produit.id_etat = etat.id_etat 
        WHERE 1=1";

if ($taille) {
    $sql .= " AND produit.id_taille = $taille";
}

if ($marque) {
    $sql .= " AND produit.id_marque = $marque";
}

if ($etat) {
    $sql .= " AND produit.id_etat = $etat";
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
        <select name="taille" id="taille">
            <option value="">Toutes les tailles</option>
            <?php
            $tailleStmt = $pdo->query("SELECT * FROM taille");
            while ($row = $tailleStmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($taille == $row['id_taille']) ? 'selected' : '';
                echo '<option value="' . $row['id_taille'] . '" ' . $selected . '>' . $row['libelle'] . '</option>';
            }
            ?>
        </select>

        <label for="marque">Marque :</label>
        <select name="marque" id="marque">
            <option value="">Toutes les marques</option>
            <?php
            $marqueStmt = $pdo->query("SELECT * FROM marque");
            while ($row = $marqueStmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($marque == $row['id_marque']) ? 'selected' : '';
                echo '<option value="' . $row['id_marque'] . '" ' . $selected . '>' . $row['nom_marque'] . '</option>';
            }
            ?>
        </select>

        <label for="etat">État :</label>
        <select name="etat" id="etat">
            <option value="">Tous les états</option>
            <?php
            $etatStmt = $pdo->query("SELECT * FROM etat");
            while ($row = $etatStmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($etat == $row['id_etat']) ? 'selected' : '';
                echo '<option value="' . $row['id_etat'] . '" ' . $selected . '>' . $row['libelle_etat'] . '</option>';
            }
            ?>
        </select>

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
