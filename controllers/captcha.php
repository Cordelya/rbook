<?php

/*
 * rbook Recipe Management System
 * Copyright (C) 2006 Andrew Violette andrew@andrewviolette.net
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

class Captcha {
  
  function Captcha() {
  }

  function generateText() {
	$str = "";
	for($i = 0; $i < 5; $i++) {
	  $num = rand(0, 62);
	  
	  if($num < 10) {
		$num = $num + 48;
	  } else if($num < 36) {
		$num = $num + 65 - 10;
	  } else {
		$num = $num + 97 - 36;
	  }
	  $str .= chr($num);
	}
	$this->challenge = $str;
  }

  function getText() {
	return $this->challenge;
  }

  function renderImage() {

	header("Content-type: image/png");
	
	$this->generateText();
	$str = $this->getText();
	$tdir = null;
	if(defined("SCRATCH_DIR")) {
		$tdir = SCRATCH_DIR;
	}
	$foo = tempnam($tdir, "captcha");
	unlink($foo);
	$foo = $foo . ".png";
	$convert = IMAGEMAGICK;
	$cl = $convert . " -size 300x150 -font Arial-Italic -pointsize 72 xc:transparent -fill darkred -draw \"text 10,100 '" . $str ."'\" -channel RGBA  " . $foo;
	system($cl, $rc);
	rb_log("Convert: "  . $cl);

	$handle = fopen($foo, "rb");
	$contents = fread($handle, filesize($foo));
	fclose($handle);
	unlink($foo);
	echo $contents;

  }
  
  function getExpectedPhrase() {
	return "test";
  }
}
?>
