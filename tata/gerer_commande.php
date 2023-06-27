<?php
session_start();
include_once 'connexion_BDD.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$pdo = connexion_bdd();
$stmt = $pdo->prepare("SELECT prenom_vendeur FROM vendeur WHERE Id_vendeur = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();
$prenom = $user['prenom_vendeur'];

$query = "SELECT commande.id_commande, commande.numero_commande, commande.date_commande, commande.montant_total, 
                 client.prenom_client, client.nom_client, client.adresse_client, client.code_postal_client, client.localite_client, 
                 produit.id_produit, produit.nom_produit
          FROM commande 
          JOIN client ON commande.id_client = client.id_client
          JOIN commande_produit ON commande.id_commande = commande_produit.id_commande
          JOIN produit ON commande_produit.id_produit = produit.id_produit
          WHERE produit.etat_vente = 'vendu'";

$stmt = $pdo->prepare($query);
$stmt->execute();
$commandes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer Commandes</title>
</head>
<body>
<h1>Bonjour, <?php echo $prenom; ?>, voici les commandes à expédier :</h1>
<table>
    <thead>
    <tr>
        <th>Numéro de Commande</th>
        <th>Date de Commande</th>
        <th>Montant Total</th>
        <th>Client</th>
        <th>Adresse de Livraison</th>
        <th>Produit</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($commandes as $commande): ?>
        <tr>
            <td><?php echo $commande['numero_commande']; ?></td>
            <td><?php echo $commande['date_commande']; ?></td>
            <td><?php echo $commande['montant_total']; ?></td>
            <td><?php echo $commande['prenom_client'].' '.$commande['nom_client']; ?></td>
            <td><?php echo $commande['adresse_client'].' '.$commande['code_postal_client'].' '.$commande['localite_client']; ?></td>
            <td><?php echo $commande['nom_produit']; ?></td>
            <td><a href="expedier_commande.php?id_commande=<?php echo $commande['id_commande']; ?>&id_produit=<?php echo $commande['id_produit']; ?>">Marquer comme en livraison</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<ul>
    <li><a href="gerer_articles.php">Gérer les articles</a></li>
    <li><a href="gerer_clients.php">Gérer les clients</a></li>
    <li><a href="ajouter_admin.php">Gérer les Admin</a></li>
    <li><a href="gerer_commande.php">Gérer les commandes</a></li>
    <li><a href="ajouter_marque_etat.php">Gérer les ajouts</a></li>
    <li><a href="gerer_ventes.php">Gérer les ventes</a></li>
    <li><a href="gerer_carte_fidelite.php">Gérer les cartes de fidélité</a></li>
    <li><a href="deconnexion.php">Déconnexion</a></li>
</ul>
</body>
<?php
include_once('footer.php');
?>
</html>
