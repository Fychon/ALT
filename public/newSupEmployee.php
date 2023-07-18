<!-- Erstellung eines neuen Lieferanten -->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}
// Admins and shopAdmins can create new suppliers
if ($_SESSION["rechte"] > 1) {
    header("location: index.php");
    exit;
}
$markt = $_SESSION["markt"];

$markt_referenz = $email = $vorname = $nachname = $tel = "";
$contact_err = $fill_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['anlegen'] == "anlegen") {
        $temp_email = trim($_POST["email"]);
        $temp_vorname = trim($_POST["vorname"]);
        $temp_nachname = trim($_POST["nachname"]);
        $temp_tel = trim($_POST["tel"]);
        $markt_referenz = trim($_POST["marktref"]);

        if ((empty($temp_email))) {
            $contact_err = "Bitte E-Mail Adresse eingeben.";
        } else {
            $email = trim($_POST["email"]);
        }
        if ((empty($temp_vorname))) {
            $contact_err = "Bitte Vorname eingeben.";
        } else {
            $vorname = trim($_POST["vorname"]);
        }
        if ((empty($temp_nachname))) {
            $contact_err = "Bitte Nachname eingeben.";
        } else {
            $nachname = trim($_POST["nachname"]);
        }
        if ((empty($temp_tel))) {
            $contact_err = "Bitte Telefonnummer eingeben.";
        } else {
            $tel = trim($_POST["tel"]);
        }
        if (empty($contact_err)) {
            require_once("./scripts/configureShop.php");
            createSupplier($markt_referenz, $vorname, $nachname, $email, $tel);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer erstellen</title>
    <link rel="stylesheet" href="/css/style.css" />
</head>

<body>
    <?php include("./components/header.php"); ?>

    <div class="createDelivery">

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h4>Neuer Benutzer:</h4>
            <input type="hidden" name="marktref" id="marktref" value="<?php echo ("$markt"); ?>">
            <div>
                <input type="text" name="vorname" id="vorname" placeholder="Vorname">
                <input type="text" name="nachname" id="nachname" placeholder="Nachname">
            </div>
            <div>
                <input type="text" name="tel" id="tel" placeholder="Telefonnummer">
                <input type="text" name="email" id="email" placeholder="Email">
            </div>
            
            <?php
            if (!empty($fill_err)) {
                echo '<div class="alert alert-danger">' . $fill_err . '</div>';
            }
            ?>
            <span class="buttons">
                <input class="decline" type="submit" name="abbrechen" id="anlegen" value="abbrechen" />
                <input class="accept" type="submit" name="anlegen" id="anlegen" value="anlegen" />
            </span>
        </form>
    </div>
    <?php include("./components/footer.php")?>

</body>

</html>