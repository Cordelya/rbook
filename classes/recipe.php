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
 * Contains a recipe and is responsible for persistence.
 *
 * @author Andrew Violette
 * @package rbook
 * @since 0.9
 * @version $Id: recipe.php,v 1.18 2007/03/23 15:55:50 maschine Exp $
 */

require_once(dirname(__FILE__) . '/base_record.php');
require_once(dirname(__FILE__) . "/category.php");

define("BASE_SEARCH_QUERY", "SELECT distinct recipes.id as recipeid," .
    "recipes.name as title,recipes.description, recipes.cached_rating, recipes.cached_ratinghits, " .
	   "users.username as username, users.name as uname, " .
    "recipes.createdate as cd, users.id as uid, users.username as username  from recipes,users ");

class Recipe extends BaseRecord {
  var $preptime;
  var $cooktime;
  var $serves;
  var $title;
  var $preheat;
  var $source;
  var $categoryName;
  var $categoryId;
  var $ingredients;
  var $steps;
  var $submittedById;
  var $submittedByName;
  var $submittedByUserName;
  var $createdate;
  var $images;
  var $uid;
  var $description;
  var $cachedRating;
  var $cachedRatingHits;
  var $lastViewed;
  var $viewCount;

  function Recipe() {
    $this->BaseRecord();
    $this->title = getMessage("NewTitle");
    $this->ingredients = array();
    $this->steps = array();
    $this->categories = array();
    $this->uid = $this->createUid();
  }

  function init($row, $id) {
    $this->title = $row["recipe_name"];
    $this->id = $id;
    $this->source = $row["source"];
    $this->submittedById = $row["submittedby"];
    $this->submittedByName = $row["submittedbyname"];
	$this->submittedByUserName = $row['submittedbyusername'];
    $this->note = $row['note'];
    $this->description = $row['description'];
    $this->preheat = $row['preheat'];
    $this->createdate = $row['cd'];
	if(preg_match('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/', $this->createdate, $matches)) {
	  $this->createdate = mktime((int)$matches[4], (int)$matches[5], (int)$matches[6], (int)$matches[2], (int)$matches[3], (int)$matches[1]);
	}
    $this->uid = $row['uniqueid'];
    $this->serves = $row['serves'];
    $this->cooktime = $row['cooktime'];
    $this->preptime = $row['preptime'];
    $this->cachedRatingHits = @$row['cached_ratinghits'];
    $this->cachedRating = @$row['cached_rating'];
	$this->lastViewed = $row['lastvisit'];
	$this->visits = $row['visits'];
  }

  /**
   * Takes a list of category names and determines if those categories are
   * available in the category list for this recipe.  If any are missing
   * they are created and added to the category list.
   */

  function reconcileCategories(&$categories) {
    $cs = array();
    if(empty($categories)) {
      $categories = array(getMessage("Cat04"));
    }
    foreach($categories as $catName) {
      $found = false;
      foreach($this->categories as $cat) {
        if($catName == $cat->name) {
          $found = true;
          break;
        }
      }
      if($found === false) {
        $cat = new Category();
        $cat->name = $catName;
        $cat->save();
      }
      $cs[] = $cat;
    }
    $this->categories = $cs;
  }


  /**
   * Updates the recipe based on the values posted to the edit page.
   * @param array $postValues the posted values
   * @access public
   */

  function update($postValues) {
    $this->serves = $postValues['serves'];
    $this->cooktime = $postValues['cooktime'];
    $this->preptime = $postValues['preptime'];
    $this->title = $this->unescapeLiteral($postValues['title']);
    $this->source = $this->unescapeLiteral($postValues['source']);
    $categories = $this->splitAndTrim($this->unescapeLiteral($postValues['categories']));
    $this->categories = Category::loadMultiple(array("name" => $categories));
    $this->reconcileCategories($categories);
    $this->preheat = $postValues['preheat'];
    $this->note = $this->unescapeLiteral($postValues['note']);
    $this->description = $this->unescapeLiteral($postValues['description']);
    $this->steps = array();
    $sets = array();

    foreach($postValues as $key => $value) {
      if(preg_match('/^step(\d+)/', $key, $match)) {
        $value = $this->unescapeLiteral($value);
        $this->steps[$match[1]] = $value;
      } else if(preg_match('/^amount-(\S+)-(\d+)/', $key, $match)) {
        $sets[$match[1]] = 1;
      }
    }
    $this->ingredients = array();
    foreach($sets as $key => $value) {
      $foo = new IngredientSet();
      $foo->id = $key;
      $foo->update($postValues);
      $this->addIngredientSet($foo);
    }
  }

