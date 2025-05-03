<?php
    require("partials/header.php");

    // odhlasit ak je uzivatel prihlaseny
    if(isset($_SESSION['logged_in']) == true && $_SESSION['logged_in'] === true) {
        session_unset();
        session_destroy();
        header("Location: logout.php");
        exit;
    }
?>

<h3 class="tm-gold-text tm-form-title">Ste odhlásený</h3>
        
<?php
    require("partials/footer.php");
?>