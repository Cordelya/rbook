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
 * Builds a list of Token objects from a string.  Tags and
 * StringTokens are the two types of token objects.
 */

function tokenize($text) {
  $toklist = array();
  $index = 0;
  $lastIndex = 0;
  while(true) {
    $index = strpos($text, "[", $index);
    if($index === false) {
      $toklist[] = new TokenString(substr($text, $lastIndex));
      return $toklist;
    }
    $toklist[] = new TokenString(substr($text, $lastIndex, $index - $lastIndex));
    $index2 = strpos($text, "]", $index);
    if($index2 === false) {
      return $toklist;
    }
    $inards = substr($text, $index+1, $index2 - $index -1);
    if(preg_match('/^(\/*)(b|i|u|recipe|a)\s*(.*)/', $inards, $match)) {
      $t = new Tag($match[2], $match[1] != '/', false, $match[3]);
      $toklist[] = $t;
    }
    $index = $index2 + 1;
    $lastIndex = $index;
  }
  return $toklist;
}

/**
 * Renders text into HTML.  Any tags that are present are encoded into
 * HTML.
 */

function formatForView($text) {
  $toklist = tokenize($text);
  $buf = '';
  for($i=0; $i < count($toklist); $i++) {
    $token = $toklist[$i];
    if(is_a($token, 'Tag')) {
      $name = $token->name;
      if($name == 'recipe') {
        $id = $token->param('id');
        $r = Recipe::load($id);
        $title = $r->title;
        $buf = $buf . "<a href=\"" . buildLink("recipe", "view", $id) . "\">$title</a>";
      } 
    }
    $buf = $buf . $toklist[$i]->to_s();
  }
  $buf = str_replace("\n", "<br/>", $buf);
  return $buf;
}
?>