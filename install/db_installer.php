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
 * Installs the database schema and builds the main configuration file.
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: db_installer.php,v 1.18 2007/04/07 01:45:00 aviolette Exp $
 */


require_once("../const.php");

if(file_exists('../config.php')) {
  include('../config.php');
  define("DBACTION", "fresh");
} else {
  define("DBACTION", "fresh");
}

if(!defined("DBNAME"))  define("DBNAME", "rbook");
if(!defined("DBHOST"))  define("DBHOST", "localhost");
if(!defined("INITIALUSER")) define("INITIALUSER", "");
if(!defined("INITIALEMAIL"))  define("INITIALEMAIL", "");
if(!defined("RBOOK_HELP")) define("RBOOK_HELP", "http://rbook.sourceforge.net/");
if(!defined("VIEW_POLICY")) define("VIEW_POLICY", "member");
if(!defined("MAXINVITATIONS")) define("MAXINVITATIONS", 0);
if(!defined("IMAGEMAGICK")) define("IMAGEMAGICK", "");
if(!defined("SKIN")) define("SKIN", "default");
if(!defined("LANGUAGE")) define("LANGUAGE", "en");

class DBInstaller {
  var $databaseName;
  var $action;
  var $databaseHost;
  var $adminUser;
  var $password;
  var $create;
  var $initialUser;
  var $initialEmail;
  var $errors;
  var $exists;
  var $dbUserName;
  var $approot;
  var $config;
  var $viewPolicy;
  var $maxInvitations;
  var $exportDirectory;
  var $buildDatabase;
  var $language;

  function __construct() {
    $this->databaseName = DBNAME;
    $this->action = DBACTION;
    $this->adminUser = "root";
    $this->databaseHost = DBHOST;
    $this->errors = array();
    $this->skin = SKIN;
    $this->viewPolicy = VIEW_POLICY;
    $this->title = (defined("APPTITLE") ? APPTITLE : "recipes");
    $this->initialUser = INITIALUSER;
    $this->initialEmail = INITIALEMAIL;
    $this->maxInvitations = MAXINVITATIONS;
    $this->convertProgram = IMAGEMAGICK;
    $this->buildDatabase = true;
    $this->exportDirectory = (defined("IMPORTDIR") ? IMPORTDIR : "");
	$this->language = LANGUAGE;
	if(!defined("APPROOT")) {
	  if(preg_match("/^(.*)\/install\/.*/", $_SERVER['REQUEST_URI'], $matches)) {
		$this->approot = $matches[1] . "/";
	  } else {
		$this->approot = "/";
	  }
	} else {
		$this->approot = APPROOT;
	}
    if(defined("DBUSER")) {
      $this->dbUserName = DBUSER;
    }
  }

  function validate() {
    if(empty($this->databaseName)) {
      return "No database specified";
    }
    if(empty($this->adminUser)) {
      return "No admin user specified";
    }
    if(empty($this->password)) {
      return "No password specified";
    }
    if(empty($this->initialUser)) {
      return "No initial user specified";
    }
    if(empty($this->initialEmail)) {
      return "No initial email addressed specified";
    }
    if(empty($this->approot) || $this->approot[0] != '/' || 
       $this->approot[strlen($this->approot) - 1] != '/') {
      return "No approot specified.  The approot must start with a / and end with a /";
    }
	if(empty($this->language)) {
      return "No language specified";
    }
    return null;
  }

  function update($postValues) {
    $this->databaseName = $postValues['databaseName'];
    $this->adminUser = $postValues['adminUserName'];
    $this->password = $postValues['password'];
    $this->databaseHost = $postValues['databaseHost'];
    $this->initialUser = $postValues['initialUser'];
    $this->initialEmail = $postValues['initialEmail'];
    $this->dbUserName = $postValues['databaseUserName'];
    $this->approot = $postValues['approot'];
    $this->skin = $postValues['skin'];
    } else {
		$this->title = $postValues['title'];
	}	
    $this->viewPolicy = $postValues['viewPolicy'];
    $this->maxInvitations = $postValues['maxInvitations'];
    $this->exportDirectory = $postValues['exportDirectory'];
    $this->convertProgram = $postValues['imageMagick'];
    $this->buildDatabase = $postValues['action'] == "on";
	$this->language = $postValues['language'];
  }

