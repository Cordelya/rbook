<?php
require_once('PHPUnit.php');
require_once('base_db_test.php');
require_once('../install/db_installer.php');
require_once('../install/mysql_db_installer.php');
require_once('../classes/base_record.php');
require_once('../classes/category.php');

class CategoryTest extends BaseDBTest {
	var $installer;
	function UserTest($name) {
		$this->PHPUnit_TestCase($name);
	}
	function testCreate() {
		$db =& BaseRecord::getDB();
		$id = $db->nextId("categories");
		$db->disconnect();
		$this->assertEquals(11, $id);
		$cat = new Category();
		$cat->name = 'Foo';
		$this->assertEquals(-1, $cat->id);
		$cat->save();
		$this->assertEquals(11, $cat->id);
		
		$cat = new Category();
		$cat->name = 'bar';
		$cat->save();
		$this->assertEquals(12, $cat->id);
	}

	function testLoadMultiple() {
		$categories = Category::loadMultiple();
		$this->assertEquals(10, count($categories));
		
		$this->assertEquals('Barbecue', $categories[0]->name);
		$this->assertEquals(1, $categories[0]->id);
		
		$this->assertEquals('Desserts', $categories[1]->name);
		$this->assertEquals(2, $categories[1]->id);

		$categories = Category::loadMultiple(array('id' => 1));
		$this->assertEquals(1, count($categories));

		$this->assertEquals('Barbecue', $categories[0]->name);
		$this->assertEquals(1, $categories[0]->id);

		

	}
}



?>
