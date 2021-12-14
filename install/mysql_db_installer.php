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
  
  function MysqlDBInstaller_f() {
    $this->DBInstaller();
  }
  
  function createDatabase() {
	
    $con = @mysqli_connect($this->databaseHost, $this->adminUser, $this->password);
    if(!$con) {
      $this->errors[] = mysqli_error();
      return;
    }
    
    $this->exists = true;
    if($this->action == "fresh") {
      mysqli_query("DROP DATABASE IF EXISTS " . $this->databaseName);
      mysqli_query("CREATE DATABASE " . $this->databaseName , $con);
      $error = mysqli_error();
      if(!empty($error)) {
        $this->errors[] = $error;
        return;
      }
    }
  }
}
?>
