<?php
    require("partials/header.php");

    // skontrolujeme ci je uzivatel prihlaseny
    $auth->continueIfUserLoggedIn();

    // vytvorime spojenie s databazou
    $db = new Database();
    $user = new User($db);
    $userid = $_SESSION["user_id"];
    $orderid = $_GET['order_id'];
    $user->continueIfUserHasOrder($userid, $orderid);
?>

<h3 class="tm-gold-text tm-form-title">Vyplnte údaje</h3>

<form action="send-order.php" method="POST">
    <input type="hidden" name="order_id" id="order_id" value="<?= $orderid ?>">

    <label for="name">Meno:</label><br>
    <input type="text" id="name" name="name" maxlength="45" required><br><br>

    <label for="surname">Priezvisko:</label><br>
    <input type="text" id="surname" name="surname" maxlength="45" required><br><br>

    <label for="country">Krajina:</label><br>
    <input type="text" id="country" name="country" maxlength="45" required><br><br>

    <label for="city">Mesto:</label><br>
    <input type="text" id="city" name="city" maxlength="45" required><br><br>

    <label for="postal_code">PSČ:</label><br>
    <input type="text" id="postal_code" name="postal_code" maxlength="45" required><br><br>

    <label for="street">Ulica:</label><br>
    <input type="text" id="street" name="street" maxlength="45" required><br><br>

    <label for="house_number">Číslo domu:</label><br>
    <input type="text" id="house_number" name="house_number" maxlength="45" required><br><br>

    <button type="submit">Odoslať objednávku</button>
</form>

<?php
    require("partials/footer.php");
?>