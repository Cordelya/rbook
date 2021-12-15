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
  
  function __construct($tableName = null) {
    $this->id = -1;
    $this->tableName = $tableName;
  }

  /**
   * Returns the db connection
   */
   function &getDb() {
   	$dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;
  	$options = [
  		PDO::ATTR_ERRMODE 		=> PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES	=> true,
	];
  	try {
		$con = new PDO($dsn, DBUSER, DBPASSWORD, $options);
  	} catch (\PDOException $e) {
  		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	}
	return $con;
		  	  }
	

  /**
   * Runs the query and returns the results.
   * @return array the result set.
   * @static
   */

  function &runQuery(&$db, $sql, $qtype = 0, $params = null, $file = null, $line = null) {
	  try {
		  $opts = array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY);
	if(isset($params)) {
		if(is_array($params)) {
		rb_log("Running query: " . $query . ", params: " . implode(",", $params));
	} else {
		rb_log("Running query: " . $query . ", params: " . $params);
	}	
			if($qtype == 0) {
				$query = $db->prepare($sql, $opts);
				$query->execute($params);
				$res = $query->rowCount();
			} else if ($qtype == 1) {
				$query = $db->prepare($sql, $opts);
				$query->execute($params);
				$res = $query->fetchAll();
			}
	} else {
		rb_log("Running query: " . $query );
		if($qtype == 0) {
			$res = $db->exec($sql);
		} else if ($qtype == 1) {
			$query = $db->exec($sql);
			$res = $query->fetchAll();
		}
	}
    
	return $res;
	} catch (Exception $e) {
		rb_log("Error details: " . $e->getMessage() . "\n");
  }
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
    $sql = "delete from :tablename " . BaseRecord::buildWhereClauseDb($qualifiers);
    $params = BaseRecord::prepareQualifiers($qualifiers);
    $res =& BaseRecord::runQuery($db, $sql, 0, $params,
                                 __FILE__, __LINE__);
    
    $db = null;
    return $res;
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
