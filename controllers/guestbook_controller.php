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
 * Controller responsible for the guestbook
 * @author Michael Schaefers
 * @since 2.2
 * @package rbook
 * @subpackage controllers
 */

class GuestbookController extends BaseController {

  /**
   * Factory method that creates a guestbook controller.  
   * @static
   */
  function &newInstance() {
	$controller = new GuestbookController();
  	$valid = array("index", "create_comment", "delete_comment");
    $auth = array("delete_comment");
      
    $controller->set_valid_actions($valid);
	$controller->set_requires_authentication($auth);
    return $controller;
  }

  /**
   * Displays the guestbook mainpage.
   */
  function index() {
    unset($_SESSION['lastsearch']);

	$c = array();
	$comments = Guestbook::loadMultiple();
	
	foreach($comments as $comment) {
	  sscanf($comment->postdate, "%d-%d-%d %d:%d:%d", $y,$m,$day, $h,$min, $s);
	  $c[] = array("id" => $comment->id,
				   "comment" => $comment->comment,
				   "postdate" => "$y-$m-$day $h:$min",
				   "name" => $comment->name);
	}

	$_SESSION['no1'] = rand(1,10);
	$_SESSION['no2'] = rand(1,10);
	
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("selectedTab", "guestbook");
    $modelView->assign("title", getMessage("guestbook"));
	$modelView->assign("hasComments", count($comments));
	$modelView->assign("comments", $c);
	$modelView->assign("no1", $_SESSION['no1']);
	$modelView->assign("no2", $_SESSION['no2']);
    $modelView->display(getFullTemplateName("guestbook"));
  }
  
  /**
   * Insert a new comment.
   */
    function create_comment() {
    if(!$this->isPost()) {
      $this->flash(getMessage("ActionValidForPostOnly"));
      gotoReferrer();
    }

    if($this->isPost()) {
      
	  if($_POST['antispam'] != $_SESSION['no1'] + $_SESSION['no2']) {
		$this->flash(getMessage("antispamwrong"));
	  } else {
	    $comment = new Guestbook();
	  
	    if(!empty($_POST['name'])) {
          $comment->name = $_POST['name'];
        } else {
	  	  $this->flash(getMessage("plsEnterName"));
	    }
	  
        if(!empty($_POST['commentArea'])) {
          $comment->comment = $_POST['commentArea'];
        } else {
		  $this->flash(getMessage("plsEnterComment"));
	    }
  
        $comment->save();
        gotoReferrer();
	  }
    } 
    gotoReferrer();
  }

  /**
   * Delete an existing comment.
   */
  function delete_comment($commentId) {
    if(!isset($commentId)) {
      $this->flash(getMessage("invalidComment"));
      $this->activateDefault();
    }
    
    $comment = Guestbook::loadOne(array("id" => $commentId));
    // verify the user is either the owner of the comment or an admin user
    if(!isAdminUser()) {
      $this->flash(getMessage("noPermissionToDeleteComment"));
      $this->activateDefault();
    }
    $comment->remove();
    gotoReferrer();
  }

}
?>
