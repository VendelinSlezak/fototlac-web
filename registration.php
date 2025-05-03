<?php
    require("_inc/classes/Database.php");
    require("partials/header.php");
?>

<h3 class="tm-gold-text tm-form-title">Registrovať sa</h3>

<?php
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['register_email'], $_POST['register_password'])) {
            // pripojime sa ku databaze
            $db = new Database();

            // skontrolujeme ci je este v databaze miesto
            // max 10000 pouzivatelov, treba doladit podla moznosti servera
            $user_count = $db->getUserCount();
            if($user_count >= 10000) {
                echo '<div class="alert alert-danger" role="alert">Chyba: Maximálny počet užívateľov je registrovaných</div>';
            }
            else {
                // pridame uzivatela do databazy
                try {
                    $db->addUser($_POST['register_email'], $_POST['register_password']);
                    header("Location: complete_registration.php");
                    exit;
                }
                catch (Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">Chyba: '.$e->getMessage().'</div>';
                }
            }
        }
        else {
            echo '<div class="alert alert-danger" role="alert">Chyba: Neboli prijaté všetky údaje z formuláru</div>';
        }
    }
?>

<form action="registration.php" method="post" class="tm-contact-form">
    <div class="form-group">
        <input type="email" id="register_email" name="register_email" class="form-control" placeholder="Email" maxlength="45" required/>
    </div>
    <div class="form-group">
        <input type="password" id="register_password" name="register_password" class="form-control" placeholder="Heslo" maxlength="72" required/>
    </div>

    <button type="submit" class="tm-btn">Vytvoriť účet</button>                          
</form> 

<?php
    require("partials/footer.php");
?>