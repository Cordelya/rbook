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
require_once(dirname(__FILE__) . '/../classes/invitation.php');
require_once(dirname(__FILE__) . '/captcha.php');

/**
 * UI Controller for managing the users.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */


class UserController extends BaseController {
  /**
   * Factory method that creates a user controller.  
   */

  function &newInstance() {
    $controller = new UserController("user");
    $actions = array("validate", "respond", "invite", "process_invite", "retrieve_password", 
                     "forgot","show_login", "edit_profile", "view_profile", "save_profile", 
					 "show_profile_after_error", "edit", "save", 
                     "index", "login", "logout", "deleteSelected");
    if(ALLOW_REGISTRATION) {
      $actions[] = "registration_complete";
      $actions[] = "submit_registration";
      $actions[] = "submit_captcha";
      $actions[] = "captcha_image";
      $actions[] = "register";
      $actions[] = "captcha";
    }
    $controller->set_valid_actions($actions);
    $controller->set_requires_authentication(array("invite", "process_invite", "edit_profile"));
    $controller->set_requires_adminaccess(array("validate", "edit", "save", "index"));
    return $controller;
  }

  /**
   * Shows the login page
   */

  function show_login() {
    $errorMessage = null;
    $redirectTo = buildLink("recipe", "index");
    if(isset($_SESSION['redirectto'])) {
      $redirectTo = $_SESSION['redirectto'];
    }
    $modelView =& $this->prepareModelAndView();

    $modelView->assign("title", getMessage("Login"));
    $modelView->assign("pageClass", "loginPage");
    $modelView->assign("userName", $_POST['user']);
    $modelView->assign("action", buildLink("user", "login"));
    if(isset($redirectTo)) {
      $modelView->assign("redirectTo", $redirectTo);
    }
    if(isset($errorMessage)) {
      $modelView->assign("pageError", $errorMessage);
    }
    $modelView->assign("showNavBar", 0);
    $modelView->display(getFullTemplateName('login'));
  }

  function deleteSelected() {
    $selectedUsers = $this->determineSelected();
    if(in_array($user->id, $selectedUsers)) {
      $this->flash(getMessage("cantDeleteYourself"));
      $redirect = false;
    } else {
      $count = User::deleteMultiple(array('id' => $selectedUsers));
      if($count != count($selectedUsers)) {
        $this->flash("userCannotBeDeleted");
      }
    }   
    $this->activateAction("index");
  }


  /**
   * Lists all the users.
   */

  function index() {
    $user = $_SESSION['user'];
    
    $users = User::loadMultiple(null, null);
    
    $action = $_POST['action'];
    $redirect = true;

    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", getMessage("Users"));
    $modelView->assign("pageTitle", getMessage("Users"));
    $modelView->assign("users", $this->buildUserList($users));
    $modelView->assign("action", buildLink("user", "index"));
    $modelView->display(getFullTemplateName('userMgmt'));
  }

  function retrieve_password() {
    if(!$this->isPost()) {
      $this->flash("Invalid action");
      $this->gotoReferrer();
    }
    $qualifiers = array("email" => $_POST['email']);
    $targetUser = User::loadOne($qualifiers);
    if(isset($targetUser)) {
      $inviter =& getUser();
      $invitation = new Invitation($targetUser->id, $targetUser->id);
	  $invitation->delete();
	  $invitation->createDate = null;
      $invitation->save();
      if($invitation->sendForgotPassword($_POST['email'], $_POST['email'], " ")) {
        $this->flash(getMessage('activationLinkSent'));
      } else {
        $invitation->delete();
        $this->flash(getMessage("emailUnsuccessful"));
		$this->activateAction("forgot");
      }
    } else {
      $this->flash(getMessage("userNotInSystem"));
	  $this->activateAction("forgot");
    }
    $this->activateController("recipe", "index");
  }

  function forgot() {
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", APPTITLE);
    $modelView->assign("pageTitle", getMessage("recoverPassword"));
    $modelView->assign("showNavBar", 0);
    $modelView->display(getFullTemplateName('forgot'));

  }

  /**
   * Saves a user
   */

  function save() {
    if(!$this->isPost()) {
      $this->activateController("user", "edit");
    }
    if($_POST['reset'] == getMessage("resetPassword")) {
      $editingUser = User::loadOne(array('id' => $_POST['id']));
      $editingUser->updatePassword('password');
    } else if($_POST['cancel'] == getMessage("cancel")) {
      $this->activateController("user", "index");
    } else {
      $editingUser = new User();
      $editingUser->update($_POST);
      $editingUser->save();
    }
    $this->activateController("user", "index");
  }
  