  /**
   * Adds an ingredient set to the recipe.
   * @param IngredientSet $set the ingredient set
   * @access public
   */

  function addIngredientSet(&$set) {
    $this->ingredients[$set->id] = $set;
  }

  /**
   * Updates a pre-existing recipe in the database.
   * @param connection $con the database connection
   * @access private
   */

  function dbUpdate($db = null) {
    $db =& $this->getDb();
    $query = "UPDATE recipes SET name = ?, preheat = ? " .
      ",source = ?, serves = ?, cooktime =?, preptime = ?,note = ?, description = ?, modifieddate = now(), cached_rating = ?, cached_ratinghits = ? WHERE ID = ?";

    $result =& $this->runQuery($db, $query,
                               array($this->title, $this->preheat,
                                     $this->source,
                                     $this->serves,
                                     $this->cooktime,
                                     $this->preptime,
                                     $this->note,
                                     $this->description,
                                     $this->cachedRating,
                                     $this->cachedRatingHits,
                                     $this->id));

    $result =& $this->runQuery($db, "select id from ingredientsets where recipeid = ?",
                               $this->id);

    $sets = array();
    while($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $sets[] = $row['id'];
    }
    if(count($sets) > 0) {
      $qualifiers = array('id' => $sets);

      for($i = 0; $i < count($sets); $i++) {
        $this->runQuery($db, "delete from ingredients where setid = ?", $sets[$i]);
      }

      $result =& $this->runQuery($db, "delete from ingredientsets " .
                                 $this->buildWhereClauseDb($qualifiers),
                                 $this->prepareQualifiers($qualifiers));
    }
    $catids = array();
    $this->runQuery($db, "delete from recipetocategory where recipeid = ?", array($this->id));
    $this->updateCategories($db);
    $this->saveIngredientSets($db);
    $this->updateSteps($db);

    $db->commit();
    $db->disconnect();

  }

  function updateCategories(&$db) {
    foreach($this->categories as $cat) {
      $this->runQuery($db, "insert into recipetocategory (recipeid, categoryid) values (?, ?)",
                      array($this->id, $cat->id));
    }
  }

  function updateSteps(&$db) {
    $numSteps = count($this->steps);
    $result =& $this->runQuery($db, "delete from steps where recipeid = ? and orderid >= ?",
                               array($this->id, $numSteps));

    $results =& $this->runQuery($db, "select count(*) from steps where recipeid = ?",
                                $this->id);

    $results->fetchInto($row, DB_FETCHMODE_ORDERED);
    $existing = $row[0];

    $i = 0;
    foreach($this->steps as $idx => $value) {
      if($i < $existing) {
        $result = $this->runQuery($db,"update steps set step = ? where recipeid = ? and orderid = ?",
                                  array($value, $this->id, $i));
      } else {
        $stepid = $db->nextId("steps");
        $result =& $this->runQuery($db,"INSERT INTO steps (id, recipeid, orderid, step) values (?, ?, ?, ?)",
                                   array($stepid, $this->id, $i, $value));
      }
      $i++;
    }
  }

  function addStep($step) {
    $this->steps[] = $step;
  }

