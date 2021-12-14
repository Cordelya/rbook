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
 * Contains functions used by the 'view' component.  These functions
 * are generally used to package the backend objects for display on
 * the PHP pages.
 *
 * @author Andrew Violette
 * @package rbook
 * @since 0.9.7.0
 * @version $Id: ui.php,v 1.22 2007/03/23 12:06:14 aviolette Exp $
 */



function buildButtonClass($text, $href, $cname) {
  return '<a class="' . $cname . '" href="' . $href . '">[' . $text . "]</a>";
}

function buildLink($controller, $action, $arg = null) {
  if(defined("REWRITE_URLS") && REWRITE_URLS === false) {
	if($arg == null) {
	  $arg = "";
	} else {
	  $arg = "?arg=" . $arg;
	}
	return APPROOT . $controller . "_" . $action . ".php" . $arg;
  } else {
	$location = "";
	if($arg == null) {
	  $arg = "";
	}
	return APPROOT . $controller . "/" . $action . "/" . $arg;
  }
}

function buildFullLink($controller, $action, $arg = null) {
  $url = "http://" . $_SERVER['SERVER_NAME'];
  return $url . buildLink($controller, $action, $arg);
}

function buildButton($text, $href) {
  return buildButtonClass($text, $href, "ebutton");
}

function buildViewUrl($id) {
  return buildLink("recipe", "view", $id);
}

function getTemplateDir() {
  if(!file_exists(SKINDIR . '/templates') && SKIN != 'default') {
    return SKINROOT . 'default/templates';
  }
  return SKINDIR . '/templates';
}


function getFullTemplateName($name) {
  return "skin:" . $name . '.tpl';
}

function getTemplateName($name) {
  $r = SKINDIR . '/templates/' . $name;
  rb_log("Original: " . $r);
  if(SKIN != 'default' && file_exists($r)) {
  } else {
    $r =SKINROOT . 'default/templates/' . $name;
  }
  rb_log("USing template: " . $r);
  return $r;
}

function skin_get_template ($resourceName, &$source, &$smarty) {
  $name = getTemplateName($resourceName);
  if(!file_exists($name)) {
    return false;
  }
  $handle = fopen($name, "r");
  $contents = '';
  while (!feof($handle)) {
    $contents .= fread($handle, 8192);
  }
  fclose($handle);
  $source = $contents;
  return true;
}

function skin_get_timestamp ($resourceName, &$timestamp, &$smarty) {
  $name = getTemplateName($resourceName);
  if(!file_exists($name)) {
    return false;
  }
  $a = stat($name);
  $timestamp = $a[9];
  return true;
}

function skin_is_secure ($resourceName, &$smarty) {
  return true;
}

function skin_is_trusted ($resourceName, &$smarty) {
}

function buildServerUrl() {
  $protocol = "http";
  if(array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS']) {
    $protocol = "https";
  }
  $port = "";
  if($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
    $port = ":" . $_SERVER['SERVER_PORT'];
  }
  return ($protocol . "://" . $_SERVER['SERVER_NAME'] . $port);	
}

function buildBaseUrl() {
 	return (buildServerUrl() . APPROOT);
}

function toPeriod($minutes) {
  $hours = floor($minutes / 60);
  if($hours > 0)
	$s_hours = $hours . " " . getMessage('hrs') . " ";
  else
	$s_hours = "";

  $mins = $minutes % 60;
  if($mins > 0)
    $s_mins = $mins . " " . getMessage('min');
  else
    $s_mins = "";
  
  return $s_hours . $s_mins;
}

function escapeLiteral($str) {
  return str_replace("'", "\\'", $str);
}

function stripExtension($name) {
	if(preg_match('/\.xml$/', $name, $matches)) {
	  return substr($name, 0, strlen($name) - 4);
	}
	return $name;
}


?>