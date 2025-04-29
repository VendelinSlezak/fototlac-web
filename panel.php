<?php
    require("_inc/classes/Database.php");
    require("partials/header.php");

    $db = new Database();

    // TODO: skontrolovat ci je uzivatel prihlaseny
?>

<section class="tm-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">

                <section>
                    <h3 class="tm-gold-text tm-form-title">Ste prihlásený</h3>
                    <p class="tm-form-description">Vaše objednávky:</p>
                </section>                      

            </div>
        </div>

    </div>
</section>
        
<?php
    require("partials/footer.php");
?>