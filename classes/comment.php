<?php

/*
 * rbook Recipe Management System
 * Copyright (C) 2006 Andrew Violette andrew@andrewviolette.net
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
 * Contains a recipe and is responsible for persistence.
 *
 * @author Andrew Violette
 * @package rbook
 * @since 1.3
 * @version $Id: comment.php,v 1.4 2007/03/16 01:24:26 aviolette Exp $
 */


require_once(dirname(__FILE__) . '/base_record.php');

class CommentFactory extends BaseRecordFactory {
  function createInstance() {
    return new Comment();
  }

  function getTable() {
    return "comments";
  }
}

class Comment extends BaseRecord {
  var $recipeid;
  var $userid;
  var $createDate;
  var $modifiedDate;
  var $comment;
  var $rating;
  var $postDate;

  function __construct() {
    $this->BaseRecord("comments");
    $this->postDate = date("y-m-d H:i:s");
  }
        
  function init(&$row) {
    $this->recipeid = $row['recipeid'];
    $this->id = $row['id'];
    $this->userid = $row['userid'];
    $this->rating = $row['rating'];
    $this->modifiedDate = $row['modifiedDate'];
    $this->createDate = $row['createdate'];
    $this->comment = $row['comment'];
  }

  function dbCreateNew() {
    $db =& $this->getDb();
    $id = $db->nextId("comments");
    $this->runQuery($db, "insert into comments (id, recipeid, userid, rating, postdate, modifieddate, createdate, comment) values (?, ?, ?, ?,  ?, now(), now(), ?)", array($id, $this->recipeid, $this->userid, $this->rating, $this->postDate, $this->comment));
    $db->commit();
    $db->disconnect();
    $this->id = $id;
  }
  function remove($db = null) {
    $cascade = isset($db);
    if(!isset($db)) {
      $db =& BaseRecord::getDb();
    }
    $this->runQuery($db, "delete from comments where id = ?", array($this->id));
    if(!$cascade) {
      $db->commit();
      $db->disconnect();
    }
  }
  function &findByRecipe($recipeid) {
    return Comment::loadMultiple(array('recipeid' => $recipeid));
  }

  function &loadOne($qualifiers) {
    $comments = Comment::loadMultiple($qualifiers, 1);
    if(count($comments)) {
      return $comments[0];
    }
    return null;
  }

  function deleteMultiple($qualifiers = null) {
    return BaseRecord::deleteMultipleOfClass($qualifiers, "comments");
  }

  function &loadMultiple($qualifiers = null, $limit = null, $db = null) {
    return BaseRecord::loadMultipleBasic(new CommentFactory(), $qualifiers, $limit, $db);
  }


}
?>
