<?php
    require("_inc/classes/Database.php");
    require("partials/header.php");

    // skontrolujeme ci je uzivatel prihlaseny
    if(isset($_SESSION['logged_in']) == false || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }

    //  vytvorime spojenie s databazou
    $db = new Database();
    $userid = $_SESSION["user_id"];
?>

<?php
    require("partials/footer.php");
?>