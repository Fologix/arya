<?php
session_start();
include_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

// Stocker le mode de livraison dans la session
if (isset($_POST['type_livraison'])) {
    $_SESSION['type_livraison'] = $_POST['type_livraison'];
} else if (isset($_GET['type_livraison'])) {
    $_SESSION['type_livraison'] = $_GET['type_livraison'];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choix du mode de paiement</title>
</head>
<body>
<div class="container">
    <h1>Choisissez votre méthode de paiement</h1>
    <form method="post" action="checkout.php">
        <input type="radio" id="credit_card" name="payment" value="credit_card" required>
        <label for="credit_card">Carte de Crédit</label><br>
        <input type="radio" id="paypal" name="payment" value="paypal">
        <label for="paypal">PayPal</label><br>
        <input type="submit" name="submit" value="Continuer">
    </form>
</div>
</body>
</html>

<?php
include_once('footer.php');
?>

