<?php
function parse_timestamp_datum($timestamp,$seconds = false)
{

    if($timestamp != '' && $timestamp != 0) {

        $maanden = array('januari','februari','maart','april','mei','juni','juli','augustus','september','october','november','december');


        $part_min = date('i',$timestamp);
        $part_hour = date('G',$timestamp);
        $part_day = date('d',$timestamp);
        $part_month = date('n',$timestamp);
        $part_year = date('Y',$timestamp);


        if($seconds == true){

            if($part_min == "0" && $part_hour == "00"){

                return $part_day." ".$maanden[$part_month-1]." ".$part_year;

            }else{

                return $part_day." ".$maanden[$part_month-1]." ".$part_year." | ".$part_hour.".".$part_min;
            }

        }else{
            return $part_day." ".$maanden[$part_month-1]." ".$part_year;
        }
    } else {
        return "";
    }
}
function parse_timestamp_NLdate($timestamp,$seperator='-',$seconds = false)
{

    if($timestamp != '') {


        if($seconds == false){

            $date = date("d-m-Y", $timestamp);
            $date_parts = explode("-",$date);

            return $date_parts[0]."{$seperator}".$date_parts[1]."{$seperator}".$date_parts[2];

        }else{

            $date = date("d-m-Y-H-i", $timestamp);

            $date_parts = explode("-",$date);

            return $date_parts[0]."{$seperator}".$date_parts[1]."{$seperator}".$date_parts[2]." ".$date_parts[3].":".$date_parts[4];

        }


    } else {

        return "";

    }
}
function parse_timestamp_fulldate($timestamp,$seconds = true)
{
    if($timestamp != '' && $timestamp != 0) {
        if($seconds == true){

            $tijd = date('G:i',$timestamp);

            if($tijd == "0:00"){

                return date("d-m-Y", $timestamp);

            }else{

                return date("d-m-Y | G:i", $timestamp);
            }


        }else{
            return date("d-m-Y", $timestamp);
        }
    } else {
        return "";
    }
}
function parse_timestamp_sitemap($timestamp,$seconds = true)
{
    if($timestamp != '') {

        $part1 = date("Y-m-d", $timestamp);
        $part2 = "T";
        $part3 = date("G:i:s", $timestamp);
        $part4 = "+01:00";

        $implode = $part1.$part2.$part3.$part4;

        return $implode;

    } else {
        return "";
    }
}
function parse_checked($value){

    if($value == 1){
        return "checked";
    }

}
function parse_checked_radio($value,$checked){

    if($checked == true){
        if(empty($value)){
            return "checked";
        }
    }else{
        if(!empty($value)){
            return "checked";
        }
    }

}
function parse_checked_radio_check($anqor,$value){

    if($anqor == $value){

        return "checked";

    }

}
function parse_on($value){

    if($value == "on"){
        return 1;
    }

}
function parse_max_text_size($string,$length){

    global $argv, $argc, $time;

    $count = strlen(strip_tags($string));
    if($count >= $length){
        $rest = substr ($string, 0, $length-1);
        $rest .= "...";
    }else{
        $rest = $string;
    }

    return $rest;

}
function parse_max_text_size2($string,$length){

    global $argv, $argc, $time;

    $count = strlen(strip_tags($string));
    if($count >= $length){
        $rest = substr ($string, 0, $length-1);
        $rest .= "";
    }else{
        $rest = $string;
    }

    return $rest;

}
function parse_max_text_size_strip($string,$length){

    global $argv, $argc, $time;

    $count = strlen(strip_tags($string));
    if($count >= $length){
        $rest = substr (strip_tags($string), 0, $length-1);
        $rest .= "...";
    }else{
        $rest = strip_tags($string);
    }

    return $rest;

}



