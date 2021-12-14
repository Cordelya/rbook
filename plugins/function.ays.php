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
 * Builds the script that prints out an "are you sure" message when activated
 * by an event.
 *
 * Usage:
 * <pre>
 * {ays id=ays button=foo url=foo.php}
 * {ays id=ays button=foo evt=deleteIt}
 * </pre>
 *
 * @author Andrew Violette
 * @version 1.0
 * @param array
 * @param Smarty
 * @return string|null
 */

function smarty_function_ays($params, &$smarty) {
	$foo = isset($params['evt']) ? $params['evt'] : ("'" . $params['url'] . "'");

	$val = '<script type="text/javascript">' . "\n" .
	  'var rb = {ays : "' . getMessage("areYouSure") .
	  '",no: "' . getMessage("no") .
	  '",yes: "' . getMessage("yes") . '"};' . "\n" .
	  'var ' . $params['id'] .
		"=new AreYouSureVerification('" . $params['button'] . "'," .
		$foo . ", rb);\n" . $params['id'] . ".prepare('" . $params['id'] . "');\n</script>";
	return $val;

}
?>