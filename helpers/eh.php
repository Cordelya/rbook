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

function redirect_to_error_page($errno, $errmsg, $filename, $linenum, $vars) {
  rb_log("[$filename,$linenum]: " . $errmsg . "\n");
  setPageError("An error has occured, see logs for detail");
  header("Location: index.php");
  exit();
}

function rb_log($msg) {
  if(!defined("DEBUG")) {
    return;
  }
  if(DEBUG === false) {
    return;
  }
  if(defined("LOG_FILE_PATH")) {
    error_log($msg . "\n", 3, LOG_FILE_PATH);
  } else {
    error_log($msg);
  }
}
?>