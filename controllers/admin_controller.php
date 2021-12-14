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

/**
 * Controls the display of the admin page.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */

class AdminController extends BaseController {
  /**
   * Factory method that creates a user controller.  
   */

  static function &newInstance() {
	$controller = new AdminController();
	$controller->set_valid_actions(array("index"));
	$controller->set_requires_adminaccess(array("index"));
	return $controller;
  }

  function index() {
	$this->before_execute("index");
	$page_title = getMessage("Admin");
	$smarty =& prepareSmarty();
	$smarty->assign("selectedTab", "admin");
	$smarty->assign("title", $page_title);
	$smarty->display(getFullTemplateName("viewAdmin"));
  }
}
?>
