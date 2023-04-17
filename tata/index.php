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

// Construire la requête SQL en fonction des critères de recherche
$sql = "SELECT * FROM produit WHERE 1=1";

if ($taille) {
    $sql .= " AND taille_produit = '$taille'";
}

if ($marque) {
    $sql .= " AND marque_produit = '$marque'";
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
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" integrity="sha384-Hzwy5Jv0SUr41SBEtVgGb0XpY3aW4qzqgH5xZGzCYn0JT7rB3y5BxVXQnKII8bHj" crossorigin="anonymous">
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
            <option value="S" <?php if ($taille == 'S') echo 'selected'; ?>>S</option>
            <option value="M" <?php if ($taille == 'M') echo 'selected'; ?>>M</option>
            <option value="L" <?php if ($taille == 'L') echo 'selected'; ?>>L</option>
            <option value="XL" <?php if ($taille == 'XL') echo 'selected'; ?>>XL</option>
        </select>

        <label for="marque">Marque :</label>
        <select name="marque" id="marque">
            <option value="">Toutes les marques</option>
            <option value="Nike" <?php if ($marque == 'Nike') echo 'selected'; ?>>Nike</option>
            <option value="Adidas" <?php if ($marque == 'Adidas') echo 'selected'; ?>>Adidas</option>
            <option value="Puma" <?php if ($marque == 'Puma') echo 'selected'; ?>>Puma</option>
            <option value="Reebok" <?php if ($marque == 'Reebok') echo 'selected'; ?>>Reebok</option>
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
                <img src="<?php echo $produit['classe_image']; ?>" alt="<?php echo $produit['nom_produit']; ?>" width="250px">
                <p>Marque : <?php echo $produit['marque_produit']; ?></p>
                <p>Taille : <?php echo $produit['taille_produit']; ?></p>
                <p>Prix : <?php echo number_format($produit['prix_vente_tvac'], 2); ?> € TTC</p>
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
            <h2>Panier ok</h2>
            <?php afficherContenuPanier(); ?>
        </div>
    </div>
</div>
</body>
    <?php
    include_once('footer.php');
    ?>
</html>
