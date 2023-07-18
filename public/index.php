<!-- Startseite
Grundmenü. Weiterleitung zu den Hauptfunktionen und Untermenüs. -->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: /login.php");
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Auslieferungstool</title>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="/css/style.css" />
</head>

<body>
  <?php include("./components/header.php"); ?>

  <div class="mainMenuCon">
    <nav class="mainMenue">
      <?php
      if($_SESSION["rechte"] < 4){
        ?>
        <a href="/newDelivery.php">
          <img src="/images/createNew.webp" alt="Neuer Auftrag">
          <h2>Neue Lieferung</h2>
        </a>
        <?php
      }
      ?>


      <a href="/deliveryPlan.php">
        <img src="/images/deliveryPlan.webp" alt="Lieferplan">

        <h2>Lieferplan</h2>

      </a>

      <a href="/settings.php">
        <img src="/images/settings.webp" alt="Einstellungen">

        <h2>Verwaltung</h2>

      </a>

    </nav>
  </div>
  <?php include("./components/footer.php")?>
</body>

</html>