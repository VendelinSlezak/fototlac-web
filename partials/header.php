<?php
    // inicializujeme sedenie
    session_start();

    // nacitat vsetky skripty
    include("_inc/Autoload.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Fototlač</title>
<!--
Classic Template
http://www.templatemo.com/tm-488-classic
-->
    <!-- load stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400">  <!-- Google web font "Open Sans" -->
    <link rel="stylesheet" href="css/bootstrap.min.css">                                      <!-- Bootstrap style -->
    <link rel="stylesheet" href="css/templatemo-style.css">                                   <!-- Templatemo style -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
          <![endif]-->
</head>

<body>
    
<div class="tm-header">
    <div class="container-fluid">
        <div class="tm-header-inner">
            <a href="#" class="navbar-brand tm-site-name">FOTOTLAČ</a>
            
            <!-- navbar -->
            <nav class="navbar tm-main-nav">

                <button class="navbar-toggler hidden-md-up" type="button" data-toggle="collapse" data-target="#tmNavbar">
                    &#9776;
                </button>
                
                <div class="collapse navbar-toggleable-sm" id="tmNavbar">
                    <ul class="nav navbar-nav">
                        <?php
                            if(isset($_SESSION['logged_in']) == true && $_SESSION['logged_in'] === true) {
                                if($_SESSION['admin'] == true) {
                                    $stranky = array("index.php" => "Domov",
                                                "admin-panel.php" => "Panel",
                                                "logout.php" => "Odhlásiť sa");
                                }
                                else {
                                    $stranky = array("index.php" => "Domov",
                                                "panel.php" => "Objednávky",
                                                "logout.php" => "Odhlásiť sa");
                                }
                            }
                            else {
                                $stranky = array("index.php" => "Domov",
                                                 "login.php" => "Prihlásiť sa");
                            }

                            foreach($stranky as $subor => $nazov) {
                                echo "<li class=\"nav-item ";
                                if(basename($_SERVER["PHP_SELF"]) == $subor) {
                                    echo "active";
                                }
                                echo "\"><a href=\"$subor\" class=\"nav-link\">$nazov</a></li>";
                            }
                        ?>
                    </ul>
                </div>
                
            </nav>  

        </div>                                  
    </div>            
</div>

<?php
    if(basename($_SERVER["PHP_SELF"]) != "index.php") {
        echo '<section class="tm-section">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            
                            <section>';
    }
?>