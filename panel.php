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

    // skontrolujeme ci uzivatel chce vytvorit novu objednavku
    if(isset($_GET['new_order']) && $_GET['new_order'] == true) {
        // TODO: skontrolovat chyby
        $number_of_orders = $db->getUserOrdersCount($userid);
        if($number_of_orders < 10) {
            $db->createNewOrder($userid);
        }
        else {
            echo '<div class="alert alert-danger" role="alert">Chyba: Nemôžete vytvoriť viac ako 10 objednávok</div>';
        }
    }

    // skontrolujeme ci uzivatel chce vymazat objednavku
    if(isset($_GET['delete_order'])) {
        // TODO: skontrolovat chyby
        $db->deleteOrder($userid, $_GET['delete_order']);
    }
?>

<h3 class="tm-gold-text tm-form-title">Rozpracované objednávky</h3>
<p class="tm-form-description">
    <?php
        $orders = $db->getUserOrders($userid);

        if(count($orders) == 0) {
            echo "Nemáte vytvorené žiadne rozpracované objednávky.";
        }
        else {
            echo '<table border="1" cellpadding="8" cellspacing="0">';
            echo '<tr><th>Číslo objednávky</th><th>Vytvorená</th><th>Stav</th><th>Akcie</th></tr>';

            foreach ($orders as $order) {
                $stav = "Rozpracovaná";
                if($order['state'] == "O") {
                    $stav = "Čakajúca na spracovanie";
                }
                echo '<tr>';
                echo '<td>' . $order['id'] . '</td>';
                echo '<td >' . $order['created_at'] . '</td>';
                echo '<td>' . $stav . '</td>';
                echo '<td> <a href="edit-order.php?edit_order=' . $order['id'] . '">Upraviť</a> <a href="?delete_order=' . $order['id'] . '">Odstrániť</a> </td>';
                echo '</tr>';
            }

            echo '</table>';
        }
    ?>
</p>
<p class="tm-form-description">
    <button class="btn btn-primary" onclick="location.href='?new_order=true'">Vytvoriť objednávku</button>
</p>
        
<?php
    require("partials/footer.php");
?>