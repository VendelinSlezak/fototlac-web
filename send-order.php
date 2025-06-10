<?php
    require("partials/header.php");

    // skontrolujeme ci je uzivatel prihlaseny
    $auth->continueIfUserLoggedIn();

    //  vytvorime spojenie s databazou
    $db = new Database();
    $user = new User($db);
    $userid = $_SESSION["user_id"];

    // overenie ci mame vsetky potrebne data
    if($_SERVER['REQUEST_METHOD'] !== 'POST' ||
       !isset($_POST['order_id']) ||
       !isset($_POST['name']) ||
       !isset($_POST['surname']) ||
       !isset($_POST['country']) ||
       !isset($_POST['city']) ||
       !isset($_POST['postal_code']) ||
       !isset($_POST['street']) ||
       !isset($_POST['house_number']) ) {
        echo '<div class="alert alert-danger" role="alert">Chýbajúce údaje vo formulári</div>';
        require("partials/footer.php");
        exit;
    }
    $orderid = $_POST['order_id'];

    // skontrolujeme ci uzivatel ma tuto objednavku
    $user->continueIfUserHasOrder($userid, $orderid);

    // overenie ci je tato objednavka rozpracovana aby uzivatel nemenil data uz odoslanej objednavky
    if($user->isOrderUnfinished($userid, $orderid) == false) {
        echo '<div class="alert alert-danger" role="alert">Táto objednávka už bola odoslaná</div>';
        require("partials/footer.php");
        exit;
    }

    // aktualizujeme objednavku na spracovanu
    $successSending = $user->sendOrder($userid, $_POST['order_id'], $_POST['name'], $_POST['surname'], $_POST['country'], $_POST['city'], $_POST['postal_code'], $_POST['street'], $_POST['house_number']);
    if($successSending == false) {
        echo '<div class="alert alert-danger" role="alert">Nepodarilo sa poslať formulár</div>';
        require("partials/footer.php");
        exit;
    }
?>

<h3 class="tm-gold-text tm-form-title">Objednávka bola odoslaná</h3>

<p>Vaša objednávka bola úspešne prijatá na spracovanie.</p>

<?php
    require("partials/footer.php");
?>