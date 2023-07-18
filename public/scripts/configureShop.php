<?php
//Beinhaltet alle Funktionen für Datenbankveränderung, Dokumentenerstellung und Formatierungsmaßnahmen
require "configureDB.php";
//Löschen eines Benutzers anhand der UserID.
function deleteUser($userID)
{
    global $link;
    $stmt = mysqli_prepare($link, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

//Erstellung eines Benutzers in der Datenbank mit allen erforderten Parametern und der Berechtigungsstufe.
function createUser($marktID, $vorname, $nachname, $email, $username, $passwd, $permission)
{
    global $link;
    $hashed_password = password_hash($passwd, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, passwd, email, firstname, lastname, shopID, permission) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssi", $username, $hashed_password, $email, $vorname, $nachname, $marktID, $permission);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

//Erstellung eines neuen Lieferanten in der Datenbank.
function createSupplier($marktID, $vorname, $nachname, $email, $tel)
{
    global $link;
    $tablename = mysqli_real_escape_string($link, $marktID . "_supplier");
    $sql = "INSERT INTO $tablename (firstname, lastname, email, phone) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $vorname, $nachname, $email, $tel);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
//Löschen eines bekannten Lieferanten in der Datenbank.
function deleteSupplier($marktID, $supID)
{
    global $link;
    $tablename = mysqli_real_escape_string($link, $marktID . "_supplier");
    $stmt = mysqli_prepare($link, "DELETE FROM $tablename WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $supID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

//Anlegung eines neuen Markts. Aufruf zum Ertellen des Marktadmin und Generierung aller benötigten Tabellen mit Default Werten.
function createShop($markt_referenz, $markt_beschreibung, $markt_plz, $markt_stadt, $markt_strasse, $markt_hausnr, $markt_kontakt_username, $markt_kontakt_vorname, $markt_kontakt_nachname, $markt_kontakt_email, $markt_kontakt_password)
{
    global $link;

    // Insert shop information into the 'shops' table
    $sql = "INSERT INTO `shops` (`shop_ref`, `name`, `postalcode`, `city`, `street`, `number`) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $markt_referenz, $markt_beschreibung, $markt_plz, $markt_stadt, $markt_strasse, $markt_hausnr);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Create user for the shop with permission level 1 (admin)
    $err = createUser($markt_referenz, $markt_kontakt_vorname, $markt_kontakt_nachname, $markt_kontakt_email, $markt_kontakt_username, $markt_kontakt_password, 1);
    if (isset($err)) {
        return $err;
    }

    // Create tables for the new shop
    $err = createTables($markt_referenz);
    if (isset($err)) {
        return $err;
    }
}

//Erstellung aller Tabellen für einen Markt. Füllung mit Default Werten für Tourgröße.
function createTables($markt_referenz)
{
    global $link;
    $customer = $markt_referenz . "_customer";
    $supplier = $markt_referenz . "_supplier";
    $zone = $markt_referenz . "_zone";
    $tour = $markt_referenz . "_tour";
    $deliverySchedule = $markt_referenz . "_deliverySchedule";
    $car = $markt_referenz . "_car";
    $tourSchedule = $markt_referenz . "_tourSchedule";
    $delivery = $markt_referenz . "_delivery";
    $_messages = $markt_referenz . "_messages";

    $sql = "CREATE TABLE $customer (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(30) NOT NULL,
        lastname VARCHAR(30) NOT NULL,
        email VARCHAR(50),
        phone VARCHAR(50),
        street VARCHAR(50),
        housenumber VARCHAR(50),
        postalcode VARCHAR(50),
        city VARCHAR(50)
    );

    CREATE TABLE $supplier (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(30) NOT NULL,
        lastname VARCHAR(30) NOT NULL,
        email VARCHAR(50),
        phone VARCHAR(50)
    );

    CREATE TABLE $zone (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        description VARCHAR(30) NOT NULL,
        postalcode VARCHAR(255)
    );

    CREATE TABLE $tour (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        allAmount INT(6) NOT NULL,
        beforeNoon INT(6) NOT NULL
    );

    INSERT INTO $tour (allAmount, beforeNoon) VALUES (10, 4);

    CREATE TABLE $deliverySchedule (
        id DATE PRIMARY KEY,
        amountAllCars INT(6) NOT NULL,
        ag_ids VARCHAR(244) NOT NULL, 
        amountAllBooked INT(6),
        amountBeforeNoonBooked INT(6)
    );

    CREATE TABLE $car (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        licensePlate VARCHAR(244) NOT NULL,
        description VARCHAR(244) NOT NULL
    );

    CREATE TABLE $tourSchedule (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        dS_id DATE NOT NULL,
        car_id INT(6) UNSIGNED,
        employee_id1 INT(6) UNSIGNED,
        employee_id2 INT(6) UNSIGNED,
        FOREIGN KEY (employee_id1) REFERENCES $supplier(id),
        FOREIGN KEY (employee_id2) REFERENCES $supplier(id),
        FOREIGN KEY (car_id) REFERENCES $car(id),
        FOREIGN KEY (dS_id) REFERENCES $deliverySchedule(id)
    );

    CREATE TABLE $delivery (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        lieferdatum DATE,
        t_id INT(6) UNSIGNED,
        c_id INT(6) UNSIGNED NOT NULL,
        geraete VARCHAR(244) NOT NULL,
        hinweis VARCHAR(244),
        orderState INT(11),
        belegnummer INT(44) NOT NULL,
        isBeforeNoon BOOLEAN,
        FOREIGN KEY (t_id) REFERENCES $tourSchedule(id),
        FOREIGN KEY (lieferdatum) REFERENCES $deliverySchedule(id),
        FOREIGN KEY (c_id) REFERENCES $customer(id),
        FOREIGN KEY (orderState) REFERENCES deliveryStates(id)
    );
        CREATE TABLE $_messages (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                d_id INT(6) UNSIGNED,
                create_date date,
                create_time time,
                create_user_id int,
                messageText TEXT,
                FOREIGN KEY (d_id) REFERENCES $delivery(id),
                FOREIGN KEY (create_user_id) REFERENCES users(id)
                );";
    if (mysqli_multi_query($link, $sql)) {
    } else {
        return false;
    }
}

//Löschen eines Marktes, aller User des Marktes und aller Tabellen des Marktes.
function deleteTables($markt)
{
    global $link;

    // Tabellen löschen
    $tableNames = [
        $markt . "_car",
        $markt . "_customer",
        $markt . "_tour",
        $markt . "_zone",
        $markt . "_supplier",
        $markt . "_deliverySchedule",
        $markt . "_tourSchedule",
        $markt . "_messages",
        $markt . "_delivery"
    ];

    // Foreign Key Check deaktivieren
    mysqli_query($link, "SET FOREIGN_KEY_CHECKS = 0");

    // Tabellen löschen
    foreach ($tableNames as $tableName) {
        $sql = "DROP TABLE IF EXISTS `$tableName`";
        mysqli_query($link, $sql);
    }

    // Foreign Key Check aktivieren
    mysqli_query($link, "SET FOREIGN_KEY_CHECKS = 1");

    // Tabellen shops und users löschen
    $stmt = mysqli_prepare($link, "DELETE FROM shops WHERE shop_ref = ?");
    mysqli_stmt_bind_param($stmt, "s", $markt);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($link, "DELETE FROM users WHERE shopID = ?");
    mysqli_stmt_bind_param($stmt, "s", $markt);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

//Abfrage aller Lieferzonen eines Marktes
function getAllZones($markt)
{
    global $link;
    $tablename = $markt . "_zone";
    $ret = [];

    $stmt = mysqli_prepare($link, "SELECT id, description, postalcode FROM $tablename");
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $beschreibung, $plz);

    while (mysqli_stmt_fetch($stmt)) {
        $ret[$id] = "$id---$beschreibung---$plz";
    }

    mysqli_stmt_close($stmt);

    return $ret;
}

//Erstellung eines neuen Autos in einem neuen Markt. Beschreibung und Kennzeichen werden abgespeichert.
function newCar($markt, $licensePlate, $description)
{
    global $link;
    $tablename = $markt . "_car";
    $sql = "INSERT INTO $tablename (licensePlate, description) VALUES (?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $licensePlate, $description);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
    } else {
        return "Can't create a new car";
    }
}

//Löschen eines existierenden Autos.
function deleteCar($markt, $carID)
{
    global $link;
    $tablename = $markt . "_car";
    $sql = "DELETE FROM $tablename WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $carID);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
    } else {
        return "Can't delete car";
    }
}

//Erstellung einer neuen Lieferzone für einen Markt.
function newZone($markt, $liefergebiet, $plz)
{
    global $link;

    $tablename = $markt . "_zone";
    $sql = "INSERT INTO $tablename (description, postalcode) VALUES (?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $liefergebiet, $plz);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
    } else {
        return "Can't create a new zone";
    }
}

//Entfernen einer  Lieferzone für einen Markt.
function deleteZone($markt, $zoneID)
{
    global $link;
    $tablename = $markt . "_zone";
    $sql = "DELETE FROM $tablename WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $zoneID);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
    } else {
        return "Can't delete zone";
    }
}

//Veränderung der Tourgröße.
function updateTour($markt, $anzahl, $anzahlVormittags)
{
    global $link;
    $tablename = $markt . "_tour";

    $sql = "UPDATE $tablename SET allAmount = ?, beforeNoon = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $anzahl, $anzahlVormittags);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
    }
}

