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

/*
 * ResultSet - contains the search results and manages the breakout of
 * the results between different pages.  The result always has an
 * active page that can be advanced by calling the resultsFor()
 * method.
 *
 * @author Andrew Violette
 * @since 0.9
 * @version $Id: result_set.php,v 1.13 2007/03/23 16:29:24 maschine Exp $
 */


class ResultSet {
  var $resultsPerPage;
  var $results;
  var $page;
  var $name;
  var $displayResultCount;
  var $nextPage;
  var $fromPage;
  /** 
   * Constructs a result set
   * resultsPerPage - the number of results to display on a page
   * results - the search results, which is an array of objects
   */

  function __construct($resultsPerPage, $results) {
    $this->resultsPerPage = $resultsPerPage;
    $this->results = $results;
    $this->page = 1;
    $this->numberOfPages = ceil(count($results) / $resultsPerPage);
    $this->name = getMessage("result_set_Results");
    $this->displayResultCount = true;
  }

  function constructPayload($page, $buttonList, &$smarty) {
    $results = (empty($page)) ? $this->results : $this->resultsFor($page);
    $resultSet = array();
    $catNames = array();
    foreach($results as $result) {
      if(isset($result->category)) {
        if(!isset($catNames[$result->category])) {
          $category = Category::loadOne(array('id' => $result->category));
          $catNames[$result->category] = $category->name;
        }
      }
      $cat = @$catNames[$result->category];
      
      $bl = array();
      if(isset($buttonList)) {
        foreach($buttonList as $button) {
          $url = sprintf($button->url, $result->recipeId);
          $b = array('url' => $url,
                     'name' => $button->name);
          $bl[] = $b;
        }
        
      }
      $res = array("url" => $result->url,
                   "image" => $result->image,
                   "title" => $result->title,
                   "id" => $result->recipeId,
                   "description" => formatForView($result->description),
                   "userId" => $result->userId,
                   "username" => $result->username,
                   "submittedBy" => $result->submittedByName,
                   "submittedWhen" => $result->submittedWhen,
                   "rating" => $result->cachedRating,
                   "ratingHits" => $result->cachedRatingHits,
                   "category" => $cat,
                   "buttons" => $bl);
      $resultSet[] = $res;
                   
    }
    $smarty->assign("results_per_page", $this->resultsPerPage);
    $smarty->assign("result_name", $this->name);
    $smarty->assign("result_count", $this->getTotalResults());
    $smarty->assign("display_count", $this->displayResultCount);
    $smarty->assign("result_page", $page);
    $smarty->assign("number_of_pages", $this->numberOfPages);
    $smarty->assign("has_previous", $this->hasPrevious());
    $smarty->assign("has_next", $this->hasNext());
    $smarty->assign("page", $this->page);
    $smarty->assign("next_page", $this->page + 1);
    $smarty->assign("prev_page", $this->page - 1);
	$smarty->assign("frompage", $this->fromPage);

    $pages = array();
    for($i=1; $i <= $this->numberOfPages; $i++) {
      $pages[] = array('id' => $i);
    }
    $smarty->assign("pages", $pages);
    $smarty->assign("results", $resultSet);
  }

  function display($page, $buttonList) {
    echo("<div id=\"results\"><h2 class=\"recipe\">" . $this->name . "</h2>");
    if($this->displayResultCount) {
      echo("<div>There were " . $this->getTotalResults() . " results</div>");
    }
    echo("<ul style=\"list-style:none\">");
    if(!empty($page)) {
      $results = $this->resultsFor($page);
    } else {
      $results = $this->results;
    }
    $catNames = array();
    foreach($results as $result) {
      echo("<li style=\"margin-left: 0px; margin-bottom: 20px\"><a style=\"font-weight: bold;text-decoration: none\" href=\"" . $result->url ."\">" . $result->title . "</a>");
      if(isset($buttonList)) {
        foreach($buttonList as $button) {
          // apply the ID to the templated URL
          $url = sprintf($button->url, $result->recipeId);
          echo("&nbsp;&nbsp;" . buildButton($button->name, $url));
        }
      }
      echo("<br/>");
      echo("<span style=\"font-size: small;\">Submitted by: <b>". $result->submittedByName ."</b></span><br/>");
      if(isset($result->category)) {
        if(isset($catNames[$result->category])) {
          $cat = $catNames[$result->category];
        } else {
          $category = Category::loadOne(array('id' => $result->category));
          $catNames[$result->category] = $category->name;
          $cat = $catNames[$result->category];
        }
        echo("<span style=\"font-size: small;\">Category: " . $cat . "</span>");
      }
      echo("</li>");
    }
    echo("</ul></div>");

  }

  /**
   * Return the total number of results in the result set
   */

  function getTotalResults() {
    return count($this->results);
  }

  /**
   * Returns whether there are pages that preceed the current page.
   */

  function hasPrevious() {
    return ($this->page > 1);
  }
  
  /**
   * Returns whether there are pages that come after the current page.
   */

  function hasNext() {
    return $this->nextPage;
  }

  /**
   * Returns the results for the specified page.  Calling this method
   * makes the specified page, the active page.
   */

  function resultsFor($page) {
    $this->page = $page;
    $this->nextPage = false;
    $start = ($page - 1) * $this->resultsPerPage;
    if($start >= count($this->results)) {
      return array();
    }
    
    $end = $start + $this->resultsPerPage ;
    if($end >= (count($this->results))) {
      $end = count($this->results);
    }
    $this->nextPage = ($end < count($this->results));
    return array_slice($this->results, $start, $this->resultsPerPage);
  }
}
?>
