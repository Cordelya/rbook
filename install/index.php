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
 * Language setting dialog.
 *
 * @author Michael Schäfers
 * @since 1.6
 * @version $Id: index.php,v 1.21 2007/04/01 14:27:16 maschine Exp $
 */
 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
<link rel="STYLESHEET" type="text/css" href="style.css"/>
<title>rbook - Language setup</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<div id="mainDiv" style="text-align:center">
<h1>Please chose your language:</h1>
<form action="check.php" method="get">
    <select name="language">
      <option value="en">English</option>
      <option value="et">Estonian</option>
      <option value="de">German</option>
    </select>
	&nbsp;
	<input type="submit" value="Go!" />
</form>
<p style="text-align:left; line-height:18px">
The chosen language will be used to guide you through rbook's setup process and will be the default language within your rbook site.
<br />
For information about adding your language to rbook, please see the <a href="http://rbook.sf.net">rbook homepage</a>.
</p>
</div>
</body>
</html>