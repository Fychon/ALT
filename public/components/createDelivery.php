<!-- createDelivery
Form inkl. Validierung in pHp für die Erstellung der Kundendaten und Gerätedaten einer neuen Lieferung.
Der Liefertag wird durch den Kalendar ausgewählt.-->

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST['anlegen'] == 'buchen') {
        $markt = $_POST['markt'];
        $c_firstname = trim($_POST['kundenNachname']);
        $c_lastname = trim($_POST['kundenVorname']);
        $c_phone = trim($_POST['telnr']);
        $c_email = trim($_POST['email']);
        $c_street = trim($_POST['strasse']);
        $c_housenumber = trim($_POST['hausnr']);
        $c_postalcode = trim($_POST['plz']);
        $c_city = trim($_POST['stadt']);
        $in_c_firstname = $in_c_lastname = $in_c_phone = $in_c_email = $in_c_street = $in_c_housenumber = $in_c_postalcode = $in_c_city = $in_artikel = $in_belegnummer = $in_geraete = $devicesString = "";
        $contact_err = $device_err = $beleg_err = "";
        if ((empty($c_firstname))) {
            $contact_err = "Bitte Vorname eingeben.";
        } else {
            $in_c_firstname = $c_firstname;
        }
        if ((empty($c_lastname))) {
            $contact_err = "Bitte Nachname eingeben.";
        } else {
            $in_c_lastname = $c_lastname;
        }
        if ((empty($c_phone))) {
            $contact_err = "Bitte Telefonnummer eingeben.";
        } else {
            $in_c_phone = $c_phone;
        }
        if ((empty($c_email))) {
            $contact_err = "Bitte E-Mail Adresse eingeben.";
        } else {
            $in_c_email = $c_email;
        }
        if ((empty($c_street))) {
            $contact_err = "Bitte Strasse eingeben.";
        } else {
            $in_c_street = $c_street;
        }
        if ((empty($c_housenumber))) {
            $contact_err = "Bitte Hausnummer eingeben.";
        } else {
            $in_c_housenumber = $c_housenumber;
        }
        if ((empty($c_postalcode))) {
            $contact_err = "Bitte Postleitzahl eingeben.";
        } else {
            $in_c_postalcode = $c_postalcode;
        }
        if ((empty($c_city))) {
            $contact_err = "Bitte Stadt eingeben.";
        } else {
            $in_c_city = $c_city;
        }
        $geraete = $artikel = [];
        $next = 0;
        $device = trim($_POST['artikelnr'.$next]);
        while(!empty($device)){
            $artikel[] = trim($_POST['artikelnr'.$next]);
            if(!empty(trim($_POST['geraete'.$next]))){
                $geraete[] = trim($_POST['geraete'.$next]);
            }
            $next++;
            if(isset($_POST['artikelnr'.$next])){
                $device = trim($_POST['artikelnr'.$next]);
            } else {
                $device = "";
            }

        }
        $belegnummer = trim($_POST['belegnummer']);
        if ((empty($artikel))) {
            $device_err = "Bitte Artikelnummer eingeben.";
        } else {
            foreach ($artikel as $art) {
                if(empty($in_artikel)){
                    $in_artikel = $art;
                } else {
                    $in_artikel = $in_artikel . "---" . $art;
                }
            }
            foreach ($geraete as $ger) {
                if(empty($in_geraete)){
                    $in_geraete = $ger;
                } else {
                    $in_geraete = $in_geraete . "---" . $ger;
                }

            }

            $devicesString = $in_artikel . "(--(-)--)" . $in_geraete;
        }
        if ((empty($belegnummer))) {
            $beleg_err = "Bitte Belegnummer eingeben.";
        } else {
            $in_belegnummer = $belegnummer;
        }

    if (empty($contact_err) && empty($markt_err) && empty($adress_err)) {
        require_once("./scripts/configureShop.php");
        $newCustomerID = newCustomer($markt, $c_firstname, $c_lastname, $c_street, $c_housenumber, $c_postalcode, $c_city, $c_phone, $c_email);
        $deliveryDate = $_POST['date'];
        require_once("./scripts/configureShop.php");
        echo (newDelivery($markt, $newCustomerID, $deliveryDate, $devicesString, $belegnummer));

    } else {
        if (!empty($contact_err)) {
            $fill_err = $contact_err;
        }
        if (!empty($device_err)) {
            $fill_err = $device_err;
        }
        if (!empty($beleg_err)) {
            $fill_err = $beleg_err;
        }
    }
       

    } else if ($_POST['anlegen'] == 'abbrechen') {
        header("location: index.php");
        exit;
    }
    $date = date_create($actualYear . '-' . $actualMonth . '-' . $selectedDay);
    $selectedDeliveryDate = date_format($date, "Y-m-d");
}
?>


<div class="createDelivery">
    <form name="createDelivery" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return validate()">
        <input type="hidden" name="markt" value="<?php echo ($markt); ?>">
        <input type="hidden" name="date" value="<?php echo ($selectedDeliveryDate); ?>">
        <h4>Kundendaten:</h4>
        <div class="name">
            <input type="text" name="kundenVorname" placeholder="Vorname">
            <input type="text" name="kundenNachname" placeholder="Nachname">
        </div>
        <div class="address">
            <input type="text" name="strasse" id="strasse" placeholder="Strasse">
            <div class="numbers">
                <input type="text" name="hausnr" id="hausnr" placeholder="Haus-Nr.">
                <input type="text" name="etage" id="etage" placeholder="Stockwerk">
            </div>
            <input type="text" name="plz" id="plz" placeholder="PLZ">
            <input type="text" name="stadt" id="stadt" placeholder="Stadt">
        </div>
        <div class="contactInfo">
            <input type="text" name="email" placeholder="E-Mail">
            <input type="text" name="telnr" placeholder="Telefonnummer">
        </div>


        <h4>Belegnummer:</h4>
        <div class="belegnummer">
            <input type="text" name="belegnummer" id="belegnummer" placeholder="Belegnummer">
        </div>

        <h4>Gerätedaten:</h4>
        <div class="deviceListContainer">
            <div id="addDeviceList" class="addDeviceList">
                <input type="text" name="artikelnr0" id="artikelnr" placeholder="Artikel-Nr.">
                <input type="text" name="geraete0" id="geraete" placeholder="Bezeichnung">
                <input id="addButton" class="addDevice" type="button" value="+" onclick="addDeviceField()">
            </div>
        </div>

        <?php
        if (!empty($fill_err)) {
            echo '<div class="alert alert-danger">' . $fill_err . '</div>';
        }
        ?>
        <span>
            <input class="decline" type="submit" name="anlegen" id="abbrechen" value="zurück" />
            <input class="accept" type="submit" name="anlegen" id="anlegen" value="buchen" />
        </span>
        <script>
            var deviceCounter = 1;
            function addDeviceField(){
                let deviceListOld = document.getElementById("addDeviceList");
                let deviceListNew = deviceListOld.cloneNode(true);
                 deviceListOld.parentElement.appendChild(deviceListNew);
                 (deviceListOld.querySelector("#addButton")).remove();
                 deviceListNew.querySelector("#artikelnr").name = "artikelnr" + deviceCounter;
                 deviceListNew.querySelector("#geraete").name = "geraete" + deviceCounter;
                 deviceListOld.id = "addDeviceList" + deviceCounter
                 deviceListNew.id = "addDeviceList"
                 deviceCounter++;

            }
        </script>
    </form>
</div>