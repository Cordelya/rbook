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
 * Validates the new password meets certain conditions.  If it fails
 * validation, then it returns the error message.  Otherwise it
 * returns null.
 * password - the new password
 * confirm - the new password, confirmed
 * oldPassword - the old password that the user typed
 */

function validateNewPassword($password, $confirm, $oldPassword) {
  if($password != $confirm) {
    return "The two new passwords are not the same";
  }
  if(!isset($password) || strlen($password) < 6) {
    return "The new password must be at least 6 characters long.";
  }
  
  if($password == $oldPassword) {
    return "The new and the old password are the same.";
  }
  return null;

}

function validatePassword() {
  if(!isset($_POST['oldpassword'])) {
    return null;
  }

  $theUser = User::loadOne(array('id' => intval($_SESSION['user']->id)));
  if(!isset($theUser)) {
    return "The supplied password is incorrect";
  } 
  $login = $theUser->validateLogin($_POST['oldpassword']);
  
  if($login === false) {
    return "The supplied password is incorrect";
  }
  
  $errorMessage = validateNewPassword($_POST['newpassword'], $_POST['confirm'], $_POST['oldpassword']);
  if($errorMessage != null) {
    return $errorMessage;
  }


  $errorMessage = $theUser->updatePassword($_POST['newpassword']);
  return $errorMessage;
}


/**
 * Returns true if the current site user is logged in.
 */

function isLoggedIn() {
  return isset($_SESSION['user']);
}

/**
 * Returns true if the site user is logged in and has admin rights.
 */

function isAdminUser() {
  $user = $_SESSION['user'];
  return isLoggedIn() && $_SESSION['user']->admin;

}

/**
 * Returns true if the user is logged in and is a read-only user.
 */

function isReadonlyUser() {
  $user = $_SESSION['user'];
  return isLoggedIn() && $_SESSION['user']->readonly;
}

/**
 * Performs a client-side redirect to the specified page relative to
 * the applicaiton root.
 */

function gotoPage($page) {
  header("Location: " . APPROOT . $page);
  exit();
}

/**
 * Performs a client-side redirect to the index page.
 */

function gotoHomePage() {
  gotoPage(buildLink("recipe", "index"));
}

/**
 * Goes to the referrer of the current page.  If the referrer is the login
 * page then it goes to the home page.
 */

function gotoReferrer() {
  if(strpos($_SERVER['HTTP_REFERER'], "login.php") !== false) {
    gotoHomePage();
  } else if(isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
  }
}

/**
 * Forces the user to login if they haven't logged and then sends them
 * back to this page when they are successful.
 */

function loginAndRedirectToCurrentPage() {
  if(!isLoggedIn()) {
    header("Location: login.php?redirectto=" . $_SERVER['REQUEST_URI']);
    exit();
  }
}

/**
 * Returns true if the specified recipe can be edited by the current user.
 */

function editableByCurrentUser($recipe) {
  return ($recipe->submittedById == $_SESSION['user']->id) || $_SESSION['user']->admin;
}

function calculateRating(&$comments, &$rating, &$ratingHits) {
	$rating = 0;
	$ratingHits = 0;
	foreach($comments as $comment) {
	  if(!empty($comment->rating)) {
		$rating += $comment->rating;
		$ratingHits++;
	  }
	}  
	if ((float)$ratingHits > 0) {
	  $rating = (float)((float)$rating / (float)$ratingHits);
	} else {
	  $rating = 0;
	}
	$rating = round($rating, 1);
}

function setUser(&$user) {
  $_SESSION['user'] =& $user;
}

/**
 * Returns the current user logged in.
 */

function &getUser() {
  return $_SESSION['user'];
}

function &getActiveRecipe() {
  return $_SESSION['recipe'];
}

function setActiveRecipe(&$recipe) {
  $_SESSION['recipe'] &= $recipe;
}


function setPageError($error) {
  if(empty($error)) {
    unset($_SESSION['pageError']);
    return;
  }
  $_SESSION['pageError'] = $error;
}

function getPageError() {
  if(array_key_exists('pageError', $_SESSION)) {
	return $_SESSION['pageError'];
  }
  return null;
}

function getBaseUrl() {
  $foo = "http";
  if(!empty($_SERVER['HTTPS'])) {
    $foo = "https";
  }
  $foo = $foo . "://" . $_SERVER['SERVER_NAME'];
  $port = $_SERVER['SERVER_PORT'];
  if($port != 80 && $port != 443) {
    $foo = $foo . ":" . $port;
  }
  return $foo . APPROOT;
}

function upUrlLevel($url, $level) {
  for($i=0; $i < $level; $i++) {
    $pos = strrpos($url, '/');
    if($pos === false) {
      return null;
    }
    $url = substr($url, 0, $pos);
  }
  if(empty($url)) {
    return "/";
  }
  return $url;

}

?>