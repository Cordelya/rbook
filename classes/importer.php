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

/*
 * Clobbers the current database and imports the data into it.
 *
 * @author Andrew Violette
 * @package rbook
 * @since 0.9
 * @version $Id: importer.php,v 1.9 2007/03/16 01:25:00 aviolette Exp $
 */

require_once(dirname(__FILE__) . "/mine.php");
require_once(dirname(__FILE__) . "/invitation.php");

class Importer extends BaseRecord {
  var $categories;
  var $users;
  var $recipe;
  var $step;
  var $onstep;
  var $iset;
  var $counter;
  var $iCounter;
  var $description;
  var $ondescription;
  var $note;
  var $onnote;
  var $recipes;
  var $recipeId;
  var $oncomment;
  var $comment;
  var $commentsOn;
  var $onguestbook;
  var $guestbookentry;
  
  function Importer() {
    $this->recipes = array();
    $this->categories = array();
    $this->users = array();
    $this->counter = 0;
	$this->commentsOn = array();
  }
  
  function &listImportFiles() {
    $importFiles = array();
    if(!is_dir(IMPORTDIR)) {
      return $importFiles;
    }
    if(!($dh = opendir(IMPORTDIR))) {
      return $importFiles;
    }
    $dir = IMPORTDIR;
    while(($file = readdir($dh)) !== false) {
      if(filetype($dir . "/" . $file) == "file" && $file[0] != ".") {
        $importFiles[] = $file;
      }

    }
    closedir($dh);
    return $importFiles;

  }

  function import($file) {
    $error = $this->validate($file);
    if(isset($error)) {
      return $error;
    }
    $xml = xml_parser_create();
    xml_set_object($xml, $this);
    xml_set_element_handler($xml, 'start_element', 'end_element');
    xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, false);
    xml_set_character_data_handler($xml, 'character_data');
    $this->clobber();
    $fp = fopen($file, 'r') or die("Can't open file: $file");
    while($data = fread($fp, 4096)) {
      xml_parse($xml, $data, feof($fp)) or die("Can't parse XML");
    }
    fclose($fp);
    xml_parser_free($xml);

	$this->postImport();