  function determineSelected() {
    $values = array();
    foreach($_POST as $key => $value) {
      if(preg_match('/^selectUser(\d+)/', $key, $match)) {
        $values[] = $match[1];
      }
    }
    return $values;
  }

  /**
   * Saves a user profile.  
   */

  function save_profile() {
    if(!$this->isPost()) {
      $this->activateDefault();
    }
    $user =& $_SESSION['profileUser'];

	// we only modify the things that are relevant to the profile
    $user->name = $_POST['name'];
    $user->email = $_POST['email'];
	$user->favorite = $_POST['favorite'];
	$user->website = $_POST['website'];

	$errors = $user->validate();
	if(count($errors)) {
	  $this->setPageErrors($errors);
	  $this->flash("There is an error on this page");
	  $this->activateAction("show_profile_after_error");
	}
    $passwordChanged = 0;
    if(!empty($_POST['newpassword']) || 
       !empty($_POST['oldpassword']) ||
       !empty($_POST['confirm'])) {
      $errorMessage = validatePassword();
      if(isset($errorMessage)) {
        $this->flash($errorMessage);
        $this->activateAction("show_profile_after_error");
      }
      $passwordChanged = 1;
    }
	$tuser = $user;
	$user = getUser();
	$user->updateProfileInformation($tuser);
    $user->save();
	unset($_SESSION['profileUser']);
    if($passwordChanged) {
      $this->flash(getMessage("passwordChanged"));
    }
    $this->activateAction("edit_profile");
  }

  /**
   * Shows the profile page as the user submitted it.  This is only
   * different from edit_profile in that it keeps the user object as
   * the current user submitted it.
   */

  function show_profile_after_error($id) {
	$user = $_SESSION['profileUser'];
	//PHP4 doesn't have clone
	if(!isset($_SESSION['profileUser'])) {
	  $user = getUser();
	  $_SESSION['profileUser'] = $user->fakeClone();
	  $user = $_SESSION['profileUser'];
	}
	$this->priv_edit_profile($id, $user);
  }

  function priv_edit_profile($id, $user) {
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", getMessage('editUser'));
    $modelView->assign("action", buildLink("user", "save_profile"));
    $modelView->assign("editprofile", 1);
	$modelView->assign("profile_favorite", $user->favorite);
	$modelView->assign("profile_website", $user->website);
    $this->prepareUser($user, $modelView);
    $modelView->display(getFullTemplateName('editUser'));
  }

  function edit_profile($id) {
	$tuser = getUser();
	if(isset($tuser) && !$tuser->isNew()) {
		$tuser = User::loadOne(array("id" => $tuser->id));
	}
	//PHP4 doesn't have clone
	$_SESSION['profileUser'] = $tuser->fakeClone();
	$user = $_SESSION['profileUser'];
    $this->priv_edit_profile($id, $user);

  }

  function view_profile($username) {
    $modelView =& $this->prepareModelAndView();
	$user =& User::loadOne(array('username' => $username));
	if(isset($user)) {
	  $modelView->assign("profileName", $user->name);
	  $modelView->assign("profileEmail", $user->email);
	  $modelView->assign("profileRecipeCount", $user->countRecipes());
	  $modelView->assign("profileId", $user->id);
	  $y = substr($user->createDate, 0, 4);
	  $m = substr($user->createDate, 5, 2);

	  $modelView->assign("memberSince", $m . "/" . $y);
	  $recentRecipes = Recipe::searchByMostRecentAndAuthor(10, $user->id);
	  $resultSet = array();
	  $c = 0;
	  foreach($recentRecipes as $r) {
		$recipe = array("url" => buildLink("recipe", "view", $r->recipeId),
						"title" => $r->title);
		$resultSet[] = $recipe;
	  }
	  $modelView->assign("recipeResults", $resultSet);
	} else {
	  trigger_error("User not found: "  . $username, E_USER_ERROR);
	}
    $modelView->display(getFullTemplateName('viewProfile'));
  }

  /**
   * Shows  the edit  user  page.  If  an  ID is  specified, the  user
   * specified by the ID is loaded into this page.  Otherwise, it loads
   * a new user into the field.
   */

  function edit($id) {
    if(isset($id)) {
      $editingUser = User::loadOne(array('id' => $id));
    } else {
      $editingUser = new User();
    }

    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", getMessage('editUser'));
    $modelView->assign("newuser", $editingUser->isNew());
    $modelView->assign("action", buildLink("user", "save"));
    $this->prepareUser($editingUser, $modelView);
    $modelView->display(getFullTemplateName('editUser'));
  }
  
