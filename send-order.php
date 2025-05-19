<?php
    require("partials/header.php");

    // skontrolujeme ci je uzivatel prihlaseny
    if(isset($_SESSION['logged_in']) == false || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }

    //  vytvorime spojenie s databazou
    $db = new Database();
    $userid = $_SESSION["user_id"];
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $orderid = $_GET['order_id'];
    }

    // zistime ci uzivatel odosiela objednavku
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // overenie ci mame vsetky potrebne data
        if (!isset($_POST['order_id']) ||
            !isset($_POST['name']) ||
            !isset($_POST['surname']) ||
            !isset($_POST['country']) ||
            !isset($_POST['city']) ||
            !isset($_POST['postal_code']) ||
            !isset($_POST['street']) ||
            !isset($_POST['house_number']) ) {
            echo "Chýbajúce údaje vo formulári";
            exit;
        }

        // TODO: overenie ci je tato objednavka rozpracovana

        // aktualizujeme objednavku na spracovanu
        $db->sendOrder($userid, $_POST['order_id'], $_POST['name'], $_POST['surname'], $_POST['country'], $_POST['city'], $_POST['postal_code'], $_POST['street'], $_POST['house_number']); // TODO: skontrolovat error
    }
?>

<h3 class="tm-gold-text tm-form-title">Objednávka bola odoslaná</h3>

<p>Vaša objednávka bola úspešne prijatá na spracovanie.</p>

<?php
    require("partials/footer.php");
?>