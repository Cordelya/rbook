<?Php

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
 * @version $Id: install.php,v 1.6 2007/04/07 01:51:51 aviolette Exp $
 */

session_start();

if(!defined("LANGUAGE")) {
	if(isset($_GET['language']))
		define("LANGUAGE", $_GET['language']);
	elseif(isset($_POST['language']))
		define("LANGUAGE", $_POST['language']);
	else
		define("LANGUAGE", "en");
}
require_once(dirname(__FILE__) . '/../helpers/resources.php');

require_once('DB.php');

require_once('db_installer.php');
require_once('mysql_db_installer.php');
$installer = new MysqlDBInstaller();


if(count($_POST) > 0) {
  $installer->update($_POST);
  $msg = $installer->validate();
  if($msg == null) {

    $installer->install();
    unset($_SESSION['categories']);
    if(count($installer->errors) > 0) {
      $msg = "Errors occured: <br/> ". implode("<br/><br/>", $installer->errors) . "<br/>";
    } else {
      $_SESSION['config'] = $installer->config;
      header("Location: installComplete.php?language=".LANGUAGE);
      exit();
    }
  }
} 

function buildHelpLink($topic) {
  echo("&nbsp;<a href=\"" . RBOOK_HELP . "/documentation/installation/#" . 
       $topic . "\" class=\"helpbox\">?</a>");
}

?><!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
<link rel="STYLESHEET" type="text/css" href="style.css"/>
<title><?php echo(getMessage('Installation')); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<div id="mainDiv">
<h1><?php echo(getMessage('Installation')); ?></h1>
<div style="font-weight: bold;color:red" id="warningDiv">
<?php  $m = empty($msg) ? "" : $msg; echo($m); ?>
</div>
<p><?php echo(getMessage('installDesc1')); ?></p>
<p><?php echo(getMessage('installDesc2')); ?></p>
<form action="install.php" id="installForm" method="post">

<h2><?php echo(getMessage('DatabaseInformation')); ?></h2>
<p><?php echo(getMessage('DatabaseInstallDesc')); ?></p>
<table>
<tr>
<td><label for="databaseHostField"><?php echo(getMessage('DatabaseHost')); ?></label>:</td>
<td><input type="text" size="30" name="databaseHost" id="databaseHostField" value="<?php echo($installer->databaseHost); ?>"/></td>
</tr>
<tr>
<td><label for="databaseNameField"><?php echo(getMessage('DatabaseName')); ?></label>:</td>
<td><input type="text" size="30" name="databaseName" id="databaseNameField" value="<?php echo($installer->databaseName); ?>"/></td>
<td><input type="checkbox" id="action" name="action" <?php echo($installer->buildDatabase ? 'checked="checked"' : ""); ?>/><label for="action"><?php echo(getMessage('createDatabase')); ?></label></td>
</tr>
<tr>
<td><label for="adminUserName"><?php echo(getMessage('AdminUser')); ?></label>:</td>
<td><input type="text" size="30" name="adminUserName" id="adminUserName" value="<?php echo($installer->adminUser); ?>"/></td>
</tr>
<tr>
<td><label for="passwordField"><?php echo(getMessage('Password')); ?></label>:</td>
<td><input type="password" size="30" name="password" id="passwordField"/></td>
</tr>
<tr>
<td><label for="databaseUserNameField"><?php echo(getMessage('DatabaseUserName')); ?></label>:</td>
<td><input type="text" size="30" name="databaseUserName" id="databaseUserName" value="<?php echo($installer->dbUserName); ?>"/></td>
</tr>
</table>

<h2><?php echo(getMessage('BasicConfiguration')); ?></h2>
<p><?php echo(getMessage('BasicConfigurationDesc')); ?></p>
<table>
<tr>
<td><label for="titleField"><?php echo(getMessage('ApplicationTitle')); ?>:</label></td>
<td><input type="text" size="30" name="title" id="titleField" value="<?php echo($installer->title); ?>"/></td>
</tr>
<tr>
<td><label for="approotField"><?php echo(getMessage('BaseURI')); ?>:</label></td>
<td><input type="text" size="30" name="approot" id="approotField" value="<?php echo($installer->approot); ?>"/></td>
</tr>
</table>

<input type="hidden"  name="stylesheet" id="stylesheetField" value="<?php echo($installer->stylesheet); ?>"/>
<input type="hidden" name="skin" value="<?= $installer->skin ?>"/>

<h2><?php echo(getMessage('InitialAdminUser')); ?></h2>
<p>
<?php echo(getMessage('InitialAdminUserDesc')); ?>
</p>
<table>
<tr>
<td><label for="initialUserField"><?php echo(getMessage('Name')); ?></label>:</td>
<td><input type="initialUser" size="50" maxlength="<?= USER_NAME_FIELD_LENGTH ?>" name="initialUser" id="initialUserField" value="<?= $installer->initialUser ?>"/></td>

</tr>
<tr>
<td><label for="initialEmailField"><?php echo(getMessage('Email')); ?></label>:</td>
<td><input type="initialEmailField" size="50" maxlength="<?= USER_EMAIL_FIELD_LENGTH ?>" name="initialEmail" value="<?= $installer->initialEmail ?>"/></td>
</tr>
</table>
<h2><?php echo(getMessage('ViewPolicy')); ?></h2>
<p><?php echo(getMessage('ViewPolicyDesc')); ?></p>
<div>
<label for="viewPolicy"><?php echo(getMessage('ViewPolicy')); ?></label>&nbsp;<select name="viewPolicy" id="viewPolicy"><option <?= $installer->viewPolicy == 'member' ? 'selected="selected"' : ''?> value="member"><?php echo(getMessage('MembersOnly')); ?></option><option <?= $installer->viewPolicy == 'all' ? 'selected="selected"' : ''?> value="all"><?php echo(getMessage('Everyone')); ?></option></select>
</div>
<h2><?php echo(getMessage('ExportImport')); ?></h2>
<p>
<?php echo(getMessage('ExportImportDesc')); ?>
</p>
<div>
<label for="exportDirectory"><?php echo(getMessage('ExportDirectory')); ?>:</label>&nbsp;<input type="text" size="50" id="exportDirectory" value="<?= $installer->exportDirectory ?>" name="exportDirectory"/>
</div>
<h2><?php echo(getMessage('Invitations')); ?></h2>
<p>
<?php echo(getMessage('InvitationsDesc')); ?>
</p>
<div><label for="maxInvitations"><?php echo(getMessage('MaxInvitations')); ?>:</label>&nbsp;<input type="text" size="3" maxlength="3" id="maxInvitations" value="<?= $installer->maxInvitations ?>" name="maxInvitations"/></div>
<h2><?php echo(getMessage('Images')); ?></h2>
<p>
<?php echo(getMessage('ImagesDesc')); ?>
</p>
<label for="imageMagick"><?php echo(getMessage('ImageMagickPath')); ?>:</label>&nbsp;<input type="text" size="50" id="imageMagick" name="imageMagick" value="<?= $installer->convertProgram ?>"/>

<div id="buttonRow">
<input type="hidden" name="language" value="<?php if(isset($_GET['language'])) echo($_GET['language']); elseif(isset($_POST['language'])) echo($_POST['language']); else echo($installer->language); ?>" />
<input type="submit" value="Install"/>
</div>
</form>
</div>
</body>
</html>
