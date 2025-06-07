<?php
    require("partials/header.php");

    // skontrolujeme ci je uzivatel prihlaseny
    $auth->continueIfUserLoggedIn();

    //  vytvorime spojenie s databazou
    $db = new Database();
    $user = new User($db);
    $userid = $_SESSION["user_id"];
    $photo_sizes = $user->getPhotoSizes(); // pouzijeme aj pri nahravani fotky, aj pri formulari na nahranie

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // uzivatel nahrava fotku

        // overenie ci mame vsetky potrebne data
        if (!isset($_FILES['photo']) ||
            !isset($_POST['copies']) ||
            !isset($_POST['size']) ||
            !isset($_POST['paper_type']) ||
            !isset($_POST['order_id']) ) {
            echo '<div class="alert alert-danger" role="alert">Chýbajúce údaje vo formulári</div>';
            require("partials/footer.php");
            exit;
        }

        // nacitame order id
        $orderid = $_POST['order_id'];
        $user->continueIfUserHasOrder($userid, $orderid);

        // nastavit velkost fotky
        $selectedSizeId = null;
        foreach ($photo_sizes as $size) {
            if ($size['id'] == $_POST['size']) {
                $selectedSizeId = $size;
                break;
            }
        }
        if ($selectedSizeId == null) {
            echo '<div class="alert alert-danger" role="alert">Neznáma veľkosť fotky</div>';
            require("partials/footer.php");
            exit;
        }
        $width = $selectedSizeId['width'];
        $height = $selectedSizeId['height'];

        // nahranie fotky
        if(isset($_FILES['photo'])) {
            $photo = $_FILES['photo'];
        }
        else {
            $photo = null;
        }
        if ($photo == null || $photo['error'] !== UPLOAD_ERR_OK) {
            echo '<div class="alert alert-danger" role="alert">Chyba pri nahrávaní fotky</div>';
            require("partials/footer.php");
            exit;
        }
        if (!in_array($photo['type'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            echo '<div class="alert alert-danger" role="alert">Nepodporovaný typ obrázku</div>';
            require("partials/footer.php");
            exit;
        }
        $photoName = uniqid() . '.' . strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION)); // generuje nazov unikatneid.priponasuboru
        $photoPath = "photos/" . $photoName; // v tomto priecinku su vsetky fotky
        if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
            echo '<div class="alert alert-danger" role="alert">Nepodarilo sa uložiť súbor</div>';
            require("partials/footer.php");
            exit;
        }

        // vlozime fotku do databazy
        $photo_type = $_POST['paper_type'];
        $copies = $_POST['copies'];
        $createSuccess = $user->createNewPhoto($orderid, $photo_type, $selectedSizeId, $photoName, $copies, $width, $height);
        if($createSuccess == false) {
            echo '<div class="alert alert-danger" role="alert">Chyba pri vytváraní položky v databáze</div>';
            require("partials/footer.php");
            exit;
        }

        // otvorit objednavku
        header("Location: edit-order.php?edit_order=" . $orderid);
        exit;
    }
    else if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $orderid = $_GET['order_id'];
        $user->continueIfUserHasOrder($userid, $orderid);
        $photo_types = $user->getPhotoTypes();
    }
    else {
        echo '<div class="alert alert-danger" role="alert">Nebolo prijaté ID objednávky</div>';
        require("partials/footer.php");
        exit;
    }
?>

<h3 class="tm-gold-text tm-form-title">Nová fotka</h3>
<p class="tm-form-description">
    <form action="create-photo.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="order_id" id="order_id" value="<?= $orderid ?>">

        <!-- Nahratie fotky -->
        <div>
            <label for="photo">Fotka:</label>
            <input type="file" name="photo" id="photo" accept="image/*" required>
        </div>

        <!-- Počet kópií -->
        <div>
            <label for="copies">Počet kópií:</label>
            <input type="number" name="copies" id="copies" value="1" min="1" max="100" required>
        </div>

        <!-- Veľkosť fotky -->
        <div>
            <label for="size">Veľkosť:</label>
            <select name="size" id="size">
                <?php foreach ($photo_sizes as $photo_size): ?>
                    <option value="<?= $photo_size['id'] ?>"><?= $photo_size['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Typ papiera -->
        <div>
            <label for="paper_type">Typ papiera:</label>
            <select name="paper_type" id="paper_type" required>
                <?php foreach ($photo_types as $photo_type): ?>
                    <option value="<?= $photo_type['id'] ?>"><?= $photo_type['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-top: 10px;">
            <button class="btn btn-primary" type="submit">Nahrať fotku do objednávky</button>
        </div>
    </form>
</p>
        
<?php
    require("partials/footer.php");
?>