//Veränderung der Tourplanung. Auslieferungsgebiet, Anzahl der Touren für einen Tag werden abgespeichert.
function updateTourScheduleForDay($markt, $anzahl, $gebiet, $date)
{
    global $link;
    $tablename = $markt . "_deliverySchedule";

    $sql = "SELECT COUNT(*) FROM $tablename WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $date);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $num_rows);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($num_rows == 0) {
        $sql = "INSERT INTO $tablename (id, amountAllCars, ag_ids, amountAllBooked, amountBeforeNoonBooked) VALUES (?, ?, ?, 0, 0)";
        $stmt = mysqli_prepare($link, $sql);
        $formattedDate = (new DateTime($date))->format("Y-m-d");
        mysqli_stmt_bind_param($stmt, "sss", $formattedDate, $anzahl, $gebiet);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
        } else {
            echo "error404";
        }
    } else {
        $sql = "UPDATE $tablename SET amountAllCars = ?, ag_ids = ? WHERE id = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $anzahl, $gebiet, $date);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
        } else {
            echo "error405";
        }
    }
}

//Abfrage um alle Tourdaten wie Auslieferungsgebiet, Anzahl der Touren für eine komplette Kalendarwoche in einem Jahr.
function getTourevorlageForWeek($markt, $kw, $year)
{
    $startAndEnd = getDatesOfWeek($year, $kw);
    $ret = [];

    foreach ($startAndEnd as $day) {
        $ret[] = getTourvorlageForDay($markt, $day);
    }

    return $ret;
}

