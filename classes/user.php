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
 * A user in the system.
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: user.php,v 1.11 2007/03/23 16:29:24 maschine Exp $
 *  
 */

require_once(dirname(__FILE__) . '/base_record.php');

class User extends BaseRecord {
  var $email;
  var $username;
  var $name;
  var $admin;
  var $auth;
  var $readonly;
  var $password;
  var $disabled;
  var $invited;
  var $createDate;
  var $favorite;
  var $website;

  function __construct() {
    $this->BaseRecord();
    $this->password = md5('password');
    $this->disabled = 0;
    $this->admin = 0;
    $this->readonly = 0;
    $this->invited = 0;
	$this->requiresValues(array("email", "username", "name"));
  }

  /**
   * PHP4 doesn't have clone yet...this is our cheap version
   */
  function &fakeClone() {
	$user = new User();
	$user->email = $this->email;
	$user->username = $this->username;
	$user->name = $this->name;
	$user->admin = $this->admin;
	$user->auth = $this->auth;
	$user->readonly = $this->readonly;
	$user->password = $this->password;
	$user->disabled = $this->disabled;
	$user->invited = $this->invited;
	$user->createDate = $this->createDate;
	$user->favorite = $this->favorite;
	$user->website = $this->website;
	return $user;
  }



  function &validate() {
	$foo = parent::validate();
	if(!empty($this->website) && !preg_match('/^[http|https]/', $this->website)) {
	  $foo['user_website'] = "error.user_website.mustBeHttpOrHttps";
	}
    return $foo;
  }

  function init($row) {
    $this->email = $row['email'];
    $this->password = $row['password'];
    $this->name = $row['name'];
    $this->admin = $row['admin'];
    $this->id = $row['id'];
    $this->readonly = $row['readonly'];
    $this->auth = $row['auth'];
    $this->disabled = $row['disabled'];
    $this->invited = $row['invited'];
	$this->createDate = $row['createdate'];
	$this->favorite = $row['favorite'];
	$this->website = $row['website'];
	$this->username = $row['username'];
  }

  /**
   * Creates a new user in the database.
   */

  function dbCreateNew($db = null) {
    $db =& $this->getDb();
    $this->id = $db->nextId('users');
    $this->runQuery($db, "insert into users (id, email,name,password,admin,disabled, " .
                    "readonly, invited, username, createdate) values (?, ?, ?, ?, ?, ?, ?, ?, ?, now())",
                    array($this->id, $this->email, $this->name,$this->password, 
                          $this->admin, $this->disabled, $this->readonly, $this->invited, $this->username));
    $db->commit();
    $db->disconnect();
  }

  function dbUpdate($db = null) {
    $db =& $this->getDb();
    $this->runQuery($db, "update users set email = ?,name = ?, admin = ?, " .
                    "readonly = ?, auth=?, disabled = ?, invited = ?, favorite = ?, website = ? where id = ?",
                    array($this->email, $this->name, $this->admin,  
                          $this->readonly, $this->auth, $this->disabled,
                          $this->invited, $this->favorite, $this->website,
                          $this->id));
    $db->commit();
    $db->disconnect();
  }

  function updateProfileInformation(&$user) {
	$this->name = $user->name;
	$this->email = $user->email;
	$this->favorite = $user->favorite;
	$this->website = $user->website;
  }

  function update($postValues) {
	$this->id = $postValues['id'];
    $this->name = $postValues['name'];
    $this->email = $postValues['email'];
	$this->favorite = $postValues['favorite'];
	$this->website = $postValues['website'];
	if($this->isNew()) {
	  $this->username = $postValues['username'];
	}
    if($postValues['rightsLevel'] == "readonly") {
      $this->readonly = 1;
      $this->admin = 0;
    } else if($postValues['rightsLevel'] == "admin") {
      $this->readonly = 0;
      $this->admin = 1;
    } else {
      $this->readonly = 0;
      $this->admin = 0;
    }
    $this->disabled = $postValues['disabled'] == "on" ? 1 : 0;
  }

  function delete() {
    if(!isset($this)){
      return;
    }
    User::deleteMultiple(array('id' => array($this->id)));
  }

