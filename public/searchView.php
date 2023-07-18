<!-- Seite für Ausgabe der Suchergebnisse
Ähnlich deliveryPlan, alle gefundenen Liferungen werdecn in Tabellenform angezeigt.
Mit Click auf die jeweilige Zeile wird der Lieferauftrag in viewDelivery angezeigt. -->

<?php
    session_start();

    // Check if the user is logged in, if not then redirect him to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
      header("location: login.php");
      exit;
    }
    $searchInput = $_POST["searchInput"];
    $markt = $_SESSION["markt"];
    require_once("./scripts/configureShop.php");
    $deliverys = searchWithKeyword($markt, $searchInput)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Suche</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="/css/style.css" />
</head>
<body>
    <?php 
      include("./components/header.php");
    ?>
<div class="deliveryPlanOverview">

<table>
  <thead>
    <th>Auftragsnummer</th>
    <th>Belegnummer</th>
    <th>Artikel</th>
    <th>Kunde</th>
    <th>Lieferadresse</th>
  </thead>
  <tbody>
    <?php
    foreach ($deliverys as $delivery) {
      $dataArray = explode("(-(-)-)", $delivery);
      echo ("<tr onclick='showDelivery(this)'>");
      echo ("<td id='deliveryID'>$dataArray[0]</td>");
      echo ("<td>$dataArray[1]</td>");
      $temp = explode("(--(-)--)", $dataArray[2]);
      $artikel = explode("---", $temp[0]);
      $geraete = explode("---", $temp[1]);
      echo("<td>");
      for ($i=0; $i < sizeof($artikel); $i++) { 
        echo($artikel[$i] . " " . $geraete[$i] . '<br>');
      }
      echo("</td>");
      echo ("<td>$dataArray[4], $dataArray[5]</td>");
      echo ("<td class='address'>$dataArray[6] $dataArray[7]<br>$dataArray[8] $dataArray[9]</td>");
      echo ("</tr>");
    }
    ?>
  </tbody>
  <script>
    function showDelivery(elem) {
      const tempID = elem.querySelector('#deliveryID').innerHTML;
      window.location.href = "/viewDelivery.php?deliveryID=" + tempID;
    }
  </script>
</table>
</div>
</body>
</html>