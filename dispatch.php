<?php
/*
 * rbook Recipe Management System
 * Copyright (C) 2007 Andrew Violette andrew@andrewviolette.net
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
 * Interprets URLs that are passed in and activates the appropriate
 * action on a controller.  A couple things are necessary here: 
 * 
 * <ul>
 * <li>In your Apache configuration, AllowOverrides must be set to
 * 'All'</li> 
 * <li>FollowSymlinks must be in your Apache options for this directory</li> 
 * <li>Your hosting provider needs to allow .htaccess</li> 
 * </ul>
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 */
require_once('init.php');


/**
 * @global string $uri - the URI that came in from the request
 */

$uri = $_SERVER['REQUEST_URI'];
$pos = strpos($uri, "?");
if($pos !== false) {
  $uri = substr($uri, 0, $pos);
}
$arg = null;
if($uri == "/" || APPROOT == $uri ) {
  $controller = "recipe";
  $func = "index";
} else {
  $pos = strpos($uri, APPROOT);
  if($pos !== false) {
	$cnt = $pos + strlen(APPROOT);
	$uri = substr($uri, $cnt);
  }
  $comps = preg_split('/\//', $uri, -1, PREG_SPLIT_NO_EMPTY);
  $c = count($comps);
  if($c == 3) {
	$arg = $comps[$c - 1];
	$func = $comps[$c - 2];
	$controller = $comps[$c - 3];
  } else if($c == 1) {
	$func = "index";
	$controller = $comps[0];
  } else {
	$func = $comps[$c - 1];
	$controller = $comps[$c - 2];
  }
}

function dispatch_error_handler($errno, $errstr, $errfile, $errline) {
  echo("<html><head><title>" . getMessage("Error") . "</title>");
  echo("<link rel='stylesheet' type='text/css' href='" . APPROOT . 
	   "skins/" . SKIN . "/style/style.css.php'/>");
  echo("<style type='text/css'>table { font-size: 1em; font-weight: bold; }</style>");
  echo("</head><body>");
  echo("<table><tr><td>" . getMessage("ErrorNumber") . 
	   "</td><td>$errorno</td></tr>");
  echo("<tr><td>" . getMessage("ErrorMessage") . 
	   "</td><td>$errstr</td></tr>");
  echo("<tr><td>" . getMessage("ErrorFile") . 
	   "</td><td>$errfile</td></tr>");
  echo("<tr><td>" . getMessage("ErrorLine") .
	   "</td><td>$errline</td></tr>");
  echo("</table><p><a href='" . buildLink("recipe", "index") . 
	   "'>Home Page</a></body></html>");
  exit();
}

$version_breakdown = explode(".", phpversion());

// The error handler doesn't like the second parameter in PHP4
if($version_breakdown[0] > 4) {
	set_error_handler("dispatch_error_handler", E_USER_ERROR | E_CORE_ERROR);
} 

// Load the controller class
require_once(dirname(__FILE__) . '/controllers/' . $controller . '_controller.php');

// If the controller name in the URI is 'recipe', the class name is 'RecipeController'
$classname = ucfirst($controller) . 'Controller';

// create the instance
$controllerInstance = call_user_func(array($classname, "newInstance"));

if(!method_exists($controllerInstance, $func)) {
  trigger_error("Invalid URL: " . $uri, E_USER_ERROR);
}

if(!$controllerInstance->is_valid_action($func)) {
  trigger_error("Invalid action: $func", E_USER_ERROR);
}

if(DEBUG) {
  rb_log("Executing controller: $controller -> $func -> $arg");
}

$controllerInstance->before_execute($func);

// execute the method
call_user_func(array(&$controllerInstance, $func), $arg);



?>