/****************************************************************
 ***********************[DB]*************************************
 * TODO Relocate DB connection to a db.php functions file       *
 * so that the only functions remaining here are the calls      *
 * to the DB to create the tables (or drop in case of uninstall *
 ****************************************************************/
  function getDbUrl() {
    return "mysql://" . $this->adminUser . ":" . $this->password . "@" . $this->databaseHost . "/" . $this->databaseName;
  }

  function &getDb() {

    $con =& DB::connect($this->getDbUrl());
    if(PEAR::isError($con)) {
	  trigger_error("Couldn't connect to database");
	  return null;
    }
    return $con;
  }

  function uninstall() {
    $db =& $this->getDb();
    $res =& $db->query("drop database " . $this->databaseName);
    if(PEAR::isError($res)) {
      $this->logError($res->getMessage(), __FILE__, __LINE__);
    }
  }

  function install() {
    $this->errors = array();
    $this->createConfigFile();

    if(count($this->errors)) {
      return;
    }

    if($this->buildDatabase) {
      $this->createDatabase();
    }
    $db = $this->getDb();
	if(!isset($db)) {
	  $this->errors[] = "Failed to connect to database";
	  return;
	}


    $this->createDatabaseUser($db);
    $this->createUsersTable($db);
    $this->createCategoriesTable($db);
    $this->createRecipesTable($db);
    $this->createRecipeToCategory($db);
    $this->createIngredientSetsTable($db);
    $this->createIngredientsTable($db);
    $this->createStepsTable($db);
    $this->createMineTable($db);
    $this->createInvitationTable($db);
    $this->createImageTable($db);
    $this->createInitialUser($db);
    $this->createGroceryListTable($db);
    $this->createCommentTable($db);
	$this->createGuestbookTable($db);
    $this->populateCategories($db);
    $db->disconnect();
  }

  function createDatabaseUser(&$db) {
    if(empty($this->dbUserName)) {
      return;
    }
    $this->runQuery($db, "grant update,insert,delete,select on " .
                    $this->databaseName . ".* to '" .
                    $this->dbUserName . "'@'" . $this->databaseHost . "'");
    $this->runQuery($db, "set password for '" . $this->dbUserName .
            "'@'" . $this->databaseHost . "' = PASSWORD('" . $this->password . "')");
  }

  function populateCategories(&$db) {
    $categories = array(getMessage('Cat01'), getMessage('Cat02'), getMessage('Cat03'),
						getMessage('Cat04'), getMessage('Cat05'), getMessage('Cat06'),
						getMessage('Cat07'), getMessage('Cat08'), getMessage('Cat09'), 
                        getMessage('Cat10'));
    for($i =0; $i < count($categories); $i++) {
      $id = $db->nextId("categories");
      $this->runQuery($db,"insert into categories (id, name) values (?, ?)",
              array($id, $categories[$i]), __FILE__, __LINE__);
    }
  }

  function &runQuery(&$db, $query, $params = null, $file = null, $line = null) {
    if(isset($params)) {
      $res =& $db->query($query, $params);
    } else {
      $res =& $db->query($query);
    }
    if(PEAR::isError($res)) {
      $file = empty($file) ? "" : $file;
      $line = empty($line) ? "" : $line;
      $this->logError($res->getMessage(), $file, $line);
      die($file . "," . $line . "," . $query . "," . $res->getMessage());
    }
    return $res;

  }



  function createInitialUser(&$db) {

    $id = $db->nextId("users");
	$params = array($id, $this->initialEmail, 'root', md5('password'), $this->initialUser);
    $res =& $db->query("insert into users (id, email, username, password, name, readonly, admin) values (?, ?, ?, ?, ?, 0, 1)",
               $params);

    if(PEAR::isError($res)) {
      $this->logError($res->getDebugInfo(), __FILE__, __LINE__);
    }
  }

  function createMineTable(&$db) {
    $this->dropTable($db, "mine");
    $this->runQuery($db,
                    "create table if not exists mine (" .
                    "userid mediumint unsigned not null," .
                    "recipeid mediumint unsigned not null," .
                    "createdate timestamp not null," .
                    "unique(userid, recipeid)," .
                    "index mine_user (userid)," .
                    "index mine_recipe (recipeid)," .
                    "foreign key (recipeid) references recipes(id) on delete cascade," .
                    "foreign key (userid) references users(id) on delete cascade" .
                    ") ENGINE = INNODB");
  }

  function createInvitationTable(&$db) {
    $this->dropTable($db, "invitations");
    $this->runQuery($db, "create table if not exists invitations (" .
                    "invitee mediumint unsigned not null," .
                    "inviter mediumint unsigned not null," .
                    "code varchar(100) not null," .
                    "modifieddate timestamp not null," .
                    "acceptdate datetime," .
                    "createdate timestamp not null," .
                    "unique(invitee, inviter)," .
                    "index code_index (code)," .
                    "index invitee_user (invitee)," .
                    "index inviter_user (inviter)," .
                    "foreign key (invitee) references users(id) on delete cascade," .
                    "foreign key (inviter) references users(id) on delete cascade" .
                    ") ENGINE=INNODB");
  }

  function createImageTable(&$db) {
    $this->createStandardTable($db, "images",
                   "create table if not exists images (" .
                   "id mediumint unsigned not null," .
                   "uid CHAR(30) NOT NULL," .
                   "recipeid mediumint unsigned not null," .
                   "recipeuid char(30) not null," .
                   "caption varchar(200) not null," .
                   "width int," .
                   "height int," .
                   "type char(4) not null," .
                   "submittedby mediumint unsigned not null," .
                   "createdate timestamp not null," .
                   "index images_submitter (submittedby)," .
                   "index images_recipe (recipeid)," .
                   "foreign key (recipeid) references recipes(id) on delete cascade," .
                   "foreign key (submittedby) references users(id) on delete cascade," .
                   "primary key(id))", null, __FILE__, __LINE__);
  }

  function createStepsTable(&$db) {
    $this->createStandardTable($db, "steps", 
                               "create table if not exists steps (" .
                               "id mediumint unsigned not null," .
                               "recipeid mediumint unsigned not null," .
                               "orderid smallint unsigned not null," .
                               "step blob not null," .
                               "index step_recipe (recipeid)," .
                               "foreign key (recipeid) references recipes(id) on delete cascade," .
                               "primary key(id))", null, __FILE__, __LINE__);
  }

  function createIngredientsTable(&$db) {
    $this->createStandardTable($db, "ingredients",
                               "create table if not exists ingredients (" .
                               "id mediumint unsigned not null," .
                               "setid mediumint unsigned not null," .
                               "amount char(" . INGREDIENT_AMOUNT_LENGTH . ") not null," .
                               "description varchar(" . INGREDIENT_DESCRIPTION_LENGTH . ") not null," .
                               "orderid smallint unsigned not null,".
                               "unique(setid,orderid)," .
                               "index ingredient_set (setid)," .
                               "foreign key (setid) references ingredientsets(id) on delete cascade," .
                               "primary key(id))", null, __FILE__, __LINE__);
  }

  function createIngredientSetsTable(&$db) {
    $this->createStandardTable($db, "ingredientsets",
                               "create table if not exists ingredientsets (" .
                               "id mediumint unsigned not null," .
                               "recipeid mediumint unsigned not null," .
                               "orderid smallint unsigned not null," .
                               "name varchar(" . INGREDIENT_SET_NAME_LENGTH . ") not null," .
                               "index set_recipe (recipeid)," .
                               "foreign key (recipeid) references recipes(id) on delete cascade," .
                               "primary key(id))");
  }

  function createRecipesTable(&$db) {
    $this->createStandardTable($db, "recipes",
                               "create table if not exists recipes (" .
                               "id MEDIUMINT UNSIGNED NOT NULL," .
                               "name CHAR(" . RECIPE_NAME_FIELD_LENGTH . ") NOT NULL," .
                               "uniqueid CHAR(30) NOT NULL," .
                               "source char(" . RECIPE_SOURCE_FIELD_LENGTH . ")," .
                               "preheat varchar(" . RECIPE_PREHEAT_FIELD_LENGTH . ")," .
                               "submittedby mediumint unsigned not null," .
                               "note blob," .
                               "description blob," .
                               "serves smallint," .
                               "preptime smallint," .
                               "cooktime smallint," .
                               "cached_rating smallint," .
                               "cached_ratinghits smallint," .
                               "modifieddate timestamp," .
                               "createdate timestamp," .
							   "visits mediumint," .
                               "lastvisit timestamp," .
                               "index recipes_users (submittedby)," . 
                               "foreign key (submittedby) references users(id) on delete restrict," .
                               "primary key(id))");
  }

  function createCategoriesTable(&$db) {
    $this->createStandardTable($db, "categories",
                               "create table if not exists categories (" .
                               "id SMALLINT UNSIGNED NOT NULL," .
                               "name CHAR(" . CATEGORY_NAME_LENGTH . ") NOT NULL," .
                               "modifieddate timestamp," .
                               "createdate timestamp," .
                               "unique(name)," .
                               "PRIMARY KEY (id))");
  }

  function createCommentTable(&$db) {
    $this->createStandardTable($db, "comments",
                               "create table if not exists comments (" .
                               "id mediumint unsigned not null," .
                               "comment blob,".
                               "recipeid mediumint unsigned not null,".
                               "userid mediumint unsigned not null," .
                               "rating smallint unsigned,".
                               "postdate datetime," .
                               "modifieddate timestamp," .
                               "createdate timestamp," .
                               "unique(recipeid,userid)," .
                               "index comments_users (userid),".
                               "index comments_recipes (recipeid)," .
                               "foreign key (recipeid) references recipes(id) on delete cascade," .
                               "foreign key (userid) references users(id) on delete cascade," .
                               "primary key (id))");
  }

  function createRecipeToCategory(&$db) {
    $this->dropTable($db, "recipetocategory");
    $this->runQuery($db,
                    "create table if not exists recipetocategory (" .
                    "recipeid mediumint unsigned not null," .
                    "categoryid smallint unsigned not null," .
                    "unique(recipeid, categoryid)," .
                    "index rc_recipes (recipeid)," .
                    "index rc_categories (categoryid)," .
                    "foreign key (recipeid) references recipes(id) on delete cascade," .
                    "foreign key (categoryid) references categories(id) on delete cascade" .
                    ") ENGINE = INNODB");
  }

  function createUsersTable(&$db) {
    $this->createStandardTable($db, "users",
                               "create table if not exists users (" .
                               "id mediumint unsigned not null," .
                               "email varchar(100) not null," .
                               "name varchar(100) not null," .
							   "username varchar(50) not null," .
                               "password char(33) not null," .
                               "auth char(32)," .
                               "disabled tinyint," .
                               "invited tinyint," .
							   "favorite varchar(100)," .
							   "website varchar(256)," .
                               "readonly smallint unsigned not null," .
                               "admin smallint unsigned not null," .
                               "modifieddate timestamp," .
                               "createdate timestamp not null," .
                               "unique (email)," .
							   "unique (username)," .
                               "primary key(id))");
  }

  function createGroceryListTable(&$db) {
    $this->createStandardTable($db, "groceryitems",
                    "create table if not exists groceryitems (" .
                    "id mediumint unsigned not null," .
                    "userid mediumint unsigned not null, " .
                    "description varchar(100) not null, " .
                    "orderid mediumint not null," . 
                    "foreign key (userid) references users(id) on delete cascade," .
                    "primary key(id))");
  }
  
    function createGuestbookTable(&$db) {
    $this->createStandardTable($db, "guestbook",
                    "create table if not exists guestbook (" .
                    "id mediumint unsigned not null," .
                    "name varchar(100) not null, " .
                    "comment blob not null, " .
                    "postdate datetime not null," .
                    "primary key(id))");
  }

  function createStandardTable(&$db, $table, $sql) {
    $this->dropTable($db, $table);
    $this->runQuery($db, $sql . " ENGINE = INNODB", null, __FILE__, __LINE__);
    $this->modifyId($db, $table);
    $this->createSequence($db, $table);
  }

  function modifyId(&$db, $table) {
  }

  function dropTable(&$db, $table) {
  }

  function createSequence(&$db, $table) {
    $sequence = $db->getSequenceName($table);
    $db->query("drop table if exists $sequence");
    @$db->createSequence($table);
  }

  function logError($log, $file, $line) {
    error_log($file . " " . $line . ": " . $log . "\n", 3, dirname(__FILE__) . "/error.log");
  }

  function createConfigFile() {
    $path = realpath("..");
    $file = $path . "/config.php";
    error_reporting(0);
    $fp = fopen($file, "w");
    $lt = '<';
    $eol = "\n";
    $gt = '>';
    if(!$fp) {
      $lt = '&lt;';
      $eol = "<br/>";
      $gt = '&gt;';
    }
    $buf = $lt . '?' . 'php' . $eol;
    $buf = $buf . "/* the host that the database is running on */$eol";
    $buf = $buf . "define(\"DBHOST\", \"" . $this->databaseHost . "\");$eol$eol";
    $user = $this->dbUserName;
    if(empty($user)) {
      $user = $this->adminUser;
    }
    $buf = $buf . "/* the database user that the application uses to access the database */$eol";
    $buf = $buf . "define(\"DBUSER\", \"" . $user . "\");$eol$eol";
    $password = $this->password;
    if(empty($password)) {
      $password = $this->dbUserName;
    }
    $buf = $buf . "/* the password used to access the database */$eol";
    $buf = $buf . "define(\"DBPASSWORD\", \"" . $password . "\");$eol$eol";
    $buf = $buf . "/* the database name */$eol";
    $buf = $buf . "define(\"DBNAME\",\"" . $this->databaseName . "\");$eol$eol";
    $buf = $buf . "/* The URI of the application.  This should always end in a '/'.";
    $buf = $buf . "For instance,$eol   if you are running this at the top-level, it would be /.  ";
    $buf = $buf . "If the URL was$eol   www.foo-bar.com/rbook/, the APPROOT would be /rbook/ */$eol";
    $buf = $buf . "define(\"APPROOT\",\"" . $this->approot . "\");$eol$eol";
    $buf = $buf . "/* the stylesheet to use (this should reside in ./style */$eol";
    $buf = $buf . "define(\"STYLESHEET\",\"style.css\");$eol$eol";
    $buf = $buf . "/* whether to display recipe if only one result is returned */$eol";
    $buf = $buf . "define(\"DISPLAYIFONLYONE\", true);$eol$eol";
    $buf = $buf . "/* The skin that is being used.  This should be the name of a directory under the skins directory */$eol";
    $buf = $buf . "define(\"SKIN\",\"" . $this->skin . "\");$eol$eol";
    $buf = $buf . "/* the title to be displayed in the title bar and in the header */$eol";
    $buf = $buf . "define(\"APPTITLE\",\"" . $this->title . "\");$eol$eol";
    $buf = $buf . "/* the intial user (not used by app, just for re-install) */$eol";
    $buf = $buf . "define(\"INITIALUSER\",\"" . $this->initialUser . "\");$eol$eol";
    $buf = $buf . "/* the intial email (not used by app, just for re-install) */$eol";
    $buf = $buf . "define(\"INITIALEMAIL\",\"" . $this->initialEmail . "\");$eol$eol";
    $buf = $buf . "/* Maximum number of invitations a user can send (0 to disable) */$eol";
    $buf = $buf . "define(\"MAXINVITATIONS\", $this->maxInvitations);$eol$eol";
    $buf = $buf . "/* Enable debugging */$eol";
    $buf = $buf . "define(\"DEBUG\", false);$eol$eol";
    $buf = $buf . "/* Log file path (e.g. /foo/bar/rbook.log) */$eol";
    $buf = $buf . "//define(\"LOG_FILE_PATH\", \"\");$eol$eol";
	$buf = $buf . "/* To turn on google analytics, uncomment this line and put the value of your google analytics acount */$eol";
	$buf = $buf . "//define(\"GOOGLE_ANALYTICS\", \"\");$eol$eol";
    $buf = $buf . "/* turn on/off recipe-suggest */$eol";
    $buf = $buf . "define(\"RECIPESUGGEST\",false);$eol$eol";
    $buf = $buf . "/* define import drop-zone.   If specified, this will be where$eol";
    $buf = $buf . " * export files will be placed for import into the current system.$eol";
    $buf = $buf . " * If this is not specified, then the Import menu option is not displayed.$eol";
    $buf = $buf . " */$eol";
    if(!empty($this->exportDirectory)) {
      $buf = $buf . "define(\"IMPORTDIR\", \"" . $this->exportDirectory . "\");$eol$eol";
    } else {
      $buf = $buf . "//define(\"IMPORTDIR\", \"\");$eol$eol";
    }
    $buf = $buf . "/* Path to convert executable program used to scale images to size */$eol";
    if(!empty($this->convertProgram)) {
      $buf = $buf . "define(\"IMAGEMAGICK\", \"" . $this->convertProgram . "\");$eol$eol";
    }
    $buf = $buf . "/* this can either be member or all.  If it is member only members can view the recipes.*/$eol";
    $buf = $buf . "define(\"VIEW_POLICY\",\"" . $this->viewPolicy . "\");$eol$eol";
	$buf = $buf . "/* The language you want rbook to be displayed in.*/$eol";
    $buf = $buf . "define(\"LANGUAGE\",\"" . $this->language . "\");$eol$eol";
    $buf = $buf . "?$gt";

    error_reporting(E_ALL);
    if($fp) {
      fwrite($fp, $buf);
      fclose($fp);
    } else {
      $this->config = $buf;
    }
  }
  
}
?>
