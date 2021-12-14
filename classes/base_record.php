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
 * BaseRecord and related interfaces.
 * @since 0.9
 * @author Andrew Violette
 * @package rbook
 * @subpackage model
 */

/** */
require_once(dirname(__FILE__) . '/../config.php');

/**
 * Interface for factory used to create subclasses of BaseRecord.
 *
 * @author Andrew Violette
 * @package rbook
 * @subpackage model
 * @version $Id: base_record.php,v 1.8 2006/11/10 01:58:42 aviolette Exp $
 */

class BaseRecordFactory {
  function createInstance() {
    die("Implement in subclass");
  }
  function getTable() {
    die("Implement in subclass");
  }
}

/**
 * Cheap O-R mapping class.  Subclasses of this method need to
 * implement dbCreateNew and dbUpdate in order to save the object to
 * the database.
 *
 * @author Andrew Violette
 * @since 0.9
 * @package rbook
 * @subpackage model
 * @version $Id: base_record.php,v 1.8 2006/11/10 01:58:42 aviolette Exp $
 */

class BaseRecord {

  /**
   * The ID of the database record.
   */

  var $id;

  var $requiresValues;

  /**
   * Constructs a base record.
   * @param tableName the table name associated with this record.
   */
  
  function BaseRecord($tableName = null) {
    $this->id = -1;
    $this->tableName = $tableName;
  }

  /**
   * Returns the URL to the database.
   */

  function getDbUrl() {
    return "mysql://" . DBUSER . ":" . DBPASSWORD . "@" . DBHOST . "/" . DBNAME;
  }

  /**
   * Returns a database connection.
   * @access protected
   * @return object a DB connection object
   */

  function &getDb() {
    $con =& DB::connect(BaseRecord::getDbUrl());
    if(PEAR::isError($con) || !isset($con)) {
      rb_log(__FILE__ . "," . __LINE__ . "," . $con->getMessage());
      trigger_error("Unable to connect to the database. The connection settings " .
          "to the database are probably improperly set up or the " . 
          "database is down.", E_USER_ERROR);
    }

    $con->autoCommit(false);
    return $con;
  }

  /**
   * Runs the query and returns the results.
   * @return array the result set.
   * @static
   */

  function &runQuery(&$db, $query, $params = null, $file = null, $line = null) {
    if(isset($params)) {
      if(is_array($params)) {
        rb_log("Running query: " . $query . ", params: " . implode(",", $params));
      } else {
        rb_log("Running query: " . $query . ", params: " . $params);
      }
      $res =& $db->query($query, $params);
    } else {
      rb_log("Running query: " . $query );
      $res =& $db->query($query);
    }
    if(PEAR::isError($res)) {
      $file = empty($file) ? "" : $file;
      $line = empty($line) ? "" : $line;
      rb_log("Error running query: " . $file . ", " . $line . ", " . $query . ", " . $res->getMessage());
	  rb_log("Error details: " . $res->getDebugInfo());
      return null;
    }
    return $res;
  }
  
  /**
   * Returns true if this object has never been persisted to the
   * database.
   */

  function isNew() {
    return $this->id == -1;
  }

  /**
   * Saves the object to the database.
   */

  function save($db = null) {
    if($this->id == -1) {
      $this->dbCreateNew($db);
    } else {
      $this->dbUpdate($db);
    }
  }

  function splitAndTrim($str) {
    if(empty($str)) {
      return "";
    }
    $foo = explode(",", $str);
    $b = array();
    foreach($foo as $bar) {
      $b[] = trim($bar);
    }
    return $b;
  }

  function unescapeLiteral($str) {
    // hack alert - for whatever reason, something is escaping the
    // single-quote when it is being submitted.  Here I unescape
    // it.
    $replaceList = array("\\'", "\\\"");
    $withList = array("'", '"');
    return str_replace($replaceList, $withList, $str);
  }

  /**
   * Deletes objects based on the qualifiers specified.
   */

  function deleteMultipleOfClass(&$qualifiers, $tableName) {
    $db =& BaseRecord::getDb();
    $res =& BaseRecord::runQuery($db, "delete from $tableName " .
                                 BaseRecord::buildWhereClauseDb($qualifiers),
                                 BaseRecord::prepareQualifiers($qualifiers),
                                 __FILE__, __LINE__);
    $numRows = $db->affectedRows();
    if($res != null) {
      $db->commit();
    } 
    $db->disconnect();
    return $numRows;
  }



  /**
   * Constructs a 'where' clause based on the qualifiers passed in.
   */

  function buildWhereClauseDb(&$qualifiers) {
    if(!(isset($qualifiers) && count($qualifiers))) {
      return "";
    }
    $whereClause = " WHERE";
    foreach($qualifiers as $key => $value) {
      $whereClause = $whereClause . " $key ";
      if(is_array($value)) {
        $whereClause = $whereClause . " in (";
        for($i=0; $i < count($value); $i++) {
          if($i+1 < count($value)) {
            $whereClause = $whereClause . "?,";
          } else {
            $whereClause = $whereClause . "?)";
          }
        }
      } else {
        $whereClause = $whereClause . " = ?";
      }
    }
    return $whereClause;
  }
  function &prepareQualifiers(&$qualifiers) {
    if(!isset($qualifiers)) {
	  $foo = array();
	  return $foo;
    }
    $values = array_values($qualifiers);
    $valueList = array();
    for($i=0; $i < count($values); $i++) {
      if(is_array($values[$i])) {
        $valueList = array_merge($valueList, $values[$i]);
      } else {
        $valueList[] = $values[$i];
      }
    }
    return $valueList;
      
  }
  function &loadMultipleBasic($factory, $qualifiers = null, $limit = null, $db = null, $orderByCSV = null) {
    $cascade = isset($db);
    if(!$cascade) {
      $db =& BaseRecord::getDb();
    }
    $query = "select * from " . $factory->getTable() .
      BaseRecord::buildWhereClauseDb($qualifiers);
    if(isset($orderByCSV)) {
      $query = $query . " order by " . $orderByCSV;
    }
    $paramList = BaseRecord::prepareQualifiers($qualifiers);
    if(isset($limit)) {
      $res =& $db->limitQuery($query, 0, $limit, $paramList);
    } else {
      $res =& BaseRecord::runQuery($db, $query, $paramList);
    }
    $results = array();
    while($res->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $img = $factory->createInstance();
      $img->init($row);
      $results[] = $img;
    }
    if(!$cascade) {
      $db->disconnect();
    }
    return $results;
  }

  function createUid() {
    return "" . time();
  }

  function requiresValues($values) {
	$this->requiresValues = $values;
  }
  
  function &validate() {
	$foo = array();
	if(isset($this->requiresValues)) {
	  $vars = get_object_vars($this);
	  foreach($this->requiresValues as $bar) {
		if(isset($vars[$bar])) {
		  if(empty($vars[$bar])) {
			$name = strtolower(get_class($this)) . "_" . $bar;
			$foo[$name] = "error." . $name . ".missing";
		  }
		}
	  }
	}
	return $foo;
  }
  
  

}


?>
