<?php
require_once('PHPUnit.php');
require_once('base_db_test.php');
require_once('../install/db_installer.php');
require_once('../install/mysql_db_installer.php');
require_once('../classes/base_record.php');
require_once('../classes/user.php');

class UserTest extends BaseDBTest {
	var $installer;
	function UserTest($name) {
		$this->BaseDBTest($name);
	}

	function testUpdatePassword() {
		$users = User::loadMultiple();
		$this->assertEquals(1, count($users));
		$users[0]->updatePassword("newpassword");
		
		$users = User::loadMultiple();
		$this->assertEquals(1, count($users));
		$this->assertEquals(md5("newpassword"), $users[0]->password);
	}

	function testUpdate() {

		$users = User::loadMultiple();
		$this->assertEquals(1, count($users));
		$id = $users[0]->id;
		$fu = "a123456";
		$users[0]->name = $fu;
		$users[0]->save();
		
		$user = User::loadOne(array('id' => 1));
		$this->assertNotNull($user);
		
		$this->assertEquals($user->name, $fu);
	}

	function testCreate() {
		$users = User::loadMultiple();
		$this->assertEquals(1, count($users));
		$id = $users[0]->id;
		$this->assertEquals(1, $users[0]->id);

		$user = $this->createFooUser();
		$user->save();

		$this->assertEquals($id + 1, $user->id);
	}

	function testDeleteUser() {
		$user =& $this->createFooUser();
		$this->assertEquals(-1, $user->id);
		$user->save();
		$id = $user->id;
		$this->assertNotNull($id);
		$this->assertTrue(is_numeric($id));
		$this->assertTrue($id > 0);
		$this->assertNotNull($user->name);
		$user->delete();
		$user = null;
		$user = User::loadOne(array('id' => $id));
		$this->assertTrue(empty($user));
		$users = User::loadMultiple();
		// contains the original user
		$this->assertTrue(count($users) > 0);

	}


	function &createFooUser() {
		$user = new User();
		$this->assertTrue($user->id == -1);
		$user->name = "foo";
		$user->email = "foo@bar.com";
		$user->admin = 1;
		$user->auth = null;
		$user->readonly = 0;
		$user->password = "password";
		return $user;
	}

	function testLoadMultiple() {
		$users = User::loadMultiple();
		$this->assertTrue(count($users) == 1);
		
		$users = User::loadMultiple(array('id' => 2));
		$this->assertTrue(count($users) == 0);

		

	}
}


?>