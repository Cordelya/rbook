<ul id="categoryMenu" class="menulist">
<li class="menuitem">{link controller=recipe action=search}{getMessage key=allRecipes}{if isset($recipecount) and $recipecount > 0} ({$recipecount}){/if}{/link}</li>
<li class="menuitem" style="padding-bottom:10px">{link controller=recipe action=random}{getMessage key=randomRecipe}{/link}</li>
{section name=cat loop=$categories}
<li class="menuitem">{link controller=recipe action=category arg=$categories[cat].id}{$categories[cat].name} {if isset($categories[cat].recipeCount) and $categories[cat].recipeCount > 0}({$categories[cat].recipeCount}){/if}{/link}</li>
{/section}
</ul>

