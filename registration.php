<?php
    require("partials/header.php");
?>

<h3 class="tm-gold-text tm-form-title">Registrovať sa</h3>

<?php
    // POST znamena ze mame registrovat noveho uzivatela
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['register_email'], $_POST['register_password'])) {
            // pripojime sa ku databaze
            $db = new Database();
            $reg = new Registration($db);
 
            // pridame uzivatela do databazy
            try {
                $reg->addUser($_POST['register_email'], $_POST['register_password']);
                header("Location: complete_registration.php");
                exit;
            }
            catch (Exception $e) {
                $reg->error($e->getMessage());
            }
        }
        else {
            $reg->error("Neboli prijaté všetky údaje z formuláru");
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