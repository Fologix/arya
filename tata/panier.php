<?php
include_once 'header.php';

if (isset($_POST['ajouter_panier'])) {
    // Récupérer les informations du produit
    $id_produit = $_POST['id_produit'] ?? null;
    $taille_produit = null; // La taille du produit est unique, donc on ne la récupère pas
    $quantite_produit = 1; // On ajoute toujours un seul produit au panier
    $action = $_POST['action'] ?? null;


    // Vérifier si le produit est déjà dans le panier
    $index = -1;
    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $key => $value) {
            if ($value['id_produit'] == $id_produit && $value['taille_produit'] == $taille_produit) {
                $index = $key;
                break;
            }
        }
    }

    // Ajouter le produit au panier ou mettre à jour la quantité
    if ($index == -1) {
        $produit = array(
            'id_produit' => $id_produit,
            'taille_produit' => $taille_produit,
            'quantite_produit' => $quantite_produit
        );
        $_SESSION['panier'][] = $produit;
    } else {
        $_SESSION['panier'][$index]['quantite_produit'] += $quantite_produit;
    }
}

if (isset($_POST['supprimer_produit'])) {
    // Supprimer le produit du panier
    $id_produit = $_POST['id_produit'];
    $taille_produit = null; // La taille du produit est unique, donc on ne la récupère pas

    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $key => $value) {
            if ($value['id_produit'] == $id_produit && $value['taille_produit'] == $taille_produit) {
                unset($_SESSION['panier'][$key]);
                break;
            }
        }
    }
}

if (isset($_POST['vider_panier'])) {
    // Vider le panier
    unset($_SESSION['panier']);
}

// Fonction pour afficher le contenu du panier
function afficherContenuPanier()
{
    if (isset($_SESSION['panier']) && count($_SESSION['panier']) > 0) {
        $ids_produits = array();
        foreach ($_SESSION['panier'] as $produit) {
            $ids_produits[] = $produit['id_produit'];
        }
        $liste_ids_produits = implode(',', $ids_produits);

        $pdo = connexion_bdd();

        // Rechercher les produits correspondant aux IDs dans le panier
        $sql = "SELECT * FROM produit WHERE id_produit IN ($liste_ids_produits)";
        $stmt = $pdo->query($sql);
        $produits_panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo '<table>';
        echo '<tr>';
        echo '<th>Nom du produit</th>';
        echo '<th>Taille</th>';
        echo '<th>Quantité</th>';
        echo '<th>Prix unitaire</th>';
        echo '<th>Prix total</th>';
        echo '</tr>';

        $total = 0;

        foreach ($_SESSION['panier'] as $produit) {
            // Rechercher le produit correspondant
            $pdo = connexion_bdd();
            $sql = "SELECT * FROM produit WHERE id_produit = :id_produit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id_produit', $produit['id_produit']);
            $stmt->execute();
            $produit_db = $stmt->fetch(PDO::FETCH_ASSOC);

            // Calculer le prix total
            $prix_total = $produit['quantite_produit'] * $produit_db['prix_vente_tvac'];

            // Afficher le produit dans le panier
            echo '<tr>';
            echo '<td>' . $produit_db['nom_produit'] . '</td>';
            echo '<td>' . $produit_db['taille_produit'] . '</td>';
            echo '<td>' . $produit['quantite_produit'] . '</td>';
            echo '<td>' . number_format($produit_db['prix_vente_tvac'], 2) . ' € TTC</td>';
            echo '<td>' . number_format($prix_total, 2) . ' € TTC</td>';
            echo '<td>';
            echo '<form method="post">';
            echo '<input type="hidden" name="id_produit" value="' . $produit_db['id_produit'] . '">';
            echo '<input type="hidden" name="taille_produit" value="' . $produit_db['taille_produit'] . '">';
            echo '<input type="hidden" name="action" value="supprimer">';
            echo '<input type="submit" name="supprimer_produit" value="Supprimer">';
            echo '</form>';
            echo '</td>';
            echo '</tr>';

            // Ajouter le prix total au total
            $total += $prix_total;
        }

        echo '</table>';

        // Afficher le total
        echo '<p>Total : ' . number_format($total, 2) . ' € TTC</p>';

        // Bouton pour vider le panier
        echo '<form method="post">';
        echo '<input type="hidden" name="action" value="vider">';
        echo '<input type="submit" value="Vider le panier">';
        echo '</form>';

        // Bouton pour passer à la validation de la commande
        if (isset($_SESSION['user_id']) && isset($_SESSION['panier'])) {
            $prix_total_panier = calculerPrixTotalPanier();
            echo '<div>';
            echo '<h2>Validation de la commande</h2>';
            echo '<p>Prix total du panier : ' . number_format($prix_total_panier, 2) . ' € TTC</p>';
            echo '<form method="post" action="validation_commande.php">';
            echo '<input type="submit" value="Passer à la validation de la commande">';
            echo '</form>';
            echo '</div>';
        }

        echo '</div>';



    }
}
/**
 * Fonction pour calculer le prix total du panier.
 * @return float Prix total du panier.
 */
function calculerPrixTotalPanier() {
    $prix_total = 0;
    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $produit) {
            $id_produit = $produit['id_produit'];
            $quantite_produit = $produit['quantite_produit'];

            $pdo = connexion_bdd();
            $stmt = $pdo->prepare('SELECT prix_vente_tvac FROM produit WHERE id_produit = :id_produit');
            $stmt->bindValue(':id_produit', $id_produit);
            $stmt->execute();
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            $prix_unitaire = $resultat['prix_vente_tvac'];

            $prix_total += $prix_unitaire * $quantite_produit;
        }
    }
    return $prix_total;
}

?>


