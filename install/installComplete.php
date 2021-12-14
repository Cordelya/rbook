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
 * Displays the "Installation Complete" page.
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: installComplete.php,v 1.14 2007/04/01 14:27:16 maschine Exp $
 */

require_once(dirname(__FILE__) . "/../helpers/auth.php");
require_once(dirname(__FILE__) . "/../helpers/ui.php");
require_once(dirname(__FILE__) . '/../helpers/resources.php');
session_start();

$config = $_SESSION['config'];

if(!defined("LANGUAGE"))
{
	if(isset($_GET['language']))
		define("LANGUAGE", $_GET['language']);
	elseif(isset($_POST['language']))
		define("LANGUAGE", $_POST['language']);
	else
		define("LANGUAGE", "en");
}
setCookie('saveid', '', 0, APPROOT);
setCookie('auth', '', 0, APPROOT);

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
  setcookie(session_name(), '', time()-42000, '/');
}
unset($_COOKIE[session_name()]);
session_destroy();

?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<link rel="STYLESHEET" type="text/css" href="style.css"/>
<head>
<title><?php echo(getMessage('InstallationComplete')); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<div id="mainDiv">
<h1><?php echo(getMessage('InstallationComplete')); ?></h1>

<ol>

<? if(!empty($config)) {
?>
<li class="big"><?php echo(getMessage('configWriteError')); ?></li>
<?
}
?>
<li class="big"><?php echo(getMessage('verifyInstallation')); ?></li>
<li class="big"><?php echo(getMessage('templatesWriteable')); ?></li>
<li class="big"><span class="important"><?php echo(getMessage('removeInstallDir1')); ?></span>. <?php echo(getMessage('removeInstallDir2')); ?></li>
<li class="big"><a href="<?= upUrlLevel($_SERVER['REQUEST_URI'], 2) ?>"><?php echo(getMessage('AccessSite')); ?></a>.</li>
</ol>

<? if(!empty($config)) { ?>
<h3>config.php</h3>
<div style="margin: 0 3em 0 3em; border: 1px solid black; padding: 3px 3px 3px 3px">
<?= $config ?>
</div>
<? } ?>
</div>

</body>
</html>
