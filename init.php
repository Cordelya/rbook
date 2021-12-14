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

/* the version of rbook  */
define("RBOOK_VERSION", "2.4");
require_once('DB.php');
require_once(dirname(__FILE__) . '/helpers/resources.php');
require_once(dirname(__FILE__) . '/extlib/Smarty/Smarty.class.php');
require_once(dirname(__FILE__) . '/extlib/Smarty/internals/core.write_file.php');
require_once(dirname(__FILE__) . '/config.php');
define("ROOT_DIRECTORY", dirname(__FILE__));
$version_breakdown = explode(".", phpversion());
if($version_breakdown[0] > 4) {
  define("MUA_ENABLED", true);
  require_once(dirname(__FILE__) . '/extlib/MobileUserAgent.php');
}
if(!defined("SKIN")) {
  define("SKIN", "default");
}
if(!defined("ALLOW_REGISTRATION")) {
	define("ALLOW_REGISTRATION", false);
}
define("SKINROOT", dirname(__FILE__) . "/skins/");
if(!defined("SKINDIR")) {
  define("SKINDIR", SKINROOT . SKIN);
}
if(!defined("DEBUG")) {
  define("DEBUG", false);
}

if(!defined("RESULTSONHOMEPAGE")) {
  define("RESULTSONHOMEPAGE", 6);
}

if(!defined("RESULTS_PER_PAGE")) {
  define("RESULTS_PER_PAGE", 4);
}

require_once(dirname(__FILE__) . '/classes/grocery_list.php');
require_once(dirname(__FILE__) . '/classes/user.php');
require_once(dirname(__FILE__) . '/classes/category.php');
require_once(dirname(__FILE__) . '/classes/image.php');
require_once(dirname(__FILE__) . '/classes/recipe.php');
require_once(dirname(__FILE__) . '/classes/guestbook.php');
require_once(dirname(__FILE__) . '/classes/ingredient.php');
require_once(dirname(__FILE__) . '/classes/ingredientset.php');
require_once(dirname(__FILE__) . '/classes/search_result.php');
require_once(dirname(__FILE__) . '/classes/token.php');
require_once(dirname(__FILE__) . '/classes/token_string.php');
require_once(dirname(__FILE__) . '/classes/tag.php');
require_once(dirname(__FILE__) . '/classes/comment.php');
require_once(dirname(__FILE__) . '/helpers/auth.php');
require_once(dirname(__FILE__) . '/helpers/tokenizer.php');
require_once(dirname(__FILE__) . '/helpers/ui.php');
require_once(dirname(__FILE__) . '/helpers/eh.php');
require_once(dirname(__FILE__) . '/const.php');

?>
