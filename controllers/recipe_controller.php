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
require_once(dirname(__FILE__) . '/../init.php');
require_once(dirname(__FILE__) . '/../classes/result_set.php');


/**
 * Controller that is responsible for display, editing, and searching for 
 * recipes. Also manages images and comments attached to a recipe.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */
class RecipeController extends BaseController {

  /**
   * Factory method that creates a recipe controller.
   * @static  
   */
  function &newInstance() {
    $controller = new RecipeController("recipe");
	$valid = array("author", "create", "create_comment", "delete", "delete", 
				  "save", "edit", "add_picture", "remove_picture", "remove_pictures",
				  "save_picture", "delete_comment", "sort_by", "view", "random", 
				   "index", "suggest", "results", "author", "category", "search");
    $auth = array("author", "create", "create_comment", "delete", "delete", 
				  "save", "edit", "add_picture", "remove_picture", "remove_pictures",
				  "save_picture", "delete_comment");

    if(VIEW_POLICY != 'all') {
      $auth[] = "view";
    }
	$controller->set_valid_actions($valid);
    $controller->set_requires_authentication($auth);
    return $controller;
  }

  /**
   * Invoke to change the sort type
   * @param by can be 'recent' or 'popular'
   */
  function sort_by($by) {
	setCookie('sortType', $by, time() + 2600000, APPROOT);
	$this->activateAction('index');
  }

  /**
   * Default action taken when site is hit
   */
  function index() {
    // redirect to installation page if the config.php file cannot be found
    if(!file_exists(dirname(__FILE__) . "/../config.php") &&
       file_exists(dirname(__FILE__) . "/../install/index.php")) {
      header("Location: " . APPROOT . "/install/");
      exit();
    }
	$sortType = $_COOKIE['sortType'];
	$results = null;
	if(isset($sortType) && $sortType == 'popular') {
	  $results = Recipe::searchForMostPopular(RESULTSONHOMEPAGE);
	} else {
	  $results = Recipe::searchForMostRecent(RESULTSONHOMEPAGE);
	  $sortType = 'recent';
	}
    unset($_SESSION['lastsearch']);
    unset($_SESSION['recipe']);
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", APPTITLE);
    $rset = new ResultSet(RESULTSONHOMEPAGE, $results);
    $rset->name = getMessage("mostRecentAdditions");
    $rset->displayResultCount = false;
    $rset->constructPayload(null, null, $modelView);
	$modelView->assign("sortType", $sortType);
    $modelView->assign("selectedTab", "home");
    $modelView->assign("showrss", "true");
    $modelView->display(getFullTemplateName('index'));
  }

  /**
   * Views a recipe
   * @param id the database ID of the recipe; if not specified, attempt to view the recipe in session
   */
  function view($id) {
    $recipe = $this->getRecipe(false, $id);
	if(isset($recipe)) {
	  $recipe->incrementViewCount($recipe);
	}
    $_SESSION['recipe'] = $recipe;
    $user = getUser();
    $groceryList = GroceryList::findByUser($user->id);
    $comments = Comment::findByRecipe($recipe->id);
    $page_title = $recipe->title;
    $hiliteCategory = $recipe->categoryName;
    $modelView =& $this->prepareModelAndView();
	$modelView->assign("hasComments", count($comments));
    $modelView->assign("title", $recipe->title);
    $modelView->assign("recipe", $this->buildDisplayableRecipe($recipe, true));
    $modelView->assign("groceryList", $this->buildGroceryList($groceryList));
    $modelView->assign("deletePictureUrl", buildLink("recipe", "remove_picture"));
    $modelView->assign('deleteUrl', buildLink('recipe', 'delete'));
    $this->buildCommentParameters($user, $comments, $modelView);
	$this->buildImageParameters($recipe->images, $modelView);
    $modelView->display(getFullTemplateName('viewRecipe'));
  }

