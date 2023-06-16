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

// Traitement des données de mise à jour du client (si le formulaire est soumis)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $codePostal = $_POST['code_postal'];
    $localite = $_POST['localite'];
    $email = $_POST['email'];

    // Valider et mettre à jour les informations du client dans la base de données
    $updateStmt = $pdo->prepare("UPDATE client SET prenom_client = :prenom, nom_client = :nom, adresse_client = :adresse, code_postal_client = :codePostal, localite_client = :localite, mail = :email WHERE id_client = :id");
    $updateStmt->execute([
        'prenom' => $prenom,
        'nom' => $nom,
        'adresse' => $adresse,
        'codePostal' => $codePostal,
        'localite' => $localite,
        'email' => $email,
        'id' => $clientId
    ]);

    // Rediriger vers la page de gestion des clients avec un message de succès
    header("Location: gerer_clients.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le client</title>
</head>
<body>
<h1>Modifier le client</h1>

<form method="POST" action="modifier_client.php?id=<?php echo $clientId; ?>">
    <label for="prenom">Prénom:</label>
    <input type="text" name="prenom" id="prenom" value="<?php echo $client['prenom_client']; ?>"><br>

    <label for="nom">Nom:</label>
    <input type="text" name="nom" id="nom" value="<?php echo $client['nom_client']; ?>"><br>

    <label for="adresse">Adresse:</label>
    <input type="text" name="adresse" id="adresse" value="<?php echo $client['adresse_client']; ?>"><br>

    <label for="code_postal">Code postal:</label>
    <input type="text" name="code_postal" id="code_postal" value="<?php echo $client['code_postal_client']; ?>"><br>

    <label for="localite">Localité:</label>
    <input type="text" name="localite" id="localite" value="<?php echo $client['localite_client']; ?>"><br>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?php echo $client['mail']; ?>"><br>

    <button type="submit">Enregistrer</button>
</form>

<a href="gerer_clients.php">Retour à la liste des clients</a>
</body>
</html>

