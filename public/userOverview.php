<!-- Admins und Marktadmin erhalten eine Übersicht aller Benutzer des Marktes und können einzelne Benutzer entfernen oder
einen neuen anlegen.

Für alle zugänglich ist die eigene Benutzerverwaltung. Hier können die aktuellen Daten eingesehen werden.-->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}
$markt = $_SESSION["markt"];
require_once "./scripts/configureShop.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['benutzerendern'] == "löschen") {
        require_once "./scripts/configureShop.php";
        deleteUser($_POST['userid']);
    } else if ($_POST['benutzerendern'] == "aendern") {
        //TODO aufruf Passwortändern, Fehlerausgabe falls Passwort nicht übereinstimmt.
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzerverwaltung</title>
    <link rel="stylesheet" href="/css/style.css" />
</head>

<body>
    <?php
    include("./components/header.php");

    ?>
    <div class="shop">
        <?php

        if ($_SESSION["rechte"] <= 1) {
            require_once "./scripts/configureDB.php";
            $stmt = mysqli_prepare($link, "SELECT id, username, firstname, lastname, shopID , permission FROM users WHERE shopID='$markt'");
            mysqli_stmt_execute($stmt);
            $stmt->bind_result($id, $username, $vorname, $nachname, $marktid, $berechtigungsid);
        ?>


            <table>
                <thead>
                    <tr>
                        <th>Markt</th>

                        <th>Username</th>
                        <th>Vorname</th>
                        <th>Nachname</th>
                        <th>Rolle</th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    /* fetch values */
                    while ($stmt->fetch()) {
                    ?>
                        <tr>
                            <td>
                                <?php
                                echo ($marktid);
                                ?>
                            </td>
                            <td><?php echo "$username"; ?></td>
                            <td><?php echo "$vorname"; ?></td>
                            <td><?php echo "$nachname"; ?></td>

                            <td><?php if ($berechtigungsid == 1) {
                                    echo ("Marktadmin");
                                } else if ($berechtigungsid == 2) {
                                    echo ("Verkäufer");
                                } else if ($berechtigungsid == 3) {
                                    echo ("Logistiker");
                                } else if ($berechtigungsid == 4) {
                                    echo ("Lieferant");
                                } else if ($berechtigungsid == 0) {
                                    echo ("Admin");
                                }
                                ?></td>
                            <td>
                                <div>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <input type="hidden" name="userid" id="userid" value="<?php echo "$id"; ?>">
                                        <!-- <input type="submit" name="benutzerendern" id="aendern-benutzerendern" value="aendern"> -->
                                        <input type="submit" name="benutzerendern" id="loeschen-benutzerendern" value="löschen">
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php
                    }
                    $stmt->close();
                    mysqli_stmt_close($stmt);

                    ?>
                </tbody>
            </table>
            <form action="newUser.php">
                <input type="submit" value="Benutzer anlegen">
            </form>


<?php
        }
?>

</div>

<div class="createDelivery">
    <?php
    require_once("./scripts/configureShop.php");
    $userData = getOwnUserData();
    switch ($userData[5]) {
        case '0':
            $rollenBeschreibung = 'Root';
            break;
        case '1':
            $rollenBeschreibung = 'Marktadmin';
            break;
        case '2':
            $rollenBeschreibung = 'Verkauf';
            break;
        case '3':
            $rollenBeschreibung = 'Lager';
            break;
        case '4':
            $rollenBeschreibung = 'Spedition';
            break;
        default:
            break;
    }
    ?>
    <h4>Benutzerdaten:</h4>
    <form action="">
        <div>
            <input readonly type="text" name="berechtigung" id="berechtigung" value="<?php echo ("$rollenBeschreibung"); ?>">
            <input readonly type="text" name="shop" id="shop" value="<?php echo ("$userData[6]"); ?>">
        </div>
        <div>
            <input readonly type="text" name="username" id="username" value="<?php echo ("$userData[1]"); ?>">
            <input readonly type="text" name="email" id="email" value="<?php echo ("$userData[2]"); ?>">
        </div>
        <div>
            <input readonly type="text" name="vorname" id="vorname" value="<?php echo ("$userData[3]"); ?>">
            <input readonly type="text" name="nachname" id="nachname" value="<?php echo ("$userData[4]"); ?>">
        </div>
        <h4>Passwort ändern:</h4>
        <div class="passwords">
            <input type="text" name="oldPassword" id="oldPassword" placeholder="altes Password">
            <input type="text" name="newPassword1" id="newPassword1" placeholder="neues Password">
            <input class="pw" type="text" name="newPassword2" id="newPassword2" placeholder="Password wiederholen">
        </div>
        <input class="accept" type="submit" value="ändern">
    </form>
</div>

</body>

</html>