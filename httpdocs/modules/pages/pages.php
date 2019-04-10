<?php

function pages_url($table,$id,$default = ''){


    $seoLink = db_pdo_fetch_array(db_pdo_select("SELECT * FROM seo WHERE tabel = :tabel and tabel_id = :id", array(':tabel' => $table,':id' => $id)));

     if ($seoLink["id"] == ""){

         $url = fc_friendly_url($default);

         $column = array();
         $value = array();

         $column[] = "tabel";
         $value[] = $table;

         $column[] = "tabel_id";
         $value[] = $id;

         $column[] = "page_url";
         $value[] = $url;

         $column[] = "language";
         $value[] = 'nl';


         db_pdo_insert_of_update("seo", $column, $value);

         return $url;

     }else {
         return $seoLink["page_url"];
     }
}

function pages_main()
{
    global $argc, $argv, $argseo, $argdata, $arglanguage,$arginfo;


    // Homepage
    if ($argc == 0){
      $get_page = db_pdo_fetch_array(db_pdo_select("SELECT * FROM pages where home = 1"));
      $seoLink = db_pdo_fetch_array(db_pdo_select("SELECT * FROM seo WHERE tabel = 'pages' and tabel_id = :tabel_id", array(':tabel_id' => $get_page["id"])));
      $argseo = $seoLink;
      $argdata["tabel"] = $seoLink["tabel"];
      $argdata["tabel_id"] = $seoLink["tabel_id"];
      $arglanguage = $seoLink["language"];
      pages_display($get_page["tem_id"]);
      exit();
    }


    //zoeken in de SEO tabel
    $url = strtok($_SERVER["REQUEST_URI"],'?');
    $seoLink = db_pdo_fetch_array(db_pdo_select("SELECT * FROM seo WHERE page_url = :url", array(':url' => ltrim($url, "/"))));


    if ($seoLink["id"] != "") {
        //componemnt
        $component = db_pdo_fetch_array(db_pdo_select("SELECT * FROM componenten where domein_id = :domein_id and table_name = :table_name", array(':domein_id' => DOMEIN_ID,':table_name' => $seoLink["tabel"]), "IRIS"));
        $argseo = $seoLink;
        $argdata["tabel"] = $seoLink["tabel"];
        $argdata["tabel_id"] = $seoLink["tabel_id"];
        $arglanguage = $seoLink["language"];
        if ($seoLink["tabel"] != "pages"){
          $arginfo = db_pdo_fetch_array(db_pdo_select("SELECT * FROM ".$argdata["tabel"]." WHERE id = :id",array(":id" => $argdata["tabel_id"])));
        }


        //stats opslaan als je geen beheerder ip hebt
        $ipadressen = db_pdo_fetch_array(db_pdo_select("SELECT * FROM ipadressen WHERE ip = :ip",array(":ip" => $_SERVER["REMOTE_ADDR"])));
        if ($ipadressen["id"] == ""){
        $stats = json_decode($argseo["stats"],true);
        if (!is_array($stats)){
            $stats = array();
        }
        // kijken of deze
        if ($_SESSION["uqi"] == ""){
          $_SESSION["uqi"] = sha1(time().rand(0,999));
        }
        
        //findToday
        if ($_SESSION["uqi"] != ""){
          $today = date(Ymd);
          if ($stats[$today] == ""){
              $stats[$today] = 1;
          }else{
              $stats[$today] = $stats[$today] + 1;
          }
          db_pdo_select("update seo set stats = '".json_encode($stats)."' where id = :id",array(":id" => $seoLink["id"]));
        }
      }


        if ($seoLink["tabel"] == "pages") {
            $component = db_pdo_fetch_array(db_pdo_select("SELECT * FROM pages where id = :id", array(":id" => $seoLink["tabel_id"])));
            pages_display($component["tem_id"]);
            exit();
        } else {
            $component = db_pdo_fetch_array(db_pdo_select("SELECT * FROM componenten where domein_id = :domein_id and  table_name = :table_name", array(':domein_id' => DOMEIN_ID,':table_name' => $seoLink["tabel"]), "IRIS"));
            pages_display($component["template"]);
            exit();
        }
        exit();
    }

    header('HTTP/1.0 404 Not Found');

}

function find_home()
{
    global $argc, $argv;


    $find_home = db_select_row_crits("pages", array("frontend_home", "active"), array(1, 1));


    if (db_num_rows($find_home) == 1) {
        // Found database page. Use this
        return $find_home;
    } else if (db_num_rows($find_home) == 0) {
        // No db based page. Let pages_main() go for a for disk one.
        return "main";

    } else {
        // TROUBLE!
        trigger_error("404 - Homepage not found", E_USER_ERROR);
    }
}

function pages_display($page, $search_by_name = false, $extra_applets = false)
{


    global $argv, $argc, $time;
    $get_page = db_pdo_fetch_array(db_pdo_select("SELECT * FROM pages where tem_id = :tem_id", array(':tem_id' => $page)));

    if ($get_page["id"] == ""){
      //no page found, so create on

      $column = array();
      $value = array();

      $column[] = "name";
      $value[] = ".".ucfirst($page);

      $column[] = "home";
      $value[] = 0;

      $column[] = "active";
      $value[] = 1;

      $column[] = "tem_id";
      $value[] = $page;

      $column[] = "edit";
      $value[] = 1;

      db_pdo_insert_of_update("pages", $column, $value);

      $get_page = db_pdo_fetch_array(db_pdo_select("SELECT * FROM pages where tem_id = :tem_id", array(':tem_id' => $page)));

    }
    pages_display_database($get_page, $search_by_name, $extra_applets);

}

