<?php
session_start();
include_once 'connexion_BDD.php';
include_once 'header.php';

$pdo = connexion_bdd();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

// Récupération de l'adresse existante de l'utilisateur
$query = $pdo->prepare("SELECT adresse_client, code_postal_client, localite_client, complement_adresse FROM client WHERE id_client = :id");
$query->execute(['id' => $_SESSION['user_id']]);
$user = $query->fetch();

$error = null;
if (isset($_POST['submit'])) {
    $adresse = $_POST['adresse'];
    $code_postal = $_POST['code_postal'];
    $localite = $_POST['localite'];
    $complement = $_POST['complement'] ? $_POST['complement'] : null; // Complément d'adresse facultatif

    if (empty($adresse) || empty($code_postal) || empty($localite)) {
        $error = "Tous les champs sont obligatoires sauf le complément d'adresse.";
    } else {
        $sql = "UPDATE client SET adresse_client = :adresse, code_postal_client = :code_postal, localite_client = :localite, complement_adresse = :complement WHERE id_client = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['adresse' => $adresse, 'code_postal' => $code_postal, 'localite' => $localite, 'complement' => $complement, 'id' => $_SESSION['user_id']]);
        header("Location: payment_choice.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation de l'adresse</title>
</head>
<body>
<div class="container">
    <h1>Vérification de l'adresse</h1>
    <form method="post">
        <label for="adresse">Adresse :</label><br>
        <input type="text" id="adresse" name="adresse" value="<?= $user['adresse_client'] ?>"><br>
        <label for="code_postal">Code Postal :</label><br>
        <input type="text" id="code_postal" name="code_postal" value="<?= $user['code_postal_client'] ?>"><br>
        <label for="localite">Localité :</label><br>
        <input type="text" id="localite" name="localite" value="<?= $user['localite_client'] ?>"><br>
        <label for="complement">Complément d'adresse (facultatif) :</label><br> <!-- Champ facultatif pour le complément d'adresse -->
        <input type="text" id="complement" name="complement" value="<?= $user['complement_adresse'] ?>"><br>
        <input type="submit" name="submit" value="Valider">
    </form>
    <?php if ($error) : ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>

</div>
</body>
</html>

<?php
include_once('footer.php');
?>
