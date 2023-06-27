<?php
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$pdo = connexion_bdd();
$stmt = $pdo->prepare("SELECT * FROM commande WHERE id_client = :id_client");
$stmt->execute(['id_client' => $_SESSION['user_id']]);
$commandes = $stmt->fetchAll();

// Requête pour récupérer les détails des produits pour chaque commande
$productDetailsStmt = $pdo->prepare("
    SELECT p.nom_produit, p.prix_vente_htva, p.classe_image 
    FROM produit p
    JOIN commande_produit cp ON p.id_produit = cp.id_produit
    WHERE cp.id_commande = :id_commande
");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Membre | Ayra </title>
</head>
<body>
<h1>Vos commandes</h1>
<table>
    <thead>
    <tr>
        <th>Numéro de commande</th>
        <th>Date de commande</th>
        <th>Montant total</th>
        <th>Adresse de livraison</th>
        <th>Détails des produits</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($commandes as $commande): ?>
        <tr>
            <td><?php echo htmlspecialchars($commande['numero_commande']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($commande['date_commande'])); ?></td>
            <td><?php echo htmlspecialchars($commande['montant_total']); ?></td>
            <td><?php echo htmlspecialchars($commande['adresse_livraison']); ?></td>
            <td>
                <?php
                $productDetailsStmt->execute(['id_commande' => $commande['id_commande']]);
                $productDetails = $productDetailsStmt->fetchAll();

                foreach ($productDetails as $product) {
                    echo "<img src='" . htmlspecialchars($product['classe_image']) . "' alt='image produit' width='250px' />";
                    echo "<br>";
                    echo "Nom du produit: " . htmlspecialchars($product['nom_produit']);
                    echo "<br>";
                    echo "Prix du produit: " . htmlspecialchars($product['prix_vente_htva']);
                    echo "<hr>";
                }
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>

<?php
include_once('footer.php');
?>
</html>
