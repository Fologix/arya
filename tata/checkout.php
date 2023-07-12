<?php
session_start();
require 'vendor/autoload.php'; // Inclut la bibliothèque Stripe PHP. Assurez-vous de l'avoir installée via Composer.

\Stripe\Stripe::setApiKey('sk_test_51N2dVtEAVA2mzTaKMmxxO22mdUBwOrTfLv4R7gZpjBm4b3XVlyItU6a6K2G6YDQRwHZQx87E9XMV7hYPtE1p2P9d00E5j1FgRT'); // Remplacez par votre clé secrète Stripe.

include_once 'connexion_BDD.php';

// Stocker le mode de livraison dans la session
if (isset($_POST['type_livraison'])) {
    $_SESSION['type_livraison'] = $_POST['type_livraison'];
} else if (isset($_GET['type_livraison'])) {
    $_SESSION['type_livraison'] = $_GET['type_livraison'];
}


$pdo = connexion_bdd();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM client WHERE id_client = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (empty($user['adresse_client']) || empty($user['code_postal_client']) || empty($user['localite_client'])) {
    header("Location: validation_adresse.php");
    exit;
}

// Vérification de la méthode de paiement choisie
if (!isset($_POST['payment'])) {
    header("Location: payment_choice.php");
    exit;
}

$payment_method = $_POST['payment'];

// Calculez le total du panier.
$total = 0;
foreach ($_SESSION['panier'] as $id => $produit) {
    $total += $produit['prix'];
}

// Convertissez le total en centimes, car Stripe travaille avec les plus petites unités monétaires.
$totalCentimes = $total * 100;

if ($payment_method == 'credit_card') {
    try {
        // Créez une nouvelle Session Stripe.
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Votre panier',
                    ],
                    'unit_amount' => $totalCentimes,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost/arya/tata/success.php?session_id={CHECKOUT_SESSION_ID}', // Remplacez par l'URL de succès.
            'cancel_url' => 'http://localhost/arya/tata/cancel.php', // Remplacez par l'URL d'annulation.
        ]);

        // Redirigez le client vers la page de paiement Stripe.
        header("Location: " . $session->url);
    } catch (Exception $e) {
        echo 'Erreur: ' . $e->getMessage();
    }
} elseif ($payment_method == 'paypal') {
    header("Location: paypal_checkout.php");
    exit;
}