//Abfrage um alle Tourdaten wie Auslieferungsgebiet, Anzahl der Touren für einen Tag.
function getTourvorlageForDay($markt, $date)
{
    global $link;
    $tablename = $markt . "_deliverySchedule";
    $stmt = mysqli_prepare($link, "SELECT id, amountAllCars, ag_ids FROM $tablename WHERE id = ?");
    $formattedDate = (new DateTime($date))->format("Y-m-d");
    mysqli_stmt_bind_param($stmt, "s", $formattedDate);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $anzahlTouren, $agID);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return "$id---$anzahlTouren---$agID";
}

//Berechnung einzelner Daten einer kompletten Kalendarwoche.
function getDatesOfWeek($year, $week)
{
    return [
        (new DateTime())->setISODate($year, $week)->format('Y-m-d'), //start date
        (new DateTime())->setISODate($year, $week, 2)->format('Y-m-d'), //start date
        (new DateTime())->setISODate($year, $week, 3)->format('Y-m-d'), //start date
        (new DateTime())->setISODate($year, $week, 4)->format('Y-m-d'), //start date
        (new DateTime())->setISODate($year, $week, 5)->format('Y-m-d'), //start date
        (new DateTime())->setISODate($year, $week, 6)->format('Y-m-d'), //start date
        (new DateTime())->setISODate($year, $week, 7)->format('Y-m-d') //end date
    ];
}

//Abfrage für Tourgröße und Anzahl Vormittags.
function getTourData($markt)
{
    global $link;
    $_tour = $markt . "_tour";
    $sql = "SELECT allAmount, beforeNoon, id FROM $_tour";
    $stmt = mysqli_prepare($link, $sql);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $amount, $amountBeforeNoon, $id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return [$amount, $amountBeforeNoon, $id];
    } else {
        return "Couldn't retrieve tour data";
    }
}

