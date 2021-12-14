#!/usr/bin/php
<?php

error_reporting(E_ALL);

if($argc != 2) {
  echo("usage: generate_controllers.php <input_dir>");
  exit(-1);
}

if(!is_dir($argv[1])) {
  echo($argv[1] . " is not a valid directory");
  exit(-2);
}

$dir = $argv[1];
$files = array();
if($dh = opendir($dir)) {
  while(($file = readdir($dh)) !== false) {
	if(preg_match("/(.*)_controller\.php/", $file, $matches)) {
	  $controller = ucfirst($matches[1]);
	  $files[] = $dir . "/" . $file ;
	  $controllers[] = $controller;
	}
  }
  closedir($dh);
}


$cnt = count($files);
for($i = 0; $i < $cnt; $i++) {
  echo($files[$i] . " " . $bar . "\n");
  system("php " . dirname(__FILE__) . "/generate_controller.php " . $files[$i] . " " . $controllers[$i]);
}


?>