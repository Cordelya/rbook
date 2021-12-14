#!/usr/bin/php
<?php

if($argc != 3) {
  echo("usage: generate_controller.php <controller_file> <controller_name>");
  exit(-1);
}

$file = $argv[1];
$lcname = strtolower($argv[2]);
$name = $argv[2] . "Controller";

include $file;

$controllerInstance = call_user_func(array($name, "newInstance"));

if($controllerInstance->validActions) {
  $ar = $controllerInstance->validActions;
  foreach($ar as $action) {
	$fname = $lcname . "_" . $action . ".php";
	$buf = "<?php\nrequire_once('init.php');require_once('" . $file . "');";
	$buf = $buf. "\$uri = \$_SERVER['REQUEST_URI'];";
	$buf = $buf . "\$pos = strpos(\$uri, \"?\");";
	$buf = $buf . "if(\$pos !== false) {";
	$buf = $buf . "\$uri = substr(\$uri, 0, \$pos);";
	$buf = $buf . "}";
	$buf = $buf . "\$arg = null;";
	$buf = $buf . "if(isset(\$_GET['arg'])) { \$arg = \$_GET['arg']; }";
	$buf = $buf . "\$controller = $name::newInstance();";
	$buf = $buf . "\$controller->before_execute('" . $action . "');";
	$buf = $buf . "\$controller->$action(\$arg);";
	$buf = $buf . "?>\n";
	file_put_contents($fname, $buf);

  }
}

?>