//Abfrage eines kompletten Monats für den Kalendar. Es wird die Auslastung und angefahrene Liefergebiete jedes Tages eines Monats berechnet.
function getDeliveryMonthOverview($markt, $month, $year)
{
    global $link;

    $tourData = getTourData($markt);

    $_deliverySchedule = $markt . "_deliverySchedule";
    $ret = [];
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    $firstDay = $year . "-" . $month . "-01";
    $lastDay = $year . "-" . $month . "-" . $daysInMonth;
    $sql = "SELECT id, amountAllCars, amountAllBooked, amountBeforeNoonBooked, ag_ids FROM $_deliverySchedule WHERE id BETWEEN ? AND ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $firstDay, $lastDay);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $gesamt, $gebucht, $bookedBeforeNoon, $ag);
        while (mysqli_stmt_fetch($stmt)) {
            $ret[$id] = $gesamt . "---" . $tourData[0] . "---" . $gebucht . "---" . $tourData[1] . "---" . $bookedBeforeNoon . "---" . $ag;
        }
        mysqli_stmt_close($stmt);
        return $ret;
    } else {
        mysqli_stmt_close($stmt);
        return "Not possible to retrieve delivery month overview";
    }
}

//Erstellung einer neuen Lieferung.
function newDelivery($markt, $customer_id, $deliveryDate, $geraete, $belegnummer)
{
    global $link;
    $_delivery = $markt . "_delivery";
    $formattedDate = (new DateTime($deliveryDate))->format("Y-m-d");

    $sql = "INSERT INTO $_delivery (c_id, lieferdatum, geraete, belegnummer, orderState) VALUES (?, ?, ?, ?, 2)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "issi", $customer_id, $formattedDate, $geraete, $belegnummer);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
    } else {
        return "Not possible to create new delivery";
    }

    $_deliverySchedule = $markt . "_deliverySchedule";
    $sql = "UPDATE $_deliverySchedule SET amountAllBooked = amountAllBooked + 1 WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $deliveryDate);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
    } else {
        return "Not possible to update delivery schedule";
    }
}

//Abspeicherung der Tour Einteilung. (Einteilung von Lieferung in ein Auto).
function saveTourScheduleForDeliverys($markt, $deliveryID, $tourID, $date)
{
    $retTour = checkIfTourExistsForDay($markt, $date);
    $_tourSchedule = $markt . "_tourSchedule";

    global $link;

    $sql = "INSERT INTO $_tourSchedule (dS_id) VALUES (?)";

    while ($tourID > sizeof($retTour) || $retTour == "none") {
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "s", $date);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            $retTour = checkIfTourExistsForDay($markt, $date);
        } else {
            return "false2";
        }
    }

    return saveDeliveryInTourSchedule($markt, $deliveryID, $retTour[$tourID - 1]);
}

//Einzelne Lieferung wird einzelnem Auto zugeteilt.
function saveDeliveryInTourSchedule($markt, $deliveryID, $tourID)
{
    global $link;
    $_delivery = $markt . "_delivery";
    $sql = "UPDATE $_delivery SET t_id = ? WHERE id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $tourID, $deliveryID);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return "true";
    } else {
        return "false";
    }
}

//Prüfung ob bereits ein Auto/Tour für den ausgewählten Tag existiert und wenn ja wie viele.
function checkIfTourExistsForDay($markt, $date)
{
    global $link;
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

//Erstellung eines neuen Kunden, Wird aufgerufen bevor die Lieferung abgespeichert wird.
function newCustomer($markt, $firstname, $lastname, $street, $housenumber, $postalcode, $city, $phone, $email)
{
    global $link;
    $_customer = $markt . "_customer";
    $sql = "INSERT INTO $_customer (firstname, lastname, street, housenumber, postalcode, city, phone, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $firstname, $lastname, $street, $housenumber, $postalcode, $city, $phone, $email);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        $id = mysqli_insert_id($link);
        return $id;
    } else {
        mysqli_stmt_close($stmt);
        return "notPossible";
    }
}

