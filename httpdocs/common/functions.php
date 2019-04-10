<?php

function price($price){

    return '&euro;'.number_format($price,2,',','');
}
function fc_getSettings($slug){
  $data = db_pdo_fetch_array(db_pdo_select("SELECT * FROM settings where slug = :slug",array(":slug" => $slug)));
  if ($data["id"] == ""){
    db_pdo_select("insert into settings (slug,naam)values(:slug,:slug)",array(":slug" => $slug));
  }else{
    return $data["value"];
  }
}

/* ARRAY FUNCTIONS */
function fc_dateNetherlands($timestamp, $showTime = false) {

    $dayNames = ["maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag", "zondag"];
    $monthNames = ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"];

    array_unshift($dayNames, "");
    array_unshift($monthNames, "");

    $dayOfWeek = ucfirst($dayNames[date('N', $timestamp)]);

    $monthDay = date('j', $timestamp);
    $month = $monthNames[date('n', $timestamp)];
    $year = date('Y', $timestamp);

    $time = date('H:i', $timestamp);

    if ($showTime != false ) {

        // example: Dinsdag 17 april 2018, 15:50
        return $dayOfWeek . ' ' . $monthDay .' ' . $month . ' ' . $year . ', ' . $time;

    } else {

        // example: Dinsdag 17 april 2018
        return $dayOfWeek . ' ' . $monthDay .' ' . $month . ' ' . $year;

    }

}

function fc_array_insert_before($key,$array, $new_key, $new_value) {
    if (array_key_exists($key, $array)) {
        $new = array();
        foreach ($array as $k => $value) {
            if ($k === $key) {
                $new[$new_key] = $new_value;
            }
            $new[$k] = $value;
        }
        return $new;
    }
    return FALSE;
}

function fc_array_insert_after($key,$array, $new_key, $new_value) {

    if (array_key_exists($key, $array)) {
        $new = array();

        foreach ($array as $k => $value) {
            $new[$k] = $value;
            if ($k === $key) {
                $new[$new_key] = $new_value;
            }
        }
        return $new;
    }
    return FALSE;
}
function fc_array_nested_empty($array){

    if(is_array($array)){
        foreach($array as $k => $v){

            if(is_array($v))
                fc_array_nested_empty($v);

            if(!empty($v))
                return true;
            else
                return false;

        }
    }else{

        return false;
    }

}

function fc_array_searchRecursive( $needle, $haystack, $strict=false, $path=array() )
{
    if( !is_array($haystack) ) {
        return false;
    }

    foreach( $haystack as $key => $val ) {
        if( is_array($val) && $subPath = fc_array_searchRecursive($needle, $val, $strict, $path) ) {
            $path = array_merge($path, array($key), $subPath);
            return $path;
        } elseif( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
            $path[] = $key;
            return $path;
        }
    }
    return false;
}
function fc_has_empty($array) {
    foreach ($array as $value) {
        if ($value != "")
            return true;
    }
    return false;
}
function fc_in_arrayr( $needle, $haystack ) {

    /* zoekt naar value in nested array */
    foreach( $haystack as $v ){

        if( $needle == $v ){

            return true;
        }else if( is_array( $v ) ){

            if( fc_in_arrayr( $needle, $v ) ){
                return true;
            }
        }
    }

    return false;
}
function fc_array_key( $needle, $haystack ) {
    /* zoekt naar meerdere keys in array */
    foreach( $needle as $key => $value){
        if(array_key_exists($value,$haystack)){
            return true;
        }
    }
    return false;
}
function fc_array_keys_multi($array,&$vals)
{
    /* Haal alle nested value uit array en zet ze in 1 level */
    foreach ($array as $key => $value) {

        if (is_array($value)) {

            fc_array_keys_multi($value,$vals);

        }else{

            $vals[] = $value;
        }
    }

    return $vals;
}
function fc_dirToArray($dir,$sub = false) {

    $result = array();

    $cdir = scandir($dir);


    foreach ($cdir as $key => $value)
    {
        if (!in_array($value,array(".","..")))
        {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
            {
                if($sub == true){
                    $result[$value] = fc_dirToArray($dir . DIRECTORY_SEPARATOR . $value,true);
                }

            }
            else
            {
                $result[] = $value;
            }
        }
    }


    return $result;
}
function fc_array_empty($mixed) {

    //check nested array is empty

    if (is_array($mixed)) {

        foreach ($mixed as $value) {
            if (fc_array_empty($value)) {
                return false;
            }
        }

    } elseif (empty($mixed)) {

        return false;

    }
    return true;
}






