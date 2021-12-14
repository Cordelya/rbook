<div id="groceryListSidebarDiv" class="box">
<h2>{getMessage key="groceryList"}</h2>
<p id="groceryListInstructions" style="padding-left: 0.5em; font-weight: normal; padding-bottom: 1em;">{getMessage key="groceryListInstructions"}</p>
<div id="groceryListEdit">{link controller=grocery action=index}{getMessage key=EditGroceryList}&raquo;{/link}</div>
<ul class="nolist" id="groceryListSidebar">
{section name=groceryitem loop=$groceryList}
<li>{$groceryList[groceryitem].description}</li>
{/section}
</ul>
</div>
<script type="text/javascript">
{literal}
Droppables.add('groceryListSidebarDiv', {
	accept: 'ingredient',
	onDrop: function(element) {
		Element.hide('groceryListInstructions');
		new Ajax.Updater('groceryListSidebar', '{/literal}{$root_path}grocery/update{literal}',
						{ method: 'post', parameters: 'arg=' + element.innerHTML.stripTags() });
	}
	});
{/literal}
	
</script>