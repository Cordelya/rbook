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
 * Java-style resource function that retrieves a resource from an associative
 * array.  This function retrieves the value from a set of arrays based on
 * the definition of the LOCALE AND LANGUAGE variables.  If both LANGUAGE and
 * LOCALE are set, it looks for a key in an array called
 * $i18n_messages_<language>_<locale>. For instance, $i18n_messages_en_US.  If
 * this array is not defined or if the value is not in the array, it looks for
 * it in an array called $i18n_messages_<language>.  If it is not in that array,
 * then it looks for it in i18n_messages.  If it cannot find a value in that
 * array then it returns null.
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: resources.php,v 1.6 2007/02/28 15:50:28 maschine Exp $
 */

include(dirname(__FILE__) . "/resources_de.php");
include(dirname(__FILE__) . "/resources_et.php");
include(dirname(__FILE__) . "/resources_en.php");

$i18n_messages =& $i18n_messages_en;

function getMessage($key) {
	global $i18n_messages;
	global $i18n_messages_de;
	global $i18n_messages_en;
	global $i18n_messages_et;
	
	$root = 'i18n_messages';
	if(defined("LANGUAGE")) {
		if(defined("LOCALE")) {
			$messages = $root . '_' . LANGUAGE . "_" . LOCALE;
			if(isset($$messages)) {
				$foo =& $$messages;
				if(isset($foo[$key])) {
					return $foo[$key];
				}
			}
		}
		$messages = $root . '_' . LANGUAGE;
		if(isset($$messages)) {
			$foo =& $$messages;
			if(isset($foo[$key])) {
				return $foo[$key];
			}
		}
	}
	$foo =& $$root;
	$value = $foo[$key];
	if(isset($value)) {
		return $value;
	} else {
		return "???" . $key . "???";
	}
}
?>