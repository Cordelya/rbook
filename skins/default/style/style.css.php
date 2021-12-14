<?php
include("../../../init.php"); 
header("Content-type: text/css");
?>

/*
 * Global layout and topography
 */
* { margin: 0; padding: 0; font-size: 1em;font-family: Verdana, Arial, sans-serif;}
/* tweek this to shrink to fonts displayed on the page */
@media screen,handheld {
body { font-size: 75%; background: #EEEEEE; }
}
#bodydiv { margin: 0; background: #EEEEEE; width: 90em;}
#container { float:none; clear: both}
@media screen,handheld {
#content { min-height: 35em; padding: 0.3em 5px 2em 20px;  background: #FFFFFF url(<?= APPROOT ?>skins/default/images/roundedtop.gif) top left no-repeat; float:right; width: 66.5em;}
}
#peek {display:none}
#topmenu a { font-weight: bold; color: #FFFFFF; }
/* h1 defines the recipe title on the edit/view pages*/
h1 { font-size: 1.75em; margin-top: 0.5em; margin-bottom: 0.5em; }
h2 { font-size: 1.25em; margin-top: 0.5em; padding-top: 0.25em; padding-bottom: 0.25em; margin-bottom: 0.75em; }
table { border-collapse: collapse; }
td { padding: 0.5em; }
table.center td { text-align: center; }
input { padding: 0.15em; }
input.button {margin-right: 10px; }
table.withborder, table.withborder th, table.withborder td { border: 0.1em solid #CFE3EF; } 
label { margin-right: 0.2em; }
.clear { float: none; clear: both; height: 0; width: 0; }
.warning { color: red; }
ul.buttonBar { list-style:none; }
ul.buttonBar li { float: left; padding: 4px 14px 4px 0px;  }
a, a:visited, a:active { text-decoration: none; }
a:hover { text-decoration:underline; }
.box, thead tr, .selected { background-color: #CFE3EF; }
/* class used by Ajax.Autocompleter for selected element */
.selected { background-color: #CCCCCC; }
#rightbox .box { background-color: #CCCCCC !important; }
#rightbox { float: right; width: 16em; }
#viewRecipeContainer { width: 50em; float: left; }
.standalone { margin-top: 2em; }
input { font-weight: normal !important;}
ul.nolist { list-style:none; }
div.controlRow { padding-bottom: 10px; }
div.controlRowSmall { padding-bottom: 5px; }
/*
 * Search Results
 */
div.result { margin-top: 1.5em; padding: 0; line-height: 150%; }
.resultDescription { font-size: 1em; }
.result a { font-weight: bold; text-decoration: none; }
ul.resultList { list-style: none; }
.resultNav { font-weight: bold; text-decoration: none; margin-top: 2em; }
.recipeSeparator {margin-top: 15px; margin-bottom: 15px; }
/*
 * Guestbook
 */
div.commentHead { background-color: #CCCCCC; border-width: 1px 1px 0px 1px; border-style: solid; border-color: #999999; padding: 5px 0 5px 8px; }
div.commentText { clear: both; border-width: 0px 1px 1px 1px; border-style: solid; border-color: #999999; margin-bottom: 25px; padding: 8px; }
div.commentAdd { margin-bottom: 25px; }
/*
 * Sidebar and header
 */
#headerSearch { float: right; }
#headerTitle {background: url(<?= APPROOT ?>skins/default/images/bgtop.gif) top repeat-x; color: #FFFFFF !important; padding: 10px 0 10px 40px; font: 4em Verdana, Arial, sans-serif bold; }
@media screen, handheld {
.boxText, a, h2, thead tr,h1.recipeTitle, .mlink, .mlinkselected, .box label { color: #397DBD; }
}
#topBar { position:relative; color: #FFFFFF; background-color: #CCCCCC; border-bottom: 0.1em solid #000000; border-top: 0.1em solid #000000; line-height: 1.1; margin-bottom: 10px; }

#searchFieldLabel { padding-left: 3em; }
#searchDropdown * { color: #000000; text-align:left; }
#searchDropdown ul { margin: 0.2em; }
#searchDropdown * li { list-style: none; line-height: 150%; }
#leftbox { float: left; margin-right: 1em; width: 18em; margin-left: 0.5em;}

.box { border: 1px  solid #999999; margin-bottom: 10px; padding: 0 5px 10px 5px; font-weight: bold; }
.boxText { line-height: 150%; }
.box h2 { background-color: #999999; color: #FFFFFF; text-align: left; padding-left: 10px; }

div.module { background-color: #EDEDED; margin: 30px 30px 30px 0; min-width: 600px; border: 1px solid #9A9C9A; }
div.moduleHeader {  }
div.moduleHeader h2 { padding: 5px 10px; }
div.moduleFooter {}
.error { font-weight: bold; color: red; }
div.errormessage { font-weight: bold; color: red; text-align: center; border: 0.1em solid #000000; width: 80%; margin-top: 0.75em; padding: 0.75em; background-color: #F7F3CE; margin: 0 auto;} 
#errorBlock { font-size: 150%; font-weight: bold; color: red; }
div.warning, .formBlock { margin: 1em 0 1em 0; }
p.comment {font-weight: normal; }
div.commentSection { margin: 10px 0; }
hr.commentSeparator { margin: 10px 0; }
/*
 * Edit Recipe Page
 */
#starRating { font-weight: normal; width: 9em; }
img.icon {vertical-align: middle; border:none; }
#descriptionSection { padding: 10px 0; }
table.ingredientTable, table.stepTable { margin-top: 0.3em;  }
.ingredientTable, .stepTable { width: 100%; }
#recipeHeader, thead tr, .warning, .boxText, #searchFieldLabel { font-weight: bold; }
.formField { line-height: 200%; font-weight: bold; }
.resultField, .helptext { color: #7D777D; }
ul.menulist { list-style: none; margin-left: 1em; }
li.menuitem { padding-top: 0.2em; padding-left: 0.2em; }
table.recipeTable, table.loginTable { margin-bottom: 0.6em; border-style:none; }
table.recipeTable td,table.loginTable td { border-style: none; text-align: left; vertical-align: top; }
table.viewIngredientTable {  margin-top: 0.6em; }
td.amountViewCell { width: 5em; }
img.recipePic { display:block; border: 1px solid #AAAAAA; padding: 5px; margin:auto;}
#loginModule { margin: 0 auto; width: 30em; }
#loginModule { font-size: 1.5em; }
#loginModule .innerModule { margin-top: -18px; }
#loginModule .moduleFooter { margin-top: -3px; }
body.loginPage, body.loginPage #bodydiv { background: #FFFFFF !important; }
.loginPage #content { float: none !important; width: 90em; }
#recipeImage { float: right; }
#imagetext { font-weight:bold; }
#sscontrols { text-align:right; }
#recipeEditBar { padding: 0.5em 0 0.5em 0; }
#sliderHelp {margin-top:10px; }

#recipeSeeAll { padding-top:10px; }

ul.tabs li {
 float:left;
 list-style:none;
 border-bottom: none;
 margin-right: 2px;
}

ul.tabs li a.selectedTab1 {
 background: url(<?= APPROOT ?>skins/default/images/selected_tab.gif) top left no-repeat;
 color: #FFFFFF;
 font-weight: bold;
}

ul.tabs li a.unselectedTab1 {
 background: url(<?= APPROOT ?>skins/default/images/unselected_tab.gif) top left no-repeat;
 color: #000000;
 text-decoration: none !important;
}

li.unselectedTab1 {
 background: url(<?= APPROOT ?>skins/default/images/unselected_tab_right.gif) top right no-repeat;
}

li.selectedTab1 {
 background: url(<?= APPROOT ?>skins/default/images/selected_tab_right.gif) top right no-repeat;
}

ul.tabs  a {
 display:block;
 padding: 10px;
}


.tabContainer {
 border-top: 3px solid #529ACE;
 margin-top: 6px;
 float:none;
 clear: both;
 padding-left: 0;
 padding-top: 10px;
 font-size: 1.2em;
}

input.tf {}

#commentAction, #bookmarks {clear:both; float: none; }

/*
 * Categories
 */
#categoryTable { width: 35em; border-style:none; }
/*
 * User Management table
 */
#userMgmtTable { width: 100%; }
/*
 * Login
 */

#loginButtonDiv { margin: 0.3em 0 0.3em 0 }
table.loginTable{ width: 30em; border-style:none;}
td.loginTable { text-align: left; vertical-align: top; }


a.onpage { text-decoration: none; font-weight: bold;}
a.resultLink { font-size: 1.5em; } 
@media screen, handheld { 
a.onpage { color: #8b0000; }
}

a:hover.onpage {
  background-color:#CFE3EF;
  text-decoration: none;
}

#stepList {
  margin: 2em;
}
#stepList li {
	padding-top: 0.2em;
	padding-bottom: 0.2em;
	line-height: 150%;
}

td.measurement {
	width: 10em;
	text-align: left;
}

table.conversionTable {
	width:30em;
	margin-top: 0.6em;
}

#topmenu {
	text-align: right;
	padding: 0.3em 0.3em 0.3em 0;
	background-color: #CCCCCC;	
}

.submenu {
	display: inline;
	padding:0;
}



#footerDiv {
	font-size: 0.75em;
	padding: 0.3em 0 0.3em 0;
	clear: both;
}

#topmenu, #footerDiv, #searchBar {
	padding-right: 0.3em;
	padding-left: 0.3em ;
}

td.cat {
	border-style:none;
	text-align:  left;
}



td.category {
	padding: 1em;
}

.category {
	border-style:none;
}

table.viewIngredientTable, table.viewIngredientTable td {
	border-style:none;
	text-align:left;
}

table.viewIngredientTable td.descCell:hover {
cursor: pointer;
}

.buttonRow, div.ingredientTable  {
	margin: 0.6em 0 0.6em 0;
	font-size: 1.2em;
}



label.right {
  padding-left: 3px; 
  vertical-align: middle;
}

.rightButtonRow {
  text-align:right;
}

.setNameDiv {
	margin: 1em 0 0.6em 0;
	padding-bottom: 5px;
	font-weight: bold;
}



div.editField {
	padding-bottom:5px;
	padding-top: 1px;
}

label.editField {
	font-size: 0.75em;
	font-weight: bold;
}


.amountCell {
		width: 7em;
		text-align: left;
	  padding: 0.3em;
}

.descCell {
	text-align:left;
  padding: 0.3em;
}


.recipeTitle { text-align: center; margin: 1.5em 0 1.5em 0; }


.fileColumn {
	width: 15em;
}

dt {
  padding-bottom: 0.5em;
  padding-top: 0.5em;
}

dd {
  margin-left: 1em;
  margin-bottom: 1em;
  line-height: 150%;
}

td.fileColumn {
  text-align: left;
}

li.taRow {
  padding: 1em 0;
 list-style:none;
}

li.taRow:hover {
 cursor: pointer;
}

#importTable {
  margin-top: 1em;
}

#importTable * td {
  padding: 0.5em;
}

.helpbox {
	border: 1px solid #397DBD;
	text-align: center;
}

.helptext {
  font-size: 0.8em;
}

.ert_name {
    text-align: left;
}
#ert {
    margin-right: auto;
    margin-left: auto;
    margin-top: 5em;
}

#ert * td {
    padding: 1em;
}

a.helpbox {
	padding: 0.1em;
	margin-left: 0.3em;
	text-decoration: none;
}

#addCommentButtonRow { padding-top: 10px; }
#editGroceryList li { padding-bottom: 10px; font-size: 150%; }
#editGroceryList { padding-top: 20px; }
li.groceryListItem:hover {  background-color:#CFE3EF; cursor: pointer; }
.giccb { margin-right: 10px; }
#groceryListSidebar li { padding-left: 0.5em; padding-bottom: 0.5em;}

#captchaImg { border: 1px solid #AAAAAA; margin: 10px 0 10px 0; }
#captchaInput {margin-right: 10px; width: 5em; }
#captchaParagraph {margin: 10px 0 10px 0; }

@media print
{
	.notprintable { display:none !important; }
	td,table,#bodydiv {
		border-style:none;
	}
	#container {
		border-style: none;
	}
	#comments, #footerDiv,div.loginBar,#topBar,div.leftbar,#bookmarks, #recipeEditBar,#rightbox,#leftbox {
		display: none;
	}
	#groceryAdd, #addGroceryButton, #saveGroceryButton,.giccb,#clearListButton {
		display: none;
	}
	h2.recipe, h1.recipeTitle {
		color: #000000;
	}
}

.box, thead tr { background-color: #FFFFFF; }

p.help {padding-bottom: 20px; text-align:justify;}