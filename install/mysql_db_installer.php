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
 * Displays the "Installation Complete" page.
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: mysql_db_installer.php,v 1.5 2006/03/03 01:43:39 aviolette Exp $
 */

class MysqlDBInstaller extends DBInstaller {
  
  function MysqlDBInstaller() {
    $this->DBInstaller();
  }
  
  function createDatabase() {
	  $dsn = "mysql:host=" . $this->databaseHost;
	  $options = [
		PDO::ATTR_ERRMODE		=> PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES	=> false,
	  ];
	  try {
		$con = new PDO($dsn, $this->adminUser, $this->password, $options);
	  } catch (\PDOException $e) {
		throw new \PDOException($e->getMessage(), (int)$e->getCode());
	  }
    //$con = @mysqli_connect($dsn, $this->adminUser, $this->password, $options);
	  
      if(!$con) {
      	$this->errors[$e] = $e;
      	return;
    }
    
    $this->exists = true;
    if($this->action == "fresh") {
	    //drop any existing db
	    $q = "DROP DATABASE IF EXISTS :db";
	    $params = array(":db" => $this->databaseName);
	    $sth = $con->prepare($q, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	    $sth->execute($params);
	    $q_result = $sth->fetchAll();
	    //create new database
	    $q = "CREATE DATABASE :db";
	    $params = array(":db" => $this-databaseName);
	    $sth = $con->prepare($q, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	    $q_result = $sth->fetchAll();
	    if ($q_result) {
		    return $q_result;
		    exit;
	    } else if ($e) {
		    return $e;
		    exit;
      }
    }
  }
}
?>