  /**
   * Validates that the user exists in the system.  Sends the string
   * "true" if it does, otherwise sends the string "false"
   */

  function validate($user) {
	header("Content-type: text/plain");
	$results = User::loadMultiple(array("username" => $user));
	if(count($results) > 0) {
	  echo("true");
	} else {
	  echo("false");
	}

  }

  /**
   * Action that is invoked when a login form is submitted.  If the
   * user login is successful, the user object is placed into the
   * session and their auth token is stored in a cookie.
   */

  function login() {
    if(!$this->isPost()) {
      $this->activateController("user", "show_login");
    }
    $redirectTo = buildLink("recipe", "index");
    if(isset($_SESSION['redirectto'])) {
      $redirectTo = $_SESSION['redirectto'];
    }
    $theUser =& User::loadOne(array('username' => $_POST['user']));
    if(!isset($theUser)) {
      $this->flash(getMessage("userOrPasswordIncorrect"));
      $this->activateAction("show_login");
    }
    if($theUser->disabled || $theUser->invited) {
      $this->flash(getMessage("accountDisabled"));
      $this->activateAction("show_login");
    }

    if($theUser->validateLogin($_POST['password'])) {
      $_SESSION['user'] =& $theUser;
      if($_POST['saveid'] == 'on') {
        $token = md5(date("Y-m-d h:i:s"));
        setcookie('saveid', $theUser->id, time() + 2592000, APPROOT);
        setcookie('auth',$token, time() + 2592000, APPROOT);
        $theUser->auth = $token;
        $theUser->save();
      } 
      header("Location: $redirectTo");
      exit();
    }
    $this->flash(getMessage("userOrPasswordIncorrect"));
    $this->activateController("user", "show_login");
  }

  function &buildUserList(&$uList) {
    $users = array();
    foreach($uList as $u) {
      $ua = array("name" => $u->name,
				  "username" => $u->username,
                  "email" => $u->email,
                  "admin" => $u->admin,
                  "id" => $u->id,
                  "disabled" => $u->disabled,
                  "invited" => $u->invited,
                  "readonly" => $u->readonly);
      $users[] = $ua;
    }
    return $users;
  }

  /**
   * Logs the user out of the session and clears their data out of the
   * session.
   */

  function logout() {
    $this->logoutInternal();
  }

  function passedCaptcha() {
    return !defined("IMAGEMAGICK") || isset($_SESSION['captcha_passed']);
  }

  function register() {
    if($this->passedCaptcha()) {
      $modelView =& $this->prepareModelAndView();
      $modelView->assign("title", getMessage("register"));
      $modelView->assign("showNavBar", 0);
      $modelView->assign("pageTitle", getMessage("register"));
      $modelView->display(getFullTemplateName('register'));
      
    } else {
      $this->activateAction("captcha");
    }
  }

  function registration_complete() {
    $user = $_SESSION['registered_user'];
    if(empty($user)) {
      $this->flash('userNotSet');
      $this->activateDefault();
    }
    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", getMessage("registrationComplete"));
    $modelView->assign("showNavBar", 0);
    $modelView->assign("registeredUser", $user);
    $modelView->assign("pageTitle", getMessage("registrationComplete"));
    if(defined("DEBUG") && DEBUG === true) {
      $modelView->assign("registrationCode", $_SESSION['registration_code']);
      unset($_SESSION['registration_code']);
    }
    $modelView->display(getFullTemplateName('registrationComplete'));
    
  }

  function submit_registration() {
    if(!$this->passedCaptcha()) {
      trigger_error("Attempt to submit registration without passing verification");
    }
    $this->processInviteInternal($regCode, DEBUG === false, false);
    $_SESSION['registration_code'] = $regCode;
    $_SESSION['registered_user'] = $_POST['email'];
    $this->activateAction("registration_complete");
  }

  function submit_captcha() {
    $captcha =& $_SESSION['captcha'];
    if($_POST['phrase'] == $captcha->getText()) {
      $_SESSION['captcha_passed'] = true;
      $this->activateAction('register');
    } else {
      $this->flash(getMessage("invalidCaptchaResponse"));
      $this->activateAction('captcha');
    }
  }

  function captcha() {
    $modelView =& $this->prepareModelAndView();
    $captcha = new Captcha();
    $_SESSION['captcha'] =& $captcha;

    $modelView->assign("pageClass", "loginPage");
    $modelView->assign("showNavBar", 0);
    $modelView->assign("title", getMessage("captcha"));
    $modelView->assign("pageTitle", getMessage("captcha"));
    $modelView->display(getFullTemplateName('captcha'));
  }

