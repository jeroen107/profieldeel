<?php
/*
	Generic, easy-to-use error reporting functionality for DSE 1.0

	We need to check for these errors:
	E_WARNING, E_NOTICE, E_USER_NOTICE, E_USER_ERROR, E_USER_WARNING

	Can't deal with E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, and E_STRICT (PHP restriction).

	Don't forget to call error_handle_notices() after each script! We want our notices dammit!
*/

// Empty array to save NOTICE type errors in
$error_notices = array();

// Array with error types
$errors = array(	1 => "E_ERROR",
					2 => "E_WARNING",
					4 => "E_PARSE",
					8 => "E_NOTICE",
					16 => "E_CORE_ERROR",
					32 => "E_CORE_WARNING",
					64 => "E_COMPILE_ERROR",
					128 => "E_COMPILE_WARNING",
					256 => "E_USER_ERROR",
					512 => "E_USER_WARNING",
					1024 => "E_USER_NOTICE",
					2047 => "E_ALL",
					2048 => "E_STRICT",
					4096 => "E_RECOVERABLE_ERROR");

// Couple of checks to see if site provided it's own configuration. Set defaults if not.
if(!defined('ERROR_MAILTO')) {
	// Mail reports to this address
	define('ERROR_MAILTO', 'mdierks@themindoffice.nl');
}
if(!defined('ERROR_DEBUG_MAIL')) {
	// Send reports over e-mail true/false
	define('ERROR_DEBUG_MAIL', false);
}
if(!defined('ERROR_DEBUG_SYSLOG')) {
	// Make entry in system log true/false
	define('ERROR_DEBUG_SYSLOG', true);
}
if(!defined('ERROR_DEBUG_SCREEN')) {
	// Show a full report on screen true/false (NOTE: admins always get a full report)
	define('ERROR_DEBUG_SCREEN', true);
}
if(!defined('ERROR_DEBUG_SCREEN')) {
    // Show a full report on screen true/false (NOTE: admins always get a full report)
    define('ERROR_DEBUG_SCREEN', true);
}
if(!defined('ERROR_SCREEN_ADMIN')) {
	// What function to use to display a full report.
	define('ERROR_SCREEN_ADMIN', 'error_show_screen_admin');
}
if(!defined('ERROR_SCREEN_USER')) {
	// What function to use to show a 'user pacifier screen'
	define('ERROR_SCREEN_USER', 'error_show_screen_user');
}
if(!defined('ERROR_ADMIN_IP')) {
	// What IP's always gets to see full report (comma seperated) Was: 80.127.200.10
	define('ERROR_ADMIN_IP', '145.53.139.112');
}
if(!defined('SCRIPT_START_TIME')) {
	// What timestamp was the script started at
	define('SCRIPT_START_TIME', time());
}
if(!defined('E_STRICT')) {
	// If this isn't PHP5, define this error level
	define('E_STRICT', 2048);
}

set_error_handler("error_report");

