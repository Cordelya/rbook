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
 * Displays a link to a user.
 *
 * @author Andrew Violette
 * @version 1.0
 * @param array
 * @param Smarty
 * @return string|null
 */

function smarty_function_buildLink($params, &$smarty) {
  $url = buildLink($params['controller'], $params['action'], $params['arg']);
  if("true" == $params['cache_defeat']) {
	$url = $url . "?" . time();
  }
  return $url;
}
?>