  /**
   * Saves this recipe to the database for the first time.  This
   * includes all the subobjects of the recipe as well.
   * @param connection $con the database connection
   * @access private
   */
  function dbCreateNew($db = null) {
    $db =& $this->getDb();
    $id = $db->nextId("recipes");
	
	// Change empty strings to null values
	if (isset($this->serves) && $this->serves == '')
		$this->serves = null;
	if (isset($this->cooktime) && $this->cooktime == '')
		$this->cooktime = null;
	if (isset($this->preptime) && $this->preptime == '')
		$this->preptime = null;
	if (isset($this->source) && $this->source == '')
		$this->source = null;		
	if (isset($this->note) && $this->note == '')
		$this->note = null;
	if (isset($this->description) && $this->description == '')
		$this->description = null;
	if (isset($this->preheat) && $this->preheat == '')
		$this->preheat = null;
		
	/**
	* For saving the createdate on import.
	* The date must be a MySQL-Timestamp YYYYMMDDhhmmss.
	* When iserting a new recipe, the current timestamp is used.
	*/
	if(isset($this->createdate) && $this->createdate != null)
		$cdate = $this->createdate;
	else
		$cdate = date("YmdHis", time());
	
    $result =& $this->runQuery($db,"INSERT INTO recipes (id, name, source, submittedby, serves, cooktime, preptime, note, description, " .
                               "preheat, uniqueid, createdate) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
                               array($id,
									 $this->title,
									 $this->source,
									 $this->submittedById,
									 $this->serves,
									 $this->cooktime,
									 $this->preptime,
									 $this->note,
									 $this->description,
									 $this->preheat,
									 $this->uid,
									 $cdate));

    $this->id = $id;

    $this->updateCategories($db);

    $i = 0;
    foreach($this->ingredients as $setName => $set) {
      $set->saveSet($db, $this, $i);
      $i++;
    }
    for($i =0; $i < count($this->steps); $i++) {
      $stepid = $db->nextId("steps");
      $result =& $this->runQuery($db,"INSERT INTO steps (id, recipeid, orderid, step) values (?, ?, ?, ?)",
                                 array($stepid, $this->id, $i, $this->steps[$i]));
    }

    $db->commit();
    $db->disconnect();
  }



  /**
   * Saves all the sub-elements of the recipe into the database.
   * @param connection $con the database connection
   * @access private
   */

  function saveIngredientSets(&$db) {
    $i =0;
    foreach($this->ingredients as $setName => $set) {
      $set->saveSet($db, $this, $i);
      $i++;
    }
  }

  /**
   * Retrieves all the sub elements of the recipe object from the
   * database.
   * @param connection $con the database connection
   * @access private
   */

  function loadElements(&$db) {
    $result =& $this->runQuery($db, "select categories.id as id, categories.name as name from categories,recipetocategory where recipeid = ? and categories.id = recipetocategory.categoryid", array($this->id));
    $this->categories = array();
    while($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $c = new Category();
      $c->id = $row['id'];
      $c->name = $row['name'];
      $this->categories[] = $c;
    }
    $result =& $this->runQuery($db,"select id, name from ingredientsets where recipeid = ? order by orderid",
                               array($this->id));

    while($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $iset = new IngredientSet();
      $iset->name = $row['name'];
      $iset->id = $row['id'];
      $this->addIngredientSet($iset);
    }


    $query = "select amount, description, setid from ingredients where setid = ? order by orderid";
    $statement = $db->prepare($query);
    foreach($this->ingredients as $setName => $set) {
      $result =& $db->execute($statement, $set->id);

      while($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
        $ingredient = new Ingredient();
        $ingredient->amount = $row['amount'];
        $ingredient->description = $row['description'];
        $this->ingredients[$row['setid']]->rows[] = $ingredient;
      }
    }

    $result =& $this->runQuery($db,"select step from steps where recipeid = ? order by orderid", array($this->id));
    $this->steps = array();

    while($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $this->steps[] = $row['step'];
    }

    $images =& Image::findForRecipe($this->id, null, $db);
    $this->images =& $images;


  }

  function deleteMultiple($qualifiers = null) {
    BaseRecord::deleteMultipleOfClass($qualifiers, "recipes");
  }

  /**
   * Deletes the recipe from the database.
   */

  function remove() {
    $db =& $this->getDb();
    rb_log("REMOVING RECIPE: " . $this->id);
    $this->removeImages($db);
    // Let the cascade remove all the children
    $this->runQuery($db,"delete from recipes where id = ?", $this->id);
    $db->commit();
    $db->disconnect();
  }

  function removeImages($db = null) {
    $cascade = isset($db);
    if(!$cascade) {
      $db =& $this->getDb();
    }
    // images have a file-system component, so they have to be deleted manually.
    $images = Image::loadMultiple(array("recipeid" => $this->id), null, $db);
    $level = error_reporting(0);
    foreach($images as $image) {
      $image->remove($db);
    }
    error_reporting($level);
    if(!$cascade) {
      $db->commit();
      $db->disconnect();
    }
  }
  
    function removeImage($imageid, $db = null) {
    $cascade = isset($db);
    if(!$cascade) {
      $db =& $this->getDb();
    }
    // images have a file-system component, so they have to be deleted manually.
    $image = Image::load($imageid, $db);
    $level = error_reporting(0);
    $image->remove($db);
    error_reporting($level);
    if(!$cascade) {
      $db->commit();
      $db->disconnect();
    }
  }

