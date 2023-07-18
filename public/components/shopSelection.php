<!-- Anzeige und Auswahl aller Shops für Admins.
Debug Funktion damit der Admin zwischen allen Märkten wechseln und einsehen kann -->

<div class="shop">
<?php
  if(isset($_POST['maerkte'])){
    if (($_SESSION["rechte"]) == 0) {
      $markt = $_POST['maerkte'];
      $_SESSION['markt'] = $markt;
    }
    

  }
  $markt = $_SESSION['markt'];

require "./scripts/configureDB.php";

if ($_SESSION["rechte"] == 0) {
    $stmt = mysqli_prepare($link, "SELECT shop_ref FROM shops");
    mysqli_stmt_execute($stmt);
    $stmt->bind_result($referenz);
    $resultCounter = 0;
?>
    <form class="maerkteForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <select id="maerkte" name="maerkte" onchange="this.form.submit()">

            <?php
            while ($stmt->fetch()) {
                if ($resultCounter == 0) {
                    $firstInput = $referenz;
                }
                $resultCounter++;
            ?>
                <option value="<?php echo "$referenz"; ?>" <?php if (isset($markt)) {
                                                                if ($markt == $referenz) {
                                                                    echo " selected";
                                                                }
                                                            } ?>><?php echo "$referenz"; ?></option>
            <?php
            }
            if (isset($firstInput) && !isset($markt)) {
                $markt = $firstInput;
            } ?>

        </select>
    </form>
<?php
} else {
    $markt = $_SESSION["markt"];
}
?>
</div>