<?php
//Einteilung der Lieferungen auf einzelne Toruen
//Es werden alle nichtsortieren Touren mit dem SetUp Menü angezeigt, alle bereits sortierten Touren werden im jeweiligen Auto angezeigt.
//Der Stand kann abgespeichert werden, zu jedem Zeitpunkt kann aus den bereits sortierten Lieferungen die TourPDF generiert werden.

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST["generatePDF"])){
        require_once("./scripts/generatePDFS.php");
        $markt = $_POST['markt'];
        $date = $_POST['date'];
        generateTourPDFForDate($markt, $date);
    } else {
        $deliveryDate = $_POST['deliveryDate'];
        $markt = $_POST['markt'];
        require_once("./scripts/configureShop.php");
        $deliverys = getUnsortedDeliverysForDay($markt, $deliveryDate);
        $tours = getTourvorlageForDay($markt, $deliveryDate);
        $tours = explode("---", $tours);
        $tourData = getTourData($markt);
    }
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <title>Tourplan erstellen</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="/css/style.css" />
</head>

<body>
    <?php include("./components/header.php"); ?>
    <div class="setUpContainer">

        <div class="deliveryCardContainer">
            <?php
            $cardCounter = 1;
            foreach ($deliverys as $delivery) {
                $dataArray = explode("(-(-)-)", $delivery);
                // if ($dataArray[10] == null) {
            ?>
                <div id="deliveryCard<?php echo ($dataArray[0]); ?>" class="deliveryCard">
                    <div class="contactInfos">
                        <p><?php echo ($dataArray[4] . ", " . $dataArray[5]); ?></p>
                        <p><?php echo ($dataArray[8] . " " . $dataArray[9]); ?></p>
                        <p><?php echo ($dataArray[6] . " " . $dataArray[7]); ?></p>
                    </div>
                    <div class="deviceInfos">
                        <?php
                            $temp = explode("(--(-)--)", $dataArray[3]);
                            $artikel = explode("---", $temp[0]);
                            $geraete = explode("---", $temp[1]);
                            $gesString = "";
                            for ($i=0; $i < sizeof($artikel); $i++) {
                                echo("<p>". $artikel[$i] . " " . $geraete[$i] . "</p>"); 
                            }
                        ?>
                    </div>
                    <div class="setUpMenue">
                        <?php
                        for ($tourCounter = 1; $tourCounter <= $tours[1]; $tourCounter++) {
                        ?>
                            <div class="carCard" onclick="changeTourFromDelivery(<?php echo ($tourCounter . ', ' . $dataArray[0]); ?>)">
                                <p class="car">Auto</p>
                                <p><?php echo ($tourCounter); ?></p>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>

            <?php
                // } else {
                //     $counter = $dataArray[10]
                // }
                $cardCounter++;
            }
            ?>

            <button onclick="saveCars()">Save</button>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="submit" value="zur pdf" name="generatePDF">
                <input type="hidden" name="markt" value="<?php echo($markt);?>">
                <input type="hidden" name="date" value="<?php echo($deliveryDate);?>">
            </form>

        </div>
        <div class="carsContainer">
            <?php
            $ids = checkIfTourExistsForDay($markt, $deliveryDate);
            for ($tourCounter = 1; $tourCounter <= $tours[1]; $tourCounter++) {
                $sortedDeliverys = getSortedDeliverysForTour($markt, $ids[$tourCounter - 1]);
                $amountSorted = sizeof($sortedDeliverys);

            ?>
                <div id="carContainer<?php echo ($tourCounter); ?>" class="carContainer">

                    <span>
                        <p>Auto <?php echo ($tourCounter); ?>: </p>
                        <p id="counter"><?php echo($amountSorted);?>/<?php echo ($tourData[0]); ?></p>
                    </span>
                    <?php
                    if($ids != "none"){
                        foreach ($sortedDeliverys as $sortedDelivery) {
                            $dataArray = explode("(-(-)-)", $sortedDelivery);
                        ?>
                            <div id="deliveryCard<?php echo ($cardCounter); ?>" class="deliveryCard">
                                <div class="contactInfos">
                                    <p><?php echo ($dataArray[4] . ", " . $dataArray[5]); ?></p>
                                    <p><?php echo ($dataArray[8] . " " . $dataArray[9]); ?></p>
                                    <p><?php echo ($dataArray[6] . " " . $dataArray[7]); ?></p>
                                </div>
                            </div>
                        <?php
                        }
                    }
                    ?>
                </div>
            <?php
            }
            ?>
        </div>


    </div>

    <script type="text/javascript">
        var dataSet = {};
        let markt = '<?php echo ($markt); ?>';
        let selectedDate = '<?php echo ($deliveryDate); ?>';

        function changeTourFromDelivery(tourID, deliveryID) {
            console.log(deliveryID);
            console.log(tourID);

            if (!document.getElementById("deliveryCard" + deliveryID).classList.contains("inCar")) {
                dataSet[deliveryID] = tourID;
                document.getElementById("deliveryCard" + deliveryID).getElementsByClassName("setUpMenue")[0].remove();
                document.getElementById("carContainer" + tourID).appendChild(document.querySelector("#" + "deliveryCard" + deliveryID));

                var counterScoreSentence = document.getElementById("carContainer" + tourID).querySelector("#" + "counter").innerHTML;
                var counterScoreArray = counterScoreSentence.split("");
                var newScore = parseInt(counterScoreArray[0]) + 1;
                counterScoreSentence = newScore;
                for (let index = 1; index < counterScoreArray.length; index++) {
                    counterScoreSentence += counterScoreArray[index];
                }

                document.getElementById("carContainer" + tourID).querySelector("#counter").innerHTML = counterScoreSentence;
                document.getElementById("deliveryCard" + deliveryID).classList.add("inCar");
            }
        }

        function serialize(obj) {
            var str = [];
            for (var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            return str.join("/");
        }

        function saveCars() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "./scripts/saveTourScheduleDataSet.php", false);

            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                // do something to response
                console.log(this.responseText);
            };
            let stringArray = serialize(dataSet);
            let stringData = "markt=" + markt + "&date=" + selectedDate + "&data=" + stringArray;
            try {
                xhr.send(stringData);
                if (xhr.status != 200) {
                    alert(`Error ${xhr.status}: ${xhr.statusText}`);
                } else {}
            } catch (err) { // instead of onerror
                alert("Request failed");
            }
            return false;
        }
    </script>
</body>

</html>