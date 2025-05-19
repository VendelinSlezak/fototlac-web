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
    $photo_types = $db->getPhotoTypes();
    $orderid = $_GET['order_id'];
    $photoid = $_GET['photo_id'];

    // zistime ci uzivatel chce upravit fotku
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // overenie ci mame vsetky potrebne data
        if (!isset($_POST['copies']) ||
            !isset($_POST['size']) ||
            !isset($_POST['paper_type']) ||
            !isset($_POST['order_id']) ) {
            echo "Chýbajúce údaje vo formulári";
            exit;
        }

        $orderid = $_POST['order_id'];
        $photoid = $_POST['photo_id'];

        //TODO: skontrolovat ci je fotka v objednavke

        // najst velkost fotky
        $sizes = $db->getPhotoSizes();
        $selectedSize = null;
        foreach ($sizes as $size) {
            if ($size['name'] === $_POST['size']) {
                $selectedSize = $size;
                break;
            }
        }
        if (!$selectedSize) {
            echo "Neznáma veľkosť fotky.";
            exit;
        }
        $width = $selectedSize['width'];
        $height = $selectedSize['height'];

        // aktualizovat udaje fotky
        $status = $db->editPhotoInfo($userid, $orderid, $photoid, $_POST['copies'], $width, $height , $_POST['paper_type']);

        $url = "Location: edit-order.php?edit_order={$orderid}";
        header($url);
        exit;
    }

    $photo_info = $db->getPhotoInfo($userid, $orderid, $photoid);

    // zistit velkost na zaklade databazy
    $photo_sizes = $db->getPhotoSizes();
    $photo_size = null;
    foreach ($photo_sizes as $size) {
        if ($size['width'] == $photo_info['size_width_in_mm']
            && $size['height'] == $photo_info['size_height_in_mm']) {
            $photo_size = $size['name'];
            break;
        }
    }
    if (!$photo_size) {
        $photo_size = "Neznáma veľkosť";
        exit;
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
                <option value="A5" <?= $photo_size === "A5" ? 'selected' : '' ?>>A5</option>
                <option value="A4" <?= $photo_size === "A4" ? 'selected' : '' ?>>A4</option>
                <option value="A3" <?= $photo_size === "A3" ? 'selected' : '' ?>>A3</option>
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