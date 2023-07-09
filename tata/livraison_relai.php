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

// Dans validation_adresse.php
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    header("Location: panier.php");
    exit;
}

// On récupère l'adresse de l'utilisateur dans la base de données
$sql = "SELECT adresse_client, code_postal_client, localite_client, complement_adresse FROM client WHERE id_client = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    //We declare the client
    $MRService = new MondialRelayWebAPI();

    //set the credentials
    $MRService->_Api_CustomerCode  = "BDTEST  ";
    $MRService->_Api_BrandId       = "11";
    $MRService->_Api_SecretKey     = "MondiaL_RelaY_44";
    $MRService->_Api_User          = "BDTEST@business-api.mondialrelay.com";
    $MRService->_Api_Password      = "]dx1SP9aSrMs)faK]jXa";
    $MRService->_Api_Version       = "2.0";

    $MRService->_Debug = false;

    // Basic Search for parcel Shops around the postal code
    $myParcelShopSearchResults = $MRService->SearchParcelShop("FR", $user['code_postal_client'], $user['localite_client']);

    $_SESSION['relay_points'] = $myParcelShopSearchResults;
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
    <h1>Choisissez un point relais</h1>

    <h3>Votre adresse : <?php echo htmlspecialchars($user['adresse_client']); ?></h3>
    <?php if (!empty($user['complement_adresse'])) : ?>
        <h3>Complément d'adresse : <?php echo htmlspecialchars($user['complement_adresse']); ?></h3>
    <?php endif; ?>
    <h3>Votre ville : <?php echo htmlspecialchars($user['localite_client']); ?></h3>
    <h3>Votre code postal : <?php echo htmlspecialchars($user['code_postal_client']); ?></h3>

    <?php if (isset($_SESSION['relay_points']) && !empty($_SESSION['relay_points'])) : ?>
        <ul>
            <?php foreach ($_SESSION['relay_points'] as $relay_point) : ?>
                <?php
                $details = $MRService->GetParcelShopDetails($relay_point->CountryCode,$relay_point->ParcelShopId);
                ?>
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
