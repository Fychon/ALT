<!-- Einstellungsseite für Auto-, Tour-, Liefer-, und Auslieferungsgebietsdaten. -->

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
if (!($_SESSION["rechte"] <= 1)) {
    header("location: index.php");
    exit;
}
$markt = $_SESSION['markt'];
$selectedKW = date('W');
require "scripts/configureShop.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['day'])) {
        $selectedDay = $_POST['day'];
        $selectedYear = $_POST['year'];
        $selectedMonth = $_POST['month'];
        $selectedDate = $selectedYear . "-" . $selectedMonth . "-" . $selectedDay;
        $selectedKW = date("W", strtotime($selectedDate));
    } else if (isset($_POST['kw'])) {
        $selectedKW = $_POST['kw'];
    }
    if ($_POST['button'] == "löschen") {
        switch ($_POST['id']) {
            case 'gebiet':
                $markt = $_POST['markt'];
                $id = $_POST['gebietid'];
                deleteZone($markt, $id);
                break;
            case 'auto':
                $markt = $_POST['markt'];
                $id = $_POST['carid'];
                deleteCar($markt, $id);
                break;
            default:
                # code...
                break;
        }
    } else if ($_POST['button'] == "ändern") {
        switch ($_POST['id']) {
            case 'tour':
                $markt = $_POST['markt'];
                $anzahl = $_POST['anzahl'];
                $vormittags = $_POST['anzahlVormittags'];
                updateTour($markt, $anzahl, $vormittags);
                break;
            case 'tourvorlage':
                $year = $_POST['year'];
                $kw = $_POST['kw'];
                $kw1 = $_POST['kw1'];
                $markt = $_POST['markt'];

                $input_touranzahlMo = $_POST['anzahl0'];
                $input_touranzahlDi = $_POST['anzahl1'];
                $input_touranzahlMi = $_POST['anzahl2'];
                $input_touranzahlDo = $_POST['anzahl3'];
                $input_touranzahlFr = $_POST['anzahl4'];
                $input_touranzahlSa = $_POST['anzahl5'];
                $input_touranzahlSo = $_POST['anzahl6'];

                $outputMo = $outputDi = $outputMi = $outputDo = $outputFr = $outputSa = $outputSo = "";
                $input_liefergebietMo = $_POST['gebiet0'];
                $length = sizeof($input_liefergebietMo);
                for ($i = 0; $i < $length; $i++) {
                    $outputMo = $input_liefergebietMo[$i] . "," . $outputMo;
                }

                $input_liefergebietDi = $_POST['gebiet1'];
                $length = sizeof($input_liefergebietDi);
                for ($i = 0; $i < $length; $i++) {
                    $outputDi = $input_liefergebietDi[$i] . "," . $outputDi;
                }
                $input_liefergebietMi = $_POST['gebiet2'];
                $length = sizeof($input_liefergebietMi);
                for ($i = 0; $i < $length; $i++) {
                        $outputMi = $input_liefergebietMi[$i] . "," . $outputMi;
                }
                $input_liefergebietDo = $_POST['gebiet3'];
                $length = sizeof($input_liefergebietDo);
                for ($i = 0; $i < $length; $i++) {
                        $outputDo = $input_liefergebietDo[$i] . "," . $outputDo;
                }
                $input_liefergebietFr = $_POST['gebiet4'];
                $length = sizeof($input_liefergebietFr);
                for ($i = 0; $i < $length; $i++) {
                        $outputFr = $input_liefergebietFr[$i] . "," . $outputFr;
                }
                $input_liefergebietSa = $_POST['gebiet5'];
                $length = sizeof($input_liefergebietSa);
                for ($i = 0; $i < $length; $i++) {
                    $outputSa = $input_liefergebietSa[$i] . "," . $outputSa;
                }
                $input_liefergebietSo = $_POST['gebiet6'];
                $length = sizeof($input_liefergebietSo);
                for ($i = 0; $i < $length; $i++) {
                    $outputSo = $input_liefergebietSo[$i] . "," . $outputSo;
                }

                if ($kw < $kw1) {
                    for ($i = $kw; $i <= $kw1; $i++) {
                        $dates = getDatesOfWeek($year, $i);
                        updateTourScheduleForDay($markt, $input_touranzahlMo, rtrim($outputMo, ","), $dates[0]);
                        updateTourScheduleForDay($markt, $input_touranzahlDi, rtrim($outputDi, ","), $dates[1]);
                        updateTourScheduleForDay($markt, $input_touranzahlMi, rtrim($outputMi, ","), $dates[2]);
                        updateTourScheduleForDay($markt, $input_touranzahlDo, rtrim($outputDo, ","), $dates[3]);
                        updateTourScheduleForDay($markt, $input_touranzahlFr, rtrim($outputFr, ","), $dates[4]);
                        updateTourScheduleForDay($markt, $input_touranzahlSa, rtrim($outputSa, ","), $dates[5]);
                        updateTourScheduleForDay($markt, $input_touranzahlSo, rtrim($outputSo, ","), $dates[6]);
                    }
                } else if ($kw == $kw1) {
                    $dates = getDatesOfWeek($year, $kw);
                    updateTourScheduleForDay($markt, $input_touranzahlMo, rtrim($outputMo, ","), $dates[0]);
                    updateTourScheduleForDay($markt, $input_touranzahlDi, rtrim($outputDi, ","), $dates[1]);
                    updateTourScheduleForDay($markt, $input_touranzahlMi, rtrim($outputMi, ","), $dates[2]);
                    updateTourScheduleForDay($markt, $input_touranzahlDo, rtrim($outputDo, ","), $dates[3]);
                    updateTourScheduleForDay($markt, $input_touranzahlFr, rtrim($outputFr, ","), $dates[4]);
                    updateTourScheduleForDay($markt, $input_touranzahlSa, rtrim($outputSa, ","), $dates[5]);
                    updateTourScheduleForDay($markt, $input_touranzahlSo, rtrim($outputSo, ","), $dates[6]);
                }

                break;
            default:
                # code...
                break;
        }
    } else if ($_POST['button'] == "anlegen") {
        switch ($_POST['id']) {
            case 'gebiet':
                $input_liefergebiet = $_POST['liefergebiet'];
                $plz = $_POST['plz'];
                $markt = $_POST['markt'];
                newZone($markt, $input_liefergebiet, $plz);
                break;
            case 'auto':
                $licensePlate = $_POST['kennzeichen'];
                $description = $_POST['beschreibung'];
                $markt = $_POST['markt'];
                newCar($markt, $licensePlate, $description);
                break;
            default:
                # code...
                break;
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


    <div class="tourSettings">

        <div>
            <h2>Tourgröße verwalten:</h2>
            <table>
                <thead>
                    <tr>
                        <th>Gesamt</th>
                        <th>Vormittag</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = getTourData($markt);
                        ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <tr>
                                <input type="hidden" name="id" value="tour">
                                <input type="hidden" name="tour_id" value="<?php echo "$data[2]"; ?>">
                                <input type="hidden" name="markt" value="<?php echo "$markt"; ?>">
                                <td><input class="number" type="text" name="anzahl" value="<?php echo "$data[0]"; ?>"></td>
                                <td><input class="number" type="text" name="anzahlVormittags" value="<?php echo "$data[1]"; ?>"></td>
                                <td>
                                    <input class="accept" type="submit" value="ändern" name="button">
                                </td>
                            </tr>
                        </form>

                    <?php
                    mysqli_stmt_close($stmt);
                    ?>
                </tbody>
            </table>
        </div>

        <div>
            <h2>Gebiete verwalten:</h2>
            <table>
                <thead>
                    <th>Liefergebiet</th>
                    <th>Postleitzahlen
                    <th></th>
                </thead>
                <tbody>
                    <?php

                    $zones = getAllZones($markt);
                    foreach ($zones as $zoneString) {
                        $zone = explode("---", $zoneString);

                    ?>
                        <tr>
                            <td><?php echo "$zone[1]"; ?></td>
                            <td><?php echo "$zone[2]"; ?></td>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <td>
                                    <input type="hidden" name="markt" value="<?php echo "$markt"; ?>">
                                    <input type="hidden" name="id" value="gebiet">
                                    <input type="hidden" name="gebietid" value="<?php echo "$zone[0]"; ?>">
                                    <input class="decline" type="submit" value="löschen" name="button">
                                </td>
                            </form>
                        </tr>
                    <?php
                    }
                    ?>

                    <tr class="new">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="zoneForm">
                            <td>
                                <div class="fieldStart">

                                    <input class="text" type="text" name="liefergebiet" id="liefergebiet" placeholder="Name">
                                </div>

                            </td>
                            <td>
                                <textarea name="plz" form="zoneForm" cols="15">plz1-plz2,&#10;plz3</textarea>
                            </td>
                            <td>
                                <div class="fieldEnd">

                                    <input type="hidden" name="markt" value="<?php echo "$markt"; ?>">
                                    <input type="hidden" name="id" value="gebiet">
                                    <input class="accept" type="submit" value="anlegen" name="button">
                                </div>

                            </td>
                        </form>

                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tourvordruck">
            <h2>Tourvordruck verwalten:</h2>
            <div>
                <div class="tourSetCal">
                    <?php include("./components/calendar.php");
                    ?>
                </div>
                <?php
                if (isset($selectedKW)) {
                ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="kwSelection">
                            <p>ab KW:</p>
                            <input type="text" name="kw" value="<?php echo "$selectedKW"; ?>">
                            <p>bis KW:</p>
                            <input type="text" name="kw1" value="<?php echo "$selectedKW"; ?>">
                        </div>

                        <table class="tour">
                            <thead>
                                <tr>
                                    <th>Wochentag</th>
                                    <th class="anzahl">Anzahl-Touren</th>
                                    <th>Lieferzonen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <input type="hidden" name="year" value="<?php echo "$actualYear"; ?>">
                                <input type="hidden" name="markt" value="<?php echo "$markt"; ?>">
                                <?php

                                require_once "./scripts/configureDB.php";

                                $tablename2 = $markt . "_zone";
                                $stmt = mysqli_prepare($link, "SELECT id, description, postalcode FROM $tablename2");
                                mysqli_stmt_execute($stmt);
                                $stmt->bind_result($id, $besch, $plz);
                                $ids = [];
                                $beschs = [];
                                $plzs = [];
                                while ($row = ($stmt->fetch())) {
                                    $beschs[] = $besch;
                                    $ids[] = $id;
                                    $plzs[] = $plz;
                                }
                                mysqli_stmt_close($stmt);
                                require_once "./scripts/configureShop.php";
                                $weekPlan = getTourevorlageForWeek($markt, $selectedKW, $actualYear);
                                $dayCounter = 0;

                                $wochentage = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');
                                foreach ($weekPlan as $day) {
                                    $dayArray = explode("---", $day);
                                    $agArray = explode(",", $dayArray[2]);

                                ?>
                                    <tr>

                                        <td><?php echo "$wochentage[$dayCounter]"; ?></td>
                                        <td>
                                            <select class="anzahl" name="anzahl<?php echo $dayCounter; ?>">
                                                <option value="0" <?php if ($dayArray[1] == 0) {
                                                                        echo "selected";
                                                                    } ?>>0</option>
                                                <option value="1" <?php if ($dayArray[1] == 1) {
                                                                        echo "selected";
                                                                    } ?>>1</option>
                                                <option value="2" <?php if ($dayArray[1] == 2) {
                                                                        echo "selected";
                                                                    } ?>>2</option>
                                                <option value="3" <?php if ($dayArray[1] == 3) {
                                                                        echo "selected";
                                                                    } ?>>3</option>
                                                <option value="4" <?php if ($dayArray[1] == 4) {
                                                                        echo "selected";
                                                                    } ?>>4</option>
                                            </select>
                                        </td>

                                        <!-- //Lieferzonen -->
                                        <td>
                                            <?php include("./components/zoneMultiSelection.php"); ?>
                                        </td>
                                    </tr>

                                <?php
                                    $dayCounter++;
                                }
                                ?>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="1">
                                        <input type="hidden" name="id" value="tourvorlage">
                                        <input class="accept" type="submit" value="ändern" name="button">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                <?php
                }
                ?>
            </div>
        </div>

        <script>
            const zoneList = document.getElementsByClassName("zoneSelection");
            for (let item of zoneList) {
                item.getElementsByClassName("anchor")[0].addEventListener("click", showList, false)
            }

            function showList(e) {
                if (this.parentElement.classList.contains('visible'))
                    this.parentElement.classList.remove('visible');
                else
                    this.parentElement.classList.add('visible');

            }
        </script>

        <div class="cars">
            <h2>Autos verwalten:</h2>
            <table>
                <thead>
                    <tr>
                        <th>Kennzeichen</th>
                        <th>Beschreibung</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once "./scripts/configureDB.php";
                    $tablename = $markt . "_car";
                    $stmt = mysqli_prepare($link, "SELECT id, licensePlate, description FROM $tablename");
                    mysqli_stmt_execute($stmt);
                    $stmt->bind_result($id, $kennzeichen, $beschreibung);
                    while ($stmt->fetch()) {
                    ?>
                        <tr>
                            <td><?php echo "$kennzeichen"; ?></td>
                            <td><?php echo "$beschreibung"; ?></td>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <td class="pt-2 px-3" colspan="2">
                                    <input type="hidden" name="id" value="auto">
                                    <input type="hidden" name="carid" value="<?php echo ($id) ?>">
                                    <input type="hidden" name="markt" value="<?php echo ($markt) ?>">
                                    <input class="decline" type="submit" value="löschen" name="button">
                                </td>
                            </form>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <td>
                                <input type="text" class="text" name="kennzeichen" id="kennzeichen" placeholder="Kennzeichen">
                            </td>
                            <td>
                                <input type="text" class="textLarge" name="beschreibung" id="beschreibung" placeholder="Beschreibung">
                            </td>
                            <td>
                                <input type="hidden" name="id" value="auto">
                                <input type="hidden" name="markt" value="<?php echo ($markt) ?>">
                                <input class="accept" type="submit" value="anlegen" name="button">
                            </td>
                        </form>

                    </tr>
                </tbody>
            </table>
        </div>

    </div>


</body>

</html>