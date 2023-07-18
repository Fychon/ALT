<!-- CALENDAR
COMPONENT for newDelivery.php, deliveryPlan.php and supTourSettings.php
zeigt auswählbare Wochentage in einer Kalendarform als HTML Tabelle an.
Monate können gewechselt werden, der Jahreswechsel funktioniert automatisch.
Die Wochentage zu Beginn und Ende eines Monats, die nicht durch den eigentlichen Monat abgedeckt werden
(z.B.) wenn der 01 eines Monats ein Donnerstag, die Wochentage Montag bis Mittwoch, mit den passenden Daten des Vor-
und Nachmonats aufgefüllt. Die Kalendarwoche wird zu der jeweiligen Woche angezeigt.-->
<div class="calendar">
    <?php

    //Prüfuen ob ein Monat o. Jahr gesetzt ist, falls nein vom aktuellen Datum beziehen. 
    //Der Tag wird manuell in deliveryPlan.php und newDelivery.php gesetzt.
    //Er ist nicht nötig bei supTourSettings.php, da hier die komplette Woche selektiert wird.
    if (!isset($_POST["month"])) {
        $actualMonth = date('m');
    } else {
        $actualMonth = $_POST["month"];
    }
    if (!isset($_POST["year"])) {
        $actualYear = date('Y');
    } else {
        $actualYear = $_POST["year"];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Wechsel eines Monats, prüfen falls 0 oder 13, für automatischen Jahreswechsel.
        if (isset($_POST['<'])) {
            if ($actualMonth == 1) {
                $actualYear--;
                $actualMonth = 12;
            } else {
                $actualMonth--;
            }
        } else if (isset($_POST['>'])) {
            if ($actualMonth == 12) {
                $actualYear++;
                $actualMonth = 1;
            } else {
                $actualMonth++;
            }
        }
    }
    //Anzahl Tage des Vormonats, Spezialfall: Januar[1] -> Vormonat: Dezember[12]
    if ($actualMonth == 1) {
        $actualDaysinPrevMonth = cal_days_in_month(CAL_GREGORIAN, 12, $actualYear - 1);
    } else {
        $actualDaysinPrevMonth = cal_days_in_month(CAL_GREGORIAN, $actualMonth - 1, $actualYear);
    }
    //Anzahl Tage des aktuellen Monats
    $actualDaysinMonth = cal_days_in_month(CAL_GREGORIAN, $actualMonth, $actualYear);
    //Datum des 01 für den Monat im aktuellen Jahr um Wochentag zu bestimmen.
    $firstDayInWeek = cal_to_jd(CAL_GREGORIAN, $actualMonth, '1', $actualYear);
    //ID des heutigen Tages.
    $actualDay = date('d');
    //ID des Wochentag für den 01 für den Monat z.B 0=Sonntag, 1=Montag,,
    $dayStart = jddayofweek($firstDayInWeek);
    //Nein, die Woche beginnt mit Montag: Sonntag -> 1.Tag!
    if ($dayStart == 0) {
        $dayStart = 7;
    }
    //Für supTourSettings.php, Kalendarwoche ausgewählt -> kein selektierbarer Tag, 
    if (isset($selectedKW)) {
        $selectedDay = null;
    } else {
        // falls nicht bilde das aktuelle Datum, selectedDay kommt aus deliveryPlan.php und newDelivery.php
        $selectedDDate = new DateTime($actualYear . "-" . $actualMonth . "-" . $selectedDay);
        $selectedDate = $selectedDDate->format("Y-m-d");
    }
    ?>

    <form class="monthLabel" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="submit" name="<" value="<">
        <input type="hidden" name="year" value="<?php echo ($actualYear); ?>">
        <input type="hidden" name="month" value="<?php echo ($actualMonth); ?>">
        <p>
            <?php
            echo jdmonthname($firstDayInWeek, 0);
            ?>
        </p>
        <input type="submit" name=">" value=">">
    </form>
     <!-- onSubmit="return clickedDay()" -->
    <form id="calendar" name="calendar" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <table class="cal">
            <thead>
                <tr>
                    <th class="kw">Kw</th>
                    <th>Mo</th>
                    <th>Di</th>
                    <th>Mi</th>
                    <th>Do</th>
                    <th>Fr</th>
                    <th>Sa</th>
                    <th>So</th>
                </tr>
            </thead>
            <tbody>
                <?php
                echo ('<input type="hidden" name="year" value="' . $actualYear . '">');
                echo ('<input type="hidden" name="month" value="' . $actualMonth . '">');
                echo ('<input type="hidden" name="markt" value="' . $markt . '">');
                $nextMonthCounter = 1;
                $counter = 1;
                $dayCounter = 1;
                $daysInMonthLeft = true;
                $daysLeft = true;
                $rowCounter = 1;
                $rowHasSpace = true;


                //Lieferinformationen von Monat bekommen. (id, date, gesamtAnzahl, gebucht, vormittags, ag_id, etc. )
                $monthOverview = getDeliveryMonthOverview($markt, $actualMonth, $actualYear);
                //Zoneninformationen bekommen. (id, plz, beschreibung)
                $allZones = getAllZones($markt);

                //solange Platz $$ das max Datum nicht erreicht ist soll der Kalendar gefüllt werden
                while ($daysLeft) {
                    $ddate = ($actualYear . '-' . $actualMonth . '-' . $dayCounter);
                    $date = new DateTime($ddate);
                    $kw = $date->format("W");
                    //KW berechnen,  falls KW bereits ausgewählt  mit der richtigen Klasse  erstellen
                    if ($kw == $selectedKW) {
                        echo ('<tr class="selected">');
                    } else {
                        echo ("<tr>");
                    }
                    //es muss jede Reihe/Row/Kalendarwoche (KW + 7 Wockentage = 8 Spalten) geschlossen werden
                    while ($rowHasSpace && $daysInMonthLeft || $daysInMonthLeft) {
                        //Falls Reihe voll, tr schließen, Counter zurücksetzen
                        if (!$rowHasSpace) {
                            echo ("</tr>");
                            $rowCounter = 1;
                            $rowHasSpace = true;
                            $ddate = ($actualYear . '-' . $actualMonth . '-' . $dayCounter);
                            $date = new DateTime($ddate);
                            $kw = $date->format("W");
                            //um wieder KW berechnen,  falls KW bereits ausgewählt  mit der richtigen Klasse  erstellen
                            if ($kw == $selectedKW) {
                                echo ('<tr class="selected">');
                            } else {
                                echo ("<tr>");
                            }
                        }
                        //In der ersten Spalte wird die Kalendarwoche ausgegeben.
                        if ($rowCounter == 1) {
                            $ddate = ($actualYear . '-' . $actualMonth . '-' . $dayCounter);
                            $date = new DateTime($ddate);
                            $kw = $date->format("W");
                            echo ('<td class="kw"><input type="submit" name="kw" value="' . $kw . '"></td>');
                            //wenn die Schleife über die ID des 01 Wochentages gekommen ist (Montag=1, ..., Sonntag=7) beginnt der eigentliche Monat.
                        } else if ($counter >= $dayStart) {
                            $date = date_create($actualYear . '-' . $actualMonth . '-' . $dayCounter);
                            $ddate = date_format($date, "Y-m-d");
                            //Lieferdaten von aktuellen Tage bekommen und in Array verpacken.
                            $arr = explode("---", $monthOverview[$ddate]);
                            //hälfte der maximalen Auslastung für 50% Grenze
                            $halfAmount = ($arr[0] / 2);
                            //Prüfen ob wir bin der Vergangenheit sind.
                            if ($ddate < date("Y-m-d")) {
                                //Prüfen ob es Daten zu diesen Tag gibt, sonst muss es kein Button sein.
                                if (isset($monthOverview[$ddate]) && ($arr[2] != 0)) {
                                    //Prüfen ob der Tag ausgewählt ist, wenn ja mit Border etc. versehen.
                                    if(isset($selectedDay) && $dayCounter == $selectedDay){
                                        echo ('<td id="' . $dayCounter . '" class="pastInMonth selected"><input" type="submit" name="day" value="' . $dayCounter . '"></td>');
                                    } else {
                                        echo ('<td id="' . $dayCounter . '" class="pastInMonth"><input type="submit" name="day" value="' . $dayCounter . '"></td>');
                                    }
                                } else {
                                    echo ('<td class="pastInMonth">' . $dayCounter . '</td>');
                                }

                            } else {
                                if (isset($selectedDay) && $dayCounter == $selectedDay && $actualMonth == date('m') && $actualYear == date('Y')) {
                                    if ($dayFreeAmount < $halfAmount && $dayFreeAmount > 0) {
                                        echo ('<td id="' . $dayCounter . '" class="halfbooked selected"><input type="submit" name="day" value="' . $dayCounnextInMontter . '"></td>');
                                    } else {
                                        echo ('<td id="' . $dayCounter . '" class="free selected"><input type="submit" name="day" value="' . $dayCounter . '"></td>');
                                    }
                                } else {
                                    if (isset($monthOverview[$ddate])) {
                                        $arr = explode("---", $monthOverview[$ddate]);
                                        if ($arr[0] == 0) {
                                            echo ('<td class"nodata">' . $dayCounter . '</td>');
                                        } else {
                                            $dayFreeAmount = ($arr[0] * $arr[1]) - $arr[2];
                                            $halfAmount = ($arr[0] * $arr[1]) / 2;
                                            $zonesFromDay = explode(",", $arr[5]);
                                            $gebieteDes = "";
                                            foreach ($zonesFromDay as $z) {
                                                $gebieteDes = $gebieteDes . explode("---", $allZones[$z])[1];
                                                $gebieteDes = $gebieteDes . ", ";
                                            }
                                            $gebieteDes = rtrim($gebieteDes, ", ");
                                            if (($arr[3] * $arr[0] - $arr[4]) > $dayFreeAmount) {
                                                $freeBeforeNoon = $dayFreeAmount;
                                            } else {
                                                $freeBeforeNoon = ($arr[3] * $arr[0] - $arr[4]);
                                            }
                                            if ($dayFreeAmount < $halfAmount && $dayFreeAmount > 0) {
                                                echo ('<td id="' . $dayCounter . '" class="halfbooked"><input type="submit" name="day" value="' . $dayCounter . '">
                                                <div>
                                                    <span class="tooltipInfos">
                                                        <p class="zone">Liefergebiet:<br>' . $gebieteDes . '</p>
                                                        <p class="title">gesamt:</p>
                                                        <p>' . $dayFreeAmount . ' frei (' . $arr[2] . '/' . ($arr[0] * $arr[1])  . ')</p>
                                                        <p class="title">davon vormittags:</p>
                                                        <p>' . ($freeBeforeNoon) . ' frei (' . $arr[4] .  '/' . ($freeBeforeNoon) . ')</p>
                                                    </span>
                                                </duv>
                                                </td>
                                                ');
                                            } else if ($dayFreeAmount > 0) {
                                                echo ('<td id="' . $dayCounter . '" class="free"><input type="submit" name="day" value="' . $dayCounter . '">
                                                <div>
                                                    <span class="tooltipInfos">
                                                        <p class="zone">Liefergebiet:<br>' . $gebieteDes . '</p>
                                                        <p class="title">gesamt:</p>
                                                        <p>' . $dayFreeAmount . ' frei (' . $arr[2] . '/' . ($arr[0] * $arr[1])  . ')</p>
                                                        <p class="title">davon vormittags:</p>
                                                        <p>' . ($freeBeforeNoon) . ' frei (' . $arr[4] .  '/' . ($arr[3] * $arr[0]) . ')</p>
                                                    </span>
                                                </duv>
                                                </td>
                                                ');
                                            } else {
                                                echo ('<td id="' . $dayCounter . '" class="booked"><input type="submit" name="day" value="' . $dayCounter . '"></td>');
                                            }
                                        }
                                    } else {
                                        echo ('<td class"nodata">' . $dayCounter . '</td>');
                                    }
                                }
                            }

                            $dayCounter++;
                        } else {
                            echo ('<td class="pastInMonth"><input type="submit" name="pastday" value="' . ($actualDaysinPrevMonth - $dayStart + $rowCounter) . '"></td>');
                        }
                        if ($rowCounter != 1) {

                            if ($dayCounter <= $actualDaysinMonth) {
                                $counter++;
                            } else {
                                $daysInMonthLeft = false;
                            }
                        }
                        if ($rowCounter <= 7) {
                            $rowCounter++;
                        } else {
                            $rowHasSpace = false;
                            if (!$daysInMonthLeft) {
                                $daysLeft = false;
                            }
                        }
                    }
                    while ($rowHasSpace && $daysLeft) {
                        echo ('<td class="nextInMonth">' . $nextMonthCounter . '</td>');
                        if ($rowCounter <= 7) {
                            $rowCounter++;
                        } else {
                            echo ("</tr>");
                            $daysLeft = false;
                        }
                        $nextMonthCounter++;
                    }
                }
                ?>
            </tbody>
        </table>
    </form>
    <!-- <script>
        const days = document.getElementsByName("day");
        var selectedDay = <?php echo($selectedDay) ?>;
        days.forEach(element => {
            element.addEventListener("click", function(element){
                document.getElementById(element.target.value).classList.add("selected");
                document.getElementById(selectedDay).classList.remove("selected");
                selectedDay = element.target.value;

                saveDay();
            });
        });

        function saveDay() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "/deliveryPlan.php", false);

            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                // do something to response
                console.log(this.responseText);
            };
            let stringData = "day=" + selectedDay;
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
    </script> -->
</div>