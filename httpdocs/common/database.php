<?php
/*
	Database functions.
*/
$conn = '';

function db_pdo_connection($dbvar = ''){


    if(!empty($dbvar)){

        $db = new PDO('mysql:host='.DATABASE_HOST.';dbname='.constant("DATABASE_NAME_".$dbvar."").';charset=utf8',constant("DATABASE_USER_".$dbvar.""),constant("DATABASE_PASS_".$dbvar.""));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }else{


        $db = new PDO('mysql:host='.DATABASE_HOST.';dbname='.DATABASE_NAME.';charset=utf8',DATABASE_USER,DATABASE_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    return $db;
}
function db_pdo_select($query,$binds = array(),$dbvar = '')
{

    $conn = db_pdo_connection($dbvar);

    $stmt = $conn->prepare($query);

    $stmt->execute($binds);

    return $stmt;

}
function db_pdo_table_exist($table,$dbvar = '') {

    try {

        $conn = db_pdo_connection($dbvar);

        $stmt = $conn->prepare("SELECT 1 FROM $table LIMIT 1");

        $stmt->execute();

    } catch (Exception $e) {

        return FALSE;
    }

    return $stmt !== FALSE;
}
function db_pdo_fetch_array($result)
{

    $array = $result->fetch(PDO::FETCH_ASSOC);

    if(defined('DATABASE_USER_IRIS')) {


        //VIND URL
        preg_match("/FROM (.[\\S]*)/", $result->queryString, $match);

        $tables = explode(',', $match[1]);

        foreach ($tables as $k => $v) {

            if($v != "pages" && $v != "pages_content" && $v != "seo" && $v != "menus" && $v != "menus_content" && $v != "instellingen"){

                $q = db_pdo_select("SELECT page_url FROM seo where `tabel` = :table AND `tabel_id` = :table_id AND `language` = :language", array(":table" => $v, ":table_id" => $array["id"], ":language" => $_SESSION["visitor"]["language"]));

                if ($q->rowCount() != 0) {

                    $d = db_pdo_fetch_array($q);

                    $array["url"] = $d["page_url"];

                }

            }

        }

        //FOTO KOLOM
        if(is_array($array) && count($array) != 0) {
            foreach ($array as $k => $v) {

                if (empty($v) && ($k == 'foto' || $k == 'afbeelding')) {

                    $array[$k] = "/img/placehold.png";

                }

            }
        }

    }

    return $array;

}

function db_pdo_insert_of_update($table, $fields, $values,$id = null,$column = 'id',$dbvar = '')
{

    $array = array();

    foreach ($fields AS $k => $v){$array[":".$v] = $values[$k];}

    if($id == null){

        try {

            $conn = db_pdo_connection($dbvar);

            $stmt = $conn->prepare("INSERT INTO `{$table}` (".implode(",", $fields).") VALUES (:".implode(",:", $fields).")");

            $stmt->execute($array);

            return $conn->lastInsertId();

        } catch(PDOExecption $e) {

            err_pdo($e);

        }

    }else{

        $array[":id"] = $id;

        $s = '';

        foreach($fields AS $key => $value){ if(!empty($s)){$s .= ',';} $s .= $value." = :".$value; }

        try {


            $q = db_pdo_select("UPDATE `{$table}` SET {$s} WHERE {$column} = :id", $array,$dbvar);

            return $id;

        } catch(PDOExecption $e) {

            err_pdo($e);

        }

    }

}
function db_pdo_select_all($query,$binds = array(),$dbvar = '')
{

    $q = db_pdo_select($query, $binds,$dbvar);

    $a = array();

    while ($d = db_pdo_fetch_array($q)) {

        $a[] = $d;

    }

    return $a;

}
function db_pdo_select_all_key($query,$binds = array(),$dbvar = '',$key,$value)
{

    $q = db_pdo_select($query, $binds,$dbvar);

    $a = array();

    while ($d = db_pdo_fetch_array($q)) {

        $a[$d[$key]] = $d[$value];

    }

    return $a;

}
function db_pdo_insert_of_update_array($array,$table){



    $column = array();
    $value = array();

    foreach ($array as $k => $v){


        $d = db_pdo_fetch_array(db_pdo_select("SHOW COLUMNS FROM {$table} WHERE Field = :val", array(":val" => $k)));

        if(!empty($d["Field"])) {

            $column[] = $k;
            $value[] = $v;

        }

    }

    if(isset($array["id"]) && !empty($array["id"])) {


        db_pdo_insert_of_update($table, $column, $value, $array["id"], 'id');

    }else{

        db_pdo_insert_of_update($table, $column, $value, null, '');

    }


}


function db_connection($dbvar = ''){

    if(!empty($dbvar)){

        $conn = mysqli_connect(DATABASE_HOST, constant("DATABASE_USER_".$dbvar.""), constant("DATABASE_PASS_".$dbvar.""));
        $conn->set_charset("utf8");
        mysqli_select_db($conn,constant("DATABASE_NAME_".$dbvar.""));

    }else{


        $conn = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS);
        $conn->set_charset("utf8");
        mysqli_select_db($conn, DATABASE_NAME);


    }

    return $conn;
}

function db_connection_oop($dbvar = ''){

    if(!empty($dbvar)){

        $db = new mysqli(DATABASE_HOST, constant("DATABASE_USER_".$dbvar.""), constant("DATABASE_PASS_".$dbvar.""), constant("DATABASE_NAME_".$dbvar.""), 3306);
        $db->set_charset("utf8");
    }else {

        $db = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME, 3306);
        $db->set_charset("utf8");
    }

    return $db;
}



