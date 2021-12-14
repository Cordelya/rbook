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
 * @author Andrew Violette
 * @version 1.0
 * @param array
 * @param Smarty
 * @return string|null
 */

function smarty_function_starRating($params, &$smarty) {
  $numStars = $params['number'];
  $dec = ceil($numStars);
  $frac = $dec - $numStars;
  $numSelected = $numStars;
  if($frac > 0) {
	$numSelected = floor($numStars);
  }
  $buf = '';
  for($i=0; $i < $numSelected && $i < 5; $i++) {
	$buf = $buf . '<img alt="selected star" src="' . $smarty->get_template_vars('skin_img') . 'star_selected.gif"/>';
  }
  if($frac > 0) {
	$buf = $buf . '<img alt="half star" src="' . $smarty->get_template_vars('skin_img') . 'star_half.gif"/>';
  } 
  for($i=$dec; $i < 5; $i++) {
	$buf = $buf . '<img alt="unselected star" src="' . $smarty->get_template_vars('skin_img') . 'star_unselected.gif"/>';
  }
  return $buf;
}
?>