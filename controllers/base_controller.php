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
 *
 * @package rbook
 * @subpackage controllers
 */

/** */
require_once(dirname(__FILE__) . "/../init.php");

/**
 * Top level controller that is responsible for controlling proper
 * authorization/authentication, activation, and preparing a view for
 * display.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */

class BaseController {

  /**
   * @var array if set, it contains all the method names that require
   * authentication.  Otherwise it is assumed that no methods require
   * authentication
   */

  var $requireAuth;

  /**
   * @var string The name of the controller.  This is passed into the
   * constructor and is used to trigger actions with the
   * activateAction method without explicitly knowing the controller's
   * name.  If this is set to null, calls to activateAction will cause
   * an error to be triggered.
   */

  var $name;
  
  /**
   * @var string the default action to call on the controller if
   * activateDefault() is called.  This is set to 'index' by default.
   */

  var $defaultAction;

  /**
   * @var bool true if all actions in the controller should be
   * incorporated into the user's session.  This is set to true by
   * default.
   */

  var $requiresSession;


  function BaseController($name = null, $requires_session = true) {
    $this->name = $name;
    $this->defaultAction = "index";
	$this->requiresSession = $requires_session;
  }

  function setPageErrors(&$errors) {
	$_SESSION['pageErrors'] = $errors;
  }

  function clearPageErrors() {
	$_SESSION['pageErrors'] = null;
  }

  function getPageErrors() {
	return $_SESSION['pageErrors'];
  }


  /**
   * Returns true if the request comes from a POST
   * @return bool true if a post, false otherwise
   */

  function isPost() {
    return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
  }

  /**
   * Activates the default action for the controller, if specified.
   * Otherwise an error is triggered.
   */

  function activateDefault() {
    if(empty($this->defaultAction)) {
      trigger_errror("No default action specified for controller", E_USER_ERROR);
    }
    $this->activateAction($this->defaultAction);
  }

  /**
   * Activates the action on the controller.  This will cause a client
   * side redirect to that action.
   *
   * @param action string the name of the action to execute 
   * @param arg string optional argument
   */

  function activateAction($action, $arg = null) {
    if(empty($this->name)) {
      trigger_error("Controller name is not set");
    }
    $this->activateController($this->name, $action, $arg);
  }
  
  /**
   * Activates a controller and redirects to it.  This effectively
   * builds the activation URL and sends a client-side redirect.
   * @param controller string the name of the controller
   * @param action string the action on the controller
   * @param arg string the optional argument
   */

  function activateController($controller, $action, $arg = null) {
    $location = buildLink($controller, $action, $arg);
    header("Location: " . $location);
    exit();
  }

  /**
   * Forces the user to login if they haven't logged and then sends them
   * back to this page when they are successful.
   */
  
  function loginAndRedirectToCurrentPage() {
    if(!isLoggedIn()) {
      $_SESSION['redirectto'] = $_SERVER['REQUEST_URI'];
      $this->activateController("user", "login");
    }
  }

  /**
   * Sets the array of actions that require authentication.
   */

  function set_requires_authentication($arr) {
    $this->requireAuth = $arr;
  }

  function set_requires_adminaccess($arr) {
    $this->requireAdmin = $arr;
  }
  
  function set_valid_actions($arr) {
    $this->validActions = $arr;
  }

  function set_default_action($action) {
    $this->defaultAction = $action;
  }

  function is_valid_action($method) {
    if(!isset($this->validActions) || in_array($method, $this->validActions)) {
      return true;
    }
    return false;
  }

  /**
   * Returns true if the specified method requires admin access
   */

  function requires_adminaccess($method) {
    if(isset($this->requireAdmin) && in_array($method, $this->requireAdmin) && !isAdminUser()) {
      return true;
    }
    return false;
  }

  /**
   * Returns true if the specified method requires authentication to execute
   * @param string $method the method name
   */

  function requires_authentication($method) {
    if(isset($this->requireAuth) && in_array($method, $this->requireAuth) || $this->requires_adminaccess($method)) {
      return true;
    }
    return false;
  }

