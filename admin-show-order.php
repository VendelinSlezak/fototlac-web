<?php
    require("partials/header.php");

    // skontrolujeme ci je prihlaseny admin
    $auth->continueIfAdminLoggedIn();

    // vytvorime spojenie s databazou
    $db = new Database();
    $admin = new Admin($db);
    $userid = $_SESSION["user_id"];
    $orderid = $_GET["order_id"];

    // zistie ci admin nechce akceptovat objednavku ako spracovanu
    if(isset($_GET['accept_order_id'])) {
        $admin->setOrderAsDone($_GET['accept_order_id']);
        header("Location: admin-panel.php");
        exit;
    }

    // nacitame objednavku
    $photos = $admin->getOrderPhotos($orderid);
    $orderInfo = $admin->getOrderDetails($orderid);
?>

<h3 class="tm-gold-text tm-form-title">Objednávka <?php echo "{$_GET['order_id']}"; ?> </h3>
<p class="tm-form-description">
    <?php
        echo '<p>Meno: ' . htmlspecialchars($orderInfo['name']) . '</p>';
        echo '<p>Priezvisko: ' . htmlspecialchars($orderInfo['surname']) . '</p>';
        echo '<p>Email: ' . htmlspecialchars($orderInfo['email']) . '</p>';
        echo '<p>Krajina: ' . htmlspecialchars($orderInfo['country']) . '</p>';
        echo '<p>Mesto: ' . htmlspecialchars($orderInfo['city']) . '</p>';
        echo '<p>PSČ: ' . htmlspecialchars($orderInfo['postal_code']) . '</p>';
        echo '<p>Ulica: ' . htmlspecialchars($orderInfo['street']) . '</p>';
        echo '<p>Číslo domu: ' . htmlspecialchars($orderInfo['house_number']) . '</p>';        
    ?>
</p>
<p class="tm-form-description">
    <?php
        if(count($photos) == 0) {
            echo "Táto objednávka neobsahuje žiadne fotky.";
        }
        else {
            echo '<table border="1" cellpadding="8" cellspacing="0">';
            echo '<tr><th>Fotka</th><th>Množstvo</th><th>Typ papiera</th><th>Rozmery</th></tr>';

            foreach ($photos as $photo) {
                echo '<tr>';
                echo '<td><a href="photos/' . htmlspecialchars($photo["file_name"]) . '">Stiahnuť fotku</a></td>'; // TODO: zobrazit fotku
                echo '<td >' . htmlspecialchars($photo["copies"]) . '</td>';
                echo '<td>' . htmlspecialchars($photo["paper_type"]) . '</td>';
                echo '<td>' . htmlspecialchars($photo["size_width_in_mm"]) . 'x' . htmlspecialchars($photo["size_height_in_mm"]) .'</td>';
                echo '</tr>';
            }

            echo '</table>';
        }
    ?>
</p>
<p class="tm-form-description">
    <button class="btn btn-primary" onclick="location.href='admin-show-order.php?accept_order_id=<?= $orderid ?>'">Označiť objednávku ako spracovanú</button>
</p>
        
<?php
    require("partials/footer.php");
?>