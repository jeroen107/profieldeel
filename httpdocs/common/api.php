<?php
function api_get_latlng(){

    global $argv;


    if($_SERVER['REMOTE_ADDR'] == '87.250.153.86') {


        $json = json_decode(file_get_contents("https://maps.google.com/maps/api/geocode/json?address=".rawurlencode($argv[2])."&sensor=false&key=AIzaSyAenS7NzohIlg7BKvu5vxpT6bkhQPAB3Lc"), true);

        $return = array();
        if(isset($json["results"][0]['geometry']['location']['lat']) && !empty($json["results"][0]['geometry']['location']['lat'])) {

            $return['lat'] = $json["results"][0]['geometry']['location']['lat'];
            $return['lng'] = $json["results"][0]['geometry']['location']['lng'];


        }

    }

    echo json_encode($return);
    exit();

}

function api_getAdresByPostcode($postcodenummers, $postcodeletters, $huisnummer)
{

    //$data_string = json_encode($data);

    $ch = curl_init('http://api.postcodeapi.nu/' . $postcodenummers . $postcodeletters . '/' . $huisnummer . '?view=bag');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

    //curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: */*',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept-Language: en*',
            'Api-Key: 0afe963315da9ef42c9bd109af3f51d7fc63bf74')
    );
    $result = curl_exec($ch);

    return $result;
}
function api_valuta_converter(){

    $q = db_select("SELECT * FROM valuta WHERE `default` = 1");
    $d = db_fetch_array($q);

    $q1 = db_select("SELECT * FROM valuta WHERE NOT `default` = 1");

    $send = 0;
    while($d1 = db_fetch_array($q1)){;


        $amount = 1;

        $from_Currency = urlencode("EUR");

        $to_Currency = urlencode($d1["code"]);

        $get = file_get_contents("https://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency");

        preg_match("/<span class=bld>([0-9.]*) {$d1["code"]}<\/span>/",$get,$matches);

        if(!empty($matches[1])){

            $column = array();
            $value = array();

            $column[] = "percent";
            $value[] = $matches[1];

            db_insert_of_update("valuta",$column,$value,$d1["id"],true,"WHERE id =");


        }else{


            @mail(ERROR_MAILTO, 'Domein : ' . $_SERVER['HTTP_HOST'], "Valuta converter api werkt niet meer");

        }

    }


}
function api_google_translate($van = "NL",$naar = "EN",$tekst){


    $vertaling = '';


    if($naar != 'nl'){
        usleep(1500);


        $response = file_get_contents('https://www.googleapis.com/language/translate/v2?key=AIzaSyCQR1dGaNoATPAqhWPGbZ3COvjMFu9FyCQ&q='.rawurlencode(fc_removeNewline($tekst)).'&source=nl&target='.$naar);

        $response_array = fc_json_decode_utf_8($response);


        $vertaling = $response_array["data"]["translations"][0]["translatedText"];


        if($tekst == $response_array["data"]["translations"][0]["translatedText"]){

            if($naar != 'en'){
                usleep(1500);

                $response = file_get_contents('https://www.googleapis.com/language/translate/v2?key=AIzaSyCQR1dGaNoATPAqhWPGbZ3COvjMFu9FyCQ&q='.rawurlencode(fc_removeNewline($tekst)).'&source=en&target='.$naar);

                $response_array = fc_json_decode_utf_8($response);


                $vertaling = $response_array["data"]["translations"][0]["translatedText"];

                if($tekst != $response_array["data"]["translations"][0]["translatedText"]){

                    $vertaling = str_replace("@ | @",TAGS_DELIMITER, $vertaling);

                    return $vertaling;

                }
            }

        }else{

            $vertaling = str_replace("@ | @",TAGS_DELIMITER, $vertaling);

            return $vertaling;

        }
    }


}
function api_geoplugin($ip = NULL, $purpose = "location", $deep_detect = TRUE) {


    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}
