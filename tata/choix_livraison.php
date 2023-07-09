<?php
session_start();
include_once 'connexion_BDD.php';
include_once 'header.php';

$pdo = connexion_bdd();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choix de Livraison</title>
</head>
<body>
<div class="container">
    <h1>Choisissez votre mode de livraison</h1>

    <div>
        <label for="magasin">Retrait en Magasin</label>
    </div>

    <div>
        <label for="domicile">Livraison à domicile</label>
    </div>

    <div>
        <label for="pointRelais">Livraison en Point Relais</label>
    </div>



</div>
</body>
</html>

<?php
include_once('footer.php');
?>
