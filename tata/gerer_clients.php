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

// Traitement de la recherche
$searchKeyword = '';
if (isset($_GET['search'])) {
    $searchKeyword = $_GET['search'];
    $clients_stmt = $pdo->prepare("SELECT * FROM client WHERE prenom_client LIKE :keyword OR nom_client LIKE :keyword");
    $clients_stmt->execute(['keyword' => "%$searchKeyword%"]);
} else {
    $clients_stmt = $pdo->prepare("SELECT * FROM client");
    $clients_stmt->execute();
}
$clients = $clients_stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les clients</title>
</head>
<body>
<h1>Bienvenue, <?php echo $prenom; ?></h1>
<h2>Clients</h2>
<form method="GET" action="gerer_clients.php">
    <input type="text" name="search" placeholder="Rechercher un client" value="<?php echo $searchKeyword; ?>">
    <button type="submit">Rechercher</button>
</form>
<br>
<table>
    <thead>
    <tr>
        <th>Id</th>
        <th>Prénom</th>
        <th>Nom</th>
        <th>Adresse</th>
        <th>Code postal</th>
        <th>Localité</th>
        <th>Email</th>
        <!-- Add headers for any other information you want to display -->
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($clients as $client): ?>
        <tr>
            <td><?php echo $client['id_client']; ?></td>
            <td><?php echo $client['prenom_client']; ?></td>
            <td><?php echo $client['nom_client']; ?></td>
            <td><?php echo $client['adresse_client']; ?></td>
            <td><?php echo $client['code_postal_client']; ?></td>
            <td><?php echo $client['localite_client']; ?></td>
            <td><?php echo $client['mail']; ?></td>
            <!-- Add cells for any other information you want to display -->
            <td>
                <a href="modifier_client.php?id=<?php echo $client['id_client']; ?>">Modifier</a> |
                <a href="supprimer_client.php?id=<?php echo $client['id_client']; ?>">Supprimer</a> |
                <a href="gerer_client.php?id=<?php echo $client['id_client']; ?>">Gérer</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<a href="panel_admin.php">Retour au panel admin</a>
</body>
</html>
