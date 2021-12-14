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
 * Redirects user to the installation app.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */


class InstallController extends BaseController {
  /**
   * Factory method that creates a user controller.  
   */

  function &newInstance() {
	$controller = new InstallController();
	$controller->set_valid_actions(array("index"));
	return $controller;
  }

  function index() {
	$this->before_execute("index");
	$_SESSION['mobile'] = true;
	// pop out of controller mode since the install app was never converted
	header("Location: index.php");
	exit();
  }
}
?>