	/**
	* Build parameters for displaying recipe images
	*/
    function buildImageParameters(&$images, &$smarty) {
	$boxheight = 0;
	$imageheight = 0;
	$imagetext = false;
	
	// More space for the slideshow controls.
	if(count($images) > 1)
		$boxheight += 15;
	
	foreach($images as $image) {
		if ($image->height > $imageheight)
			$imageheight = $image->height;
	
		if (isset($image->caption)) {
			$imagetext = true;
		}
	}
	
	//Some extra space for the image border
	$imageheight += 10;
		
	$boxheight += $imageheight;

	// More space for the image caption.
	if($imagetext)
		$boxheight += 20;
	
	$smarty->assign("imagetext", $imagetext);
	$smarty->assign("imageboxheight", $boxheight);
	$smarty->assign("imageheight", $imageheight);
  }

  function buildCommentParameters(&$user, &$comments, &$smarty) {
	$l = array();
	$hasComment = false;
	$rating = 0;
	$ratingHits = 0;
	
	calculateRating($comments, $rating, $ratingHits);
	
	foreach($comments as $comment) {
	  $u = User::loadOne(array("id" => $comment->userid));
	  if($u->id == $user->id) {
		$hasComment = true;
	  }
	  sscanf($comment->createDate, "%d-%d-%d %d:%d:%d", $y,$m,$day, $h,$min, $s);
	  $l[] = array("comment" => $comment->comment,
				   "id" => $comment->id,
				   "submitted" => "$y-$m-$day",
				   "rating" => $comment->rating,
				   "userid" => $u->id,
				   "user" => $u->name);
	}
	$smarty->assign("rating", $rating);
	$smarty->assign("ratingVotes", $ratingHits);
	$smarty->assign("userHasComment", $hasComment);
	$smarty->assign("comments", $l);
  }
  

  /**
   * Creates a new recipe and loads it into the editor.
   */
  function create() {
    if(isReadonlyUser()) {
      $this->flash(getMessage('invalidAction'));
      $this->activateDefault();
    }
    $user =& getUser();
    
    $recipe = new Recipe();
    $recipe->submittedById = $user->id;
    $recipe->submittedByName = $user->name;
    
    $_SESSION['recipe'] = $recipe;
    
    unset($_SESSION['lastsearch']);
    $this->activateController("recipe", "edit");
  }

  /**
   * Creates a comment and inserts it into the recipe
   */

  function create_comment() {
    if(!$this->isPost()) {
      $this->flash(getMessage("ActionValidForPostOnly"));
      gotoReferrer();
    }

    $recipe =& $this->getRecipe(false);

    if($this->isPost()) {
      $user =& getUser();
      $comment = new Comment();
      $comment->userid = $user->id;
      $comment->recipeid = $recipe->id;
      if(!empty($_POST['commentArea'])) {
        $comment->comment = $_POST['commentArea'];
      }
      $rating = $_POST['starRating'];
      if(empty($rating) || $rating == 'notspecified') {
        $comment->rating = null;
      } else if($rating < 0) {
        $comment->rating = 0;
      } else if($rating > 5) {
        $comment->rating = 5;
      } else {
        $comment->rating = $rating;
      }
      $comment->save();
      $comments = Comment::findByRecipe($recipe->id);
      
      calculateRating($comments, $rating, $ratingHits);
      $recipe->cachedRating = $rating;
      $recipe->ratingHits = $ratingHits;
      $recipe->updateRating($rating, $ratingHits);
      unset($_SESSION['categories']);
      $this->activateController("recipe", "view", $recipe->id);
    } 
    $this->activateController("recipe", "view", $recipe->id);
  }


  function delete_comment($commentId) {
    if(!isset($commentId)) {
      $this->flash(getMessage("invalidComment"));
      $this->activateDefault();
    }
    
    $comment = Comment::loadOne(array("id" => $commentId));
    $user =& getUser();
    // verify the user is either the owner of the comment or an admin user
    if($user->id != $comment->userid && !isAdminUser()) {
      $this->flash(getMessage("noPermissionToDeleteComment"));
      $this->activateDefault();
    }
    $comment->remove();
	$recipe =& $this->getRecipe(false);
	$comments = Comment::findByRecipe($recipe->id);
      
	calculateRating($comments, $rating, $ratingHits);
	$recipe->cachedRating = $rating;
	$recipe->ratingHits = $ratingHits;
	$recipe->updateRating($rating, $ratingHits);

    gotoReferrer();
  }


  /**
   * Saves an image that is posted.
   */

