<?php
session_start();
include_once 'connexion_BDD.php';
$pdo = connexion_bdd();

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// Récupération des informations du client connecté
$stmt = $pdo->prepare("SELECT prenom_client, nom_client, adresse_client, localite_client, code_postal_client FROM client WHERE id_client = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

// Vérification de la soumission du formulaire de commande
if (isset($_POST['submit'])) {
    // Récupération des informations de la commande
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $code_postal = $_POST['code_postal'];
    $produit = $_POST['produit'];
    $quantite = $_POST['quantite'];

    // Vérification des données du formulaire
    $erreurs = array();
    if (empty($nom)) {
        $erreurs[] = "Le nom est obligatoire";
    }
    if (empty($adresse)) {
        $erreurs[] = "L'adresse est obligatoire";
    }
    if (empty($ville)) {
        $erreurs[] = "La ville est obligatoire";
    }
    if (empty($code_postal)) {
        $erreurs[] = "Le code postal est obligatoire";
    }
    if (empty($produit)) {
        $erreurs[] = "Le produit est obligatoire";
    }
    if (empty($quantite) || $quantite <= 0) {
        $erreurs[] = "La quantité est obligatoire et doit être supérieure à 0";
    }

    // Si aucune erreur, on enregistre la commande dans la base de données
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO commande (id_client, nom_client, adresse_client, ville_client, code_postal_client, produit, quantite) VALUES (:id, :nom, :adresse, :ville, :code_postal, :produit, :quantite)");
        $stmt->execute(['id' => $_SESSION['user_id'], 'nom' => $nom, 'adresse' => $adresse, 'ville' => $ville, 'code_postal' => $code_postal, 'produit' => $produit, 'quantite' => $quantite]);

        // Redirection vers la page de confirmation de commande
        header('Location: confirmation_commande.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Validation de commande</title>
</head>
<body>
<h1>Validation de commande</h1>

<?php if (!empty($erreurs)) : ?>
    <ul>
        <?php foreach ($erreurs as $erreur) : ?>
            <li><?php echo $erreur; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post">
    <div>
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($user['nom_client']); ?>">
    </div>
    <div>
        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($user['prenom_client']); ?>">
    </div>
    <div>
        <label for="adresse">Adresse :</label>
        <input type="text" name="adresse" id="adresse" value="<?php echo htmlspecialchars($user['adresse_client']); ?>">
    </div>
    <div>
        <label for="ville">Ville :</label>
        <input type="text" name="ville" id="ville" value="<?php echo htmlspecialchars($user['localite_client']); ?>">
    </div>
    <div>
        <label for="code_postal">Code postal :</label>
        <input type="text" name="code_postal" id="code_postal" value="<?php echo htmlspecialchars($user['code_postal_client']); ?>">
    </div>
    <!--
    <div>
        <label for="pays">Pays :</label>
        <input type="text" name="pays" id="pays" value="<?php echo htmlspecialchars($user['pays_client']); ?>">
    </div>
    -->
    <div>
        <input type="submit" name="valider_commande" value="Valider la commande">
    </div>
</form>
