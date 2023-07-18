<!-- header compinent
beeinhaltet Navigationselemente, Titel, Suchleiste und Logout Funktion.
Für Admins wird zusätzlich die ShopSelection angezeigt, damit man zwischen den Märkten wechseln kann. -->

<header>
  <nav>
    <!-- Home Button -->
    <a href="/index.php">
      <img src="/images/home-icon.png" alt="Home">
    </a>
    <!-- Back Button js  -->
    <a href="javascript:history.go(-1)">
      <img style="height: 48px;" src="./images/back-icon.png" alt="Back">
    </a>

    <?php
    if (($_SESSION["rechte"]) == 0) {
      include("./components/shopSelection.php");
    }
    ?>
  </nav>

  <h3>Auslieferungstool</h3>

  <div>
    <!-- Suchleiste mit Suchknopf -->
    <form id="field" action="searchView.php" method="post">
      <input class="field" type="text" name="searchInput" placeholder="suchen">
      <input class="image" type="image" name="submit" src="./images/search-icon.webp">
    </form>

    <a href="/scripts/logout.php">
      <img style="height: 48px;" src="./images/logOut.webp" alt="ausloggen">
    </a>
  </div>
  <hr>
    <script>
      document.getElementById("field").addEventListener("focusin", (event) => {
        document.getElementById("field").classList.add("focused");
        console.log("test");
      });

      document.getElementById("field").addEventListener("focusout", (event) => {
        document.getElementById("field").classList.remove("focused");
      });

    </script>
</header>