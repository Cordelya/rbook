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
 * Controller responsible for displaying the conversion page.
 * @author Andrew Violette
 * @since 2.0
 * @package rbook
 * @subpackage controllers
 */

class ConversionController extends BaseController {

  /**
   * Factory method that creates a user controller.  
   * @static
   */

  function &newInstance() {
    $controller = new ConversionController();
    $controller->set_valid_actions(array("index"));
    return $controller;
  }

  /**
   * Displays the conversion page.
   */

  function index() {
    unset($_SESSION['lastsearch']);
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("selectedTab", "conversion");
    $modelView->assign("title", getMessage("Conversions"));
    $modelView->display(getFullTemplateName("conversion"));
  }

}
?>
