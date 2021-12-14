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
 * Smarty plugin: form
 * @author Andrew Violette
 * @package rbook
 * @subpackage plugins
 */

/**
 * Builds a form based on the controller/action 
 *
 * @author Andrew Violette
 * @version 1.0
 * @param params the array of attributes (if this is the open tag)
 * @param content the content (if this represents the closed tag)
 * @param smarty the smarty object
 * @param repeat set to true if repeating allowed
 * @return string|null
 * @package rbook
 * @subpackage plugins
 */

function smarty_block_form($params, $content, &$smarty, &$repeat) {
  if(is_null($content)) {
	$link = "<form  action=\"". buildLink($params['controller'], $params['action'], $params['arg']) . "\"";
	$method = empty($params['method']) ? 'post' : $params['method'];
	$link = $link . " method=\"" . $method . "\"";
	$enctype = empty($params['enctype']) ? '' : (" enctype=\"" . $params['enctype'] . "\"");
	$link = $link . $enctype;
	if(isset($params['id'])) {
	  $link = $link . " id=\"" . $params['id'] . "\"";
	}
	$smarty->myform = $link . ">";
  } else {
	return $smarty->myform . $content . "</form>";
  }
}
?>