function api_trace_visitor(){


    $url = "http://api.db-ip.com/addrinfo?addr={$_SERVER['REMOTE_ADDR']}&api_key=".DBIP;
    $array = @get_headers($url);
    $string = $array[0];

    if(strpos($string,"200")) {


        $json = file_get_contents($url);

        $array = json_decode($json, true);

        if (!isset($array["error"])) {

            $array = json_decode($json, true);


            if (is_array($array) && isset($array["latitude"]) && isset($array["longitude"])) {


                $_SESSION["visitor"]["latitude"] = $array["latitude"];
                $_SESSION["visitor"]["longitude"] = $array["longitude"];
                $_SESSION["visitor"]["ip"] = $_SERVER['REMOTE_ADDR'];

                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

                if (!empty($lang)) {
                    $_SESSION["visitor"]["language"] = $lang;
                }


                db_query("CREATE TABLE IF NOT EXISTS `visitors` (
                    `id` int(11) unsigned NOT NULL auto_increment,
                    `session_id` varchar(45) NULL default '',
                    `cookie` varchar(45) NULL default '',
                    `ip` varchar(45) NULL default '',
                    `language` varchar(45) NULL default '',
                    `lat` varchar(45) NULL default '',
                    `lng` varchar(45) NULL default '',
                    `modified` int(11) NULL default null,
                    `created` int(11) NULL default null,
                    PRIMARY KEY  (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8");


                $column = array();
                $value = array();

                $column[] = "session_id";
                $value[] = session_id();

                $column[] = "ip";
                $value[] = $_SERVER['REMOTE_ADDR'];

                $column[] = "language";
                $value[] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

                $column[] = "lat";
                $value[] = $array["latitude"];

                $column[] = "lng";
                $value[] = $array["longitude"];

                $column[] = "modified";
                $value[] = time();


                if (isset($_SESSION["visitor"]["id"])) {

                    db_insert_of_update("visitors", $column, $value, $_SESSION["visitor"]["id"], true, "WHERE id=");


                } else if (isset($_COOKIE[FTP_HOST]) && db_count("id", "visitors", "cookie = '{$_COOKIE[FTP_HOST]}'") != 0) {


                    $_SESSION["visitor"]["id"] = db_insert_of_update("visitors", $column, $value, $_COOKIE[FTP_HOST], true, "WHERE cookie=");

                } else {

                    $cookie = fc_rand_sha1(32);

                    $column[] = "cookie";
                    $value[] = $cookie;

                    $column[] = "created";
                    $value[] = time();

                    setcookie(FTP_HOST, $cookie, time() + (86400 * 30), "/");

                    $_SESSION["visitor"]["id"] = db_insert_of_update("visitors", $column, $value, '', true);


                }


            }


        } else {

            @mail(ERROR_MAILTO, 'Domein : ' . $_SERVER['HTTP_HOST'], "Trace visitor api werkt niet meer: {$array["error"]}");

        }
    }else{

        @mail(ERROR_MAILTO, 'Domein : ' . $_SERVER['HTTP_HOST'], "Trace visitor api werkt niet meer: {$array["error"]}");

    }
}

function api_sent_mail($to,$subject,$message){

    include_once('modules/phpmailer/PHPMailerAutoload.php');

    $mail = new PHPMailer;


    $mail->IsSMTP();                                      // Set mailer to use SMTP
    $mail->Host = MAIL_HOST;                              // Specify main and backup server
    $mail->Port = 587;                                    // Set the SMTP port
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = MAIL_USER;                          // SMTP username
    $mail->Password = MAIL_PASS;                          // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

    $mail->From = 'noreply@'.BEHEERDER_DOMAIN;
    $mail->FromName = BEHEERDER_BEDRIJFSNAAM;


    $mail->AddAddress($to);  // Add a recipient

    $mail->addCustomHeader('X-MC-Track','opens');

    $mail->IsHTML(true);                                  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->AltBody = strip_tags($message);

    if($mail->Send()){
        return true;
    }else{

        err($mail->ErrorInfo);
    }

}
?>