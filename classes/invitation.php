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
 * Represents an invitation to a person to join the site.  To do this, a code
 * is generated, sent to the user and persisted to the database.  The user then
 * uses the code to retrieve the invitation and activate his/her account.  This
 * can also be sent to the same user for password retrieval.
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: invitation.php,v 1.3 2006/03/02 01:49:56 aviolette Exp $
 */

require_once(dirname(__FILE__) . '/base_record.php');
require_once(dirname(__FILE__) . "/category.php");

class Invitation extends BaseRecord {
  var $inviter;
  var $invitee;
  var $code;
  var $acceptedDate;
  var $createDate;

  function Invitation($invited, $inviter) {
    $this->BaseRecord();
    $this->invitee = $invited;
    $this->inviter = $inviter;
    $this->code = md5(time());
    $this->createDate = null;
    $this->acceptedDate = null;
  }

  function save() {
    if(isset($this->createDate)) {
      $this->dbUpdate();
    } else {
      $this->dbCreateNew();
    }
  }

  function delete() {
    $db =& $this->getDb();
    $this->runQuery($db, "delete from invitations where invitee = ? and inviter = ?", 
                    array($this->invitee, $this->inviter));
    $db->commit();
    $db->disconnect();
  }

  function init($row) {
    $this->invitee = $row['invitee'];
    $this->inviter = $row['inviter'];
    $this->code = $row['code'];
    $this->acceptedDate = $row['acceptdate'];
    $this->createDate = $row['createdate'];
  }

  function dbUpdate() {
    $db =& $this->getDb();
    $this->runQuery($db, "update invitations set " .
                    "code = ?, modifieddate = now(), acceptdate = ?",
                    array($this->code, 
                          $this->acceptedDate));
    $db->commit();
    $db->disconnect();
  }

  function load($code) {
    $db =& BaseRecord::getDb();

    $results =& BaseRecord::runQuery($db, "select invitee, inviter, code, createdate, modifieddate, " .
                                "acceptdate, createdate  from invitations where code = ?",
                                array($code));
    $invite = null;
    if($results->fetchInto($row, DB_FETCHMODE_ASSOC)) {
      $invite = new Invitation(null, null);
      $invite->init($row);
    }
    $db->disconnect();
    return $invite;
  }

  function dbCreateNew() {
    $db =& $this->getDb();

    $this->runQuery($db, "insert into invitations (invitee, inviter, code, " .
                    "modifieddate, acceptdate, createdate) values (?, ?, ?, now(), NULL, NULL)",
                    array($this->invitee, $this->inviter, $this->code));

    $db->commit();
    $db->disconnect();
  }
  
  function sendForgotPassword($to) {
    return 
      $this->send($to, $to, 
                  "You have requested that you have your password reset.", 
                  "Forgot your password");
  }

  function send($to, $from, $url, $userMessage = "", $subject = null) {
    $headers = "From: " . $from . "\r\n" .
      			"X-Mailer: PHP/" .  phpversion() . "\r\n";
    $headers .= "Return-Path: " . $from . "\r\n";      
    $headers .= "X-Sender: \r\n";
	$headers .= "X-Priority: 3\r\n";
	
    $message = $userMessage . "\n\n";
	rb_log("MAIL: $to, $from, \n$headers");
    $url = buildBaseUrl() . "user/respond/" . $this->code;
    $message = $message . "Click here to activate your account: " . $url . "\n\n";
    if(empty($subject)) {
      $subject = getMessage("invitationSubject");
      $subject = sprintf($subject, APPTITLE);
    }
    return @mail($to, $subject, $message, $headers, "-f $from");
  }
}

?>