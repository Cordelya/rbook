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
 * Exports the data to an export file that the importer can read from.
 *
 * @author Andrew Violette
 * @package rbook
 * @subpackage model
 * @since 0.9
 * @version $Id: exporter.php,v 1.8 2007/03/16 01:25:00 aviolette Exp $
 */

class Exporter extends BaseRecord {
  var $con;
  var $fh;
  var $db;
  var $numRecipes;
  var $numCategories;
  var $numUsers;

  function setUp() {
    $this->db =& $this->getDb();
    $this->numRecipes=0;
    $this->numUsers=0;
    $this->numCategories=0;
  }

  function escape($foo) {
    $foo = str_replace("&", "&amp;", $foo);
    $foo = str_replace("<", "&gt;", $foo);
    $foo = str_replace(">", "&lt;", $foo);
	$foo = str_replace("\"", "&quot;", $foo);
    return $foo;

  }

  function write($str) {
    if(isset($this->fh)) {
      fwrite($this->fh, $str);
    } else {
      print($str);
    }
  }

  function writeExport() {
    $this->write("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n");
    $this->write("<rbook version='" . RBOOK_VERSION . "'>\n");
    $this->exportCategories();
    $this->exportUsers();
    $recipeids = $this->retrieveRecipeIds();
    $this->exportRecipes($recipeids);
    $this->exportInvitations();
    $this->exportMine();
    $this->exportImages();
	$this->exportComments();
	$this->exportGuestbook();
    $this->write("</rbook>\n");
  }

  function disconnect() {
    $db =& $this->db;
    $db->disconnect();
  }
  
  function exportFile($file) {
    $this->setUp();
    $this->fh = fopen($file, "w");
    $this->writeExport();
    fclose($this->fh);
    $this->disconnect();
  }
  
