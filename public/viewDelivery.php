<?php
//Anzeige eines bestimmten Lieferauftrags mit allen Kunden-, Liefer- und Geräteinformationen. 
//Nachrichten und Bemerkungen können von allen Benutzern hinzugefügt werden. 
//Das PDF Dokument für diese Lieferung kann von hier generiert werden.
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}
$markt = $_SESSION["markt"];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  $deliveryID = $_GET["deliveryID"];
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['Lieferauftrag'])) {
    $markt =  $_POST['markt'];
    $deliveryID =  $_POST['deliveryID'];
    require_once("./scripts/generatePDFS.php");
    getDeliveryPDF($markt, $deliveryID);
    
  } else if (isset($_POST['speichern'])) {
    $message = $_POST['message'];
    $markt = $_POST['markt'];
    $deliveryID =  $_POST['deliveryID'];
    require_once("./scripts/configureShop.php");
    saveMessageForDelivery($markt, $deliveryID, $message);
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Lieferung</title>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="/css/style.css" />
</head>

<body>

  <?php
  include("./components/header.php");

  require_once("./scripts/configureShop.php");
  $delData = getDeliveryData($markt, $deliveryID);
  ?>

  <div class="createDelivery">


    <form id="deliveryView" class="halfView" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input class="delivery" type="submit" value="als PDF" name="Lieferauftrag">  
    <input type="hidden" name="markt" value="<?php echo($markt);?>">
    <input type="hidden" name="deliveryID" value="<?php echo ($deliveryID); ?>">

    <div>
        <label>Kontaktdaten</label>
        <input readonly type="text" value="<?php echo ($delData[5]); ?>">
        <input readonly type="text" value="<?php echo ($delData[4]); ?>">
        <input readonly type="text" value="<?php echo ($delData[10]); ?>">
        <input readonly type="text" value="<?php echo ($delData[11]); ?>">

        <label>Lieferadresse</label>
        <input readonly type="text" value="<?php echo ($delData[6]); ?>">
        <input readonly type="text" value="<?php echo ($delData[7]); ?>">
        <input readonly type="text" value="<?php echo ($delData[8]); ?>">
        <input readonly type="text" value="<?php echo ($delData[9]); ?>">
      </div>

      <div>

        <label>Belegdaten</label>
        <input readonly type="text" value="<?php echo ($delData[2]); ?>">

        <label>Lieferdatum</label>
        <input readonly type="text" value="<?php echo ($delData[1]); ?>">

        <label>Gerätedaten</label>
        <?php
        $temp = explode("(--(-)--)", $delData[3]);
        $artikel = explode("---", $temp[0]);
        $geraete = explode("---", $temp[1]);
        for ($i=0; $i < sizeof($artikel); $i++) { 
          echo('<input readonly type="text" value="'.$artikel[$i].'">');
          echo('<input readonly type="text" value="'.$geraete[$i].'">');
        }
        ?>
            </div>

      <div class="messages">
        <label>Interne Memos</label>
        <textarea form="deliveryView" placeholder="Neue Nachricht hinzufügen..." name="message" cols="120" rows="10"></textarea>
        
        <span>
          <!-- <input multiple type="file" id="newImage" name="newImage" accept="image/*"> -->
          <input class="accept" type="submit" name="speichern" value="speichern">
        </span>
        


        <?php
        $messages = getMessagesForDelivery($markt, $deliveryID);
        foreach ($messages as $message) {
          $messageArraay = explode("(-(-)-)", $message);
        ?>
          <div class="messageEntry">

            <textarea readonly placeholder="<?php echo ($messageArraay[3]); ?>" cols="120" rows="10"></textarea>
            <div class="createMark">
              <p class="author"><?php echo ($messageArraay[2]); ?></p>
              <p><?php 
              $date=date_create($messageArraay[0]);
              echo date_format($date,"d.m.Y");
               ?></p>
              <p><?php echo ($messageArraay[1]); ?></p>

            </div>
          </div>



        <?php
        }
        ?>
      </div>


    </form>
  </div>


</body>

</html>