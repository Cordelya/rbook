<?php
require_once('PHPUnit.php');
require_once('../install/db_installer.php');
require_once('../install/mysql_db_installer.php');

class MysqlDBInstallerTest extends PHPUnit_TestCase {
	function MysqlDBInstallerTest($name) {
		$this->PHPUnit_TestCase($name);
	}
	function setUp() {
		
	}
	
	function tearDown() {
	}

	function testConstructor() {
		$installer = new MysqlDBInstaller();
		$this->assertNotNull($installer->databaseName, "Database name is null");
		
	}

	function testInstall() {
		
	}

}
?>