  function save_picture() {
    $recipe =& $this->getRecipe(false);
    if($this->isPost()) {
      if(strlen($_POST['cancel']) > 3) {
        $this->activateDefault();
      } else {
        if(is_uploaded_file($_FILES['imagefile']['tmp_name'])) {
          $image = new Image();
          $user =& getUser();
          
          $image->caption = $_POST['caption'];
          $image->recipeid = $recipe->id;
          $image->recipeuid = $recipe->uid;
          $image->submittedBy = $user->id;
          $image->determineType($_FILES['imagefile']['type']);
          if($image->isValid()) {
            $imageDir = dirname(__FILE__) . '/../' . $image->getRelativeDirectory();
            if(!file_exists($imageDir)) {
              $imgrootdir = dirname(__FILE__) . '/../img';
              if(!file_exists($imgrootdir)) {
                $rc2 = mkdir($imgrootdir, 0770);
                if(!$rc2) {
                  die("Failed to make image root directory: " . $imgrootdir);
                }
              }
              $rc = mkdir($imageDir, 0770);
              if(!$rc) {
                die("Failed to make image directory: " . $imageDir);
              }
            }
            $fname = $image->getFullPath();
            move_uploaded_file($_FILES['imagefile']['tmp_name'], $fname);
            $image->buildThumb();
            $image->resizeToMax();
			$image->getDimensions();
			$image->save();
          } else {
            $this->flash(getMessage("invalidImgType"));
          }
        }

      }
      unset($_SESSION['categories']);
      $this->activateController("recipe", "view", $recipe->id);
    }
  }
  
  function remove_picture($id = null) {
    $recipe =& $this->getRecipe(true);
    if (isset($id)) {
	   // Just delete a single picture
	   $recipe->removeImage($id);
    } else {
       $recipe->removeImages();
	}
	
    $this->activateController("recipe", "view", $recipe->id);
  }
  
  function remove_pictures() {
    $recipe = $this->getRecipe(false);
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", $recipe->title);
    $modelView->assign("hiliteCategory", $recipe->categoryName);
    $modelView->assign("recipe", $this->buildDisplayableRecipe($recipe, false));
    $modelView->display(getFullTemplateName('removePictures'));
  }

  function add_picture() {
    $recipe = $this->getRecipe(false);
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", $recipe->title);
    $modelView->assign("caption", $_POST['caption']);
    $modelView->assign("hiliteCategory", $recipe->categoryName);
    $modelView->assign("recipe", $this->buildDisplayableRecipe($recipe, false));
    $modelView->display(getFullTemplateName('addPicture'));
  }

  /**
   * Displays a list of items in a list that match the suggested list.
   */
  function suggest($search) {
    header("Content-type: text/plain");
    $results = Recipe::searchByTitle($search);
    echo("<ul>");
    if(count($results) > 0) {
      foreach($results as $result) {
        echo ("<li>" . $result->title . "</li>");
      }
    }
    echo("</ul>");
  }

  /**
   * Deletes the active recipe.
   */
  function delete() {
    $recipe =& $this->getRecipe(true);
    if(isset($recipe)) {
      $recipe->remove();
	  unset($_SESSION['categories']);
    }
	$this->activateAction("index");
   }

  /**
   * Displays the results that are in the result set that is in
   * session.
   */
  function results($page) {
    $modelView =& $this->prepareModelAndView();
    $rset = $_SESSION['results'];
    $_SESSION['lastsearch'] = $REQUEST_URI;
    $rset->page = intval($page);
    $rset->constructPayload($page, null, $modelView);
	$modelView->assign("title", $rset->name);
    $modelView->display(getFullTemplateName('search'));
  }

  /**
   * Search by author's database ID and forward to results view.
   */
  function author($authorId) {
    $results = Recipe::searchByAuthor($authorId);
    $u =& User::loadOne(array("id" => $authorId));
    $rset = new ResultSet(RESULTS_PER_PAGE, $results);
    $rset->name = getMessage("recipesBy") . $u->name;
    $rset->displayResultCount = false;
    $_SESSION['results'] =& $rset;
    $this->activateController("recipe", "results", "1");
  }

  function category($category) {
    unset($_SESSION['results']);
    $results = Recipe::searchByCategory($category);
    $cat = Category::loadOne(array('id' => $category));
    $rset = new ResultSet(RESULTS_PER_PAGE, $results);
    $rset->name = $cat->name;
    $rset->displayResultCount = false;
	$rset->fromPage = 'category';
    $_SESSION['results'] =& $rset;
    $this->activateController("recipe", "results", "1");
  }

