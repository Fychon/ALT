<!-- Anzeige des Kalendar. Ausgabe aller Lieferung für den selektierten Kalendartag.
Soll auch als Archiv nutbar sien und somit Lieferaufträge der Vergangenheit anzeigen. -->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}
$markt = $_SESSION["markt"];
require("./scripts/configureShop.php");

if (!isset($_POST['day'])) {
  $selectedDay = date('d');
} else {
  $selectedDay = $_POST['day'];
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['day'])) {
    $selectedDay = $_POST['day'];
  } 
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Lieferplan</title>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="/css/style.css" />
</head>

<body>
  <?php include("./components/header.php"); ?>
  <?php include("./components/calendar.php"); ?>

  <?php
  $deliverys = getDeliverysForDay($markt, $selectedDate);
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
          echo ("<td>");
          $temp = explode("(--(-)--)", $dataArray[2]);
          $artikel = explode("---", $temp[0]);
          $geraete = explode("---", $temp[1]);
          for ($i=0; $i < sizeof($artikel); $i++) { 
            echo($artikel[$i] . " " . $geraete[$i] . '<br>');
          }
          echo ("</td>");
          echo ("<td>$dataArray[4] $dataArray[5]</td>");

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

        <?php
          if($_SESSION["rechte"]<=1 || $_SESSION["rechte"]==4){
            ?>
              <form action="setupDeliverySchedule.php" method="post">
              <input type="hidden" name="deliveryDate" value="<?php echo($selectedDate);?>">
              <input type="hidden" name="markt" value="<?php echo($markt);?>">
                <input class="accept" type="submit" value="einteilen" name="einteilen">
              </form>
            <?php
          }
        ?>

  </div>
</body>

</html>