<?php

$zip = new ZipArchive;
$res = $zip->open('../master.zip');

if ($res === TRUE) {
  $zip->extractTo($_SERVER["HOME"].'/');
  $zip->close();
  rename($_SERVER["HOME"]."/httpdocs", $_SERVER["HOME"]."/httpdocs-old");
  rename($_SERVER["HOME"]."/httpdocs-master", $_SERVER["HOME"]."/httpdocs");
  unlink($_SERVER["HOME"]."/httpdocs/etc/config.php");
  rename($_SERVER["HOME"]."/config.php",$_SERVER["HOME"]."/httpdocs/etc/config.php");
  header("location: http://master.iris-tmo.nl");
}
?>
