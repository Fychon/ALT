<!-- Zwischenmenü Seite -->

<?php
// Initialize the session
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lieferantenverwaltung</title>
    <link rel="stylesheet" href="/css/style.css" />
</head>

<body>
    <?php include("./components/header.php"); ?>


    <nav class="mainMenue">
        <a href="/supEmployee.php">
            <img src="/images/employee.webp" alt="Personalverwaltung">
            Personal</a>

<?php if ($_SESSION["rechte"] <= 1 ) {
    ?>
        <a href="/supTourSettings.php">
            <img src="/images/car.webp" alt="Toureinstellungen">
            Tour</a>
    <?php
  }
  ?>
    </nav>

    <?php include("./components/footer.php")?>

</body>

</html>