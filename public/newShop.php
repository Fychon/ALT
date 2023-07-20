<!-- Erstellugn eines neuen Marktes. Abfrage ob die Marktreferenz bereits vergeben ist. Validierung in php. -->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /login.php");
    exit;
}

//Only admins can create new shops.
if ($_SESSION["rechte"] != 0) {
    header("location: index.php");
    exit;
}

// Include config file
require_once "./scripts/configureDB.php";
require_once "./scripts/configureShop.php";

// Define variables and initialize with empty values
$markt_referenz = $markt_beschreibung = $markt_strasse = $markt_hausnr = $markt_plz = $markt_stadt = $markt_kontakt_username = $markt_kontakt_email = $markt_kontakt_vorname = $markt_kontakt_nachname = $markt_kontakt_password = "";
$markt_err = $adress_err = $contact_err = $fill_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["markt"]))) {
        $markt_err = "Bitte eine Markt Referenz eingeben.";
    } else {
        $sql = "SELECT id FROM shops WHERE shop_ref = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            $prepared_markt = strtolower(trim($_POST["markt"]));
            mysqli_stmt_bind_param($stmt, "s", $prepared_markt);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if shop exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $markt_err = "Diese Marktreferenz ist schon vergeben.";
                } else {
                    $markt_referenz = $prepared_markt;
                }
            }
            // mysqli_stmt_close($stmt);
        }
        // mysqli_close($link);
    }

    if (empty(trim($_POST["marktname"]))) {
        $markt_err = "Bitte Marktname eingeben.";
    } else {
        $markt_beschreibung = trim($_POST["marktname"]);
    }
    $temp_strasse = trim($_POST["strasse"]);
    $temp_hausnr = trim($_POST["hausnr"]);
    $temp_plz = trim($_POST["plz"]);
    $temp_stadt = trim($_POST["stadt"]);
    if ((!empty($temp_strasse))) {
        $markt_strasse = trim($_POST["strasse"]);
    } else {
        $adress_err = "Bitte Strasse eingeben.";
    }
    if (((!empty($temp_hausnr)))) {
        $markt_hausnr = trim($_POST["hausnr"]);
    } else {
        $adress_err = "Bitte Hausnummer eingeben.";
    }
    if ((!empty($temp_plz))) {
        $markt_plz = trim($_POST["plz"]);
    } else {
        $adress_err = "Bitte Postleitzahl eingeben.";
    }
    if ((!empty($temp_stadt))) {
        $markt_stadt = trim($_POST["stadt"]);
    } else {
        $adress_err = "Bitte Stadt eingeben.";
    }

    $temp_email = trim($_POST["email"]);
    $temp_username = trim($_POST["username"]);
    $temp_vorname = trim($_POST["vorname"]);
    $temp_nachname = trim($_POST["nachname"]);
    $temp_password = trim($_POST["password"]);
    if ((empty($temp_email))) {
        $contact_err = "Bitte E-Mail Adresse eingeben.";
    } else {
        $markt_kontakt_email = trim($_POST["email"]);
    }
    if (((empty($temp_username)))) {
        $contact_err = "Bitte Username eingeben.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            $prepared_user = strtolower(trim($_POST["username"]));
            mysqli_stmt_bind_param($stmt, "s", $prepared_user);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if shop exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $contact_err = "Dieser Username ist schon vergeben.";
                } else {
                    $markt_kontakt_username = $prepared_user;
                }
            }
    }
}
    if ((empty($temp_vorname))) {
        $contact_err = "Bitte Vorname eingeben.";
    } else {
        $markt_kontakt_vorname = trim($_POST["vorname"]);
    }
    if ((empty($temp_nachname))) {
        $contact_err = "Bitte Nachname eingeben.";
    } else {
        $markt_kontakt_nachname = trim($_POST["nachname"]);
    }
    if ((empty($temp_password))) {
        $contact_err = "Bitte Passwort eingeben.";
    } else {
        $markt_kontakt_password = trim($_POST["password"]);
    }


    if (empty($contact_err) && empty($markt_err) && empty($adress_err)) {
        require_once "./scripts/configureShop.php";
        $err = createShop($markt_referenz, $markt_beschreibung, $markt_plz, $markt_stadt, $markt_strasse, $markt_hausnr, $markt_kontakt_username, $markt_kontakt_vorname, $markt_kontakt_nachname, $markt_kontakt_email, $markt_kontakt_password);
        if (isset($err)) {
            echo ($err);
        }
    } else {
        if (!empty($contact_err)) {
            $fill_err = $contact_err;
        }
        if (!empty($adress_err)) {
            $fill_err = $adress_err;
        }
        if (!empty($markt_err)) {
            $fill_err = $markt_err;
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
    <title>Marktverwaltung</title>
    <link rel="stylesheet" href="/css/style.css" />
</head>

<body>
    <?php include("./components/header.php"); ?>
    <div class="createDelivery">

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

            <div>
                <h4>Markt Referenz:</h4>
                <h4>Marktname:</h4>
            </div>

            <div>
                <input type="text" name="markt" id="markt" placeholder="m000/s000">
                <input type="text" name="marktname" id="marktname">
            </div>

            <h4>Adresse:</h4>
            <div class="address">
                <input type="text" name="strasse" id="strasse" placeholder="Strasse">
                <input type="text" name="hausnr" id="hausnr" placeholder="Haus-Nr.">
                <input type="text" name="plz" id="plz" placeholder="PLZ">
                <input type="text" name="stadt" id="stadt" placeholder="Stadt">
            </div>



            <h4>Kontaktperson/Admin:</h4>
            <div>
                <input type="text" name="vorname" id="vorname" placeholder="Vorname">
                <input type="text" name="nachname" id="nachname" placeholder="Nachname">
            </div>
            <div>
                <input type="text" name="username" id="username" placeholder="Username">
                <input type="text" name="email" id="email" placeholder="Email">
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
                <input class="decline" type="submit" name="abbrechen" id="anlegen" value="abbrechen" />
                <input class="accept" type="submit" name="anlegen" id="anlegen" value="anlegen" />
            </span>


        </form>
    </div>

</body>

</html>