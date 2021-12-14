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
 *
 * @package rbook
 * @subpackage controllers
 */

/** */
require_once(dirname(__FILE__) . "/../init.php");
require_once(dirname(__FILE__) . "/base_controller.php");
require_once(dirname(__FILE__) . '/../classes/importer.php');
require_once(dirname(__FILE__) . '/../classes/exporter.php');
require_once(dirname(__FILE__) . "/user_controller.php");

/**
 * Controller responsible for Import and Export.
 *
 * @author Andrew Violette
 * @since version 2.0
 * @package rbook
 * @subpackage controllers
 */

class ExporterController extends BaseController {

  function ExporterController($name) {
    $this->BaseController($name);
  }
  /**
   * Factory method that creates a user controller.  
   */

  function &newInstance() {
    $controller = new ExporterController("exporter");
    $controller->set_requires_adminaccess(array("index", "import", "show_exporter", "export", "download_export", "show_results"));
    return $controller;
  }


  function prepareImportPage(&$fileList, &$smarty) {
	$hasImportFiles = count($fileList);
	$smarty->assign("hasImportFiles", $hasImportFiles);
	if(!$hasImportFiles) {
	  return;
	}
	$l = array();
	$first = null;
	foreach($fileList as $foo) {
	  $path = pathinfo($foo);
	  $name = $path['basename'];
	  $id = stripExtension($name);
	  if(preg_match('/\.xml$/', $name, $matches)) {
		$id = substr($name, 0, strlen($name) - 4);
	  }
	  if(!isset($first)) {
		$first = $name;
	  }
	  $l[] = array("name" => $name,
				   "id" => $id,
				   "created" => date("Y-m-d H:i:s", filemtime(IMPORTDIR . "/" . $foo)));
	}
	$smarty->assign("importFiles", $l);
	$smarty->assign("importFile", $name);
	
  }


  /**
   * Main page that shows the options to import the files.
   */

  function index() {
    $this->before_execute("index");

    $modelView =& $this->prepareModelAndView();
    $importer = new Importer();
    $files = $importer->listImportFiles();

    $this->prepareImportPage($files, $modelView);

    $modelView->assign('title', getMessage("ExportImport"));
    $modelView->assign('pageTitle', getMessage("ExportImport"));
    $modelView->assign('file', $_POST['file']);
    $modelView->assign('exportFile', "export-" . time() . ".xml");
    $modelView->display(getFullTemplateName('import'));
  }

  /**
   * Called after authentication, but before each action.  Here we
   * make sure that import/export is setup correctly.  If not, we
   * forward to the home page.
   */

  function before_action() {
    if(!defined("IMPORTDIR")) {
      gotoHomePage();
    }
  }

  function export() {
    $this->before_execute("export");
    if(!$this->isPost()) {
      $this->activateDefault();
    }
    if(empty($_POST['exportFile'])) {
      setPageError(getMessage("noExportFileSpecified"));
      return;
    }
    $exporter = new Exporter();
    $exportFile = IMPORTDIR . "/" . $_POST['exportFile'];
    $exporter->exportFile($exportFile);
    $_SESSION['exportFile'] = $_POST['exportFile'];
    $_SESSION['exporter'] =& $exporter;
    setPageError(getMessage('exportSuccessful'));
    $this->activateAction("show_results");
  }

  function show_results() {
    $this->before_execute("show_results");
    $exporter = $_SESSION['exporter'];

    $modelView =& $this->prepareModelAndView();
    $modelView->assign('numRecipes', $exporter->numRecipes);
    $modelView->assign('numCategories', $exporter->numCategories);
    $modelView->assign('numUsers', $exporter->numUsers);
    $modelView->assign('title', getMessage('ExportResults'));
    $modelView->assign('pageTitle', getMessage('ExportResults'));
    $modelView->assign('file', $_POST['file']);
    $modelView->assign('exportFile', $_SESSION['exportFile']);
    $modelView->assign('exportFileId', stripExtension($_SESSION['exportFile']));
    $modelView->display(getFullTemplateName('exportResults'));
  }

  function show_exporter() {
    $this->before_execute("show_exporter");
    $modelView =& $this->prepareModelAndView();
    $modelView->assign('title', getMessage('Export'));
    $modelView->assign('pageTitle', getMessage('Export'));
    $modelView->assign('file', $_POST['file']);
    $modelView->assign('exportFile', "export-" . time() . ".xml");
    $modelView->display(getFullTemplateName('export'));
  }

  function download_export($fileName) {
    $this->before_execute("download_export");
    if(empty($fileName)) {
      setPageError(getMessage("noImportFile"));
      gotoReferrer();
    }

    $fileName = IMPORTDIR . "/" . $fileName . ".xml";
    $fp = fopen($fileName, 'r');
    if(!$fp) {
      setPageError(getMessage("errorOpenImport") . $fileName);
      gotoReferrer();
    }
    
    header("Content-Type: text/xml");
    header("Content-disposition: attachment; filename=" . $fileName);
    
    while($data = fread($fp, 4096)) {
      print($data);
    }
    fclose($fp);
  }

  function delete_import($id) {
    $this->before_execute("delete_import");
	$path = IMPORTDIR . "/" . $id . ".xml";
	if(!empty($id)) {
	  if(is_file($path)) {
		unlink($path);
	  }
	}
	$this->activateDefault();
  }

  /**
   * Imports the file specified in importFile into the repository.
   * Afterwards, the user data is cleared and the user is logged out
   * and redirected to the home page.
   */

  function import() {
    $this->before_execute("import");
    if(!$this->isPost()) {
      $this->activateDefault();
    }
    $importer = new Importer();
    $errorMessage = $importer->import(IMPORTDIR . "/" . $_POST['importFile']);
    setPageError($errorMessage);
    unset($_SESSION['categories']);
    $uc = UserController::newInstance();
    $uc->logoutInternal();
  }
}
?>
