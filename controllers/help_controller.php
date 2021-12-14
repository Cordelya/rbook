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
 * Controller responsible for the help page
 * @author Michael Schaefers
 * @since 2.3
 * @package rbook
 * @subpackage controllers
 */

class HelpController extends BaseController {

  /**
   * Factory method that creates a help page controller.  
   * @static
   */
  function &newInstance() {
	$controller = new HelpController();
  	$valid = array("index", "create_plugin");
    $auth = array("create_plugin");
      
    $controller->set_valid_actions($valid);
	$controller->set_requires_authentication($auth);
    return $controller;
  }

  /**
   * Displays the help page.
   */
  function index() {
    
	$pluginURL = "http://" . $_SERVER['SERVER_NAME'] . APPROOT . "searchplugin.src";
	$pluginIcon = "http://" . $_SERVER['SERVER_NAME'] . APPROOT . "skins/" . SKIN . "/images/favicon.ico";
	$pluginName = APPTITLE;
	
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("selectedTab", "help");
    $modelView->assign("title", getMessage("help"));
	$modelView->assign("purl", $pluginURL);
	$modelView->assign("picon", $pluginIcon);
	$modelView->assign("pname", $pluginName);
    $modelView->display(getFullTemplateName("help"));
  }
}
?>
