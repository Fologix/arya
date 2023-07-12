<?php
session_start();

// Vérifiez si l'ID du point relais a été passé en paramètre
if (!isset($_SESSION['relay_point_id'])) {
    // Redirigez vers une autre page si l'ID du point relais n'est pas défini
    header('Location: choice_delivery.php');
    exit();
}

require_once('includes/MondialRelay.API.Class.php');

//We declare the client
$MRService = new MondialRelayWebAPI();

//set the credentials
$MRService->_Api_CustomerCode  = "BDTEST  ";
$MRService->_Api_BrandId      = "11";
$MRService->_Api_SecretKey    = "*****";
$MRService->_Api_User         = "BDTEST@business-api.mondialrelay.com";
$MRService->_Api_Password     = "****";
$MRService->_Api_Version      = "2.0";

$MRService->_Debug = false;

//set the merchant adress
//sender adress
$merchantAdress = new Adress();
$merchantAdress->Adress1 = "My book shop";
$merchantAdress->Adress2 = "";
$merchantAdress->Adress3 = "10 rue des écoles";
$merchantAdress->Adress4 = "";
$merchantAdress->PostCode = "59000";
$merchantAdress->City = "Lille";
$merchantAdress->CountryCode = "FR";
$merchantAdress->PhoneNumber = "+33300000000" ;
$merchantAdress->PhoneNumber2 ="";
$merchantAdress->Email = "hello@mybookshop.com";
$merchantAdress->Language = "FR";

//-------------------------------------------------
//Shipment Creation Sample
//-------------------------------------------------
//Create a new shipment object
$myShipment = new ShipmentData();

//set the delivery options
$myShipment->DeliveryMode = new ShipmentInfo()  ;
$myShipment->DeliveryMode->Mode = "LDP";
//parcel Shop ID when required
$myShipment->DeliveryMode->ParcelShopId = $_SESSION['relay_point_id'];
$myShipment->DeliveryMode->ParcelShopContryCode = "FR";

// ...

//Create the shipment
//this will return the stickers URL and Shipment number to track the parcel

//creation with Internationnal API
$ShipmentDatas = $MRService->CreateShipment($myShipment);

print_r($ShipmentDatas);
echo '<a href="'.$ShipmentDatas->LabelLink.'" >Download Stickers</a>';
?>
