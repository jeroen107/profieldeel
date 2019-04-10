<?php

if (file_exists($_SERVER["HOME"]."/master.sql")){
  $query = file_get_contents($_SERVER["HOME"]."/master.sql");
  db_pdo_select($query,array());
  unlink($_SERVER["HOME"]."/master.sql");

  //defaults
  db_pdo_select("INSERT INTO `talen` (`id`, `naam`, `afkorting`, `default_taal`) VALUES (NULL, 'Nederlands', 'nl', 'ja')",array());

  header("location: /");
}


?>
