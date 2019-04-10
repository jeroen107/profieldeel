<?php

//BEVEILING PLATFORM
include_once('common/firewall.php');

ob_start();

error_reporting(E_ALL);

date_default_timezone_set('Europe/Amsterdam');


//START SYSTEM
include_once('etc/config.php');
include_once('common/error_reporting.php');
include_once('common/database.php');
include_once('common/rewrite.php');
include_once('common/parser.php');
include_once('common/functions.php');
include_once('common/validation.php');
include_once('common/api.php');

if(CACHE == true){ parse_headers_cache($_SERVER['SCRIPT_FILENAME'], filemtime($_SERVER['SCRIPT_FILENAME'])); }


module_load("templates");
module_load("pages");
module_load("users");


db_connection();


include_once('common/installer.php');

//api_trace_visitor();


parse_url_get();


engine_init();

//error_handle_notices();

ob_end_flush();

?>
