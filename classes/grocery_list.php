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


require_once(dirname(__FILE__) . '/base_record.php');
class GroceryListFactory extends BaseRecordFactory {
  function createInstance() {
    return new GroceryList();
  }

  function getTable() {
    return "groceryitems";
  }
}

class GroceryList extends BaseRecord {
  var $description;
  var $orderid;
  var $userid;

  function GroceryList() {
    $this->BaseRecord();
    $this->orderid = 0;
  }

  function init($row) {
    $this->orderid = $row['orderid'];
	$this->description = $row['description'];
	$this->userid = $row['userid'];
	$this->id = $row['id'];
  }

  /**
   * Creates a new user in the database.
   */

  function dbCreateNew($db = null) {
    $db =& $this->getDb();
    $this->id = $db->nextId('users');
    $this->runQuery($db, "insert into groceryitems (id,userid,description,orderid) " .
                    " values (?, ?, ?, ?)",
                    array($this->id, $this->userid, $this->description, $this->orderid));
    $db->commit();
    $db->disconnect();
  }

  function dbUpdate($db = null) {
    $db =& $this->getDb();
    $this->runQuery($db, "update groceryitems set " .
                    "userid = ?, description=?, orderid= ? where id = ?",
                    array($this->userid, $this->description, $this->orderid, $this->id));
    $db->commit();
    $db->disconnect();
  }

  function delete() {
    if(!isset($this)){
      return;
    }
    GroceryList::deleteMultiple(array('id' => array($this->id)));
  }

  function deleteMultiple($qualifiers = null) {
    return BaseRecord::deleteMultipleOfClass($qualifiers, "groceryitems");
  }

  function findByUser($id) {
	return GroceryList::loadMultiple(array('userid' => $id));
  }

  /**
   * Returns an array of users
   */

  function &loadMultiple($qualifiers = null, $limit = null) {
    return BaseRecord::loadMultipleBasic(new GroceryListFactory(), $qualifiers, null, null);
  }


  function &loadOne($qualifiers) {
    $users = User::loadMultiple($qualifiers, 1);
    if(count($users)) {
      return $users[0];
    }
    return null;
  }
}

?>