//Alle Lieferungen eines Tages. Wird genutzt für die Lieferübersicht.
function getDeliverysForDay($markt, $date)
{
    global $link;
    $_delivery = $markt . "_delivery";
    $_customer = $markt . "_customer";
    $ret = [];
    $sql = "SELECT $_delivery.id, $_delivery.belegnummer, $_delivery.geraete, $_customer.firstname, $_customer.lastname, $_customer.postalcode, $_customer.city, $_customer.street, $_customer.housenumber FROM $_delivery INNER JOIN $_customer ON $_delivery.c_id = $_customer.id WHERE $_delivery.lieferdatum = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $date);

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

//Abfrage alles Lieferungen die bereits in der Tour abgespeichert sind.
function getSortedDeliverysForTour($markt, $tour)
{
    global $link;
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

//Alle Lieferungen eines Tages, die noch nicht eingeteilt wurden.
function getUnsortedDeliverysForDay($markt, $date)
{
    global $link;
    $_delivery = $markt . "_delivery";
    $_customer = $markt . "_customer";
    $ret = [];
    $sql = "SELECT $_delivery.id, $_delivery.belegnummer, $_delivery.geraete, $_customer.firstname, $_customer.lastname, $_customer.postalcode, $_customer.city, $_customer.street, $_customer.housenumber FROM $_delivery INNER JOIN $_customer ON $_delivery.c_id = $_customer.id WHERE $_delivery.lieferdatum = ? AND $_delivery.t_id IS NULL";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $date);

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

//Abfrage der eigenen Benutzerdaten: Vorname, Nachname, Rolle, E-Mail, Shopreferenz.
function getOwnUserData()
{
    global $link;
    $uname = $_SESSION['username'];

    $sql = "SELECT id, username, email, firstname, lastname, permission, shopID FROM users WHERE username = ?";
    $stmt = mysqli_prepare($link, $sql);
    if (mysqli_stmt_bind_param($stmt, "s", $uname)) {
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $id, $username, $email, $firstname, $lastname, $permission, $shopID);
            if (mysqli_stmt_fetch($stmt)) {
                mysqli_stmt_close($stmt);
                return [$id, $username, $email, $firstname, $lastname, $permission, $shopID];
            } else {
                mysqli_stmt_close($stmt);
                return "No data found";
            }
        } else {
            mysqli_stmt_close($stmt);
            return "Not possible to fetch user data";
        }
    }
}

//Informationen einer bestimmten Leiferung.
function getDeliveryData($markt, $deliveryID)
{
    global $link;
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

//Abfrage alle Memos zu einer Lieferung erhalten.
function getMessagesForDelivery($markt, $deliveryID)
{
    global $link;
    $_messages = $markt . "_messages";
    $messageArr = [];
    $sql = "SELECT $_messages.id, $_messages.create_date, $_messages.create_time, users.username, $_messages.messageText FROM $_messages INNER JOIN users ON $_messages.create_user_id = users.id WHERE $_messages.d_id = ? ORDER BY $_messages.create_date, $_messages.create_time DESC";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $deliveryID);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $date, $time, $user, $message);
        while (mysqli_stmt_fetch($stmt)) {
            $messageArr[$id] = $date . "(-(-)-)" . $time . "(-(-)-)" . $user . "(-(-)-)" . $message;
        }
    }
    mysqli_stmt_close($stmt);
    return $messageArr;
}

//Abspeicherung einer Bemerkung/Memo zu einen Lieferauftrag.
function saveMessageForDelivery($markt, $deliveryID, $newMessage)
{
    global $link;
    $actualDate = date("Y-m-d");
    $actualTime = date("H:i:s");
    $createUser = getOwnUserData()[0];
    $_messages = $markt . "_messages";
    $sql = "INSERT INTO $_messages (d_id, create_date, create_time, create_user_id, messageText) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sssis", $deliveryID, $actualDate, $actualTime, $createUser, $newMessage);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
    }
}

//SQL Abfrage für die Suchleiste. Suchinput wird mit Vorname, Nachnamen und Belegnummer verglichen.
function searchWithKeyword($markt, $searchInput)
{
    $searchResults = [];
    global $link;
    $_delivery = $markt . "_delivery";
    $_customer = $markt . "_customer";
    $sql = "SELECT $_delivery.id, $_delivery.belegnummer, $_delivery.geraete, $_customer.firstname, $_customer.lastname, $_customer.postalcode, $_customer.city, $_customer.street, $_customer.housenumber FROM $_delivery INNER JOIN $_customer ON $_delivery.c_id = $_customer.id WHERE $_customer.firstname = ? OR $_customer.lastname = ? OR $_delivery.belegnummer = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $searchInput, $searchInput, $searchInput);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $id, $beleg, $geraete, $fistname, $lastname, $postal, $city, $street, $hnumber);
        while (mysqli_stmt_fetch($stmt)) {
            $deviceArray = explode("()()()()", $geraete);
            $searchResults[$id] = $id . "(-(-)-)" . $beleg . "(-(-)-)" . $deviceArray[0] . "(-(-)-)" . $deviceArray[1] . "(-(-)-)" . $fistname . "(-(-)-)" . $lastname . "(-(-)-)" . $postal . "(-(-)-)" . $city . "(-(-)-)" . $street . "(-(-)-)" . $hnumber;
        }
    }
    mysqli_stmt_close($stmt);
    return $searchResults;
}
