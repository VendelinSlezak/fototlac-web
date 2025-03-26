<?php
    require("partials/header.php");
?>
<div class="tm-contact-img-container">
    
</div>

<section class="tm-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">

                <section>
                    <h3 class="tm-gold-text tm-form-title">Užívateľský panel</h3>
                    <?php
                        echo "<p class=\"tm-form-description\">Vitajte ";
                        echo $_POST["login_name"];
                        echo "</p>";
                    ?>
                </section>                      

            </div>
        </div>

    </div>
</section>
        
<?php
    require("partials/footer.php");
?>