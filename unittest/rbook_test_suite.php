<?php
require_once('PHPUnit.php');
require_once(dirname(__FILE__) . '/mysql_db_installer_test.php');
require_once(dirname(__FILE__) . '/user_test.php');
require_once(dirname(__FILE__) . '/category_test.php');
require_once(dirname(__FILE__) . '/recipe_test.php');
//$suite = new PHPUnit_TestSuite("MysqlDBInstallerTest");
//$result = PHPUnit::run($suite);


$suite = new PHPUnit_TestSuite("UserTest");
$result = PHPUnit::run($suite);
echo $result->toString() . "\n";

$suite = new PHPUnit_TestSuite("RecipeTest");
$result = PHPUnit::run($suite);
echo $result->toString() . "\n";

//$suite->addTest(new CategoryTest("testCreate"));
//$suite->addTest(new CategoryTest("testLoadMultiple"));

?>