function db_escape($value,$dbvar = ''){

    return mysqli_real_escape_string(db_connection($dbvar),$value);
}
function db_query($query,$dbvar = '')
{

    return mysqli_query(db_connection($dbvar),$query);
}

function db_close()
{
    mysql_close();
}

function db_table_exists($table,$dbvar = '')
{
    if (mysqli_num_rows(mysqli_query(db_connection($dbvar),"SHOW TABLES LIKE '".$table."'")) == 1) {
        return true;
    } else {
        return false;
    }
}

function db_strip_empty_fields($fields, $values, $escape_fields = false,$dbvar = '')
{
    $i = 0;

    foreach ($values as $value) {
        if($value == "") { // NOTE: do not use php's empty() function here; it treats 0 as empty. And 0 just so happens to be a very, very valid integer number.
            // unset($values[$i], $fields[$i]);
            $values[$i] = "NULL";
        } else {
            ($escape_fields) ? $values[$i] = "'".mysqli_real_escape_string(db_connection($dbvar),$values[$i])."'" : $values[$i] = "'".$values[$i]."'";
        }
        $i++;
    }

    return array($fields,$values);
}

function db_count($count_what='*', $count_table='', $count_where = "",$dbvar = '')
{
    if ($count_where != ""){
        $count_where = " where ".$count_where;
    }


    $search = db_pdo_select_all("select ".$count_what." from ".$count_table." ".$count_where,array(),$dbvar);

    return count($search);
}
function db_add_string($to_add, $glue = " ")
{
    if($to_add != "") {
        return $glue.$to_add;
    }
}



/*
 'insert' functions.
 All return id for new row.
*/
function db_insert($query)
{
    mysqli_query(db_connection(),$query);
    return mysqli_insert_id(db_connection());
}

function db_insert_auto_fields($table, $fields, $values, $escape_fields = false)
{
    global $time;

    // Fix user id: it might not be present.
    (isset($_SESSION["user"]["id"])) ? $use_id = $_SESSION["user"]["id"] : $use_id = 0;

    list($fields,$values) = db_strip_empty_fields($fields, $values, $escape_fields);
    $query = "INSERT INTO {$table} (".implode(",", $fields).", created, modified, owner) VALUES (".implode(",", $values).", '$time', '$time', '{$use_id}');";
    mysqli_query(db_connection(),$query);

    return mysqli_insert_id(db_connection());
}
function db_insert_of_update($table, $fields, $values,$id = null,$escape_fields = true,$where = "",$dbvar = '')
{



    if($id == null){


        list($fields,$values) = db_strip_empty_fields($fields, $values, $escape_fields,$dbvar);

        $query = "INSERT INTO `{$table}` (".implode(",", $fields).") VALUES (".implode(",", $values).");";


        $connection = db_connection($dbvar);

        $result = mysqli_query($connection,$query) or die(mysqli_error($dbvar));

        if(!$result){err($connection -> error);}

        $nummer = mysqli_insert_id($connection);

        return $nummer;

    }else{




        list($fields,$values) = db_strip_empty_fields($fields, $values, $escape_fields,$dbvar);
        $s = "";

        foreach($fields AS $key => $value){
            if(!empty($s)){
                $s .= ',';
            }
            $s .= "`".$value."` = ".$values[$key];
        }

        $query = "UPDATE `{$table}` SET {$s} {$where} '{$id}'";

        $connection = db_connection($dbvar);

        $result = mysqli_query($connection,$query) or die(mysqli_error($dbvar));

        if(!$result){err($connection -> error);}

        return $id;

    }

}

