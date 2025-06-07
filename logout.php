<?php
    require("_inc/classes/Authenticate.php");

    // inicializujeme sedenie
    session_start();

    // skontrolujeme ci je niekto prihlaseny
    $auth = new Authenticate();
    $auth->continueIfLoggedIn();

    // vymazat data sedenia
    $_SESSION = array();

    // kontrola ci PHP pouziva cookies na spravu sedeni
    if(ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();

        // nastavime cookie na prazdnu hodnotu s casom expiracie v minulosti aby sa odstranila
        setcookie(
            session_name(),        // Názov session cookie (napr. PHPSESSID)
            '',                    // Prázdna hodnota (cookie bude vymazaná)
            time() - 3600,         // Expiračný čas v minulosti (1 hodina späť)
            $params["path"],       // Rovnaká cesta ako pôvodná cookie
            $params["domain"],     // Rovnaká doména ako pôvodná cookie
            $params["secure"],     // Nastavenie zabezpečenia HTTPS (ak bolo nastavené)
            $params["httponly"]    // Nastavenie HttpOnly (ochrana proti XSS)
        );
    }

    // znicit sedenie
    session_destroy();

    // presmerovanie na prihlasovanie
    header("Location: login.php");
    exit;
?>