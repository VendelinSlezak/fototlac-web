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

    // skontrolujeme ci je pouzivatel admin
    if($db->isUserAdmin($userid) == false) {
        header("Location: panel.php");
        exit;
    }
?>

<h3 class="tm-gold-text tm-form-title">Prijaté objednávky</h3>
<p class="tm-form-description">
    <?php
        $orders = $db->getAllSendedOrders();

        if(count($orders) == 0) {
            echo "Aktuálne žiadne nespracované objednávky od zákazníkov.";
        }
        else {
            echo '<table border="1" cellpadding="8" cellspacing="0">';
            echo '<tr><th>Číslo objednávky</th><th>Vytvorená</th><th>Akcie</th></tr>';

            foreach ($orders as $order) {
                echo '<tr>';
                echo '<td>' . $order['id'] . '</td>';
                echo '<td >' . $order['created_at'] . '</td>';
                echo '<td> <a href="admin-show-order.php?order_id=' . $order['id'] . '">Zobraziť</a> </td>';
                echo '</tr>';
            }

            echo '</table>';
        }
    ?>
</p>
        
<?php
    require("partials/footer.php");
?>