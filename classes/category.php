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

require_once(dirname(__FILE__) . '/base_record.php');

/**
 * Factory responsible for creating Category objects.
 *
 * @author Andrew Violette
 * @since 0.9
 * @package rbook
 * @subpackage model
 * @version $Id: category.php,v 1.7 2007/04/01 14:38:35 maschine Exp $
 */

class CategoryFactory extends BaseRecordFactory {
    function createInstance() {
        return new Category();
    }
    function getTable() {
        return "categories";
    }
}

/**
 * Represents a category in the categories table.
 *
 * @author Andrew Violette
 * @since 0.9
 * @package rbook
 * @subpackage model
 * @version $Id: category.php,v 1.7 2007/04/01 14:38:35 maschine Exp $
 */

class Category extends BaseRecord {
  var $name;
  var $recipeCount;

  function Category() {
    $this->BaseRecord();
  }

  function init($row) {
    $this->id = $row['id'];
    $this->name = $row['name'];
  }

  /**
   * Creates a new category.
   */

  function dbCreateNew($db = null) {
    $cascade = isset($db);
    if(!$cascade) {
      $db =& $this->getDb();
    }
    $id = $db->nextId("categories");
    $this->runQuery($db, "insert into categories (id, name, createdate) values (?, ?, now())", 
                    array($id, $this->name));
    $this->id = $id;
    if(!$cascade) {
      $db->commit();
      $db->disconnect();
    }      
  }

  function dbUpdate($db = null) {
    die("not implemented");
  }

  /**
   * Returns a collection of categories whose ids are not in the set
   * passed in the $notIn parameter.
   */

  function &loadCategoriesNotIn(&$notIn) {
    $questions = array();
    for($i = 0; $i < count($notIn); $i++) {
      $questions[] = "?";
    }
    $db =& BaseRecord::getDb();
    $results =& BaseRecord::runQuery($db, "select * from categories " . 
                                    "where id not in (" . 
                                    implode(",", $questions) . ")", $notIn);
                                    
    $cats =& Category::processResults($results);
    $db->disconnect();
    return $cats;
  }
  
  /**
   * Returns a collection of all categories.
   */

  function &loadAllCategories() {
    $db =& BaseRecord::getDb();
    $results =& BaseRecord::runQuery($db, "select * from categories");
                                    
    $cats =& Category::processResults($results);
    $db->disconnect();
    return $cats;
  }

  function &processResults(&$results) {
    $categories = array();
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $cat = new Category();
      $cat->init($row);
      $categories[] = $cat;
    }
    return $categories;
  }


  /**
   * Deletes multiple categories.
   */

  function deleteMultiple($qualifiers = null) {
    BaseRecord::deleteMultipleOfClass($qualifiers, "categories");
  }

  /**
   * Returns an array of categories limited by the qualifiers passed in
   * @param qualifiers an associative array of qualifiers
   * @param limit a limit on the number of results to pass in (or null for no limit).
   */

	function &loadMultiple($qualifiers = null, $limit = null, $db = null) {
		if(!isset($db)) {
			$db = BaseRecord::getDb();
		}
		$results =& BaseRecord::loadMultipleBasic(new CategoryFactory(),
			$qualifiers, $limit, $db, "name");
		// load up the recipe counts for the various objects
		$foo = array();
		foreach($results as $cat) {
			$qualifiers = array("categoryid" => $cat->id);
			$query = "SELECT count(recipeid) from recipetocategory " . BaseRecord::buildWhereClauseDb($qualifiers);
			$result =& BaseRecord::runQuery($db,$query, BaseRecord::prepareQualifiers($qualifiers));
			$c = 0;
			if($result->fetchInto($row, DB_FETCHMODE_ORDERED)) {
				$c = $row[0];
				if(!isset($c)) {
					$c = 0;
				}
			}
			$cat->recipeCount = $c;
			$foo[] = $cat;
		}
		$db->disconnect();
		return $foo;
	}

  /** 
   * Returns one Category specified by the qualifiers
   */

  function loadOne($qualifiers, $db = null) {
    $cat = Category::loadMultiple($qualifiers, 1, $db);
    if(count($cat)) {
      return $cat[0];
    }
    return null;

  }
}

?>