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
 * Manages the rss feed.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */



class RssController extends BaseController {
  /**
   * Factory method that creates a user controller.  
   */

  function &newInstance() {
	$controller = new RssController("rss", false);
	$controller->set_valid_actions(array("index", "atom", "atom_user"));
	return $controller;
  }

  /**
   * Returns an atom feed that has all the users recipes in them.
   * For example /rss/atom_user/aviolette returns the recipes posted by me
   */

  function atom_user($username) {
	$user = User::loadOne(array("username" => $username));
	$recipes = array();
	if(isset($user)) {
	  $recipes = Recipe::searchByAuthor($user->id);
	}
	$this->atomFromList($recipes);
  }

  /** 
   * Returns the 15 most recent recipes in descending order.
   */

  function atom() {
	$this->atomFromList(Recipe::searchForMostRecent(15));
  }

  function atomFromList(&$recipes) {
	header("Content-Type: application/atom+xml");
	echo("<?xml version=\"1.0\"?>\n");
	echo("<feed xmlns=\"http://www.w3.org/2005/Atom\">");
	echo("<title>" . APPTITLE . "</title>");
	echo('<link href="' . buildFullLink("recipe", "index") . '"/>');
	for($i=0; $i < count($recipes); $i++) {
	  echo("<entry>\n");
	  echo("  <title>" . $recipes[$i]->title . "</title>\n");
	  echo("<link href='" . buildLink("recipe", "view", $recipes[$i]->recipeId) . "' />\n");
	  $r = Recipe::load($recipes[$i]->recipeId);
	  echo("<published>" . $this->dateToAtomFormat($r->createdate) . "</published>");
	  echo("<author>");
	  echo("<name>" . $r->submittedByName . "</name>");
	  echo("<uri>" . buildFullLink("user", "view_profile", $r->submittedByUserName) . "</uri>");
	  echo("</author>");
	  echo("  <description>" . getBaseUrl() . "</description>\n");
	  $modelView = $this->prepareModelAndView();
	  $modelView->assign("recipe", $this->buildDisplayableRecipe($r, true));
	  echo("<content type='xhtml'><div xmlns='http://www.w3.org/1999/xhtml'>"  . $modelView->fetch(getFullTemplateName('altRecipe')) . "</div></content>\n");
	  echo("</entry>\n");
	}
	echo("</feed>");


  }

  function dateToAtomFormat($date) {
	return date("Y-m-d\Th:i:s\Z", $date) ;
  }

  function index() {
	header("Content-Type: application/rss+xml");
	echo("<?xml version=\"1.0\"?>\n");
	echo('<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN" "http://my.netscape.com/publish/formats/rss-0.91.dtd">');
	echo("\n<rss version=\"0.91\">\n");
	echo("<channel>\n");
	echo("<title>" . APPTITLE . "</title>\n");
	echo("<language>en-US</language>\n");
	$recipes = Recipe::searchForMostRecent(10);
	for($i=0; $i < count($recipes); $i++) {
	  echo("<item>\n");
	  echo("  <link>" . getBaseUrl() . "viewRecipe.php?id=" . $recipes[$i]->recipeId . "</link>\n");
	  echo("  <title>" . $recipes[$i]->title . "</title>\n");
	  echo("  <description>" . getBaseUrl() . "</description>\n");
	  echo("</item>\n");
	}
	echo("</channel>\n");
	echo("</rss>\n");
  }
}
?>
