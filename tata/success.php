<?php
session_start();
require 'vendor/autoload.php';
require 'connexion_BDD.php';
require 'send_confirmation_email.php';  // Ajoutez cette ligne ici pour inclure votre fichier d'envoi de mail.

\Stripe\Stripe::setApiKey('sk_test_51N2dVtEAVA2mzTaKMmxxO22mdUBwOrTfLv4R7gZpjBm4b3XVlyItU6a6K2G6YDQRwHZQx87E9XMV7hYPtE1p2P9d00E5j1FgRT');

try {
    $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);
    $payment_intent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

    if ($payment_intent->status == 'succeeded') {
        // Le paiement a réussi.
        $pdo = connexion_bdd();

        // Récupérer les informations de l'utilisateur et de l'adresse de livraison depuis la base de données
        $stmt = $pdo->prepare("SELECT * FROM client WHERE id_client = :id_client");
        $stmt->execute(['id_client' => $_SESSION['user_id']]);
        $user = $stmt->fetch();

        // Enregistrer les informations de la commande dans la base de données
        $stmt = $pdo->prepare("
    INSERT INTO commande (numero_commande, date_commande, montant_total, id_client, adresse_livraison, complement_adresse, localite_client, code_postal_client, methode_paiement, type_livraison) 
    VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)
");
        $stmt->execute([
            $payment_intent->id, // Utilisez l'ID d'intention de paiement comme numéro de commande
            $payment_intent->amount_received / 100, // Le montant est renvoyé en centimes, le convertir en euros
            $_SESSION['user_id'], // L'ID du client
            $user['adresse_client'], // L'adresse de livraison
            $user['complement_adresse'], // Le complément d'adresse
            $user['localite_client'], // La localité
            $user['code_postal_client'], // Le code postal
            $payment_intent->payment_method_types[0], // Le type de méthode de paiement (par exemple, 'card')
            $_SESSION['type_livraison'] // Le type de livraison
        ]);


        $id_commande = $pdo->lastInsertId(); // Récupérez l'ID de la commande que vous venez d'insérer

        // Enregistrez chaque produit de la commande dans la table commande_produit
        foreach ($_SESSION['panier'] as $id_produit => $produit) {
            $stmt = $pdo->prepare("
                INSERT INTO commande_produit (id_commande, id_produit) 
                VALUES (?, ?)
            ");
            $stmt->execute([$id_commande, $id_produit]);

            // Mettez à jour l'etat_vente du produit dans la table `produit`.
            $stmt = $pdo->prepare("UPDATE produit SET etat_vente = ? WHERE id_produit = ?");
            $stmt->execute(['vendu', $id_produit]);
        }

        $_SESSION['panier'] = []; // Videz le panier.

        // Après avoir vidé le panier, envoyez le mail de confirmation
        //send_confirmation_email($user['mail'], $payment_intent->id, $payment_intent->amount_received / 100);

        echo "<h1>Merci pour votre achat!</h1>";
        echo "<p>Votre paiement a été reçu avec succès. Votre panier a été vidé.</p>";
        echo "<a href='index.php'>retour au site</a>";
    } else {
        // Le paiement a échoué pour une raison quelconque.
        echo "<h1>Une erreur s'est produite</h1>";
        echo "<p>Nous n'avons pas pu traiter votre paiement. Veuillez réessayer.</p>";
    }
} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