/* ARRAY FUNCTIONS */




function fc_if_url_exists($url) {
    if (!$fp = curl_init($url)) return false;
    return $url;
}


function fc_save_image($inPath,$outPath)
{ //Download images from remote server

    $url = $inPath;
    $header_response = get_headers($url, 1);
    if ( strpos( $header_response[0], "404" ) !== false )
    {
        // FILE DOES NOT EXIST
    }
    else
    {
        $in =  fopen($inPath, "rb");
        $out = fopen($outPath, "wb");

        if($in && $out){

            while ($chunk = fread($in,8192))
            {
                fwrite($out, $chunk, 8192);
            }
            fclose($in);
            fclose($out);

        }

    }

}
function fc_encodeURI($url) {
    // http://php.net/manual/en/function.rawurlencode.php
    // https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/encodeURI
    $unescaped = array(
        '%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
        '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
    );
    $reserved = array(
        '%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
        '%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$'
    );
    $score = array(
        '%23'=>'#'
    );
    return strtr(rawurlencode($url), array_merge($reserved,$unescaped,$score));

}



function fc_putcsv($filePointer,$dataArray,$delimiter,$enclosure)
{
    // Write a line to a file
    // $filePointer = the file resource to write to
    // $dataArray = the data to write out
    // $delimeter = the field separator

    // Build the string
    $string = "";

    // No leading delimiter
    $writeDelimiter = FALSE;
    foreach($dataArray as $dataElement)
    {
        // Replaces a double quote with two double quotes
        $dataElement=str_replace("\"", "\"\"", $dataElement);

        // Adds a delimiter before each field (except the first)
        if($writeDelimiter) $string .= $delimiter;

        // Encloses each field with $enclosure and adds it to the string
        $string .= $enclosure . $dataElement . $enclosure;

        // Delimiters are used every time except the first.
        $writeDelimiter = TRUE;
    } // end foreach($dataArray as $dataElement)

    // Append new line
    $string .= "\n";

    // $fp = fopen($filePointer, 'w');
    // Write the string to the file
    //fwrite($fp,$string);
    file_put_contents($filePointer, $string, FILE_APPEND | LOCK_EX);
}
function fc_putcsv_array($filePointer,$dataArray,$delimiter,$enclosure)
{
    // Write a line to a file
    // $filePointer = the file resource to write to
    // $dataArray = the data to write out
    // $delimeter = the field separator

    // Build the string
    $string = "";

    // No leading delimiter
    $writeDelimiter = FALSE;

    foreach($dataArray as $k){

        foreach($k as $dataElement)
        {
            // Replaces a double quote with two double quotes
            $dataElement=str_replace("\"", "\"\"", $dataElement);

            // Adds a delimiter before each field (except the first)
            if($writeDelimiter) $string .= $delimiter;

            // Encloses each field with $enclosure and adds it to the string
            $string .= $enclosure . $dataElement . $enclosure;

            // Delimiters are used every time except the first.
            $writeDelimiter = TRUE;
        } // end foreach($dataArray as $dataElement)
        $writeDelimiter = FALSE;
        // Append new line
        $string .= "\n";

    }

    // $fp = fopen($filePointer, 'w');
    // Write the string to the file
    //fwrite($fp,$string);





    file_put_contents($filePointer, utf8_decode($string) , FILE_APPEND | LOCK_EX);
}








function fc_json_encode_utf_8($array){


    $str = json_encode($array);

    $str = preg_replace_callback(
        '/\\\\u([0-9a-f]{4})/i',
        function ($matches) {
            $sym = mb_convert_encoding(
                pack('H*', $matches[1]),
                'UTF-8',
                'UTF-16'
            );
            return $sym;
        },
        $str
    );

    return $str;


}
function fc_json_decode_utf_8($str){


    $str = preg_replace_callback(
        '/\\\\u([0-9a-f]{4})/i',
        function ($matches) {
            $sym = mb_convert_encoding(
                pack('H*', $matches[1]),
                'UTF-8',
                'UTF-16'
            );
            return $sym;
        },
        $str
    );


    $array = json_decode($str,true);


    return $array;


}




