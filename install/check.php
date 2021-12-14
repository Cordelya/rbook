<?php

/*
 * rbook Recipe Management System
 * Copyright (C) 2005 Andrew Violette andrew@andrewviolette.net
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Form that processes the installation parameters.
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: check.php,v 1.3 2007/04/05 20:58:41 aviolette Exp $
 */

define("LANGUAGE", $_GET['language']);
require_once(dirname(__FILE__) . '/../helpers/resources.php');
 
session_start();

$errors = array();

/******************************************************************************
 * Short open tag is not used too much in this code any more but warn just 
 * in-case
 *****************************************************************************/

$foo = ini_get('short_open_tag');

if(!$foo) {
  $errors[] = "W|".getMessage('errSOT')."|".getMessage('solSOT');
}

/******************************************************************************
 * Check that we can upload a file.  This is necessary for images.
 *****************************************************************************/

$foo = ini_get('file_uploads');
if(!$foo) {
  $errors[] = "W|".getMessage('errFU')."|".getMessage('solFU');
}

/******************************************************************************
 * Check that we can create a directory.  This is necessary for using images.
 *****************************************************************************/

error_reporting(0);
$foo = dirname(__FILE__) . "/test";
mkdir($foo);
if(!file_exists($foo)) {
  $errors[] = "W|".getMessage('errMKDIR')."|".getMessage('solMKDIR');
} else {
  rmdir($foo);
}

/******************************************************************************
 * Check that we can create a file.  This is necessary for using images.  
 *****************************************************************************/

$foo = dirname(__FILE__) . "/test.txt";
$fp = fopen($foo, "w");
if($fp) {
  fwrite($fp, "test");
  fclose($fp);
}

if(!file_exists($foo)) {
  $errors[] = "W|".getMessage('errWriteFile')."|".getMessage('solWriteFile');
} else {
  unlink($foo);
}


/******************************************************************************
 * Check for the inclusion of PDO
 *****************************************************************************/

if(!class_exists("PDO")) {
  $errors[] = "E|".getMessage('errPDONotFound')."|".getMessage("PDONotFound").$f;
}


# TODO check whether this is needed since we're using PDO which is core
/******************************************************************************
 * Check that mysqli extension is installed
 *****************************************************************************/

if(!class_exists('PDO_mysql')) {
  $errors[] = "E|".getMessage('mysqlNotFound')."|".getMessage("mysqlNotFound").$f;
}

/******************************************************************************
 * Check that gd extension is installed
 *****************************************************************************/

/*
if(!extension_loaded('gd')) {
  $errors[] = "W|".getMessage('gdNotFound')."|".getMessage("gdNotFound").$f;
}
*/

if(count($errors) == 0) {
  header("Location: install.php?language=".$_GET['language']);
  exit();
}

?><!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
<link rel="STYLESHEET" type="text/css" href="style.css"/>
<title>rBook - Setup Scan</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style type="text/css">
#errorTable { border-collapse: collapse; }
#errorTable, td, th { border: 2px solid black; }
th, td { padding: 0.5em; }
.warningCol { width: 8em; }
.descCol { width: 15em; }
.E { background-color: red; }
.W { background-color: orange; }
</style>
</head>
<body>
<div id="mainDiv">
<h1><?php echo getMessage('configurationScan'); ?></h1>
<p><?php echo getMessage('issuesConfiguration'); ?></p>
<p><?php print_r($errors); ?>
<table id="errorTable">
<thead>
<tr>
<th>&nbsp;</th>
<th><?php echo getMessage('Description'); ?></th>
<th><?php echo getMessage('Solution'); ?></th>
</tr>
</thead>
<tbody>
<?php foreach($errors as $error) { 
  $foo = explode('\|', $error);
?>
<tr>
  <td class="warningCol <?php echo($foo[0]);?>"><?php echo($foo[0] == "W" ? getMessage('Warning') : getMessage('Error')); ?></td>
  <td class="descCol <?php echo($foo[0]);?>"><?php echo($foo[1]); ?></td>
  <td class="solCol <?php echo($foo[0]);?>"><?php echo($foo[2]); ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<p><a href="check.php?language=<?php echo($_GET['language']); ?>"><?php echo getMessage('rerunScan'); ?></a> or <a href="install.php?language=<?php echo($_GET['language']); ?>"><?php echo getMessage('proceedInstallation'); ?></a>.</p>

</div>
</body>
</html>
