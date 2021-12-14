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

require_once(dirname(__FILE__) . "/base_controller.php");


/**
 * Grocery list controller.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */

class GroceryController extends BaseController {

  /**
   * Factory method that creates an image controller.  
   */

  function &newInstance() {
	$controller = new GroceryController("grocery");
	$controller->set_valid_actions(array("clear", "save", "update", "remove", "index", "save_in_place"));
	$controller->set_requires_authentication(array("clear", "save", "save_in_place", "update", "remove", "index"));
	return $controller;
  }

  /**
   * Saves the grocery list posted from an AJAX transaction
   */

  function save_in_place() {
	$user =& getUser();
	$this->saveCommon($user);
	echo("SAVED");
  }

  function saveCommon(&$user) {
	GroceryList::deleteMultiple(array('userid' => $user->id));
	$i = 0;
	for($i = 0; ; $i++) {
	  $f = $_REQUEST['gi' . $i];
	  if(!empty($f)) {
		$gi = new GroceryList();
		$gi->description = $f;
		$gi->userid = $user->id;
		$gi->orderid = $i;
		$gi->save();
	  } else {
		break;
	  }
	}
  }

  /**
   * Saves the list that was posted.
   */

  function save() {
	if(!$this->isPost()) {
	  trigger_error("only valid for POST");
	}
	if(isset($_POST['clear'])) {
	  $this->activateAction('clear');
	}
	$user =& getUser();
	$this->saveCommon($user);
	$this->activateDefault();
  }

  function clear() {
	$user =& getUser();
	GroceryList::deleteMultiple(array('userid' => $user->id));
	$this->activateDefault();
  }

  function remove() {
  }

  function index() {
	$user = getUser();
	$groceryList = GroceryList::findByUser($user->id);
	$page_title = getMessage("GroceryList");
	$modelView =& $this->prepareModelAndView();
	$modelView->assign("selectedTab", "grocery");
	$modelView->assign("title", $page_title);
	$modelView->assign("groceryList", $this->buildGroceryList($groceryList));
	$modelView->display(getFullTemplateName("viewGroceryList"));
  }

  /**
   * Shows the dialog that adds a picture to the recipe
   */

  function update($item) {
	$item = $_POST['arg'];
    header("Content-type: text/plain");
	$user =& getUser();
	$list = GroceryList::findByUser($user->id);
	if(!empty($item)) {
	  $gi = new GroceryList();
	  $gi->description = $item;
	  $gi->userid = $user->id;
	  $gi->orderid = count($list) + 1;
	  $gi->save();
	  $list[] = $gi;
	}
	
	foreach($list as $gi) {
	  echo("<li>" . $gi->description . "</li>");
	}
  }

}
?>