  /**
   Increments the number of times the recipe was visited
  */

  function incrementViewCount($recipe, $db = null) {
	$cascade = isset($db);
	if(!$cascade) {
	  $db =& $this->getDb();
	}

	BaseRecord::runQuery($db, "update recipes set visits = ?, lastvisit = now() where ID = ?",
						 array($recipe->visits + 1, $recipe->id));

	if(!$cascade) {
	  $db->commit();
	  $db->disconnect();
	}
  }

  /**
   * Loads the recipe specified by the database id.  Returns the
   * recipe object or null if it cannot be found.
   */

  function &load($id) {
    $db =& BaseRecord::getDb();

    $result =& BaseRecord::runQuery($db,"select recipes.serves, cooktime, preptime, visits, lastvisit, recipes.name as recipe_name,source,preheat,uniqueid" .
                                    ",categories.name as category_name,recipes.id,recipes.note,recipes.description, recipes.createdate as cd," .
                                    "users.username as submittedbyusername, users.name " .
                                    "as submittedbyname,submittedby from recipes, " .
                                    "categories, users where recipes.id = ? and " .
                                    "submittedby = users.id",
                                    array($id));

    $recipe = null;
    if($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $recipe = new Recipe();
      $recipe->init($row, $id);
      $recipe->loadElements($db);
    }
    $db->disconnect();
    return $recipe;
    
  }


  /**
   * Returns true if there are any recipes in the categories specified
   * in the list.
   * @param categories the list of category ids
   */

  function categoriesUsed($categories) {
    $db =& BaseRecord::getDb();
    $qualifiers = array("categoryid" => $categories);
    $query = "SELECT count(recipeid) from recipetocategory " . BaseRecord::buildWhereClauseDb($qualifiers);
    $result =& BaseRecord::runQuery($db,$query, BaseRecord::prepareQualifiers($qualifiers));
    
    $rc = false;
    if($result->fetchInto($row, DB_FETCHMODE_ORDERED)) {
      $rc = $row[0] > 0;
    }
    $db->disconnect();
    return $rc;
  }

  function updateRating($rating, $ratingHits) {
    $db =& BaseRecord::getDb();
    $this->runQuery($db, "update recipes set cached_rating = ?, cached_ratinghits = ? where id = ?",
                    array($rating, $ratingHits, $this->id));
    $db->commit();
    $db->disconnect();
  }

  /**
   * Puts any recipe that are in any of the categories in the category
   * list in the category specified by toCategory.
   * @param categories a list of category ids
   * @param toCategory the category to put the recipes in
   */
  function changeCategories($categories, $toCategory) {
    $db =& BaseRecord::getDb();
    $qualifiers = array("categoryid" => $categories);
    $query = "update ignore recipetocategory set categoryid = ? " .
      BaseRecord::buildWhereClauseDb($qualifiers);
    $paramList = array_merge(array($toCategory),
                             BaseRecord::prepareQualifiers($qualifiers));
    rb_log("PARAM LIST: " . implode(",", $paramList));                             
    BaseRecord::runQuery($db, $query, $paramList);
    $db->commit();
    $db->disconnect();
  }

  function &searchByAuthor($authorId) {
    $db =& BaseRecord::getDb();
    $query = "SELECT distinct recipes.id as recipeid,recipes.name as title," .
      "users.name as uname, users.id as uid, users.username as username, recipes.createdate as cd, recipes.cached_rating, recipes.cached_ratinghits from recipes,users ".
      "where users.id = ? and recipes.submittedby = users.id " .
      "order by recipes.createdate desc";
    $res =& BaseRecord::runQuery($db, $query, array($authorId));
    return Recipe::processResults($db, $res);
  }

  function &searchByMostRecentAndAuthor($limit, $authorId) {
    $db =& BaseRecord::getDb();
    $query = "SELECT distinct recipes.id as recipeid,recipes.name as title," .
      "users.name as uname, users.id as uid, users.username, recipes.createdate as cd, recipes.cached_rating, recipes.cached_ratinghits from recipes,users ".
      "where users.id = ? and recipes.submittedby = users.id " .
      "order by recipes.createdate desc";
    $res =& $db->limitQuery($query, 0, $limit, array($authorId));
    return Recipe::processResults($db, $res);
  }

  /**
   Searches for the most popular recipes
   */

