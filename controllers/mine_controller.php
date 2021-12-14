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
require_once(dirname(__FILE__) . "/../init.php");
require_once(dirname(__FILE__) . "/base_controller.php");
require_once(dirname(__FILE__) . '/../classes/result_set.php');


/**
 * Responsible for managing the 'my recipes' section.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */


class MineController extends BaseController {
  /**
   * Factory method that creates a myrecipes controller.  
   */

  function &newInstance() {
	$controller = new MineController("mine");
	$controller->set_valid_actions(array("index", "take", "delete", "results"));
	$controller->set_requires_authentication(array("index", "take", "delete", "results"));
	return $controller;
  }

  function take($id) {
	$user = $_SESSION['user'];
	if(isset($id)) {
	  $recipe =& Recipe::load($id);
	} else {
	  $recipe =& $_SESSION['recipe'];
	}
	
	if(!isset($recipe)) {
	  $this->flash(getMessage("noActiveRecipe"));
	  $this->activateDefault();
	}
	$user->addToMyRecipes($recipe->id);
	gotoReferrer();

  }
  
  /**
   * Shows the results for a particular page in the result set.
   */

  function results($page) {
	$user = $_SESSION['user'];
	unset($_SESSION['lastsearch']);
	unset($_SESSION['recipe']);

	$rset =& $_SESSION['mine'];
	$button = new stdClass();
	$button->url = buildLink("mine", "delete", "%d");
	$button->name = getMessage('remove');
	$modelView =& $this->prepareModelAndView();
	$rset->constructPayload(intval($page), array($button), $modelView);  
	$javascripts = null;
	$modelView->assign("selectedTab", "mine");
	$modelView->assign("title", getMessage("MyRecipes"));
	$modelView->assign("return_page", buildLink("mine", "index"));
	$modelView->display(getFullTemplateName('search'));
  }

  /**
   * Deletes the association between a user and a recipe
   */

  function delete($id) {
	$user = $_SESSION['user'];
	if(!empty($id)) {
	  $recipe = Recipe::load($id);
	} else {
	  $recipe = $_SESSION['recipe'];
	}
	if(!isset($recipe)) {
	  $this->flash(getMessage('noActiveRecipe'));
	  $this->activateDefault();
	}
	$user->removeFromMyRecipes($recipe->id);
	$this->activateDefault();
  }

  /**
   * Shows the list of recipes that are associated with a user.
   */

  function index() {
	$user = $_SESSION['user'];
	unset($_SESSION['lastsearch']);
	unset($_SESSION['recipe']);
	unset($_SESSION['mine']);
	$results = $user->searchForMine();
	$rset = new ResultSet(7, $results);
	$rset->name = getMessage("MyRecipes");
	$rset->displayResultCount = false;
	$_SESSION['mine'] =& $rset;
	$this->activateController("mine", "results", "1");
  }

}
?>
