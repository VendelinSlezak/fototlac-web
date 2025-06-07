<?php
    require("partials/header.php");

    // pripojime sa ku databaze
    $db = new Database();
    $login = new Login($db);
?>

<h3 class="tm-gold-text tm-form-title">Prihlásiť sa</h3>
<p class="tm-form-description">Pokiaľ ešte nemáte účet, vytvorte si ho na stránke <a href="registration.php">Registrovať sa</a>.</p> 

<?php
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['login_email'], $_POST['login_password'])) {
            // overime ci tento uzivatel existuje
            if($login->doesUserExist($_POST['login_email'], $_POST['login_password']) == false) {
                $login->error("Neplatné údaje");
            }
            else {
                // inicializujeme sedenie na prihlasenie
                $user_id = $login->getUserId($_POST['login_email']);
                $_SESSION['user_id'] = $user_id["id"];

                // zistime ci je uzivatel admin alebo user
                if($login->isUserAdmin($_SESSION['user_id']) == true) {
                    $_SESSION['admin'] = true;
                    header("Location: admin-panel.php");
                }
                else {
                    $_SESSION['admin'] = false;
                    header("Location: user-panel.php");
                }
                exit;
            }
        }
        else {
            $login->error("Neboli prijaté všetky údaje z formuláru");
        }
    }
?>

<form action="login.php" method="post" class="tm-contact-form">                                
    <div class="form-group">
        <input type="email" id="login_email" name="login_email" class="form-control" placeholder="Email" required/>
    </div>
    <div class="form-group">
        <input type="password" id="login_password" name="login_password" class="form-control" placeholder="Heslo" required/>
    </div>

    <button type="submit" class="tm-btn">Prihlásiť sa</button>                          
</form>   
        
<?php
    require("partials/footer.php");
?>