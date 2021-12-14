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
 * @version $Id: image.php,v 1.1 2006/11/10 01:58:42 aviolette Exp $
 */


require_once(dirname(__FILE__) . '/base_record.php');

class ImageFactory extends BaseRecordFactory {
  function createInstance() {
    return new Image();
  }

  function getTable() {
    return "images";
  }
}

class Image extends BaseRecord {
  var $recipeid;
  var $caption;
  var $width;
  var $height;
  var $submittedBy;
  var $createDate;
  var $type;
  var $recipeuid;
  var $uid;
  var $id;

  function Image() {
    $this->BaseRecord("images");
    $this->uid = $this->createUid();
  }
        
  function init(&$row) {
    $this->recipeid = $row['recipeid'];
    $this->recipeuid = $row['recipeuid'];
    $this->caption = $row['caption'];
    $this->id = $row['id'];
    $this->width = $row['width'];
    $this->height = $row['height'];
    $this->submittedBy = $row['submittedBy'];
    $this->createDate = $row['createDate'];
    $this->submittedByName = $row['name'];
    $this->type = $row['type'];
    $this->uid = $row['uid'];
  }

  function determineType($mimeType) {
    if($mimeType == "image/jpeg" || $mimeType == "image/jpg" || $mimeType == "image/pjpeg") {
      $this->type = "jpg";
    } else if($mimeType == "image/gif") {
      $this->type = "gif";
    } else if($mimeType == "image/png" || $mimeType == "image/x-png") {
      $this->type = "png";
    }
  }

  function &findForRecipe($recipeid, $limit = null, $db = null) {
    if(!isset($db)) {
      $db =& BaseRecord::getDb();
    }
    $query = "select uid,images.id,recipeuid,recipeid,caption,width,height,submittedBy,images.createDate,type,users.name from images,users where recipeid = ? and users.id = images.submittedBy";
    if(isset($limit)) {
      $result =& $db->limitQuery($query, 0, 5, array($recipeid));
    } else {
      $result =& BaseRecord::runQuery($db, $query , array($recipeid));
    }
    $rows = array();
    while($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $foo = new Image();
      $foo->init($row);
      $rows[] = $foo;
    }
    $db->disconnect();
    return $rows;
  }

  function isValid() {
    return isset($this->type);
  }

  function getRelativeDirectory() {
    return 'img/' . $this->recipeuid;
  }

  function getWebPath() {
    return APPROOT . $this->getRelativePath();
  }

  function getRelativePath() {
    return $this->getRelativeDirectory() . '/' . $this->uid . '.' . $this->type;
  }

  function getFullPath() {
    return ROOT_DIRECTORY . "/" . $this->getRelativePath();
  }

  function getThumbPath() {
    $basePath = ROOT_DIRECTORY;
    if($basePath == "/") {
      $basePath = "";
    }
    return $basePath . "/" . $this->getRelativeDirectory() . '/' . $this->getThumbName();
  }

  function getThumbName() {
    return $this->uid . '-thumb.' . $this->type;
  }

  function getThumbWebPath() {
    return APPROOT . $this->getRelativeDirectory() . '/' . $this->getThumbName();
  }

  function buildThumb() {
    $this->resize("100x100", $this->getFullPath(), $this->getThumbPath());
  }
  
  function resize($dims, $src, $target) {
    if(defined("IMAGEMAGICK")) {

      $convert = IMAGEMAGICK;
      $cl = $convert . " " . $src . " -resize " . $dims . " " . $target;
      rb_log("Convert: "  . $cl);
      system($cl, $rc);
    }

  }

  function resizeToMax() {
    $this->resize("300x300", $this->getFullPath(), $this->getFullPath());
  }

  function getDimensions() {
    $path = $this->getFullPath();
    $size = GetImageSize($path);
    $this->width = $size[0];
	$this->height = $size[1];
  }

  function dbCreateNew() {
    $db =& $this->getDb();
    $id = $db->nextId("images");
    $this->runQuery($db, "insert into images (id, uid, recipeuid,recipeid, caption, width, height, submittedBy, type) values (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($id, $this->uid, $this->recipeuid, $this->recipeid, $this->caption, $this->width, $this->height, $this->submittedBy, $this->type));
    $db->commit();
    $db->disconnect();
    $this->id = $id;
  }
  
  function remove($db = null) {
    unlink($this->getFullPath());
    unlink($this->getThumbPath());
    $cascade = isset($db);
    if(!isset($db)) {
      $db =& BaseRecord::getDb();
    }
    $this->runQuery($db, "delete from images where id = ?", array($this->id));
    if(!$cascade) {
	  $db->commit();
      $db->disconnect();
    }
	}
  
  function &load($id, $db = null) {
    $cascade = isset($db);
	if (!isset($db)) {
	    $db =& BaseRecord::getDb();
	}
    $result =& BaseRecord::runQuery($db, "select images.id id, recipeid, recipeuid, caption, width, height, submittedBy, images.createDate createDate, name, type, uid from images,users where images.id = ? and users.id = images.submittedby", array($id));
    $img = null;
    if($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $img = new Image();
      $img->init($row);
    }
    if(!$cascade) {
      $db->disconnect();
    }
    return $img;
  }

  function &loadMultiple($qualifiers = null, $limit = null, $db = null) {
    return BaseRecord::loadMultipleBasic(new ImageFactory(), $qualifiers, $limit, $db);
  }


}
?>
