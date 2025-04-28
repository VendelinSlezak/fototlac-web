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
                            if(isset($_POST['register_name'], $_POST['register_email'], $_POST['register_password'])) {
                                // skontrolujeme ci je este v databaze miesto
                                // max 10000 pouzivatelov, treba doladit podla moznosti servera
                                $user_count = $db->getUserCount();
                                if($user_count >= 10000) {
                                    echo "<h3>Chyba</h3><p>Maximálny počet užívateľov je registrovaných.</p>";
                                    return;
                                }

                                // pridame uzivatela do databazy
                                try {
                                    $db->addUser($_POST['register_name'], $_POST['register_email'], $_POST['register_password']);
                                    echo   '<h3 class="tm-gold-text tm-form-title">Úspešná registrácia</h3>
                                            <p class="tm-form-description">Teraz sa môžete prihlásiť.</p>';
                                }
                                catch (Exception $e) {
                                    echo "<h3>Chyba</h3><p>{$e->getMessage()}</p>";
                                }
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