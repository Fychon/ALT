<?php
function checkIfTourExistsForDay($markt, $date)
{
    require_once("configureDB.php");
    $_tourSchedule = $markt . "_tourSchedule";
    $ids = [];
    $sql = "SELECT id FROM $_tourSchedule WHERE dS_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $date);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id);
        $resCounter = 0;
        while (mysqli_stmt_fetch($stmt)) {
            $ids[$resCounter] = $id;
            $resCounter++;
        }
        mysqli_stmt_close($stmt);
        if (sizeof($ids) != 0) {
            return $ids;
        } else {
            return "none";
        }
    } else {
        return "none";
    }
}
function getSortedDeliverysForTour($markt, $tour)
{
    require("configureDB.php");
    $_delivery = $markt . "_delivery";
    $_customer = $markt . "_customer";
    $ret = [];
    $sql = "SELECT $_delivery.id, $_delivery.belegnummer, $_delivery.geraete, $_customer.firstname, $_customer.lastname, $_customer.postalcode, $_customer.city, $_customer.street, $_customer.housenumber FROM $_delivery INNER JOIN $_customer ON $_delivery.c_id = $_customer.id WHERE $_delivery.t_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $tour);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $beleg, $geraete, $firstname, $lastname, $postal, $city, $street, $hnumber);
        while (mysqli_stmt_fetch($stmt)) {
            $ret[$id] = $id . "(-(-)-)" . $beleg . "(-(-)-)" . $geraete . "(-(-)-)" . $geraete . "(-(-)-)" . $firstname . "(-(-)-)" . $lastname . "(-(-)-)" . $postal . "(-(-)-)" . $city . "(-(-)-)" . $street . "(-(-)-)" . $hnumber;
        }
        mysqli_stmt_close($stmt);
        return $ret;
    } else {
        return "notPossible";
    }
}
function getDeliveryData($markt, $deliveryID)
{
    require_once("configureDB.php");
    $_delivery = $markt . "_delivery";
    $_customer = $markt . "_customer";
    $sql = "SELECT $_delivery.id, $_delivery.lieferdatum, $_delivery.belegnummer, $_delivery.geraete, $_customer.firstname, $_customer.lastname, $_customer.postalcode, $_customer.city, $_customer.street, $_customer.housenumber, $_customer.phone, $_customer.email FROM $_delivery INNER JOIN $_customer ON $_delivery.c_id = $_customer.id WHERE $_delivery.id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $deliveryID);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $date, $beleg, $geraete, $firstname, $lastname, $plz, $city, $street, $hnr, $phone, $mail);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return [$id, $date, $beleg, $geraete, $firstname, $lastname, $plz, $city, $street, $hnr, $phone, $mail];
    } else {
        echo "notPossible";
    }
}
?>