function parse_select($id='',$name,$class='',$db_table='',$db_column='',$db_where='',$db_order='',$db_sort='ASC',$option_top='',$selected_itemId='',$multiple=false,$size='',$style='',$trigger='',$array='',$db_table_id=''){
	
	if(!empty($db_table)){
		
		if(empty($db_table_id)){trigger_error("Maken van select: db_table_id is leeg", E_USER_ERROR);}
		if(empty($db_table)){trigger_error("Maken van select: db_order is leeg", E_USER_ERROR);}
		if(empty($db_column)){trigger_error("Maken van select: db_order is leeg", E_USER_ERROR);}


		$query = db_select("SELECT * FROM {$db_table} {$db_where} ORDER BY {$db_order} {$db_sort}");


		$string = "";
		if($multiple == false){
			$string .= "<select id='{$id}' name='{$name}' class='select2_category form-control' data-placeholder='{$option_top}'  size='{$size}' style='{$style}' {$trigger}>";
		}else{
			$string .= "<select id='{$id}' name='{$name}[]' multiple=\"multiple\" class='select2_category form-control' data-placeholder='{$option_top}' size='{$size}' style='{$style}'>";
		}

		if($option_top != ""){
			$string .= "<option value='0'>{$option_top}</option>";
		}
		
		while ($data = db_fetch_array($query)){
			if(is_array($selected_itemId)){
				$gezet = 0;
				foreach($selected_itemId as $k => $v){
					
					if($data[$db_table_id] == $v){
						
						$result = stripcslashes($data[$db_column]);
						$string .= "<option value='{$data[$db_table_id]}' selected='selected'>{$result}</option>";
						$gezet = 1;
					}
				
				}
				if($gezet == 0){
				$result = stripcslashes($data[$db_column]);
				$string .= "<option value='{$data[$db_table_id]}'>{$result}</option>";
				}
				
			}else{
				if($data[$db_table_id] != $selected_itemId){
					$result = stripcslashes($data[$db_column]);
					$string .= "<option value='{$data[$db_table_id]}'>{$result}</option>";
				}else{
					$result = stripcslashes($data[$db_column]);
					$string .= "<option value='{$data[$db_table_id]}' selected='selected'>{$result}</option>";
				}
			}
			
		}
	}else{
		
		if(empty($array)){trigger_error("Maken van select: array is leeg", E_USER_ERROR);}
	


		$string = "";
		if($multiple == false){
			$string .= "<select id='{$name}' name='{$name}' class='select2_category form-control' data-placeholder='{$option_top}' size='{$size}' style='{$style}' {$trigger}>";

            if($option_top != ""){
                $string .= "<option value=''>{$option_top}</option>";
            }

            if($array)
            foreach($array AS $key => $value){

                if($key != $selected_itemId){

                    $string .= "<option value='{$key}'>".stripcslashes($value)."</option>";
                }else{

                    $string .= "<option value='{$key}' selected='selected'>".stripcslashes($value)."</option>";
                }
            }
		}else{
			$string .= "<select id='{$name}' name='{$name}[]' multiple='multiple' class='{$class} select2' size='{$size}' style='width:100%'>";

            if($option_top != ""){
                $string .= "<option value='0'>{$option_top}</option>";
            }

            foreach($array AS $key => $value){

                if(is_array($selected_itemId)){
                    if(!in_array($key,$selected_itemId)){

                        $string .= "<option value='{$key}' >".stripcslashes($value)."</option>";
                    }else{

                        $string .= "<option value='{$key}' selected='selected' >".stripcslashes($value)."</option>";
                    }
                }else{
                    $string .= "<option value='{$key}' >".stripcslashes($value)."</option>";
                }
            }
		}


	}

	$string .= "</select>";
	
	return $string;
}
function parse_translate($fc,$fv){

    $blok = db_fetch_array(db_select("select * from vertalingen where slug = '".$fv[0]."'","IRIS"));

    return  output($blok["vertaling"]);

}

function taal(){

    return $_SESSION["visitor"]["language"];

}
function url($fc,$fv){

    $d = db_fetch_array(db_select("SELECT * FROM pages WHERE page = '".input($fv[0])."' AND language = '".input($_SESSION["visitor"]["language"])."'"));

    return "/".$_SESSION["visitor"]["language"]."/".$d["url"];
}
function info($fc,$fv){

    $d = db_pdo_fetch_array(db_pdo_select("SELECT * FROM instellingen WHERE slug = :val", array(":val" => $fv[0])));

    if(fc_isJson($d["waarde"])) {
        $waard = json_decode($d["waarde"], true);

        return $waard[$_SESSION["visitor"]["language"]];
    }else{
        return $d["waarde"];
    }

}
?>