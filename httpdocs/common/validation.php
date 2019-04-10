<?php
function validation_valid($array)
{
    if (!$_POST) exit;


    array_walk_recursive($_POST,"fc_strip");


    $results = count($array);

    $r = array();
    $f = array();

    foreach ($array as $key => $value)
    {

        //Variable karakters
        if($value[1] == "varchar"){

            if($value[2] == false){
                if(preg_match("/.*/",$value[0])) continue;
            }else{
                if(preg_match("/.+/",$value[0])) continue;
            }

        }


        //Alleen Letters
        if($value[1] == "text"){
            if($value[2] == false){
                if(preg_match("/[a-z]*/i",$value[0])) continue;
            }else{
                if(preg_match("/[a-z]+/i",$value[0])) continue;
            }
        }


        //Email
        if($value[1] == "email"){

            if (filter_var($value[0], FILTER_VALIDATE_EMAIL)) continue;

        }


        //Telefoon
        if($value[1] == "telefoon"){

            $value[0] = preg_replace("/\s*|\W*/", "", $value[0]);
            
            if(preg_match("/^0{1}/",$value[0])){
                if(preg_match("/^0{2}/",$value[0])){
                    if(preg_match("/^(\d{13,14})$/",$value[0])) continue;
                }else{
                    if(preg_match("/^\d{10}$/",$value[0])) continue;
                }
            }else{
                if(preg_match("/^\d{11,12}$/",$value[0])) continue;
            }
        }


        //Postcode
        if($value[1] == "postcode"){
            if($value[2] == false) {
                if (preg_match("/^[0-9]{4}.?[a-zA-Z]{2}$/", $value[0])) continue;
            }else{
                if (preg_match("/^[0-9]{4}.?[a-zA-Z]{2}$/s", $value[0])) continue;
            }
        }


        //Website
        if($value[1] == "url"){
            if($value[2] == false) {

                if (filter_var($value[0], FILTER_VALIDATE_URL) || empty($value[0])) continue;
            }else{
                if (filter_var($value[0], FILTER_VALIDATE_URL))  continue;
            }
        }

        //BTWNUMMER
        if($value[1] == "btwnummer"){
            if($value[2] == false) {
                if (validation_btw_nummer($value[0]) || empty($value[0])) continue;
            }else{
                if (validation_btw_nummer($value[0]))  continue;
            }
        }

        //PASSWORD
        if($value[1] == "password"){

            //GROTER DAN 7 KARAKTER, MOET MIN 1 CIJFER EN 1 LETTER BEVATTEN

            if (strlen($value[0]) >= 8) {

                if (preg_match("/[0-9]+/", $value[0])) {

                    if (preg_match("/[a-zA-Z]+/", $value[0])) {

                        continue;

                    }
                }

            }

        }

        //CAPTCHA
        if($value[1] == "captcha"){


            if($value[0] === $_SESSION["captcha"]["code"]){

                unset($_SESSION["captcha"]);

                $_SESSION['captcha'] = simple_php_captcha();

                continue;

            }
        }

        //CRFS Token
        if($value[1] == "token"){

            if($value[0] === $_SESSION["token"]){

                continue;

            }
        }


        echo json_encode(array("status" => "fail", "message" => $value[3]));
        exit();

    }



}
function validation_proef11($bankrek){
    $csom = 0;                            // variabele initialiseren
    $pos = 9;                             // het aantal posities waaruit een bankrekeningnr hoort te bestaan
    for ($i = 0; $i < strlen($bankrek); $i++){
        $num = substr($bankrek,$i,1);       // bekijk elk karakter van de ingevoerde string
        if ( is_numeric( $num )){           // controleer of het karakter numeriek is
            $csom += $num * $pos;                        // bereken somproduct van het cijfer en diens positie
            $pos--;                           // naar de volgende positie
        }
    }
    $postb = ($pos > 1) && ($pos < 7);    // True als resterende posities tussen 1 en 7 => Postbank
    $mod = $csom % 11;                                        // bereken restwaarde van somproduct/11.
    return( $postb || !($pos || $mod) );  // True als het een postbanknr is of restwaarde=0 zonder resterende posities
}
function validation_checked($needle,$value){

    if($needle == $value){

        return "checked='checked'";

    }
}
function validation_btw_nummer($vat_number){


    if(!empty($vat_number))
    {

        $vat_number = str_replace(" ","",$vat_number);


        $resp = file_get_contents("http://apilayer.net/api/validate?access_key=de046bf6d42f7c3b85b991dc51eb0079&vat_number=".$vat_number);

        $json = json_decode($resp,true);

        if($json["valid"] == 1)
        {
            return true;
        }else{

            return false;
        }

    }else{
        return false;
    }

    return false;


}
function validation_password($pwd, &$errors) {

    $errors_init = $errors;

    if (strlen($pwd) < 8) {
        $errors[] = "Password too short!";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "Password must include at least one number!";
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors[] = "Password must include at least one letter!";
    }

    return ($errors == $errors_init);
}

function validation_isEmail($email) {


    //CHECK THIS
    return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i",$email));

}

//SYSTEM VALIDATION
function input($input,$dbvar = ''){

    return mysqli_real_escape_string(db_connection($dbvar),$input);

}
function output($output,$language = '',$ifempty = ''){

    if(!empty($_SESSION["visitor"]["language"]) && empty($language)){

        $language = $_SESSION["visitor"]["language"];

    }

    if(is_array($output)){

        array_walk_recursive($output,"output_array_walker",$language);

    }else{

        $output = output_parser($output,$language);
    }

    return $output;

}

function output_array_walker(&$item, $key,$language) {


    $item = output_parser($item,$language);

}
function output_parser($output,$language){

    $aJson = json_decode($output,true);

    if (is_array($aJson) == true && array_key_exists($language,$aJson)){

        $output = $aJson[$language];

    }

    $output = html_entity_decode($output,ENT_COMPAT, 'UTF-8');


    /* DISABLE TO BIG LIBARY
    $purifier = new HTMLPurifier();

    $output = $purifier->purify($output);


    if(!empty($ifempty)){


        if(empty($output)){

            $output = $ifempty;
        }
    }
    */

    return $output;

}

?>