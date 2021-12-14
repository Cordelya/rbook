<div id="topBar">
{form id=searchForm action=search controller=recipe method=get}
<div id="topmenu">
	<ul class="buttonBar" id="headerActions">
		{include file="skin:tablist.tpl"}
		{if not $is_mobile and $loggedin}<li id="newRecipeLink">{link controller=recipe action=create}{getMessage key=newRecipe}{/link}</li>{/if}
	</ul>
	
	<div id="headerSearch"><label id="searchFieldLabel" for="searchField">{getMessage key=TitleSearch}:</label><input id="searchField" type="text" maxlength="50" size="30" name="search"/><input type="submit" value="{getMessage key=searchGo}"/>
		<div id="searchDropdown" style="display:none;color: black; border:1px solid black;background-color:white"></div>
	</div>

	<div id="topmenuclear" class="clear">&nbsp;</div>

{literal}

<script type="text/javascript">
// <![CDATA[
var ac = new Ajax.Autocompleter("searchField", "searchDropdown",
     "{/literal}{$root_path}{literal}recipeSuggest.php", {method: 'get'});
// ]]>
</script>
{/literal}
</div>

<div id="topbarclear" class="clear">&nbsp;</div>
{/form}
</div>

