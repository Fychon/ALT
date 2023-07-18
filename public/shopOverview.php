<!-- Anzeige aller existierenden Märkte
Märkte können gelöscht werden.
Ein neuer Markt kann angelegt werden.
Nur von Admin aufrufbar. -->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}

// Admins only can view the shopView
if ($_SESSION["rechte"] >= 1 ) {
    header("location: index.php");
    exit;
  }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['marktaendern'] == "löschen") {
        require "./scripts/configureShop.php";
        deleteTables($_POST['marktid']);
    } else if ($_POST['marktaendern'] == "ändern") {

    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marktverwaltung</title>
    <link rel="stylesheet" href="/css/style.css" />
</head>

<body>
    <?php include("./components/header.php"); ?>



    <div class="shop">
        <?php
        require_once "./scripts/configureDB.php";

        // Prepare a select statement
        $stmt = mysqli_prepare($link, "SELECT id, shop_ref, name FROM shops");
        mysqli_stmt_execute($stmt);

        $stmt->bind_result($id, $markt_referenz, $beschreibung);
        ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Marktreferenz</th>
                    <th colspan="3">Name</th>
                    <th colspan="2"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                /* fetch values */
                while ($stmt->fetch()) {
                ?>
                    <tr>
                        <td><?php echo "$id"; ?></td>
                        <td><?php echo "$markt_referenz"; ?></td>
                        <td colspan="3"><?php echo "$beschreibung"; ?></td>
                        <td>
                            <div>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <input type="hidden" name="marktid" id="marktid" value="<?php echo "$markt_referenz"; ?>">
                                    <!-- <input type="submit" name="marktaendern" id="aendern-marktaendern" value="ändern"> -->
                                    <input type="submit" name="marktaendern" id="loeschen-marktaendern" value="löschen">
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

        <form action="newShop.php">
            <input type="submit" value="Markt anlegen">
        </form>
    </div>


    <?php include("./components/footer.php")?>

</body>

</html>