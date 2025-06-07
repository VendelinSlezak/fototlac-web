<?php
    require("partials/header.php");

    // skontrolujeme ci je uzivatel prihlaseny
    $auth->continueIfUserLoggedIn();

    //  vytvorime spojenie s databazou
    $db = new Database();
    $user = new User($db);
    $userid = $_SESSION["user_id"];

    // skontrolujeme ci mame cislo objednavky
    if(isset($_GET['edit_order']) == false) {
        echo '<div class="alert alert-danger" role="alert">Chyba: Objednávka nie je špecifikovaná</div>';
        require("partials/footer.php");
        exit;
    }

    // vytvorime spojenie s databazou
    $db = new Database();
    $user = new User($db);
    $userid = $_SESSION["user_id"];
    $orderid = $_GET['edit_order'];

    // skontrolujeme ci uzivatel ma tuto objednavku
    $user->continueIfUserHasOrder($userid, $orderid);

    // skontrolujeme ci uzivatel chce vymazat fotku
    if(isset($_GET['delete_photo'])) {
        $deleteSuccess = $user->deletePhoto($userid, $orderid, $_GET['delete_photo']);
        if($deleteSuccess == false) {
            echo '<div class="alert alert-danger" role="alert">Nepodarilo sa vymazať fotku</div>';
        }
    }
?>

<h3 class="tm-gold-text tm-form-title">Objednávka <?php echo "{$_GET['edit_order']}"; ?> </h3>
<p class="tm-form-description">
    <?php
        $photos = $user->getOrderPhotos($userid, $orderid);

        if(count($photos) == 0) {
            echo "Táto objednávka neobsahuje žiadne fotky.";
        }
        else {
            echo '<table border="1" cellpadding="8" cellspacing="0">';
            echo '<tr><th>Fotka</th><th>Množstvo</th><th>Typ papiera</th><th>Rozmery</th><th>Cena</th><th>Akcie</th></tr>';

            foreach ($photos as $photo) {
                echo '<tr>';
                echo '<td><img width="30" height="30" src="photos/' . htmlspecialchars($photo["file_name"]) . '"</td>'; // TODO: zobrazit fotku
                echo '<td >' . htmlspecialchars($photo["copies"]) . '</td>';
                echo '<td>' . htmlspecialchars($photo["paper_type"]) . '</td>';
                echo '<td>' . htmlspecialchars($photo["size_width_in_mm"]) . 'x' . htmlspecialchars($photo["size_height_in_mm"]) .'</td>';
                echo '<td>' . htmlspecialchars($photo["price"]) . ' €</td>';
                echo '<td> <a href="edit-photo.php?order_id=' . $orderid . '&photo_id=' . htmlspecialchars($photo['photo_id']) . '">Upraviť</a> <a href="?edit_order=' . $orderid . '&delete_photo=' . htmlspecialchars($photo['photo_id']) . '">Odstrániť</a> </td>';
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