  function exportMine() {
    $this->write("<myrecipes>\n");
    $results =& $this->runQuery($this->db, "select userid, recipeid from mine");
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $this->write("  <mine userid=\"" . $row['userid'] . "\" recipeid=\"" . $row['recipeid'] . "\"/>\n");
    }
    $this->write("</myrecipes>\n");
  }
  
  function exportInvitations() {
    $this->write("<invitations>\n");
    $results =& $this->runQuery($this->db, "select invitee, inviter, code, acceptdate from invitations");
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $this->write("  <invitation invitee=\"" . $row['invitee'] . "\" inviter=\""
                   . $row['inviter'] . "\" code=\"" . $row['code'] . 
                   "\" acceptdate=\"" . $row['acceptdate'] . "\"/>");
    }
    $this->write("</invitations>\n");
  }
  
  function exportUsers() {
    $this->write("<users>\n");

    $results =& $this->runQuery($this->db, "select id,invited,email,name,username,password,admin,readonly from users");
    
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $this->numUsers++;
      $this->write("  <user id='" . $row['id'] . "' email='" . $row['email'] .
				   "' username='" . $this->escape($row['username']) .
                   "' name='" . $this->escape($row['name']) . "' password='" . $row['password'] .
                   "' invited='" . $row['invited']  .
                   "' admin='" . $row['admin'] . "' readonly='" . $row['readonly'] ."'/>\n");
    }

    $this->write("</users>\n");
  }

  function exportComments() {
	$this->write("<comments>\n");
	$results =& $this->runQuery($this->db, "select * from comments");
	while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
	  $this->write("  <comment id='" . $row['id'] . "' recipeid='" . 
				   $row['recipeid'] . "' userid='" . $row['userid'] . "' rating='" . $row['rating'] . "' postdate='" . $row['createdate'] . "'>");
	  if(isset($row['comment'])) {
		$this->write("<![CDATA[" . $row['comment'] . "]]>");
	  }
	  $this->write("</comment>\n");
	}
	$this->write("</comments>\n");
  }
  
  function exportGuestbook() {
	$this->write("<guestbook>\n");
	$results =& $this->runQuery($this->db, "select * from guestbook");
	while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
	  $this->write("  <guestbookentry id='" . $row['id'] . "' name='" . 
				   $row['name'] . "' postdate='" . $row['postdate'] . "'>");
	  if(isset($row['comment'])) {
		$this->write("<![CDATA[" . $row['comment'] . "]]>");
	  }
	  $this->write("</guestbookentry>\n");
	}
	$this->write("</guestbook>\n");
  }

  function exportImages() {
  $this->write("<images>\n");
    $results =& $this->runQuery($this->db, "select * from images");
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
        $this->write("  <image id='" . $row['id'] . "' recipeid='" .
                    $row['recipeid'] . "' caption='" . $row['caption'] .
                    "' recipeuid='" . $row['recipeuid'] . "' uid='" . $row["uid"] .
                    "' width='" . $row['width'] . "' height='" . $row['height'] .
                    "' type='" . $row['type'] . "' submittedby='" . $row['submittedby'] .
                    "'/>\n");
    }
    $this->write("</images>\n");
  }

  function exportRecipes($recipeids) {
    $this->write("<recipes>\n");
    foreach($recipeids as $id) {
      $this->exportRecipe($id);
    }
    $this->write("</recipes>\n");
  }
  function exportCategories() {

    $results =& $this->runQuery($this->db, "select id,name from categories");
    $this->write("<categories>\n");
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $this->numCategories++;
      $this->write("  <category id='" . $row['id'] . "' name='" . $this->escape($row['name']) . "'/>\n");
    }
    $this->write("</categories>\n");
  }

  function exportRecipe($id) {
    $this->numRecipes++;
    $results =& $this->runQuery($this->db, "select * from recipes where id = $id");
    $results->fetchInto($row, DB_FETCHMODE_ASSOC);
    if(!$row) {
      return;
    }
    $name = $this->escape($row['name']);
    $source = $this->escape($row['source']);
    $uid = $row['uniqueid'];
    $category = $row['category'];
    $preheat = $this->escape($row['preheat']);
	$serves = $row['serves'];
	$cooktime = $row['cooktime'];
	$preptime = $row['preptime'];
    $sb = $row['submittedby'];
	$cdate = $row['createdate'];
    $this->write("  <recipe serves=\"$serves\" cooktime=\"$cooktime\" preptime=\"$preptime\" id=\"$id\" uid=\"$uid\" name=\"$name\" source=\"$source\"  category=\"$category\" preheat=\"$preheat\" createdate=\"$cdate\" submittedby=\"$sb\">\n");
    $this->exportIngredientSetsFor($id);
    $this->exportStepsFor($id);
    $this->exportRecipeCategoriesFor($id);
    $this->exportNoteFor($row['note']);
	$this->exportDescriptionFor($row['description']);
    $this->write("  </recipe>\n");
  }

  function exportRecipeCategoriesFor($id) {
    $this->write("    <rcs>\n");
    $results =& $this->runQuery($this->db,
                "select categoryid from recipetocategory where recipeid = $id");
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $this->write("      <rc id=\"" . $row['categoryid'] . "\"/>\n");
    }
    $this->write("    </rcs>\n");
  }

  function exportNoteFor($note) {
    if(!empty($note)) {
      $this->write("<note><![CDATA[" . $note . "]]></note>\n");
    }
  }

  function exportDescriptionFor($description) {
    if(!empty($description)) {
      $this->write("    <description><![CDATA[" . $description . "]]></description>\n");
    }
  }

  function exportStepsFor($id) {
    $results = $this->runQuery($this->db, 
                               "select step from steps where recipeid = $id order by orderid");
    $this->write("    <steps>\n");
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $this->write("      <step><![CDATA[" . $row['step'] . "]]></step>\n");
    }
    $this->write("    </steps>\n");
    
  }

  function exportIngredientSetsFor($id) {
    $results =& $this->runQuery($this->db,
                               "select * from ingredientsets where recipeid = $id order by orderid");
    $sets = array();
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $set = new stdClass();
      $set->id = $row['id'];
      $set->name = $this->escape($row['name']);
      $sets[] = $set;
    }
    
    foreach($sets as $set) {
      $name = $this->escape($set->name);
      $this->write("    <ingredientset id=\"$set->id\" name=\"$name\">\n");
      $this->exportIngredientsForSet($set->id);
      $this->write("    </ingredientset>\n");
    }
  }

  function exportIngredientsForSet($id) {
    $results = $this->runQuery($this->db, 
                               "select * from ingredients where setid = $id order by orderid");
    while($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $this->write("      <ingredient amount=\"" . $this->escape($row['amount']) .
                   "\" description=\"" . $this->escape($row['description']) . "\"/>\n");
    }
  }

  function retrieveRecipeIds() {
    $result =& $this->runQuery($this->db, "select id from recipes");
    
    $rset = array();
    
    while($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $rset[] = $row['id'];
    }
    return $rset;
  }
}
?>