// Error handling function
function error_report($error_number, $error_message, $error_file, $error_line)
{
	
	global $error_notices, $errors;


    if (error_reporting() === 0) {

        return true;

    }else {


        if ($error_number != E_NOTICE AND $error_number != E_USER_NOTICE AND $error_number != E_STRICT) {
            // Nuke output buffering
            // FIXME: store buffer somewhere before nuking it, we might need it.
            while (@ob_end_clean()) ;

            if (ERROR_DEBUG_MAIL or ERROR_DEBUG_SCREEN or $_SERVER['REMOTE_ADDR'] == ERROR_ADMIN_IP) {
                // Make pretty HTML text to mail or show on screen
                // FIXME: translate $error_number to human readable

                //debug_print($errors[$error_number]);
                $admin_message = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\"><html><head><title>Error report for {$_SERVER['HTTP_HOST']}</title></head><body><h1>" . $_SERVER['HTTP_HOST'] . " has encountered an error.</h1><b>Type:</b> {$errors[$error_number]}<br /><b>Time:</b> " . SCRIPT_START_TIME . "<br /><b>File:</b> {$error_file}<br /><b>Line:</b> {$error_line}<br /><b>Message:</b> {$error_message}<br/><br /><b>Backtrace:</b><br/><pre>" . print_r(debug_backtrace(), true) . "</pre></body></html>";


            }

            if (ERROR_DEBUG_MAIL) {
                // Send mail to admin. Make it HTML e-mail so we can use pretty formatting.
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                $headers .= "To: Sysadmin <" . ERROR_MAILTO . ">\r\n";
                $headers .= "From: Website " . $_SERVER['HTTP_HOST'] . " <root@" . $_SERVER['HTTP_HOST'] . ">\r\n";
                @mail(ERROR_MAILTO, 'ERROR: ' . $_SERVER['HTTP_HOST'], $admin_message);
            }

            if (ERROR_DEBUG_SYSLOG) {
                // Make notice in syslog. No need to open it first.
                $syslog_message = "DSE error for " . $_SERVER['HTTP_HOST'] . " at {$error_file} line {$error_line}";
                if ($error_number == E_WARNING || $error_number == E_USER_WARNING) {
                    syslog(LOG_WARNING, $syslog_message);
                } elseif ($error_number == E_USER_ERROR) {
                    syslog(LOG_ERR, $syslog_message);
                } else {
                    syslog(LOG_NOTICE, $syslog_message);
                }
            }

            // Show something on screen. Show backtrace if admin or options tell us so
            if (ERROR_DEBUG_SCREEN or $_SERVER['REMOTE_ADDR'] == ERROR_ADMIN_IP) {
                call_user_func(ERROR_SCREEN_ADMIN, $admin_message);
            } else {
                call_user_func(ERROR_SCREEN_USER, $error_number, $error_message, $error_file, $error_line);
            }

            exit();
        } else {
            $error_notices[] = "<b>NOTICE</b> in <b>{$error_file}</b> on <b>line {$error_line}</>: {$error_message}<br />";
        }
    }

}

function error_show_screen_admin($admin_message)
{
	// Show backtrace on screen for admins
	// FIXME: add templating
	print($admin_message);
}

function error_show_screen_user($error_number, $error_message, $error_file, $error_line)
{
	// Show user a nice 'an error has occured' page, but do not show him any details
	// FIXME: add templating
	header("location: /404/index.php");
	exit();
$error_page = <<<NASTY_ERROR
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

	<head>

		<title>Foutmelding</title>
		<link rel="stylesheet" href="/css/main_front.css" />
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
		<meta http-equiv="Content-Style-Type" content="text/css" />

	</head>

	<body>

		<table id="positioner_table" cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
			<tr height="100%">
				<td height="100%" width="100%"><center>
					<img src="/images/error.gif" alt="Foutmelding">
				</center></td>
			</tr>
		</table>

	</body>

</html>
NASTY_ERROR;

	print($error_page);
}

function error_handle_notices()
{
	global $error_notices;

	if(count($error_notices) > 0) {

		$admin_message = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\"><html><head><title>Notice report for {$_SERVER['HTTP_HOST']}</title></head><body><h1>".$_SERVER['HTTP_HOST']." has encountered ".count($error_notices)." notice(s).</h1><b>Time:</b> ".SCRIPT_START_TIME."<br />".implode("\n",$error_notices)."</body></html>";

		if(ERROR_DEBUG_MAIL) {
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "To: Sysadmin <".ERROR_MAILTO.">\r\n";
			$headers .= "From: Website ".$_SERVER['HTTP_HOST']." <root@".$_SERVER['HTTP_HOST'].">\r\n";
			@mail(ERROR_MAILTO, ': '.$_SERVER['HTTP_HOST'], $admin_message);
		}

		if($_SERVER['REMOTE_ADDR'] == ERROR_ADMIN_IP) {
			call_user_func(ERROR_SCREEN_ADMIN, "<!-- ".implode("\n",$error_notices)." -->");
		}
	}
	exit();
}

function err($var, $exit = true,$runCount = 0)
{

    global $run;

    if($runCount != 0){

        if($run == $runCount){

            print("<pre>".print_r($var,true)."</pre>");

            if($exit) {
                exit("Debugged exit.");
            }

        }else{

            $run++;
        }

    }else{

        print("<pre>".print_r($var,true)."</pre>");

        if($exit) {
            exit("Debugged exit.");
        }

    }

}
function err_pdo($e)
{

    print("<pre>Foutmelding: ".$e->getMessage()."</pre>");

    exit();

}
function err_trace($exit = false)
{
	print("<pre>".debug_backtrace()."</pre>");

	if($exit) {
		exit("Debugged exit.");
	}
}



function error_log_query($string){
	

	
	return $string;
	
}
function error_log_sql($result){
	
	if(ERROR_DEBUG_SQL == true){
		
	}
	
	return $result;
	
}

?>