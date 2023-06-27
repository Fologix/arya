<?php
session_start();
include_once 'header.php';

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$taille = isset($_GET['taille']) ? $_GET['taille'] : [];
$marque = isset($_GET['marque']) ? $_GET['marque'] : [];
$etat = isset($_GET['etat']) ? $_GET['etat'] : [];

// Traitement du formulaire de suppression
if (isset($_POST['retirer_panier'])) {
    $id = $_POST['id_produit'];

    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }

    // Redirection vers la page actuelle pour rafraîchir les données du panier affichées
    header("location: index.php");
    exit();
}

$sql = "SELECT produit.*, marque.nom_marque, taille.libelle AS libelle_taille, etat.libelle_etat AS libelle_etat 
        FROM produit 
        LEFT JOIN marque ON produit.id_marque = marque.id_marque 
        LEFT JOIN taille ON produit.id_taille = taille.id_taille 
        LEFT JOIN etat ON produit.id_etat = etat.id_etat 
        WHERE produit.etat_vente = 'disponible'";


if (!empty($taille)) {
    $sql .= " AND produit.id_taille IN (" . implode(',', array_map('intval', $taille)) . ")";
}

if (!empty($marque)) {
    $sql .= " AND produit.id_marque IN (" . implode(',', array_map('intval', $marque)) . ")";
}

if (!empty($etat)) {
    $sql .= " AND produit.id_etat IN (" . implode(',', array_map('intval', $etat)) . ")";
}

$pdo = connexion_bdd();
$stmt = $pdo->query($sql);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['ajouter_panier'])) {
    $id = $_POST['id_produit'];
    $index = array_search($id, array_column($produits, 'id_produit'));

    if ($index !== false) {
        $produit = $produits[$index];

        $_SESSION['panier'][$id] = [
            'id' => $id,
            'image' => $produit['classe_image'],
            'titre' => $produit['nom_produit'],
            'taille' => $produit['libelle_taille'],
            'prix' => $produit['prix_vente_htva']
        ];
    } else {
        echo ('Pas de produit avec cet ID trouvé');
    }
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css"
          integrity="sha384-Hzwy5Jv0SUr41SBEtVgGb0XpY3aW4qzqgH5xZGzCYn0JT7rB3y5BxVXQnKII8bHj"
          crossorigin="anonymous">
    <link rel="stylesheet" href="css/index.css">
    <title>Boutique</title>
</head>
<script>
    document.getElementById("boutiqueBtn").addEventListener("click", function() {
        var boutique = document.getElementById("boutique");
        if (boutique.style.display === "none") {
            boutique.style.display = "block";
        } else {
            boutique.style.display = "none";
        }
    });
</script>

<body>
<div class="container">
    <h1>Boutique</h1>

    <div class="panier-resume">
        <h2>Panier</h2>
        <p>Nombre d'articles : <?php echo count($_SESSION['panier']); ?></p>

        <?php foreach ($_SESSION['panier'] as $id => $produit): ?>
            <div class="produit-resume">
                <img src="<?php echo $produit['image']; ?>" alt="<?php echo $produit['titre']; ?>" width="50">
                <h3><?php echo $produit['titre']; ?></h3>
                <p>Prix : <?php echo number_format($produit['prix'], 2); ?> €</p>
                <form method="post">
                    <input type="hidden" name="id_produit" value="<?php echo $id; ?>">
                    <input type="submit" name="retirer_panier" value="Retirer du panier">
                    <input type="hidden" name="action" value="retirer">
                </form>
            </div>
        <?php endforeach; ?>
        <a href="panier.php">Voir le panier</a>
    </div>

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
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</body>
<?php
include_once('footer.php');
?>
</html>
