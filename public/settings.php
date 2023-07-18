<!-- Menü auf verschiedene Verwaltungskategorien -->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verwaltung</title>
    <link rel="stylesheet" href="/css/style.css" />

</head>

<body>

    <?php include("./components/header.php"); ?>

    <nav class="mainMenue">
        <a href="/userOverview.php">
            <img src="/images/person.webp" alt="Benutzerverwaltung">
            Benutzer</a>


        <?php
        if ($_SESSION["rechte"] <= 1) {
        ?>
            <a href="/supplierOverview.php">
                <img src="/images/supplier.webp" alt="Lieferantenverwaltung">
                Lieferanten</a>

        <?php
        }
        ?>
        <?php
        if ($_SESSION["rechte"] == 0) {
        ?>
            <a href="/shopOverview.php">
                <img src="/images/markt.webp" alt="Markteinstellung">
                Markt
            </a>

        <?php
        }
        ?>
    </nav>
    <?php include("./components/footer.php")?>

</body>

</html>