function fc_rand_sha1($length) {
    $max = ceil($length / 40);
    $random = '';
    for ($i = 0; $i < $max; $i ++) {
        $random .= sha1(microtime(true).mt_rand(10000,90000));
    }
    return substr($random, 0, $length);
}



function fc_post_default($default,$value){

    if(!empty($value)){
        return $value;
    }else{
        return $default;
    }

}


function fc_stripcslashes_deep($value)
{
    $value = is_array($value) ? array_map('fc_stripcslashes_deep', $value) : stripcslashes($value);

    return $value;
}
function fc_removeNewline($subject){

    $newstring = preg_replace("/[\n\r]/","",$subject);
    $string = str_replace("\t",'',$newstring);

    return $string;

}
function fc_saveOrder($table,$colomn,$value,$idname = 'id')
{

    $array = db_select_row_crits($table,$colomn,$value,true,"volgorde ASC");

    if(!empty($array)){
        $i = 1;
        foreach($array AS $key => $value)
        {

            db_update("UPDATE $table SET volgorde={$i} WHERE $idname = {$value["$idname"]}");
            $i++;
        }
    }
}

function fc_switch_active($table,$id){

    $active = db_count("active",$table,"id='{$id}' AND active='1'");

    if($active == 1){

        db_update("UPDATE $table SET active='0' WHERE id='{$id}'");
    }else{
        db_update("UPDATE $table SET active='1' WHERE id='{$id}'");
    }


}
function fc_select($array,$selectItem = '')
{

    return parse_select('selectItem','selectItem','','','','','','','Selecteer',$selectItem,$multiple=false,'','','',$array);

}

function fc_valuta($bedrag,$butto_netto="",$btwonly=0,$formated=0,$entity=false,$valuta = '',$btw = 0)
{


    if($butto_netto == "bruto"){
        $uitkomst = $bedrag + ($bedrag*($btw/100));
    }else if($butto_netto == "netto"){
        $uitkomst = $bedrag - ($bedrag*($btw/100));
    }else{
        $uitkomst = $bedrag;
    }

    if($btwonly == 1){

        $uitkomst = $uitkomst*($btw/100);

    }


    if($formated == 0){

        return round($uitkomst,2);

    }else{


        if($valuta != ''){

            $aValuta = db_select_row_crits("facturatie_valuta","id",$valuta,true);

        }else{

            $aValuta = db_select_row_crits("facturatie_valuta","id",$_SESSION["bezoeker"]["valutaID"],true);
        }


        $uitkomst = round($uitkomst * $aValuta[0]["percent"],2);

        if($entity)
            return $aValuta[0]["symbool_entity"]." ".number_format($uitkomst,2,',','.');
        else
            return $aValuta[0]["symbool"]." ".number_format($uitkomst,2,',','.');
    }



}
function fc_friendly_url($str, $delimiter='-') {


    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

    return $clean;
}


function fc_translate($json,$language){


    //deprecated: NEW function output in validation.php

    $array = json_decode($json,true);

    if (is_array($array) == true){
        $s =  html_entity_decode($array[$language],ENT_COMPAT, 'UTF-8');
    }else{
        $s =  html_entity_decode($json,ENT_COMPAT, 'UTF-8');
    }


    return $s;
}



function fc_email_data($id,$array,$language,$db = 'IRIS'){



    $data = db_pdo_fetch_array(db_pdo_select("SELECT * FROM email_messages em JOIN email_templates et ON em.template_id = et.id  WHERE em.id='{$id}'"));


    $temp = $data["html"];

    $temp = preg_replace('/#DSE_INSTALL_URL#/', DSE_INSTALL_URL, $temp);
    $temp = preg_replace('/#MESSAGE#/', fc_translate($data["message"],$language), $temp);
    $temp = preg_replace('/#SUBJECT#/', fc_translate($data["subject"],$language), $temp);


    $aString[] = fc_translate($data["subject"],$language);
    $aString[] = html_entity_decode($temp);


    foreach($aString as $k => $v){
        $returned_int = preg_match_all("/#([A-Za-z_0-9]{2,50})*?#/", $v, $matches);

        $patterns = array();
        $replacements = array();




        if($returned_int != 0){
            $colomn = implode(",", $matches[1]);

            foreach ($matches[1] as $key => $value){

                if($value == "table"){


                    $s = "<table width='275'> <tbody> ";
                    foreach ($array[$value] as $kTable => $vTable){

                        if(is_int($vTable) && strlen($vTable) == 10){
                            $s .= "<tr> <td width='150'><b>" . ucfirst($kTable) . "<br /></b></td> <td>".parse_timestamp_fulldate($vTable)."</td> </tr> ";
                        }else {
                            $s .= "<tr> <td width='150'><b>" . ucfirst($kTable) . "<br /></b></td> <td>{$vTable}</td> </tr> ";
                        }

                    }
                    $s .= "</tbody></table>  ";

                    $patterns[] = "/#({$value})#/";
                    $replacements[] = $s;



                }else{

                    $patterns[] = "/#({$value})#/";
                    $replacements[] = $array[$value];

                }


            }

        }


        $aReturn[] = preg_replace($patterns,$replacements,$v);


    }

    return $aReturn;
}



