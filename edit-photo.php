<?php
    require("partials/header.php");

    // skontrolujeme ci je uzivatel prihlaseny
    $auth->continueIfUserLoggedIn();

    // vytvorime spojenie s databazou
    $db = new Database();
    $user = new User($db);
    $userid = $_SESSION["user_id"];

    // zistime ci uzivatel chce upravit fotku
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // overenie ci mame vsetky potrebne data
        if (!isset($_POST['copies']) ||
            !isset($_POST['size']) ||
            !isset($_POST['paper_type']) ||
            !isset($_POST['order_id']) ||
            !isset($_POST['photo_id'])) {
            echo "Chýbajúce údaje vo formulári";
            exit;
        }
        $orderid = $_POST['order_id'];
        $photoid = $_POST['photo_id'];

        // overime ci ma uzivatel pravo upravovat tuto fotku
        $user->continueIfUserHasOrder($userid, $orderid);
        if($user->isOrderUnfinished($userid, $orderid) == false) {
            echo '<div class="alert alert-danger" role="alert">Táto objednávka už bola odoslaná</div>';
            require("partials/footer.php");
            exit;
        }
        if($user->isPhotoInOrder($orderid, $photoid) == false) {
            echo '<div class="alert alert-danger" role="alert">Nemáte právo upravovať túto fotku</div>';
            require("partials/footer.php");
            exit;
        }

        // aktualizovat udaje fotky
        $status = $user->editPhotoInfo($userid, $orderid, $photoid, $_POST['copies'], $_POST['paper_type'], $_POST['size']);

        // zobrazit aktualizovanu objednavku
        $url = "Location: edit-order.php?edit_order={$orderid}";
        header($url);
        exit;
    }
    else {
        $orderid = $_GET['order_id'];
        $photoid = $_GET['photo_id'];

        // overime ci ma uzivatel pravo upravovat tuto fotku
        $user->continueIfUserHasOrder($userid, $orderid);
        if($user->isOrderUnfinished($userid, $orderid) == false) {
            echo '<div class="alert alert-danger" role="alert">Táto objednávka už bola odoslaná</div>';
            require("partials/footer.php");
            exit;
        }
        if($user->isPhotoInOrder($orderid, $photoid) == false) {
            echo '<div class="alert alert-danger" role="alert">Nemáte právo upravovať túto fotku</div>';
            require("partials/footer.php");
            exit;
        }

        $photo_sizes = $user->getPhotoSizes();
        $photo_types = $user->getPhotoTypes();
        $photo_info = $user->getPhotoInfo($userid, $orderid, $photoid);
    }
?>

<h3 class="tm-gold-text tm-form-title">Upraviť parametre fotky</h3>
<p class="tm-form-description">
    <form action="edit-photo.php" method="POST">
        <input type="hidden" name="order_id" id="order_id" value="<?= $orderid ?>">
        <input type="hidden" name="photo_id" id="photo_id" value="<?= $photoid ?>">

        <div>
            <label for="copies">Počet kópií:</label>
            <input type="number" name="copies" id="copies" value="<?= $photo_info["copies"] ?>" min="1" max="100" required>
        </div>

        <div>
            <label for="size">Veľkosť:</label>
            <select name="size" id="size">
                <?php foreach ($photo_sizes as $photo_size): ?>
                    <option value="<?= $photo_size['id'] ?>" <?= $photo_size['id'] === $photo_info['photo_size_id'] ? 'selected' : '' ?>><?= $photo_size['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="paper_type">Typ papiera:</label>
            <select name="paper_type" id="paper_type" value="<?= $photo_info["copies"] ?>" required>
                <?php foreach ($photo_types as $photo_type): ?>
                    <option value="<?= $photo_type['id'] ?>"><?= $photo_type['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-top: 10px;">
            <button class="btn btn-primary" type="submit">Uložiť zmeny</button>
        </div>
    </form>
</p>
        
<?php
    require("partials/footer.php");
?>