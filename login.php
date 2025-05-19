<?php
    require("partials/header.php");
?>

<h3 class="tm-gold-text tm-form-title">Prihlásiť sa</h3>
<p class="tm-form-description">Pokiaľ ešte nemáte účet, vytvorte si ho na stránke <a href="registration.php">Registrovať sa</a>.</p> 

<?php
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['login_email'], $_POST['login_password'])) {
            // pripojime sa ku databaze
            $db = new Database();

            // overime ci tento uzivatel existuje
            // TODO: vyhodit priamu chybu co sa presne stalo
            if($db->doesUserExist($_POST['login_email'], $_POST['login_password']) == false) {
                echo '<div class="alert alert-danger" role="alert">Chyba: Neplatné údaje</div>';
            }
            else {
                // inicializujeme sedenie na prihlasenie
                $_SESSION['logged_in'] = true;
                $user_id = $db->getUserId($_POST['login_email']); // TODO: spracovat potencionalnu chybu
                $_SESSION['user_id'] = $user_id["id"];

                // zistime ci je uzivatel admin
                if($db->isUserAdmin($_SESSION['user_id']) == true) {
                    $_SESSION['admin'] = true;
                    header("Location: admin-panel.php");
                    exit;
                }
                else {
                    $_SESSION['admin'] = false;
                }

                // ak nie, presmerujeme uzivatela na uzivatelsky panel
                header("Location: panel.php");
                exit;
            }
        }
        else {
            echo '<div class="alert alert-danger" role="alert">Chyba: Neboli prijaté všetky údaje z formuláru</div>';
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