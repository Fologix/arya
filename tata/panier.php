<?php
session_start();
include_once 'connexion_BDD.php';
include_once 'header.php';

$pdo = connexion_bdd();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

// Dans panier.php
$panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : [];
if (!empty($panier)) {
    $ids = implode(',', array_map('intval', array_keys($panier)));
    $sql = "SELECT * FROM produit WHERE id_produit IN ($ids)";
    $stmt = $pdo->query($sql);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $produits = [];
}

// Traitement du formulaire de suppression
if (isset($_POST['retirer_panier'])) {
    $id = $_POST['id_produit'];

    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }

    header("location: panier.php");
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier</title>
</head>
<body>
<div class="container">
    <h1>Votre panier</h1>

    <?php if (empty($produits)) : ?>
        <p>Votre panier est vide.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($produits as $produit) : ?>
                <li>
                    <h3><?php echo $produit['nom_produit']; ?></h3>
                    <p>Taille : <?php echo $produit['libelle_taille']; ?></p>
                    <p>Prix : <?php echo number_format($produit['prix_vente_htva'], 2); ?> €</p>
                    <form method="post">
                        <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                        <input type="submit" name="retirer_panier" value="Retirer du panier">
                        <input type="hidden" name="action" value="retirer">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="checkout.php">Passer au paiement</a>
    <?php endif; ?>
</div>
</body>
</html>

<?php
include_once('footer.php');
?>
