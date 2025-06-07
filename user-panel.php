<?php
    require("partials/header.php");

    // skontrolujeme ci je uzivatel prihlaseny
    $auth->continueIfUserLoggedIn();

    //  vytvorime spojenie s databazou
    $db = new Database();
    $user = new User($db);
    $userid = $_SESSION["user_id"];

    // skontrolujeme ci uzivatel chce vytvorit novu objednavku
    if(isset($_GET['new_order']) && $_GET['new_order'] == true) {
        $number_of_orders = $user->getUserOrdersCount($userid);

        if($number_of_orders < 10) {
            $user->createNewOrder($userid);
        }
        else {
            echo '<div class="alert alert-danger" role="alert">Chyba: Nemôžete vytvoriť viac ako 10 objednávok</div>';
        }
    }

    // skontrolujeme ci uzivatel chce vymazat objednavku
    if(isset($_GET['delete_order'])) {
        $deleteSuccess = $user->deleteOrder($userid, $_GET['delete_order']);
        if($deleteSuccess == false) {
            echo '<div class="alert alert-danger" role="alert">Nepodarilo sa vymazať objednávku</div>';
        }
    }
?>

<h3 class="tm-gold-text tm-form-title">Objednávky</h3>
<p class="tm-form-description">
    <?php
        $orders = $user->getUserOrders($userid);

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
                echo '<td>' . htmlspecialchars($order['id']) . '</td>';
                echo '<td >' . htmlspecialchars($order['created_at']) . '</td>';
                echo '<td>' . htmlspecialchars($stav) . '</td>';
                if($order['state'] == "N") {
                    echo '<td> <a href="edit-order.php?edit_order=' . htmlspecialchars($order['id']) . '">Upraviť</a> <a href="?delete_order=' . htmlspecialchars($order['id']) . '">Odstrániť</a> </td>';
                }
                else {
                    echo '<td> </td>';
                }
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