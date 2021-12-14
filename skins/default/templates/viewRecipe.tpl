{include file="skin:header.tpl"}

<script type="text/javascript">
{literal}
onload=viewRecipeOnLoad;
{/literal}
</script>

<ul class="buttonBar" id="recipeEditBar">
<li><a class="ebutton" href="#" onclick="printRecipe()">{getMessage key=printRecipe}</a></li>
{if $loggedin && $recipe.editable}
<li>{link controller=recipe action=edit}{getMessage key=editRecipe}{/link}</li>
<li><a href="#" id="deleteRecipeButton" class="ebutton">{getMessage key=deleteRecipe}</a>{ays id=ays button=deleteRecipeButton url=$deleteUrl}</li>
<li>{link controller=recipe action=add_picture}{getMessage key=addPicture}{/link}</li>
{if count($recipe.images) > 1}
<li>{link controller=recipe action=remove_pictures}{getMessage key=deletePictures}{/link}</li>
{elseif count($recipe.images) == 1}
<li><a href="#" id="deletePictureButton" class="ebutton">{getMessage key=deletePicture}</a>{ays id=ays button=deletePictureButton url=$deletePictureUrl}</li>
{/if}
{/if}
</ul>
{include file="skin:bookmarks.tpl"}
<div id="viewRecipeContainer">
<h1 id="viewRecipeTitle" class="recipeTitle">{$recipe.title}</h1>
<div id="headersection">
{if isset($recipe.images)}
{include file="skin:imagebox.tpl"}
{/if}
	<table id="datasection">
	<tbody>
	<tr>
	<td class="resultField">{getMessage key=Categories}:</td>
    <td>{section name=cat loop=$recipe.categories}
        {link class_name=onpage controller=recipe action=category arg=$recipe.categories[cat]->id}{$recipe.categories[cat]->name}{/link}&nbsp;&nbsp;
      {/section}</td>
	</tr>
    {if isset($recipe.submittedBy)}
	<tr>
	<td class="resultField">{getMessage key=submittedBy}</td><td>{$recipe.submittedBy}</td>
	</tr>
    {/if}
	{if isset($rating) and $ratingVotes != 0}
	<tr>
	<td class="resultField">{getMessage key=Rating}</td>
	<td>{starRating number=$rating} {getParameterizedMessage key=starsOutOfVotes}{param}{$rating}{/param}{param}{$ratingVotes}{/param}{/getParameterizedMessage}
	</td>
	</tr>
	{/if}
    {if isset($recipe.preheat) }
	<tr>
    <td class="resultField">{getMessage key=Preheat}:</td><td>{$recipe.preheat}</td>
	</tr>
    {/if}
    {if isset($recipe.sourceaslink) }
    <tr>
    <td class="resultField">{getMessage key=Source}:</td><td>{$recipe.sourceaslink}</td>
    </tr>
    {/if}
	{if isset($recipe.serves) and $recipe.serves > 0 }
	<tr>
	<td class="resultField">{getMessage key=Serves}:</td><td><span id="servesField">{$recipe.serves}</span>
		{if not $is_mobile}
		<div id="track1" style="width:200px;background-color:#aaa;height:5px;">
		  <div id="handle1" style="width:15px;height:5px;background-color:#f00;"> </div>
		</div>
		<div id="sliderHelp" style="display:none" class="helptext">{getMessage key=SlideTo}</div>
		{/if}

	</td>
	</tr>
	{/if}
	{if isset($recipe.preptimePeriod) and $recipe.preptime > 0}
	<tr>
	<td>{getMessage key=PrepTime}</td>
	<td>{$recipe.preptimePeriod}</td>
	</tr>
	{/if}
	{if isset($recipe.cooktimePeriod) and $recipe.cooktime > 0}
	<tr>
	<td>{getMessage key=CookTime}</td>
	<td>{$recipe.cooktimePeriod}</td>
	</tr>
	{/if}
	{if isset($recipe.totaltimePeriod) and $recipe.totaltime > 0}
	<tr>
	<td>{getMessage key=TotalTime}</td>
	<td>{$recipe.totaltimePeriod}</td>
	</tr>
	{/if}
	</tbody>
	</table>
</div>
<p id="descriptionSection">{$recipe.description}</p>
<div id="ingredientsSection">
{section name=set loop=$recipe.ingredients}
<h2>{$recipe.ingredients[set].name}</h2>
{if $is_mobile}
<ul style="list-style: none; margin: 0; padding: 0;">
{section name=row loop=$recipe.ingredients[set].rows}
<li style="margin: 0; padding: 0">{$recipe.ingredients[set].rows[row].amount}&nbsp;&nbsp;{$recipe.ingredients[set].rows[row].description}</li>
{/section}
</ul>
{else}
<table class="viewIngredientTable">
    <tbody>
{section name=row loop=$recipe.ingredients[set].rows}
<tr><td class="amountCell">{$recipe.ingredients[set].rows[row].amount}</td><td class="descCell"><span class="ingredient" id="ing_{$recipe.ingredients[set].id}_{$smarty.section.row.index}">{$recipe.ingredients[set].rows[row].description}</span><script type="text/javascript">new Draggable('ing_{$recipe.ingredients[set].id}_{$smarty.section.row.index}', {literal}{revert: true}{/literal});</script></td></tr>
{/section}
    </tbody>
</table>
{/if}
{/section}
</div>
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
</div>
{include file="skin:recipenav.tpl"}
{include file="skin:footer.tpl"}
