<?php
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$pdo = connexion_bdd();

// Variables pour afficher les messages
$message = '';
$isSuccess = false;

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Récupérer les données du formulaire
        $adresse = $_POST['adresse'];
        $complement_adresse = $_POST['complement_adresse'];
        $code_postal = $_POST['code_postal'];
        $localite = $_POST['localite'];

        // Mettre à jour l'adresse de l'utilisateur dans la base de données
        $stmt = $pdo->prepare("UPDATE client SET adresse_client = :adresse, complement_adresse = :complement, code_postal_client = :code_postal, localite_client = :localite WHERE id_client = :id_client");
        $stmt->execute([
            'adresse' => $adresse,
            'complement' => $complement_adresse,
            'code_postal' => $code_postal,
            'localite' => $localite,
            'id_client' => $_SESSION['user_id']
        ]);

        // Afficher un message de confirmation
        $message = "Adresse enregistrée avec succès.";
        $isSuccess = true;
    } else if (isset($_POST['delete'])) {
        // Supprimer l'adresse de l'utilisateur de la base de données
        $stmt = $pdo->prepare("UPDATE client SET adresse_client = NULL, complement_adresse = NULL, code_postal_client = NULL, localite_client = NULL WHERE id_client = :id_client");
        $stmt->execute(['id_client' => $_SESSION['user_id']]);

        // Afficher un message de confirmation
        $message = "Adresse supprimée avec succès.";
        $isSuccess = true;
    }
}

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM client WHERE id_client = :id_client");
$stmt->execute(['id_client' => $_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Membre | Ayra </title>
</head>
<body>
<h1>Votre adresse</h1>

<?php if ($isSuccess): ?>
    <div class="success-message"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="adresse.php">
    <label for="adresse">Adresse :</label>
    <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($user['adresse_client']); ?>" required><br>

    <label for="complement_adresse">Complément d'adresse :</label>
    <input type="text" id="complement_adresse" name="complement_adresse" value="<?php echo htmlspecialchars($user['complement_adresse']); ?>"><br>

    <label for="code_postal">Code postal :</label>
    <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($user['code_postal_client']); ?>" required><br>

    <label for="localite">Localité :</label>
    <input type="text" id="localite" name="localite" value="<?php echo htmlspecialchars($user['localite_client']); ?>" required><br>

    <input type="submit" name="update" value="Modifier">
    <input type="submit" name="delete" value="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre adresse ?');">
</form>

</body>

<?php
include_once('footer.php');
?>
</html>
