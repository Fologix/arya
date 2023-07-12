<?php
session_start();
include_once 'connexion_BDD.php';
include_once 'header.php';
include_once 'includes/MondialRelay.API.Class.php';

$pdo = connexion_bdd();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    header("Location: panier.php");
    exit;
}

$MRService = new MondialRelayWebAPI();

$MRService->_Api_CustomerCode  = "BDTEST  ";
$MRService->_Api_BrandId       = "11";
$MRService->_Api_SecretKey     = "MondiaL_RelaY_44";
$MRService->_Api_User          = "BDTEST@business-api.mondialrelay.com";
$MRService->_Api_Password      = "]dx1SP9aSrMs)faK]jXa";
$MRService->_Api_Version       = "2.0";

$MRService->_Debug = false;

$relay_points_details = [];

if (isset($_POST['search'])) {
    // Check if input is a valid postal code
    if(preg_match("/^[0-9]{5}$/", $_POST['search'])) {
        $myParcelShopSearchResults = $MRService->SearchParcelShop("FR", $_POST['search'], '');

        foreach ($myParcelShopSearchResults as $relay_point) {
            $details = $MRService->GetParcelShopDetails($relay_point->CountryCode,$relay_point->ParcelShopId);
            $relay_points_details[] = $details;
        }

        $_SESSION['relay_points'] = $relay_points_details;
    } else {
        echo "Veuillez entrer un code postal valide";
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
    <h1>Retrait en magasin</h1>
    <form method="POST" action="payment_choice.php">
        <input type="hidden" name="type_livraison" value="en magasin"/>
        <input type="submit" value="Choisir ce mode de livraison"/>
    </form>
</div>

<div class="container">
    <h1>Livraison à domicile</h1>
    <form method="POST" action="payment_choice.php">
        <input type="hidden" name="type_livraison" value="à domicile"/>
        <input type="submit" value="Choisir ce mode de livraison"/>
    </form>
</div>

<div class="container">
    <h1>Choisissez un point relais</h1>

    <form method="POST">
        <input type="text" name="search" placeholder="Recherchez un point relais par code postal" pattern="[0-9]{5}" required title="Veuillez entrer un code postal à 5 chiffres"/>
        <input type="submit" value="Rechercher"/>
    </form>

    <?php if (isset($_SESSION['relay_points']) && !empty($_SESSION['relay_points'])) : ?>
        <ul>
            <?php foreach ($_SESSION['relay_points'] as $details) : ?>
                <li>
                    <h3><?php echo $details->Name; ?></h3>
                    <p>Adresse : <?php echo $details->Adress1; ?></p>
                    <p>Code postal : <?php echo $details->PostCode; ?></p>
                    <p>Ville : <?php echo $details->City; ?></p>
                    <a href="confirmation.php?relay_point_id=<?php echo $details->ParcelShopId; ?>">Choisir ce point relais</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</body>
</html>

<?php
include_once('footer.php');
?>

