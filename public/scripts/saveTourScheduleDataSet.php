<!-- Skript für Ajax Aufrug aus setupDeliverySchedule
Eingeteilte Lieferungen für Datum werden anhand Tour abgespeichert. -->

<?php
    if(isset($_POST)){
        $markt = $_POST['markt'];
        $data = $_POST['data'];
        $date = $_POST['date'];
        $data = explode("/",$data);
        foreach ($data as $dataEntry) {
            $entryArray = explode("=", $dataEntry);
            // echo($entryArray[0].'<br>');
            // echo($markt);
            print_r($entryArray);
            require_once "configureShop.php";
            echo(saveTourScheduleForDeliverys($markt, $entryArray[0], $entryArray[1], $date));
        }
    }
?>