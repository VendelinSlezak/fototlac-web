<?php
    require("partials/header.php");
?>

<section class="tm-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">

                <section>
                    <h3 class="tm-gold-text tm-form-title">Prihlásiť sa</h3>
                    <p class="tm-form-description">Pokiaľ ešte nemáte účet, vytvorte si ho na stránke <a href="registration.php">Registrovať sa</a>.</p> 

                    <form action="panel.php" method="post" class="tm-contact-form">                                
                        <div class="form-group">
                            <input type="text" id="login_email" name="login_email" class="form-control" placeholder="Email" required/>
                        </div>
                        <div class="form-group">
                            <input type="password" id="login_password" name="login_password" class="form-control" placeholder="Heslo" required/>
                        </div>
                    
                        <button type="submit" class="tm-btn">Prihlásiť sa</button>                          
                    </form>   
                </section>                      

            </div>
        </div>

    </div>
</section>
        
<?php
    require("partials/footer.php");
?>