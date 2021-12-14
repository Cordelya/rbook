<?
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
 * Represents the data in a line-item in a search result.
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: search_result.php,v 1.10 2007/03/16 01:26:29 aviolette Exp $
 */
class SearchResult {
  
  var $title;
  var $url;
  var $recipeId;
  var $category;
  var $submittedByName;
  var $submittedWhen;
  var $userId;
  var $image;
  var $description;
  var $rating;
  var $votes;
  var $cachedRating;
  var $cachedRatingHits;
  var $username;

  function SearchResult($title, $url, $recipeId, $submittedBy, $username, $submittedWhen, 
						$category, $userId, $description, $cachedRating, $cachedRatingHits) {
    $this->title = $title;
    $this->url = $url;
    $this->recipeId = $recipeId;
    $this->submittedByName = $submittedBy;
    $this->category = $category;
    $this->submittedWhen = $submittedWhen;
    $this->userId = $userId;
    $this->category = $category;
	$this->description = $description;
	$this->cachedRating = $cachedRating;
	$this->cachedRatingHits = $cachedRatingHits;
	$this->username = $username;
  }
}

?>