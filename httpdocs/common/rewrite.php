<?php
$argv = array();
$page = array();
$argc = 0;

function parse_url_get() {
    global $argc, $argv;

    if(strlen($_GET["url"]) > 0 && substr($_GET["url"], -1) != "/") {
        $_GET["url"] .= "/";
    }
    if(isset($_GET['url']) && $_GET['url'] != "/") {
        parse_url_explode("/", $_GET['url']);
    }
}

function parse_url_explode($sep, $url)
{
    global $argc, $argv;

    //ARGS VULLEN
    $args = array();

    $count = substr_count($url, "/");
    $full_url = "/".trim($url,"/");

    for ($i = 0; $i < $count; $i++) {
        if($i != 0) {

            $url = substr($url, 1);
        }
        $pos = strpos($url, "/");
        if($pos === FALSE) {
            $pos = strlen($url);
        }
        $arg = substr($url, 0, $pos);
        if($arg != "") {
            $args[] = urldecode($arg);
        }
        $url = substr($url, $pos);
    }

    $argv = filter_var_array($args,FILTER_SANITIZE_STRING);
    $argc = count($argv);


    //PAGE VULLEN

    if(defined('DATABASE_USER_WWW')) {

        if (db_pdo_table_exist("seo", "WWW")) {


            $q = db_pdo_select("SHOW COLUMNS FROM seo", array(), "WWW");

            if ($q->rowCount() != 0) {

                $q = db_pdo_select("SELECT * FROM seo WHERE page_url = :url LIMIT 1", array(":url" => $full_url), "WWW");
                if ($q->rowCount() != 0) {
                    while ($d = db_pdo_fetch_array($q)) {
                        $page["seo"] = $d;

                        $q2 = db_pdo_select("SELECT * FROM " . $d["tabel"] . " WHERE id = :tabel_id", array(":tabel_id" => $d["tabel_id"]), "WWW");
                        if ($q2->rowCount() != 0) {
                            while ($d2 = db_pdo_fetch_array($q2)) {
                                $page["data"] = $d2;
                            }
                        }


                    }
                }

            }
        }
    }






}


function engine_init()
{
	global $argv, $argc, $time;


	include_once(parse_module());

    $secure_functions = array('db','parse','fc','engine','form','error');


        if (!@in_array($argv[0], $secure_functions)) {

            $func = parse_function();
            $func();

        } else {

            session_destroy();
            header("Location: http://{$_SERVER['HTTP_HOST']}/", true, 301);
            exit;

        }


}


function parse_function() {

	global $argc, $argv;


    //TOEGANG TOT VERSCHILLENDE MODULES VANUIT DE URL
    if(isset($_SESSION["user_backend"]["id"])) {

        $access = array("controllers", "users", "account", "views", "admin","email","facturatie","offerte","vertalingen","jobs","nieuwsbrief");

    }else if(isset($_SESSION["user_frontend"]["id"])) {

        $access = array("controllers", "users", "account");

    }else{

        $access = array("controllers","users","jobs","api");

    }



    if($argc > 1 && in_array($argv[0],$access) && function_exists($argv[0].'_'.$argv[1])){


        return $argv[0].'_'.$argv[1];

    }else{


        if(function_exists("pages_main")) {


            return 'pages_main';
        } else {


            trigger_error("Module function not found (looking for {$argv[0]}_{$argv[1]})", E_USER_ERROR);
        }
    }





}

function parse_module() {
	global $argv, $argc;


	if(isset($argv[0]) && file_exists('modules/'.$argv[0].'/'.$argv[0].'.php')) {

		return 'modules/'.$argv[0].'/'.$argv[0].'.php';

	} else if(file_exists('modules/pages/pages.php')) {

        return 'modules/pages/pages.php';
	}

}

function module_load($module, $halt_on_error = true)
{


	if(file_exists('modules/'.$module.'/'.$module.'.php')) {

		include_once('modules/'.$module.'/'.$module.'.php');

		return true;

	} else {

		if($halt_on_error) {

			trigger_error("Module does not exist");

		} else {

			return false;
		}
	}
}





?>
