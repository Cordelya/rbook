<?php
require 'init.php';
header("Content-type: text/xml");
$results = User::loadMultiple(array("email" => $_REQUEST['e']));
if(count($results) > 0) {
  echo("true");
} else {
  echo("false");
}
?>