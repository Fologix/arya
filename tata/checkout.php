<?php
session_start();
include_once 'connexion_BDD.php';
include_once 'header.php';
require 'vendor/autoload.php';

use SumUp\SumUp;

$pdo = connexion_bdd();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : [];
$total = 0.00;
if (!empty($panier)) {
    foreach ($panier as $item) {
        $total += $item['prix'];
    }
}

// Remplacez 'YOUR_SUMUP_ACCESS_TOKEN' par votre vrai token
$accessToken = 'sup_sk_qzu72WX9NRdCkh1edk4Te4OIEL9Gxwibl';

try {
    $sumup = new SumUp('yassine.verriez@hotmail.com', 'MC4EGPVD', 'http://localhost/arya/tata/index.php');
    $sumup->authenticate('YOUR_AUTHORIZATION_CODE'); // Replace with your SumUp authorization code

    $checkoutsService = $sumup->getCheckoutService();
    $response = $checkoutsService->create($total, 'EUR', uniqid(), 'yassine.verriez@hotmail.com', 'Description du paiement');

    $checkoutId = $response->getBody()->id;

    if(isset($checkoutId)) {
        header("Location: " . $checkoutId);
        exit;
    } else {
        throw new Exception('La réponse de SumUp ne contient pas de lien de paiement.');
    }

} catch (\Exception $e) {
    echo 'Erreur lors de la création du paiement SumUp: ' . $e->getMessage();
}

include_once('footer.php');
?>
