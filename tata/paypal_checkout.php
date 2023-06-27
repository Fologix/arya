<?php
session_start();
require __DIR__  . '/vendor/autoload.php';

// Calculez le total du panier.
$total = 0;
foreach ($_SESSION['panier'] as $id => $produit) {
    $total += $produit['prix'];
}

// Créez un objet APIContext. Remplacez par vos identifiants de client et de client secret.
$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        'AWZOtKDKdfzLaB8vozFxFDdZEo50XXItExsyOCrAkwm07coE262ogbVn1cetyjy6KKmsb9B-cO02Xwdz',     // Remplacez par votre identifiant de client PayPal.
        'EKk0JXiSEHwXWKmQGo2_i1L-dh4bUDEFVF4bTgddc20b7v6q1TBnLA43ixzsYTpVyNv37pFn3LGnZtEB'  // Remplacez par votre client secret PayPal.
    )
);

// Créez une nouvelle transaction de paiement.
$payer = new \PayPal\Api\Payer();
$payer->setPaymentMethod('paypal');

$amount = new \PayPal\Api\Amount();
$amount->setTotal($total); // Utilisez la variable $total pour le total du panier.
$amount->setCurrency('EUR'); // Assurez-vous que la devise correspond à celle de vos produits.

$transaction = new \PayPal\Api\Transaction();
$transaction->setAmount($amount);

$redirectUrls = new \PayPal\Api\RedirectUrls();
$redirectUrls->setReturnUrl("http://localhost/arya/tata/success.php") // Remplacez par l'URL de succès.
->setCancelUrl("http://localhost/arya/tata/cancel.php"); // Remplacez par l'URL d'annulation.

$payment = new \PayPal\Api\Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions(array($transaction))
    ->setRedirectUrls($redirectUrls);

// Créez le paiement et redirigez l'utilisateur.
try {
    $payment->create($apiContext);
    header("Location: " . $payment->getApprovalLink());
    exit;
} catch (\PayPal\Exception\PayPalConnectionException $ex) {
    // Gestion des erreurs.
    echo $ex->getCode(); // Imprime le code d'erreur, si le cas échéant.
    echo $ex->getData(); // Imprime la réponse et le corps de la demande.
    die($ex);
} catch (Exception $ex) {
    die($ex);
}
