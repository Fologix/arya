<?php
session_start();
include_once 'connexion_BDD.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$pdo = connexion_bdd();

if (isset($_GET['id'])) {
    $clientId = $_GET['id'];

    // Vérifier si le client existe
    $clientStmt = $pdo->prepare("SELECT * FROM client WHERE id_client = :id");
    $clientStmt->execute(['id' => $clientId]);
    $client = $clientStmt->fetch();

    if (!$client) {
        // Rediriger ou afficher un message d'erreur approprié
        header("Location: gerer_clients.php");
        exit;
    }
} else {
    // Rediriger ou afficher un message d'erreur approprié
    header("Location: gerer_clients.php");
    exit;
}

// Traitement de la suppression du client
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Supprimer le client de la base de données
    $deleteStmt = $pdo->prepare("DELETE FROM client WHERE id_client = :id");
    $deleteStmt->execute(['id' => $clientId]);

    // Rediriger vers la page de gestion des clients avec un message de succès
    header("Location: gerer_clients.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer le client</title>
</head>
<body>
<h1>Supprimer le client</h1>
<p>Êtes-vous sûr de vouloir supprimer le client "<?php echo $client['prenom_client'] . ' ' . $client['nom_client']; ?>" ?</p>
<form method="POST" action="supprimer_client.php?id=<?php echo $clientId; ?>">
    <button type="submit">Oui, supprimer</button>
</form>

<a href="gerer_clients.php">Annuler</a>
</body>
</html>

