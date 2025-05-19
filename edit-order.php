<?php
    require("partials/header.php");

    // skontrolujeme ci je uzivatel prihlaseny
    if(isset($_SESSION['logged_in']) == false || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }

    // skontrolujeme ci uzivatel dopytuje objednavku
    if(isset($_GET['edit_order']) == false) {
        echo '<div class="alert alert-danger" role="alert">Chyba: Neexistujúca objednávka</div>';
        exit; // TODO:
    }

    //  vytvorime spojenie s databazou
    $db = new Database();
    $userid = $_SESSION["user_id"];
    $orderid = $_GET['edit_order'];

    // TODO: skontrolujeme ci objednavka existuje

    // skontrolujeme ci uzivatel chce vymazat fotku
    if(isset($_GET['delete_photo'])) {
        // TODO: skontrolovat chyby
        $db->deletePhoto($userid, $orderid, $_GET['delete_photo']);
    }
?>

<h3 class="tm-gold-text tm-form-title">Objednávka <?php echo "{$_GET['edit_order']}"; ?> </h3>
<p class="tm-form-description">
    <?php
        $photos = $db->getOrderPhotos($userid, $orderid);

        if(count($photos) == 0) {
            echo "Táto objednávka neobsahuje žiadne fotky.";
        }
        else {
            echo '<table border="1" cellpadding="8" cellspacing="0">';
            echo '<tr><th>Fotka</th><th>Množstvo</th><th>Typ papiera</th><th>Rozmery</th><th>Cena</th><th>Akcie</th></tr>';

            foreach ($photos as $photo) {
                echo '<tr>';
                echo '<td><img width="30" height="30" src="photos/' . $photo["file_name"] . '"</td>'; // TODO: zobrazit fotku
                echo '<td >' . $photo["copies"] . '</td>';
                echo '<td>' . $photo["paper_type"] . '</td>';
                echo '<td>' . $photo["size_width_in_mm"] . 'x' . $photo["size_height_in_mm"] .'</td>';
                echo '<td>' . $photo["price"] . ' €</td>';
                echo '<td> <a href="edit-photo.php?order_id=' . $orderid . '&photo_id=' . $photo['photo_id'] . '">Upraviť</a> <a href="?edit_order=' . $orderid . '&delete_photo=' . $photo['photo_id'] . '">Odstrániť</a> </td>';
                echo '</tr>';
            }

            echo '</table>';
        }
    ?>
</p>
<p class="tm-form-description">
    <button class="btn btn-primary" onclick="location.href='create-photo.php?order_id=<?= $orderid ?>'">Pridať novú fotku</button>
    <button class="btn btn-primary" onclick="location.href='send-order-form.php?order_id=<?= $orderid ?>'">Odoslať objednávku</button>
</p>

<?php
    require("partials/footer.php");
?>