  function search() {
    unset($_SESSION['results']);
    $results = Recipe::searchByKeyword($_GET['search']);
    if(isset($results) && count($results) == 1 && (DISPLAYIFONLYONE === true)) {
      $this->activateController("recipe", "view", $results[0]->recipeId);
    }
    $rset = new ResultSet(RESULTS_PER_PAGE, $results);
    $_SESSION['results'] =& $rset;
	$rset->fromPage = 'search';
	$rset->name = $_GET['search'];
    $rset->displayResultCount = false;
    $this->activateController("recipe", "results", "1");
  }

  function &getRecipe($editable = false, $id = null) {
    if(!empty($id)) {
      $recipe = Recipe::load($id);
    } else {
      $recipe =& getActiveRecipe();
    }
    
    if(!isset($recipe)) {
      $this->flash(getMessage("noRecipeSelected"));
      $this->activateDefault();
    }

    if($editable && !editableByCurrentUser($recipe)) {
      $this->flash(getMessage("noPermissionToEditRecipe"));
      $this->activateController("recipe", "view");
    }
    return $recipe;
  }

  /**
     * Saves a recipe. This method is called from the UI.
     */
  function save() {
    $recipe =& $this->getRecipe(true);
	if(isset($_POST['discardAndView'])) {
	  if($recipe->isNew()) {
		$this->activateAction("index");
	  } else {
		$this->activateAction("view", $recipe->id);
	  }
	}
    $recipe->update($_POST);
	$this->validatePostData($_POST);
    $recipe->save($link);
    unset($_SESSION['categories']);
	if(isset($_POST['saveAndEdit'])) {
	  $this->activateAction("edit", $_POST['selectedTab']);
	} else {
	  $this->activateController("recipe", "view", $recipe->id);
	}
  }
 
  /**
   * Edit a recipe that is in-session.
   */
  function edit($section) {
    $recipe = $this->getRecipe(true);
    if(!$recipe->isNew()) {
	  $recipe = Recipe::load($recipe->id);
    }
    
    //Category DropDown
    $category = new Category();
    $catIDs = array();
    $catNames = array();
    $categories = $category->loadAllCategories();
	sort($categories);
    foreach($categories as $cat) {
      $catIDs[] = $cat->id;
      $catNames[] = $cat->name;
    }
    
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", $recipe->title);
    $modelView->assign("hiliteCategory", $recipe->categoryName);
    $modelView->assign("recipe", $this->buildDisplayableRecipe($recipe, false));
    $modelView->assign('catIDs', $catIDs);
    $modelView->assign('catNames', $catNames);
	$modelView->assign('selectedTab', $section);
    $modelView->assign('deleteUrl', buildLink('recipe', 'delete'));
    $modelView->display(getFullTemplateName('editRecipe'));
  }
  
  /**
     * Validates all data entered while editing the recipe (or creating a new recipe).
     * For example a recipe needs a name and several other fields. 
     */
  function validatePostData($post) {
	if(empty($post['title'])) {
      $this->flash(getMessage("missingTitle"));
      $this->activateAction("edit", "information");
    }
    if(empty($post['categories'])) {
      $this->flash(getMessage("missingCategory"));
      $this->activateAction("edit", "information");
    }
	
	// Check if there are steps and ingredient sets.
	$steps = false;
	$ingredientsets = false;
	foreach($post as $key => $value) {
      if(preg_match('/^step(\d+)/', $key, $match)) {
		$steps = true;
      } else if(preg_match('/^amount-(\S+)-(\d+)/', $key, $match)) {
        $ingredientsets = true;
      }
    }
	
    if(!$ingredientsets) {
      $this->flash(getMessage("missingIngredients"));
      $this->activateAction("edit", "ingredients");
    }	
    if(!$steps) {
      $this->flash(getMessage("missingSteps"));
      $this->activateAction("edit", "step");
    }	
	return;
  }
  
  /**
     * Get a random recipe from the db and diplay it.
     */
  function random() {
    $randomid = Recipe::getRandomId();
	$this->activateController("recipe", "view", $randomid);
  }
}
?>