function pages_display_database(&$page, &$search_by_name, &$extra_applets)
{
    global $argv, $argc, $time;

    // Convert page data to applets


    $applets = pages_applets_initiate($page);

    if ($extra_applets) {
        $applets = array_merge($applets, $extra_applets);
    }


    $template = templates_load_by_id($page["tem_id"]);


    unset($page, $page);

    $pagina = pages_applets_search($template, $applets);


    if (defined('DATABASE_USER_IRIS')) {

        $pagina = pages_content_search($pagina, $applets["id"]);

    }

    //AUTOMATISCH ALT TAG
    //$pagina = pages_set_alt_tag($pagina);

    //AUTOMATISCH COMMENTS VERWIJDEREN
    $pagina = preg_replace('/<!--(.*)-->/Uis', '', $pagina);


    print($pagina);

}

function pages_applets_initiate($page)
{
    // Make applets from page meta data
    $applets = array();

    if (!empty($page)) {
        foreach ($page as $key => $value) {
            $applets[$key] = $value;
        }
    }
    return $applets;
}


function pages_applets_search(&$subject, $applets = "")
{
    // Search for applets in $subject.
    $page = $subject;
    $offset = 0;
    $i = 0;

    $matches = array();

    $returned_int = preg_match_all("/" . APPLET_BOUNDARY . "[A-Za-z_0-9]{2,50}(,.*?)*" . APPLET_BOUNDARY . "/", $subject, $matches);


    //TRY RULE $returned_int = preg_match_all("/".APPLET_BOUNDARY."[A-Za-z_0-9]{2,50}(,[A-Za-z0-9]{1,10})*".APPLET_BOUNDARY."/", $subject, $matches);

    foreach ($matches[0] as $match) {
        $applet = str_replace(APPLET_BOUNDARY, "", $match);

        if (array_key_exists($applet, $applets)) {
            // Applet is in applets array; hence is a variable
            $page = str_replace(APPLET_BOUNDARY . $applet . APPLET_BOUNDARY, $applets[$applet], $page);

        } else {
            // Applet might be a function, go check.
            // NOTE: content also is a function.
            // NOTE: javascript now also is. Can parse applets that way
            $page = str_replace(APPLET_BOUNDARY . $applet . APPLET_BOUNDARY, pages_applets_launch($applet, $applets), $page);
        }

        $i++;
    }


    return $page;
}

function pages_applets_launch(&$applet, &$applets)
{
    global $argc, $argv, $time;

    $applet_parts = explode(",", $applet);
    $function = $applet_parts[0];
    $func_argc = count($applet_parts) - 1;
    $func_argv = array();

    for ($i = 1; $i <= $func_argc; $i++) {
        // Gather arguments. Arguments might be applets (of the variable type) themselves.
        if (array_key_exists($applet_parts[$i], $applets)) {
            $func_argv[] = $applets[$applet_parts[$i]];
        } else {
            $func_argv[] = $applet_parts[$i];
        }
    }

    if (function_exists($function)) {

        return $function($func_argc, $func_argv, $applets);

    } else {

        // No function found. See if there's a module name prefixed.
        if (substr_count($applet_parts[0], "_") > 0) {
            $module = explode("_", $applet_parts[0]);
            if (!module_load($module[0], false)) {

                return pages_session_applet($_SESSION, $applet_parts, 0);

            } else {


                // Module is loaded, see if function exists now
                if (function_exists($function)) {
                    return $function($func_argc, $func_argv, $applets);
                } else {
                    trigger_error("Applet is neither variable nor function. Specified module does not contain any function");
                }
            }
        } else {
            trigger_error("Applet is neither variable nor function. No module specified");
        }
    }
}

function pages_session_applet($session, $parts, $depth)
{


    if (isset($session[$parts[$depth]]) && is_array($session[$parts[$depth]])) {

        return pages_session_applet($session[$parts[$depth]], $parts, $depth + 1);

    } else {

        return $session[$parts[$depth]];
    }


}

function pages_get_info($func_argc, $func_argv, $applets = false)
{
    global $argc, $argv;


    $query = db_select("SELECT * FROM pages WHERE id = {$applets["id"]}");
    $data = db_fetch_array($query);

    return $data[$func_argv[1]];


}


function pages_content_search(&$subject, $id = '')
{
  //tekst ophalen
      global $argseo;
      preg_match_all('/{{(.*?)}}/', $subject, $matchesVAR, PREG_SET_ORDER, 0);
      foreach ($matchesVAR as $k => $v){
          $matchesVAR[$k]["type"] = "var";
      }

      //page content
      $matches = $matchesVAR;
      $pageContent = db_pdo_select_all("SELECT * FROM pages_content WHERE page_id = :val", array(":val" => $argseo["tabel_id"]));

      //templatecontent
      $templateContent = db_pdo_select_all("SELECT * FROM templates_content");

      $content = array();
      foreach ($pageContent as $k => $v) {
          $content[$v["tag"]] = $v;
      }
      foreach ($templateContent as $k => $v) {
          $content[$v["tag"]] = $v;
      }

      $array = array();
      $i = 1;
      $elementen = array();

      if(is_array($matches)) {
          foreach ($matches as $k => $v) {
              $subject = str_replace($v[0], output($content[$v[1]]["html"]), $subject);
          }
      }
      //page content

    return $subject;
}



?>
