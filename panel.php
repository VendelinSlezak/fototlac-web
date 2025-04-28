<?php
    require("_inc/classes/Database.php");
    require("partials/header.php");

    $db = new Database();
?>

<section class="tm-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">

                <section>
                    <?php
                        if($_SERVER['REQUEST_METHOD'] === 'POST') {
                            if(isset($_POST['login_email'], $_POST['login_password'])) {
                                // overit ci tento uzivatel existuje
                                if($db->doesUserExist($_POST['login_email'], $_POST['login_password']) == false) {
                                    echo "<h3>Chyba</h3><p>Neplatné užívateľské údaje.</p>";
                                    return;
                                }

                                // vykreslit uzivatelsky panel
                                echo "<h2>Si prihlaseny</h2>";
                            }
                            else {
                                echo "<h3>Chyba</h3><p>Neboli prijaté všetky údaje z formuláru</p>";
                            }
                        }
                        else {
                            echo "<h3>Chyba</h3><p>Formulár nebol odoslaný</p>";
                        }
                    ?>
                </section>                      

            </div>
        </div>

    </div>
</section>
        
<?php
    require("partials/footer.php");
?>