/*
 'update' functions.
*/
function db_update($query,$dbvar = '')
{
    mysqli_query(db_connection($dbvar),$query) or die(mysqli_error(db_connection($dbvar)));
}



/*
 'delete' functions
*/
function db_delete_where($table, $fields, $values)
{
    if(count($fields) == count($values)) {
        $pairs = array();

        if(is_array($fields)) {
            for($i = 0; $i < count($fields); $i++) {
                $pairs[] = $fields[$i]." = '".$values[$i]."'";
            }
        } else {
            $pairs[] = $fields." = '".$values."'";
        }

        $query = "DELETE FROM {$table} WHERE ".implode(" AND ", $pairs);
        mysqli_query(db_connection(),$query);
    } else {
        trigger_error("Fields and value count mismatch", E_USER_ERROR);
    }
}

function db_delete ($query,$dbvar = '')
{
    return mysqli_query(db_connection($dbvar),$query);
}


/*
 'select' functions
*/



function db_select($query,$dbvar = '',$check = false)
{

    $conn = db_connection($dbvar);

    $result = mysqli_query($conn,$query);

    if(!$result){err($conn -> error);}

    return $result;

}
function db_select_row ($table, $id)
{
    $query = "SELECT * FROM {$table} WHERE id = {$id}";
    return mysql_fetch_array(mysql_query($query), MYSQL_ASSOC);
}

function db_select_row_crits ($table, $fields, $values, $return_rows = false, $sort_clause = "",$isNot = false,$dbvar = '')
{


    if(count($fields) == count($values)) {
        $pairs = array();

        if(!empty($fields)){



            if($isNot == false){

                if(is_array($fields)) {
                    for($i = 0; $i < count($fields); $i++) {
                        $pairs[] = $fields[$i]." = '".mysqli_real_escape_string(db_connection($dbvar),$values[$i])."'";
                    }
                } else {


                    $pairs[] = $fields." = '".mysqli_real_escape_string(db_connection($dbvar),$values)."'";

                }
            }else{


                if(is_array($fields)) {
                    for($i = 0; $i < count($fields); $i++) {
                        $pairs[] = $fields[$i]." <> '".mysqli_real_escape_string(db_connection($dbvar),$values[$i])."'";
                    }
                } else {
                    $pairs[] = $fields." <> '".mysqli_real_escape_string(db_connection($dbvar),$values)."'";
                }
            }

            $query = "SELECT * FROM {$table} WHERE ".implode(" AND ", $pairs);

        }else{
            $query = "SELECT * FROM {$table}";
        }


        if(!empty($sort_clause)) {
            $query .= " ORDER BY ".$sort_clause;
        }



        $result =  db_query($query,$dbvar);


        if($return_rows) {

            if(mysqli_num_rows($result)) {

                $rows = array();
                while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {


                    $rows[] = db_stripcslashes_deep($row);
                }




                return $rows;


            } else {

                return array();
            }
        } else {


            return $result;
        }
    }
}
function db_stripcslashes_deep($value)
{
    $value = is_array($value) ? array_map('db_stripcslashes_deep', $value) : stripcslashes($value);

    return $value;
}
function db_num_rows($result)
{

    return mysqli_num_rows($result);
}

function db_select_all_rows($table, $order_clause = false,$connection)
{
    $query = "SELECT * FROM {$table}";
    if($order_clause) {
        $query .= " ORDER BY {$order_clause}";
    }

    $result = mysql_query($query,$connection);

    if(mysql_num_rows($result) > 0) {
        return db_fetch_array_all($result);
    } else {
        return false;
    }
}



/*
 'fetch' functions
*/
function db_fetch_array ($result)
{
    return mysqli_fetch_array($result, MYSQL_ASSOC);
}

function db_fetch_array_all($result)
{
    $rows = array();
    while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $rows[] = $row;
    }
    return $rows;
}

function db_fetch_array_key($result,$key,$value)
{
    $rows = array();
    while($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $rows[$row[$key]] = $row[$value];
    }
    return $rows;
}


?>