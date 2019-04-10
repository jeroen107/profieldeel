<?php

///////////////////////OWASP https://www.whitehats.nl/owasp-top-10

/*
 *
 *
 * A1-Injection
 *
 * Iris maakt gebruik van PDO werk methodiek
 *
 */


/*
 *
 *
 * A2-Broken Authentication and Session Management
 *
 * Iris maak gebruik van een session check. Hieronder gedefineerd
 *
 *
 */



session_start();
session_name('PHPSESSID');


if (defined($_SERVER['REMOTE_ADDR'])) {
    $forward = $_SERVER['REMOTE_ADDR'];
}

if (!$_SESSION || $_SESSION['initialized'] !== true) {

    session_regenerate_id(true);
    session_cache_expire(480);
    session_set_cookie_params(28800, '/tmp', $_SERVER["HTTP_HOST"], false, true);

    $_SESSION['initialized'] = true;
    $_SESSION['fingerprint'] = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

} else if ($_SESSION['fingerprint'] != md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']) && $_SESSION['initialized']) {
    session_destroy();
    exit();
}





/*
 *
 *
 * A3-Cross-Site Scripting (XSS)
 *
 * Iris maakt filtert en controleert de uitgaande data
 *
 */


/*
 *
 *
 * A4-Insecure Direct Object Reference
 *
 * Iris maakt gebruik van htaccess in de upload map zodat nooit schadelijke scripts tot uitvoering kan worden gebracht
 *
 *
 */



/*
 *
 *
 * A5-Security Misconfiguration
 *
 * Iris maakt gebruik server van Internet Services die gecertificeerd is met ISO 27001, server wordt met vaste regelmaat geupdate.
 *
 *
 */


/*
 *
 *
 * A6-Sensitive Data Exposure
 *
 * Iris versleuteld gevoelige informatie zoals wachtwoorden. Identificatie/Creditcard gegevens worden niet opgeslagen.
 *
 *
 */


/*
 *
 *
 * A7-Missing Function Level Access Control
 *
 * Iris versleuteld gevoelige informatie zoals wachtwoorden met password hash. Identificatie/Creditcard gegevens worden niet opgeslagen.
 *
 *
 */


/*
 *
 *
 * A8-Cross-Site Request Forgery (CRFS)
 *
 *
 *
 */





if (empty($_SESSION['token'])) {

    $max = ceil(32 / 40);
    $random = '';
    for ($i = 0; $i < $max; $i ++) {
        $random .= sha1(microtime(true).mt_rand(10000,90000));
    }
    $_SESSION['token'] = $random;
}


/*
 *
 *
 * A9-Using Components with Known Vulnerabilities
 *
 * Iris maakt gebruik van alleen libaries van bekende software ontwikkelaars.
 *
 *
 */


/*
 *
 *
 * A10-Unvalidated Redirects and Forwards
 *
 * Iris maakt gebruik alleen in overeenstemming redirects.
 *
 *
 */

?>