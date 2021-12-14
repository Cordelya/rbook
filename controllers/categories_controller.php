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
require_once(dirname(__FILE__) . "/base_controller.php");
/**
 * Controller responsible for the management of categories on the site.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */
class CategoriesController extends BaseController {

  function CategoriesController($name) {
    $this->BaseController($name);
  }
    
  /**
   * Factory method that creates a category controller.  
   * @static
   */

  function &newInstance() {
    $controller = new CategoriesController("categories");
    $controller->set_valid_actions(array("index", "add", "delete", 
                                         "show_replacements", 
                                         "pick_replacement"));
    $controller->set_requires_adminaccess(array("index", "add", "delete", 
                                                "show_replacements", 
                                                "pick_replacement"));
        
    return $controller;
  }



  function add() {
    unset($_SESSION['categories']);
    $cat = new Category();
    $cat->name = $_POST['name'];
    $cat->save();

    $this->activateAction("index");
  }

  function show_replacements() {
    $selectedCategories = $_SESSION['selectedCategories'];
    $editCategories = Category::loadCategoriesNotIn($selectedCategories);

    $modelView =& $this->prepareModelAndView();
    $modelView->assign("pageTitle", getMessage("PickReplacementCategory"));
    $modelView->assign("title", getMessage("PickReplacementCategory"));
    $modelView->assign("editCategories", $this->buildCategoryList($editCategories));
    $modelView->display(getFullTemplateName('pickReplacementCategory'));
  }

  function pick_replacement() {
    $selectedCategories = $_SESSION['selectedCategories'];
    Recipe::changeCategories($selectedCategories, $_POST['category']);
    Category::deleteMultiple(array('id' => $selectedCategories));
    unset($_SESSION['selectedCategories']);
    unset($_SESSION['categories']);
    $this->activateAction("index");
  }

  function delete() {
    $selectedCategories = $this->determineSelectedCategories();
    if(count($selectedCategories) == 0) {
      $this->activateAction("index");
    }
    if(Recipe::categoriesUsed($selectedCategories)) {
      $_SESSION['selectedCategories'] = $selectedCategories;
      $this->activateAction("show_replacements");
    } else {
      Category::deleteMultiple(array('id' => $selectedCategories));
      unset($_SESSION['categories']);
    }
    $this->activateAction("index");
  }

  function determineSelectedCategories() {
    $results = array();
    foreach($_POST as $key => $value) {
      if(preg_match('/^cat(\d+)/', $key, $match)) {
        $results[] = $match[1];
      }
    }
    return $results;
  }


  function index() {
    $editCategories = Category::loadMultiple();

    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", getMessage("Categories"));
    $modelView->assign("pageTitle", getMessage("editCategories"));
    $modelView->assign("action", APPROOT. "categories.php");
    $modelView->assign("editCategories", $this->buildCategoryList($editCategories));
    $modelView->assign("category_name_length", CATEGORY_NAME_LENGTH);
    $modelView->display(getFullTemplateName("categories"));
  }

}
?>