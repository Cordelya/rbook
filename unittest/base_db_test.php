<?php
require_once('PHPUnit.php');
require_once('../install/db_installer.php');
require_once('../install/mysql_db_installer.php');
require_once(dirname(__FILE__) . "/../classes/recipe.php");
class BaseDBTest extends PHPUnit_TestCase {

	var $installer;

	function BaseDBTest($name) {
		$this->PHPUnit_TestCase($name);
	}

	function tearDown() {
		$installer =& $this->installer;
		$installer->uninstall();
	}
	function getInitialUser() {
		return "andrew";
	}


	function setUp() {
		$installer = new MysqlDBInstaller();
		$this->installer =& $installer;
		$installer->databaseName = "rbook_test";
		$installer->adminUser = "root";
		$installer->password = "foobar";
		$installer->action = "fresh";
		$installer->databaseHost = "localhost";
		$installer->initialUser = $this->getInitialUser();
		$installer->initialEmail = "andrew@foo.com";
		$installer->install();
		error_reporting(E_ALL);
	}
	

}
?>