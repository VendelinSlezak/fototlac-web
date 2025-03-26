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
                    <h3 class="tm-gold-text tm-form-title">Registrovať sa</h3>

                    <form action="complete_registration.php" method="post" class="tm-contact-form">                                
                        <div class="form-group">
                            <input type="text" id="register_name" name="register_name" class="form-control" placeholder="Užívateľské meno" required/>
                        </div>
                        <div class="form-group">
                            <input type="email" id="register_email" name="register_email" class="form-control" placeholder="Email" required/>
                        </div>
                        <div class="form-group">
                            <input type="password" id="register_password" name="register_password" class="form-control" placeholder="Heslo" required/>
                        </div>
                    
                        <button type="submit" class="tm-btn">Vytvoriť účet</button>                          
                    </form>   
                </section>                      

            </div>
        </div>

    </div>
</section>
        
<?php
    require("partials/footer.php");
?>