  function &searchForMostPopular($limit) {
	$db =& BaseRecord::getDb();
	$query = BASE_SEARCH_QUERY .
	  "where recipes.submittedby = users.id order by visits desc";
	$res =& $db->limitQuery($query, 0, $limit);
	return Recipe::processResults($db, $res);
  }

  /**
   * Searches for the most recent recipes added to the database. 
   */

  function &searchForMostRecent($limit) {
    $db =& BaseRecord::getDb();
    $query = BASE_SEARCH_QUERY .
      "where recipes.submittedby = users.id order by recipes.createdate desc";
    $res =& $db->limitQuery($query, 0, $limit);
    return Recipe::processResults($db, $res);
  }

  function getCookTime() {
    return isset($this->cooktime) ? $this->cooktime : 0;
  }

  function getPrepTime() {
    return isset($this->preptime) ? $this->preptime : 0;
  }

  function getTotalTime() {
    return ($this->getCookTime() + $this->getPrepTime());
  }

  function &searchByTitle($title) {
    $db =& BaseRecord::getDb();
    $title = $db->escapeSimple($title);
    $query = BASE_SEARCH_QUERY . " WHERE recipes.name like '$title%'" .
      " and recipes.submittedby = users.id order by recipes.name";
    $res =& BaseRecord::runQuery($db,$query);

    return Recipe::processResults($db, $res);
  }

  function &searchByKeyword($keyword) {
    if(empty($keyword)) {
      return Recipe::searchByTitle($keyword);
    }
    $db =& BaseRecord::getDb();
    $keyword = $db->escapeSimple($keyword);
    $query = BASE_SEARCH_QUERY . ",steps where (recipes.name like '%" . $keyword . "%'".
      " or steps.step like '%$keyword%') and recipes.submittedby = users.id " .
      " and steps.recipeid = recipes.id order by recipes.name";

    $res =& BaseRecord::runQuery($db,$query);
    
    return Recipe::processResults($db, $res);
  }

  function &processResults($db, &$res) {
    $resultSet = array();
    while ($res->fetchInto($row,  DB_FETCHMODE_ASSOC)) {
      $resultSet[] = new SearchResult($row['title'], buildViewUrl($row['recipeid']),
                                      $row['recipeid'], $row['uname'], $row['username'],
                                      $row['cd'], null, $row['uid'], $row['description'],
                                      $row['cached_rating'], $row['cached_ratinghits']);
    }
    Recipe::addImages($db, $resultSet);

    $db->disconnect();
    return $resultSet;
  }

  function addImages(&$db, &$results) {
    for($i = 0; $i < count($results); $i++) {
      $foo =& $results[$i];
      $images =& Image::findForRecipe($foo->recipeId);
      if(count($images) > 0) {
        $image =& $images[0];
        $foo->image = $image->getThumbWebPath();
      }
    }
  }

  function &searchByCategory($categoryId) {
    $db =& BaseRecord::getDb();
    $query = "SELECT distinct recipes.id as recipeid,recipes.name as title,recipes.description," .
      "users.name as uname, users.username as username, recipes.cached_rating, recipes.cached_ratinghits, recipetocategory.categoryid as categoryid," .
      "users.id as uid,recipes.createdate as cd  " .
      "FROM recipes,users,recipetocategory WHERE recipetocategory.categoryid = ? " .
      "and recipetocategory.recipeid = recipes.id and recipes.submittedby = users.id order by recipes.name";
    $res =& BaseRecord::runQuery($db,$query, array($categoryId));
    return Recipe::processResults($db, $res);
  }

  function &categoryList() {
    $foo = array();
    foreach($this->categories as $cat) {
      $foo[] = $cat->name;
    }
    return $foo;
  }
  
  /**
     * Get a random ID from the db.
     **/	 
  function &getRandomId() {
    $db =& BaseRecord::getDb();
    $query = "SELECT id FROM recipes ORDER BY RAND() LIMIT 1";
    $res =& BaseRecord::runQuery($db,$query);
	$res->fetchInto($row,  DB_FETCHMODE_ASSOC);
    return $row[id];
  }
  
  /**
     * Gets the number of all available recipes in the db.
     **/	 
  function &getRecipeCount() {
    $db =& BaseRecord::getDb();
    $query = "SELECT count(id) number FROM recipes";
    $res =& BaseRecord::runQuery($db,$query);
	$res->fetchInto($row,  DB_FETCHMODE_ASSOC);
    return $row[number];
  }
}
?>