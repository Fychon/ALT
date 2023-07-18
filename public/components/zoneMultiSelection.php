<!-- MultiSelect DropDown Menue for diffrent deliveryZones. Selected Zones should be shown selected, 
when clicked a dropdown menue will open and show all zones. Changes can be made in DropDown with CheckField. -->

<?php
require_once("./scripts/configureShop.php");
$zones = getAllZones($markt);
?>
<div class="zoneSelection">
    <span class="anchor">Zone auswählen</span>
    <ul class="items">

        <?php
        foreach ($zones as $tempZone) {
            $oneZone = explode("---", $tempZone);
        ?>
            <li>
                <input value="<?php echo("$oneZone[0]"); ?>" name="<?php echo("gebiet" . $dayCounter ."[]") ?>" type="checkbox" <?php if(in_array($oneZone[0], $agArray))  echo("checked"); ?> /> <?php echo ($oneZone[1]); ?> 
            </li>
        <?php
        }
        ?>
    </ul>
    <?php


    ?>

</div>