  function deleteMultiple($qualifiers = null) {
    return BaseRecord::deleteMultipleOfClass($qualifiers, "users");
  }

  /**
   * Returns an array of users
   */

  function &loadMultiple($qualifiers = null, $limit = null) {
    $db = BaseRecord::getDb();
    
    $query = "select * from users " . 
      BaseRecord::buildWhereClauseDb($qualifiers) . 
      " order by name";
    $paramList = BaseRecord::prepareQualifiers($qualifiers);
    if(isset($limit)) {
      $rs = $db->limitQuery($query, 0, $limit, $paramList);
    } else {
      $rs = BaseRecord::runQuery($db, $query, $paramList, __FILE__, __LINE__);
    }
    $users = array();
    while($line = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
      $user = new User();
      $user->init($line);
      $users[] = $user;
    }
    $db->disconnect();
    return $users;
  }


  function &loadOne($qualifiers) {
    $users = User::loadMultiple($qualifiers, 1);
    if(count($users)) {
      return $users[0];
    }
    return null;
  }

  function validateLogin($password) {
    return (md5($password) == $this->password || ($password == $this->password));
  }

  /**
   * Updates a password for a user.
   */
  function updatePassword($password) {
    if(!isset($this)) {
      die("Call to updatePassword without this pointer");
    }
    
    $db =& $this->getDb();
    $pw = $password;
    $password = md5($password);

    $this->runQuery($db, "update users set password = ? where id = ?",
                    array($password, $this->id));
    
    $db->commit();    
    $db->disconnect();
  }

  function invitationsUsed() {
    $db =& $this->getDb();

		/* Invitations are used both to invite others and to recover
		 * passwords.  Here we check that the invitee does not equal the
		 * user so that we can not include password recoveries in the
		 * results. 
		 */

    $result =& $this->runQuery($db, "select count(*) from invitations where inviter = ? and invitee <> ?", 
															 array($this->id, $this->id));
    if($result->fetchInto($row, DB_FETCHMODE_ORDERED)) {
      $amount = $row[0];
    } else {
      $amount = 0;
    }
    $db->disconnect();
    return $amount;
  }

  function removeFromMyRecipes($recipe) {
    $db =& $this->getDb();
  
    $this->runQuery($db, "delete from mine where recipeid = ? and userid = ?", 
                    array($recipe, $this->id));
    
    $db->commit();
    $db->disconnect();

  }

  /**
   * Returns a list records, each with (title, url, and submittedbname)
   * that are currently in the users 'mine' list.
   */
  function &searchForMine() {
    $db =& $this->getDb();

    $query = "SELECT recipes.id as id, recipes.createdate as date,recipes.name as name, " . 
			 "recipes.cached_rating as ra, recipes.cached_ratinghits as rahi, users.username " .
			 "as submittedbyusername, users.name as submittedbyname " .
			 "FROM recipes, mine, users " .
			 "WHERE mine.userid = ? and mine.recipeid = recipes.id AND submittedby = users.id ORDER BY recipes.name";
    $result =& $this->runQuery($db, $query, array($this->id));
    
    $mine = array();
    while($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $rec = new SearchResult($row['name'], buildViewUrl($row['id']),
                              $row['id'], $row['submittedbyname'], $row['submittedbyusername'], $row['date'], 0,
							  $this->id, "", $row['ra'], $row['rahi']);
      $mine[] = $rec;
    }
	Recipe::addImages($db, $mine);
    $db->disconnect();
    return $mine;
  }
  
  function countRecipes() {
    $db =& $this->getDb();

    $query = "select count(id) from recipes where submittedby = ?";
    $result =& $this->runQuery($db, $query, array($this->id));
    if($result->fetchInto($row, DB_FETCHMODE_ORDERED)) {
      $amount = $row[0];
    } else {
      $amount = 0;
    }
    
    $db->disconnect();
    return $amount;

  }
  
  function addToMyRecipes($recipe) {
    $db =& $this->getDb();
    $this->runQuery($db, "insert into mine (recipeid, userid) values(?, ?) " . 
                    "on duplicate key update userid = ?", 
                    array($recipe, $this->id, $this->id));
    $db->commit();
    $db->disconnect();
  }
}

?>
