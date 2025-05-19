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
    $photo_sizes = $db->getPhotoSizes();
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $orderid = $_GET['order_id'];
    }

    // zistime ci uzivatel nahrava fotku
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // overenie ci mame vsetky potrebne data
        if (!isset($_FILES['photo']) ||
            !isset($_POST['copies']) ||
            !isset($_POST['size']) ||
            !isset($_POST['paper_type'])
            || !isset($_POST['order_id']) ) {
            echo "Chýbajúce údaje vo formulári";
            exit;
        }

        $orderid = $_POST['order_id'];

        // nastavit velkost fotky
        $sizes = $db->getPhotoSizes();
        $selectedSizeId = null;
        foreach ($sizes as $size) {
            if ($size['id'] == $_POST['size']) {
                $selectedSizeId = $size;
                break;
            }
        }
        if (!$selectedSizeId) {
            echo "Neznáma veľkosť fotky.";
            exit;
        }
        $width = $selectedSizeId['width'];
        $height = $selectedSizeId['height'];

        // Nahranie fotky
        $photo = $_FILES['photo'];
        if ($photo['error'] !== UPLOAD_ERR_OK) {
            echo "Chyba pri nahrávaní fotky.";
            exit;
        }
        if (!in_array($photo['type'], ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            echo "Nepodporovaný typ obrázku.";
            exit;
        }
        $photoName = uniqid() . '.' . strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
        $photoPath = "photos/" . $photoName;
        if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
            echo "Nepodarilo sa uložiť súbor.";
            exit;
        }

        // vlozime fotku do databazy
        $photo_type = intval($_POST['paper_type']);
        $copies = intval($_POST['copies']);
        $db->createNewPhoto($orderid, $photo_type, $selectedSizeId, $photoName, $copies, $width, $height); // TODO: skontrolovat error

        // otvorit objednavku
        header("Location: edit-order.php?edit_order=" . $orderid);
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