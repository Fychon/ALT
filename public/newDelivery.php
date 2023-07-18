<!-- Erstellung eines neuen Lieferauftrag
Kunden und Gerätedaten kommen aus createDelivery, Lieferdatum aus Kalendar.-->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}
require("./scripts/configureShop.php");

//Check if the user is a suoplier -> Can´t create newDeliverys so back to home.
if ($_SESSION["rechte"] == 4) {
  header("location: index.php");
  exit;
}
$markt = $_SESSION['markt'];

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
<script>
  function validate() {
    let kundenVornameInput = document.forms["createDelivery"]["kundenVorname"].value;
    if (kundenVornameInput == "") {
      alert("Vorname eingeben");
      return false;
    }
    let kundenNachnameInput = document.forms["createDelivery"]["kundenNachname"].value;
    if (kundenNachnameInput == "") {
      alert("Nachname eingeben");
      return false;
    }
    let plzInput = document.forms["createDelivery"]["plz"].value;
    if (plzInput == "") {
      alert("Postleitzahle eingeben");
      return false;
    }
    let stadtInput = document.forms["createDelivery"]["stadt"].value;
    if (stadtInput == "") {
      alert("Stadt eingeben");
      return false;
    }
    let hausnrInput = document.forms["createDelivery"]["hausnr"].value;
    if (hausnrInput == "") {
      alert("Hausnummer eingeben");
      return false;
    }
    let streetInput = document.forms["createDelivery"]["strasse"].value;
    if (streetInput == "") {
      alert("Straße eingeben");
      return false;
    }
    let emailInput = document.forms["createDelivery"]["email"].value;
    if (emailInput == "") {
      alert("E-Mail eingeben");
      return false;
    }
    let phoneInput = document.forms["createDelivery"]["telnr"].value;
    if (phoneInput == "") {
      alert("Telefonnummer eingeben");
      return false;
    }
  }
</script>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Lieferung erstellen</title>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="/css/style.css" />

</head>

<body>
  <?php include("./components/header.php"); ?>

  <?php require_once "./scripts/configureDB.php"; ?>

  <?php include("./components/calendar.php"); ?>

  <?php include("./components/createDelivery.php"); ?>



</body>

</html>