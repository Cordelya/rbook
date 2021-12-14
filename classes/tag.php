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

class Tag extends Token {
  var $name;
  var $begin;
  var $both;
  function Tag($name, $begin, $both, $paramlist) {
    $this->name = $name;
    $this->begin = $begin;
    $this->both = $both;
    $this->params = $this->parse($paramlist);
  }

  function parse($l) {
    $ret = array();
    if(preg_match_all('/(\S+)=(\S+)/', $l, $matches)) {
      for($i=0; $i < count($matches); $i++) {
        $i++;
        $name = $matches[$i][0];
        $i++;
        $value = $matches[$i][0];
        $ret[$name] = $value;
      }
    }
    return $ret;
  }

  function param($key) {
    return $this->params[$key];
  }

  function to_s() {
    $beginField = ($this->begin ? '' : '/');
    $bothField = ($this->both ? '/' : '');
    if($this->name != 'recipe') {
      return "<" . $beginField . $this->name . $bothField . ">";
    } else {
      return "";
    }
  }
}

?>