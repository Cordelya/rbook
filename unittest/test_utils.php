<?php
require_once('../install/db_installer.php');
require_once('../install/mysql_db_installer.php');

class TestUtils {


	function &setupTestDatabase() {
		$installer = new MysqlDBInstaller();
		$installer->databaseName = "rbook_test";
		$installer->adminUser = "root";
		$installer->password = "foobar";
		$installer->action = "fresh";
		$installer->databaseHost = "localhost";
		$installer->install();
		return $installer;
	}
	
	function teardownTestDatabase(&$installer) {
		$installer->uninstall();
	}
}

?>