  /**
   * Handles common stuff before an action method is handled.  Ideally, this should
   * be handled by a proxy class, but I can't figure out how to do that in a PHP4 
   * compatible way.
   */

  function before_execute($method) {
	if($this->requiresSession) {
	  session_start();
	}
	$this->currentAction = $method;
	
    if($this->requires_authentication($method)) {
      $this->loginAndRedirectToCurrentPage();
    }
    if($this->requires_adminaccess($method) && !isAdminUser()) {
	  $this->activateDefault();
    }
    $this->before_action();
  }

  /**
   * Called after authentication, but before the rest of the controller action.  Default
   * implementation does nothing.
   */

  function before_action() {
  }
  

  /**
   * Creates a smarty instance and populates it with the objects that
   * are need on all pages.
   */
  
  function &prepareModelAndView() {
	$smarty = new Smarty();
	$smarty->register_resource('skin', 
							   array("skin_get_template",
									 "skin_get_timestamp",
									 "skin_is_secure",
									 "skin_is_trusted"));
	$smarty->compile_check = true;
	$smarty->assign("appTitle", APPTITLE);
	$smarty->debugging = false;
	$smarty->config_dir = SKINDIR . '/configs';
	
	$smarty->template_dir = getTemplateDir();
	$smarty->compile_dir = SKINDIR . '/templates_c';
	if(!file_exists($smarty->compile_dir)) {
	  mkdir($smarty->compile_dir);
	}
	$smarty->plugin_dir = ROOT_DIRECTORY . '/plugins';
	$smarty->assign("controller", $this->name);
	$smarty->assign("action", $this->currentAction);
	$smarty->assign("showrss", "false");
	$smarty->assign("stylesheet", 'style/style.css');
	$smarty->assign("stylesheetPrint", "style/style-print.css");
	$smarty->assign("stylesheetHandheld", "style/style-handheld.css");
	if(array_key_exists('categories', $_SESSION)) {
	  $categories = $_SESSION['categories'];
	}
	if(!isset($categories)) {
	  $categories =& Category::loadMultiple(null, null);
	  $_SESSION['categories'] = $categories;
	} 
	$smarty->assign("categories", $this->buildCategoryList($categories));
	if(array_key_exists('recipecount', $_SESSION)) {
	  $recipecount = $_SESSION['recipecount'];
	}
	if(!isset($recipecount)) {
	  $recipecount =& Recipe::getRecipeCount();
	  $_SESSION['recipecount'] = $recipecount;
	} 
	$smarty->assign("recipecount", $recipecount);
	
	$user = $_SESSION['user'];
	$this->prepareUser($user, $smarty);
	$loggedIn = false;
	if(!isset($user) && isset($_COOKIE['saveid']) && strlen($_COOKIE['saveid'])) {
	  $user = User::loadOne(array('id' => intval($_COOKIE['saveid'])));
	  if($user && $user->auth == $_COOKIE['auth']) {
		$_SESSION['user'] = $user;
	  } else {
		$user = null;
	  }
	}
	if(defined("GOOGLE_ANALYTICS")) {
	  $smarty->assign("googleAnalyticsAccount", GOOGLE_ANALYTICS);
	}
	$smarty->assign("enable_invitations", defined("MAXINVITATIONS") && MAXINVITATIONS > 0);
	if(isset($user)) {
	  $smarty->assign("email", $user->email);
	  $smarty->assign("username", $user->name);
	  $smarty->assign("userId", $user->id);
	  $smarty->assign("username", $user->username);
	  $smarty->assign("user_recipe_count", $user->countRecipes());
	  if(defined("MAXINVITATIONS")) {
		$smarty->assign("remaining_invitations", MAXINVITATIONS - $user->invitationsUsed());
	  }
	}
	$smarty->assign("onload", "");
	$smarty->assign("loggedin", isLoggedIn());
	$smarty->assign("readonly", isReadonlyUser());
	$smarty->assign("admin", isAdminUser());
	$smarty->assign("root_path", APPROOT);
	$baseUrl = buildBaseUrl();
	$smarty->assign("base_url", $baseUrl);
	$skinUrl = APPROOT . "skins/" . SKIN;
	$smarty->assign("skin_img", $skinUrl . "/images/");
	$smarty->assign("skin_script", $skinUrl . "/script/");
	$smarty->assign("skin_style", $skinUrl . "/style/");
	$smarty->assign("is_ie", strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") ? true : false);
	$smarty->assign("allow_registration", ALLOW_REGISTRATION);
	$smarty->assign("rbook_version", RBOOK_VERSION);
	$smarty->assign("recipe_name_length", RECIPE_NAME_FIELD_LENGTH);
	$smarty->assign("recipe_preheat_length", RECIPE_PREHEAT_FIELD_LENGTH);
	$smarty->assign("recipe_source_length", RECIPE_SOURCE_FIELD_LENGTH);
	$smarty->assign("ingredient_amount_length", INGREDIENT_AMOUNT_LENGTH);
	$smarty->assign("ingredient_description_length", INGREDIENT_DESCRIPTION_LENGTH);
	$smarty->assign("recipe_suggest", RECIPESUGGEST === false ? 0 : 1);
	$smarty->assign("user_email_length", USER_EMAIL_FIELD_LENGTH);
	$smarty->assign("user_name_length", USER_NAME_FIELD_LENGTH);
	$smarty->assign("user_password_length", USER_PASSWORD_FIELD_LENGTH);
	$smarty->assign("useImport", defined("IMPORTDIR"));
	$smarty->assign("showNavBar", 1);
	$smarty->assign("images_enabled", defined("IMAGEMAGICK"));
	$mobile = false;
	if(isset($_SESSION['mobile'])) {
	  $mobile = $_SESSION['mobile'];
	} else if(defined("MUA_ENABLED")) {
      $mua = new MobileUserAgent();
      $mobile = $mua->success();
	} 
	
	$smarty->assign("is_mobile", $mobile);
	
	$error= getPageError();
	if(isset($error)) {
	  $smarty->assign("pageError", $error);
	  $this->flash(null);
	}
	$errors = $this->getPageErrors();
	if(isset($errors)) {
	  $smarty->assign("pageErrors", $errors);
	  $this->clearPageErrors();
	}

	$javascripts = array();
    $javascripts[] = APPROOT . "script/prototype.js";
    $javascripts[] = APPROOT . "script/scriptaculous.js";
    $javascripts[] = APPROOT . "script/recipe.js";
	$javascripts[] = APPROOT . "script/slideshow.js";
	$smarty->assign("javascripts", $javascripts);
	return $smarty;

  }


  function buildGroceryList(&$groceryitems) {
	$items = array();
	foreach($groceryitems as $item) {
	  $c = array("id" => $item->id,
				 "description" => $item->description,
				 "order" => $item->orderid);
	  $items[] = $c;
	}
	return $items;
  }
  
  /**
   * Builds a list of smarty "objects" that contain the name and ID of
   * all the categories defined in the system.
   */
  
  function &buildCategoryList(&$categories) {
	$cats = array();
	foreach($categories as $cat) {
		
	  $c = array("id" => $cat->id,
				 "recipeCount" => $cat->recipeCount,
				 "name" => $cat->name);
	  $cats[] = $c;
	}
	return $cats;
  }

  function prepareUser(&$user, &$smarty) {
	$smarty->assign("user_username", $user->username);
	$smarty->assign("user_name", $user->name);
	$smarty->assign("user_email", $user->email);
	$smarty->assign("user_admin", $user->admin);
	$smarty->assign("user_disabled", $user->disabled);
	$smarty->assign("user_readonly", $user->readonly);
	$smarty->assign("user_id", $user->id);
  }

  /**
   * Builds a smarty recipe "object."   
   *
   * @param recipe - the recipe object that will be transformed
   * @param applyFormatting - if true, rbook tags in the page will be
   * converted to html tags.
   */
  
  function &buildDisplayableRecipe(&$recipe, $applyFormatting) {
	if($recipe == null) {
	  return array();
	}
	$sets = $recipe->ingredients;
	$sa = array();
	foreach($sets as $name => $set) {
	  $rowCount = count($set->rows);
	  $rows = array();
	  $rowList = $set->rows;
	  for($i = 0; $i < $rowCount; $i++) {
		$r = array("amount" => $rowList[$i]->amount,
				   "description" => escapeLiteral($rowList[$i]->description));
		$rows[] = $r;
	  }
	  
	  $s = array("name" => $set->name,
				 "id" => $set->id,
				 "rows" => $rows);
	  $sa[] = $s;
	}
	
	$steps = $recipe->steps;
	$stepa = array();
	for($i=0; $i < count($steps); $i++) {
	  $stepa[] = ($applyFormatting ? formatForView($steps[$i]) : $steps[$i]);
	}
	
	$r = array("editable" => editableByCurrentUser($recipe),
			   "isNew" => $recipe->isNew(),
			   "id" => $recipe->id,
			   "cooktime" => $recipe->cooktime,
			   "preptime" => $recipe->preptime,
			   "cooktimePeriod" => toPeriod($recipe->cooktime),
			   "preptimePeriod" => toPeriod($recipe->preptime),
			   "totaltimePeriod" => toPeriod($recipe->getTotalTime()),
			   "totaltime" => $recipe->getTotalTime(),
			   "serves" => $recipe->serves,
			   "image" => null,
			   "numSteps" => count($steps),
			   "numSets" => count($recipe->ingredients),
			   "title" => htmlspecialchars($recipe->title),
			   "submittedBy" => $recipe->submittedByName,
			   "submittedByUserName" => $recipe->submittedByUserName,
			   "categoryId" => $recipe->categoryId,
			   "categoryList" => implode(",", $recipe->categoryList()),
			   "categories" => $recipe->categories,
			   "ingredients" => $sa,
			   "source" => $recipe->source,
			   "steps" => $stepa
			   );
			   
	// Multiple images possible.
	$imgs =& $recipe->images;
	if(isset($imgs) && count($imgs) > 0) {
	  $images = array();
	  for($i = 0; $i < count($imgs); $i++) {
		$img = array("pic" => $imgs[$i]->getWebPath(),
					 "id" => $imgs[$i]->id,
		             "caption" => $imgs[$i]->caption);
		$images[] = $img;
	  }
	  $r['images'] = $images;
	}

	if(!empty($recipe->note)) {
	  $r['note'] = ($applyFormatting) ? formatForView($recipe->note) : $recipe->note;
	}
	if(!empty($recipe->description)) {
	  $r['description'] = ($applyFormatting) ? formatForview($recipe->description) : $recipe->description;
	}
	
	if(!empty($recipe->preheat)) {
	  $r['preheat'] = $recipe->preheat;
	}
	
	if(!empty($recipe->source)) {
	  if(strpos($recipe->source, "http") !== false)
        $r['sourceaslink'] = "<a href=\"".$recipe->source."\">".parse_url($recipe->source, PHP_URL_HOST)."</a>";
	  else
        $r['sourceaslink'] = $recipe->source;
	}
	return $r;
  }

  /**
   * Goes to the referrer of the current page.  If the referrer is the login
   * page then it goes to the home page.
   */
  
  function gotoReferrer() {
	if(strpos($_SERVER['HTTP_REFERER'], "login") !== false) {
	  $this->activateDefault();
	} else if(isset($_SERVER['HTTP_REFERER'])) {
	  header("Location: " . $_SERVER['HTTP_REFERER']);
	  exit();
	}
  }

  /**
   * Just like RoR's
   */

  function flash($error) {
	if(empty($error)) {
	  unset($_SESSION['pageError']);
	  return;
	}
	$_SESSION['pageError'] = $error;
  }

}
?>