    return $file;
  }

  function postImport() {
	// update the cached ratings
	if(!isset($this->commentson)) {
		return;
	}
	foreach($this->commentson as $rid => $oldRid) {
	  $comments = Comment::findByRecipe($rid);
	  calculateRating($comments, $rating, $ratingHits);
	  $recipe =& $this->recipes[$oldRid];
	  if(isset($recipe)) {
		$recipe->updateRating($rating, $ratingHits);
	  }
	}
  }

  function clobber() {
    Recipe::deleteMultiple();
    User::deleteMultiple();
    Category::deleteMultiple();
	Comment::deleteMultiple();
	Guestbook::deleteMultiple();
    $qualifers = null;
    // just to make sure
    $this->deleteMultipleOfClass($qualifiers, "ingredients");
    $this->deleteMultipleOfClass($qualifiers, "ingredientsets");
    $this->deleteMultipleOfClass($qualifiers, "steps");
    $this->deleteMultipleOfClass($qualifiers, "mine");
    $this->deleteMultipleOfClass($qualifiers, "images");
    $this->onstep = 0;
    $this->onnote = 0;
	$this->ondescription = 0;
	$this->oncomment = 0;
	$this->onguestbook = 0;
  }

  function start_element($parser, $tag, $attributes) {
    if($tag == "categories") {
      $this->handleCategories($parser, $tag, $attributes);
    } else if($tag == "category") {
      $this->handleCategory($parser, $tag, $attributes);
    } else if($tag == "users") {
      $this->handleUsers($parser, $tag, $attributes);
    } else if($tag == "user") {
      $this->handleUser($parser, $tag, $attributes);
    } else if($tag == "recipe") {
      $this->handleRecipe($parser, $tag, $attributes);
    } else if($tag == "step") {
      $this->handleStep($parser, $attributes);
    } else if($tag == "ingredientset") {
      $this->handleIngredientSet($parser, $attributes);
    } else if($tag == "ingredient") {
      $this->handleIngredient($parser, $attributes);
    } else if($tag == "note") {
      $this->handleNote($parser, $attributes);
	} else if($tag == "description") {
	  $this->handleDescription($parser, $attributes);
    } else if($tag == "mine") {
      $this->handleMine($parser, $attributes);
    } else if($tag == "invitation") {
      $this->handleInvitation($parser, $attributes);
    } else if($tag == "rc") {
      $this->handleRecipeCategory($parser, $attributes);
    } else if($tag == "image") {
      $this->handleImage($parser, $attributes);
    } else if($tag == "comment") {
	  $this->handleComment($parser, $attributes);
	} else if($tag == "guestbookentry") {
	  $this->handleGuestbook($parser, $attributes);
	}
  }

  function end_element($parser, $tag) {
    if($tag == "recipe") {
      $this->handleRecipeEnd($parser);
    } else if($tag == "step") {
      $this->handleStepEnd($parser);
    } else if($tag == "ingredientset") {
      $this->handleIngredientSetEnd($parser);
    } else if($tag == "note") {
      $this->handleNoteEnd($parser);
    } else if($tag == "description") {
	  $this->handleDescriptionEnd($parser);
	} else if($tag == "comment") {
	  $this->handleCommentEnd($parser);
	} else if($tag == "guestbookentry") {
	  $this->handleGuestbookEnd($parser);
	}
  }
  
  function handleInvitation($parser, $attributes) {
    $inviter = $this->users[$attributes['inviter']];
    $invitee = $this->users[$attributes['invitee']];
    $invitation = new Invitation($invitee->id, $inviter->id);
    $invitation->code = $attributes['code'];
    $invitation->acceptedDate = empty($attributes['acceptdate']) ? null : date("Y-m-d H:i:s");
    $invitation->save();
  }

  function handleImage($parser, $attributes) {
    $image = new Image();
    $recipe =& $this->recipes[$attributes['recipeid']];
    $image->recipeid = $recipe->id;
    $image->caption = $attributes['caption'];
    $image->recipeuid = $attributes['recipeuid'];
    $image->uid = $attributes['uid'];
    $image->width = $attributes['width'];
    $image->height = $attributes['height'];
    $user =& $this->users[$attributes['submittedby']];
    $image->submittedBy = $user->id;
    $image->type = $attributes['type'];
    $image->save();
  }

  function handleComment($parser, $attributes) {
	$this->oncomment = 1;
	$comment = new Comment();
	$recipe = $this->recipes[$attributes['recipeid']];
	$comment->recipeid = $recipe->id;
	$this->commentson[$comment->recipeid] = $attributes['recipeid'];
	$comment->rating = $attributes['rating'];
	$user =& $this->users[$attributes['userid']];
	$comment->userid = $user->id;
	$comment->postDate = $attributes['postdate'];
	$this->comment =& $comment;
  }

  function handleCommentEnd($parser) {
	$comment =& $this->comment;
	$comment->save();
	$this->oncomment = 0;
  }

  function handleGuestbook($parser, $attributes) {
	$this->onguestbook = 1;
	$guestbookentry = new Guestbook();
	$guestbookentry->name = $attributes['name'];
	$guestbookentry->postdate = $attributes['postdate'];
	$this->guestbookentry =& $guestbookentry;
	}

  function handleGuestbookEnd($parser) {
	$guestbookentry =& $this->guestbookentry;
	$guestbookentry->save();
	$this->oncomment = 0;
  }
  
  function handleMine($parser, $attributes) {
    $mine = new Mine();
    $recipe =& $this->recipes[$attributes['recipeid']];
    $user =& $this->users[$attributes['userid']];
    $mine->userid = $user->id;
    $mine->recipeid = $recipe->id;
    $mine->save();
  }

  function handleDescription($parser, $attributes) {
	$this->ondescription = 1;
  }

  function handleDescriptionEnd($parser) {
	$r =& $this->recipe;
	$r->description = $this->description;
	$this->ondescription =0;
  }

  function handleNote($parser, $attributes) {
    $this->onnote = 1;
  }

  function handleNoteEnd($parser) {
    $this->onnote = 0;
    $r =& $this->recipe;
    $r->note = $this->note;
  }

  function handleIngredient($parser, $attributes) {
    $iset =& $this->iset;
    $ingredient = new stdClass();
    $ingredient->amount = $attributes['amount'];
    $ingredient->description = $attributes['description'];
    $ingredient->order = $this->iCounter++;
    $iset->rows[] =& $ingredient;
  }

  function handleIngredientSetEnd($parser) {
    $r =& $this->recipe;
    $r->addIngredientSet($this->iset);
    $this->iset = null;
  }

  function handleIngredientSet($parser, $attributes) {
    $this->iset = new IngredientSet();
    $iset =& $this->iset;
    $iset->id = $iset->id . $this->counter;
    $iset->name = $attributes['name'];
    $this->iCounter = 0;
  }

  function handleRecipeCategory($parser, $attributes) {
    $cat = $this->categories[$attributes['id']];
    $r =& $this->recipe;
    $r->categories[] = $cat;
  }
  
  function handleStep($parser) {
    $this->onstep = 1;
  }

  function handleStepEnd($parser) {
    $this->onstep = 0;
    if(isset($this->step)) {
      $r =& $this->recipe;
      $r->addStep($this->step);
      unset($step);
    }
  }

  function handleStepsEnd($parser) {
  }

  function handleRecipe($parser, $tag, $attributes) {
    $r = new Recipe();
    $r->title = $attributes['name'];
    rb_log("Importing recipe: " . $r->title);
    $r->source = $attributes['source'];
    $r->uid = $attributes['uid'];
    $cat = $this->categories[$attributes['category']];
    if(!empty($cat)) {
      $r->categories[] = $cat;
    }
    $user = $this->users[$attributes['submittedby']];
    $r->submittedById = $user->id;
    $r->preheat = $attributes['preheat'];
	$r->createdate = substr($attributes['createdate'], 0, 4) . substr($attributes['createdate'], 5, 2) . 
					 substr($attributes['createdate'], 8, 2) . substr($attributes['createdate'], 11, 2).
					 substr($attributes['createdate'], 14, 2). substr($attributes['createdate'], 17, 2);
    $r->serves = $attributes['serves'];
	$r->cooktime = $attributes['cooktime'];
	$r->preptime = $attributes['preptime'];
    $this->recipe =& $r;
    $this->recipeId = $attributes['id'];
  }
  
  function handleRecipeEnd($parser) {
    $r =& $this->recipe;
    if(empty($r->submittedById)) {
      return;
    }
    if(empty($r->uid)) {
      $r->uid = $r->createUid();
    }
    $r->save();
    $this->recipes[$this->recipeId] = $r;
  }

  function handleUser($parser, $tag, $attributes) {
    $user = new User();
    $user->email = $attributes['email'];
    $user->name = $attributes['name'];
	//This is for compatibility reasons that also export files that were created before
	//the username field was in the DB can be imported.
	if ($attributes['username'] == null)
		$user->username = $attributes['name'];
	else
		$user->username = $attributes['username'];
    $user->password = $attributes['password'];
    $user->admin = $attributes['admin'];
    $user->readonly = $attributes['readonly'];
    $user->invited = $attributes['invited'];
    $user->save();
    $this->users[$attributes['id']] =& $user;
  }
  
  function handleUsers($parser, $tag, $attributes) {
  }
  
  function handleCategory($parser, $tag, $attributes) {
    $cat = new Category();
    $cat->name = $attributes['name'];
    $cat->save();
    $this->categories[$attributes['id']] =& $cat;
  }

  function handleCategories($parser, $tag, $attributes) {
  }
  
  function character_data($parser, $data) {
    if($this->onstep) {
      $this->step = $data;
    } else if($this->onguestbook) {
	  $guestbookentry =& $this->guestbookentry;
	  $guestbookentry->comment = $data;
	} else if($this->onnote) {
      $this->note = $data;
    } else if($this->ondescription) {
	  $this->description = $data;
	} else if($this->oncomment) {
	  $comment =& $this->comment;
	  $comment->comment = $data;
	}
  }

  function validate($file) {
    if(empty($file)) {
      return "No file specified.";
    }
    return null;
  }
}

?>