<!-- Übersicht aller bereits angelegten Mitarbeiter des Lieferanten -->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}

// Admins and shopAdmins can create new suppliers
if ($_SESSION["rechte"] > 1 ) {
    header("location: index.php");
    exit;
  }
$markt = $_SESSION["markt"];
require_once "./scripts/configureShop.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['lieferantAendern'] == "löschen") {
        deleteSupplier($_POST['shopID'], $_POST['userid']);
    } else if ($_POST['lieferantAendern'] == "ändern") {
    }
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
    <div class="shop">


    <?php

        require_once "./scripts/configureDB.php";
        $tableName = $markt . '_supplier';
        $stmt = mysqli_prepare($link, "SELECT id, firstname, lastname, email, phone FROM $tableName");
        mysqli_stmt_execute($stmt);
        $stmt->bind_result($id, $vorname, $nachname, $email, $tel);
    
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <table>
                <thead>
                    <tr>
                        <th>Vorname</th>
                        <th>Nachname</th>
                        <th>email</th>
                        <th>tel</th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    /* fetch values */
                    while ($stmt->fetch()) {
                    ?>
                        <tr>
                            <td><?php echo "$vorname"; ?></td>
                            <td><?php echo "$nachname"; ?></td>
                            <td><?php echo "$email"; ?></td>
                            <td><?php echo "$tel"; ?></td>
                            <td>
                                <div>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <input type="hidden" name="shopID" id="shopID" value="<?php echo "$markt"; ?>">
                                        <input type="hidden" name="userid" id="userid" value="<?php echo "$id"; ?>">
                                        <!-- <input type="submit" name="lieferantAendern" id="aendern-benutzerendern" value="ändern"> -->
                                        <input type="submit" name="lieferantAendern" id="loeschen-benutzerendern" value="löschen">
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php
                    }
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);
                    ?>
                </tbody>
            </table>

        </form>
        <form action="newSupEmployee.php">
            <input type="submit" value="Lieferanten anlegen">
        </form>
        </div>
        <?php include("./components/footer.php")?>

</body>

</html>