function fc_myhsc(&$item, $key) {


    $item = htmlentities(trim($item),ENT_QUOTES,"UTF-8",false);

}
function fc_strip(&$item, $key) {


    $item = strip_tags($item);

}

function fc_visitors_analytics($kolom){


    $maand = date("n");
    $jaar = date("Y");


    $d = db_fetch_array(db_select("SELECT * FROM visitors_analytics WHERE maand = {$maand} AND jaar = {$jaar}"));


    if(!empty($d["id"])){

        db_update("UPDATE visitors_analytics SET {$kolom} = {$kolom} + 1, modified = '".time()."' WHERE id = {$d["id"]}");

    }else{

        $column = array();
        $value = array();

        $column[] = $kolom;
        $value[] = 1;

        $column[] = "maand";
        $value[] = $maand;

        $column[] = "jaar";
        $value[] = $jaar;

        $column[] = "modified";
        $value[] = time();

        db_insert_of_update("visitors_analytics", $column, $value);

    }



}
function fc_controle_btw_nummer($vat_number){


    if(!empty($vat_number))
    {

        $vat_number = str_replace(" ","",$vat_number);


        $resp = file_get_contents("http://apilayer.net/api/validate?access_key=".APILAYER."&vat_number=".$vat_number);

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
function fc_url_exist($url){

    $url = str_replace(" ","%20",$url);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200){
        $status = true;
    }else{
        $status = false;
    }
    curl_close($ch);
    return $status;
}
function fc_friendly_array_url(&$item, $key) {


    $item = fc_friendly_url($item);

}

function fc_email_sent($to,$subject,$html,$reply_email = '',$reply_name = '',$bijlage = array()){


    require_once $_SERVER["DOCUMENT_ROOT"].'/common/plugins/mandrill/Mandrill.php';


    try {

        $mandrill = new Mandrill(fc_getSettings('mandrill_api'));

        if(!empty($reply_email) AND !empty($reply_email)) {
            $reply = array('Reply-To' => $reply_name.' <'.$reply_email.'>');
        }else if(!empty($reply_email) AND empty($reply_name)){
            $reply = array('Reply-To' => $reply_email);
        }else{
            $reply = null;
        }

        $message = array(
            'html' => $html,
            'text' => strip_tags($html),
            'subject' => $subject,
            'from_email' => fc_getSettings('email_afzender'),
            'from_name' => fc_getSettings('email_afzenderadres'),
            'to' => array(
                array(
                    'email' => $to,
                    'type' => 'to'
                )
            ),
            'headers' => $reply,
            'important' => false,
            'track_opens' => true,
            'track_clicks' => true,
            'auto_text' => true,
            'auto_html' => true,
            'inline_css' => false,
            'url_strip_qs' => null,
            'preserve_recipients' => null,
            'view_content_link' => null,
            'merge' => false
        );

        if (is_array($bijlage["bijlage"]) and count($bijlage["bijlage"]) > 0){

            foreach ($bijlage["bijlage"] as $k => $v){

                $message["attachments"][$k]["type"] = $v["type"];
                $message["attachments"][$k]["name"] =  $v["name"];
                $message["attachments"][$k]["content"] =  $v["content"];

            }
        }


        $result = $mandrill->messages->send($message);


    } catch(Mandrill_Error $e) {


        err('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());

        throw $e;


    }

}
function fc_isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

function fc_isOriginAllowed($incomingOrigin, $allowOrigin)
{
    $pattern = '/^http:\/\/([\w_-]+\.)*' . $allowOrigin . '$/';

    $allow = preg_match($pattern, $incomingOrigin);
    if ($allow)
    {
        return true;
    }
    else
    {
        return false;
    }
}

?>
