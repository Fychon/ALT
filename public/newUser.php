<!-- Erstellung eines neuen Benutzers -->



<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Admins and shopAdmins can create new user, everybody else back to index.
if ($_SESSION["rechte"] >= 2) {
    header("location: index.php");
    exit;
}
$markt = $_SESSION["markt"];
$markt_referenz = $username = $email = $vorname = $nachname = $password = "";
$contact_err = $fill_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['anlegen'] == "anlegen") {
        $temp_email = trim($_POST["email"]);
        $temp_username = trim($_POST["username"]);
        $temp_vorname = trim($_POST["vorname"]);
        $temp_nachname = trim($_POST["nachname"]);
        $temp_password = trim($_POST["password"]);
        $markt_referenz = trim($_POST["marktref"]);

        if ((empty($temp_email))) {
            $contact_err = "Bitte E-Mail Adresse eingeben.";
        } else {
            $email = trim($_POST["email"]);
        }
        if (((empty($temp_username)))) {
            $contact_err = "Bitte Username eingeben.";
        } else {
            $username = trim($_POST["username"]);
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
        if ((empty($temp_password))) {
            $contact_err = "Bitte Passwort eingeben.";
        } else {
            $password = trim($_POST["password"]);
        }
        if (empty($contact_err)) {
            require_once("./scripts/configureShop.php");
            switch ($_POST["permission"]) {
                case '1':
                    createUser($markt_referenz, $vorname, $nachname, $email, $username, $password, 1);
                    break;
                case '2':
                    createUser($markt_referenz, $vorname, $nachname, $email, $username, $password, 2);
                    break;
                case '3':
                    createUser($markt_referenz, $vorname, $nachname, $email, $username, $password, 3);
                    break;
                case '4':
                    createUser($markt_referenz, $vorname, $nachname, $email, $username, $password, 4);
                    break;
                default:
                    # code...
                    break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
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
                <select name="permission" id="permission">
                    <option value="2">Verkäufer</option>
                    <option value="4">Lieferant</option>
                    <option value="3">Logistik</option>
                    <option value="1">Admin</option>
                </select>
            </div>
            <div>
                <input type="text" name="vorname" id="vorname" placeholder="Vorname">
                <input type="text" name="nachname" id="nachname" placeholder="Nachname">
            </div>
            <div>
                <input type="text" name="email" id="email" placeholder="Email">
                <input type="text" name="username" id="username" placeholder="Username">
            </div>
            <div class="passwords">
                <input class="pw" type="text" name="password" id="password" placeholder="Passwort">

            </div>
            <?php
            if (!empty($fill_err)) {
                echo '<div class="alert alert-danger">' . $fill_err . '</div>';
            }
            ?>
            <span class="buttons">
                <input class="decline" type="submit" name="abbrechen" id="anlegen" value="zurück" />
                <input class="accept" type="submit" name="anlegen" id="anlegen" value="anlegen" />
            </span>
        </form>
    </div>
    <?php include("./components/footer.php")?>

</body>

</html>