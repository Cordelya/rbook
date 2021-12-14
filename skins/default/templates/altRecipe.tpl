<h1 id="viewRecipeTitle" class="recipeTitle">{$recipe.title}</h1>

<p>{$recipe.description}</p>
{section name=set loop=$recipe.ingredients}
<h2>{$recipe.ingredients[set].name}</h2>
<ul>
{section name=row loop=$recipe.ingredients[set].rows}
<li>{$recipe.ingredients[set].rows[row].amount} {$recipe.ingredients[set].rows[row].description}</li>
{/section}
</ul>
{/section}
<h2>{getMessage key=Steps}</h2>
<ol id="stepList">
{section name=step loop=$recipe.steps}
<li>{$recipe.steps[step]}</li>
{/section}
</ol>
{if isset($recipe.note)}
<h2>{getMessage key=Note}</h2>
<p id="note">{$recipe.note}</p>
{/if}


{include file="skin:bookmarks.tpl"}