  function captcha_image() {
    $captcha =& $_SESSION['captcha'];
    if(!isset($captcha)) {
      trigger_error("captcha not set");
    }
    $captcha->renderImage();
  }

  function process_invite() {
    $this->processInviteInternal($regCode, DEBUG === true, true);
	$this->activeDefault();
  }

  function processInviteInternal(&$regCode, $cleanupOnError = true, $invite = false) {
    if(!$this->isPost()) {
      $this->flash(getMessage('invalidAction'));
	  $this->gotoReferrer();
    }
    $users = User::loadMultiple(array("email" => $_POST['email']));
    if(count($users) > 0) {
      $this->flash(getMessage('userinsystem'));
	  $this->gotoReferrer();
    }
    if(!isset($_POST['email']) || !isset($_POST['name'])) {
      trigger_error("Email and/or name not set");
	  $this->gotoReferrer();
    }

    $user = new User();
    $user->invited = 1;
    $user->email = $_POST['email'];
    $user->name = $_POST['name'];
	$user->username = $_POST['username'];
    $user->save();
    rb_log("User: " . $user->id);

    $inviter =& getUser();
    $activeUser =& getUser();
	$userId = $user->id;
	// When users register they are basically inviting themselves.
	if($invite) {
	  $userId = $activeUser->id;
	}
    $invitation = new Invitation($user->id, $userId);
    $invitation->save();
    $invitation->acceptedDate = date("Y-m-d H:i:s");
    $regCode = $invitation->code;
    rb_log("Invite: " . $invitation->code);
	$from = $user->email;
	if(!$invite) {
		$from = REGISTER_EMAIL;
	}
    $url = buildLink("user", "respond", $invitation->code);
    if($invitation->send($user->email, $from, $url, $_POST['message'])) {
      $this->flash(getMessage('invitationSuccessful'));
    } else {
      if($cleanupOnError) {
        $user->delete();
      }
      $this->flash(getMessage('emailUnsuccessful'));
    }
  }

  function respond($code) {
    if(empty($code)) {
      $this->flash(getMessage("invalidInvitation"));
      $this->activateController("recipe", "index");
    }

    $invitation = Invitation::load($code);

    if(!isset($invitation)) {
      $this->flash(getMessage('invalidInvitation'));
      $this->activateController("recipe", "index");
    } else if(isset($invitation->acceptedDate)) {
      $this->flash(getMessage('invitationUsed'));
      $this->activateController("recipe", "index");
    } else if($_POST['code']) {
      $targetUser =& User::loadOne(array("email" => $_POST['email']));
      if(isset($targetUser)) {
        if($invitation->invitee == $targetUser->id) {
          // setting the accept date prevents it from being reused.
          $invitation->acceptedDate = date("Y-m-d H:i:s");
          $invitation->save();
          // setting the invited flags allows the user to log on.
          $targetUser->invited = 0;
          $targetUser->save();
          $targetUser->updatePassword($_POST['password']);
          setUser($targetUser);
	      $this->activateController("recipe", "index");
        } else {
          $this->flash(getMessage('emailDoesNotMatch'));
        }
      } else {
        $this->flash(sprintf(getMessage('errorRetrievingUser'), $_POST['email']));
      }
    }

    $modelView =& $this->prepareModelAndView();
    $modelView->assign("invitation_code", $code);
    $modelView->assign("title", getMessage("VerifyInvitation"));
    $modelView->assign("pageTitle", getMessage("VerifyInvitation"));
    $modelView->assign("showNavBar", 0);
    $modelView->display(getFullTemplateName('receiveInvite'));
  }

  function invite() {
    if(isReadonlyUser() || MAXINVITATIONS == 0) {
      $this->flash(getMessage('invalidAction'));
      $this->activeDefault();
    }

    $modelView =& $this->prepareModelAndView();
    $modelView->assign("title", getMessage("sendInvitation"));
    $modelView->assign("pageTitle", getMessage("sendInvitation"));
    $modelView->display(getFullTemplateName('invite'));
  }

  function logoutInternal() {
    setCookie('saveid', '', 0, APPROOT);
    setCookie('auth', '', 0, APPROOT);
    
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
      setcookie(session_name(), '', time()-42000, '/');
    }
    unset($_COOKIE[session_name()]);
    session_destroy();
    $this->activateController("recipe", "index");
  }
}
?>
