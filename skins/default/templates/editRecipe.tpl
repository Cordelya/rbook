{config_load file="skin.conf"}
{include file="skin:header.tpl"}

{form name=editForm id=editForm action=save controller=recipe}
<ul  id="editRecipeTabs" class="tabs">
<li class="unselectedTab1"><a class="unselectedTab1" id='informationTab' href="#">{getMessage key=Information}</a></li>
<li class="unselectedTab1"><a class="unselectedTab1" id="ingredientsTab" href="#">{getMessage key=Ingredients}</a></li>
<li class="unselectedTab1"><a class="unselectedTab1" id="stepTab" href="#">{getMessage key=Steps}</a></li>
<li class="unselectedTab1"><a class="unselectedTab1" id="noteTab" href="#">{getMessage key=Notes}</a></li>
</ul><br/>
<div id="informationModule" class="tabContainer">

<table id="recipeInformationTable" class="recipeTable">
<tr>
<td class="nameField"><label for="titleField">{getMessage key=Title}:</label>
<td class="valueField"><input class="tf" size="40" maxlength="{$recipe_name_length}" id="titleField" type="text" value="{$recipe.title}" name="title"/>
</tr>
<tr>
<td class="nameField"><label for="preheatField">{getMessage key=Preheat}:</label></td>
<td class="valueField"><input class="tf" size="40" maxlength="{$recipe_preheat_length}" id="preheatField" type="text" value="{$recipe.preheat}" name="preheat"/></td>
</tr>
<tr>
<td class="nameField"><label for="sourceField">{getMessage key=Source}:</label></td>
<td class="valueField"><input class="tf" size="40" maxlength="{$recipe_source_length}" id="sourceField" type="text" value="{$recipe.source}" name="source"/></td>
</tr>
<tr>
<td><label for="servesField">{getMessage key=Serves}:</label></td>
<td><input maxlength="4" id="servesField" type="text" value="{$recipe.serves}" name="serves"/></td>
</tr>
<tr>
<td class="nameField"><label for="categories">{getMessage key=Categories}:</label><br/><span class="helptext">{getMessage key=commaSeparated}</span></td>
<td class="valueField">
<input size="40" type="text" id='categories' name="categories" value="{$recipe.categoryList}"/>
<select name="categoryList" id="categoryList">
{html_options values=$catIDs output=$catNames}
</select>
<input id="addCatButton" type="button" value="{getMessage key=Add}"/>
</td>
</tr>
<tr>
<td class="nameField"><label for="preptime">{getMessage key=PrepTimeInMins}</label></td>
<td class="valueField"><input maxlength="4" type="text" id="preptime" name="preptime" value="{$recipe.preptime}"/></td>
</tr>
<tr>
<td class="nameField"><label for="cooktime">{getMessage key=CookTimeInMins}</label></td>
<td class="valueField"><input maxlength="4" type="text" id="cooktime" name="cooktime" value="{$recipe.cooktime}"/></td>
</tr>
</table>
<div><label for="descriptionArea">{getMessage key=Description}</label></div>
<textarea name="description" rows="3" cols="70" id="descriptionArea">{if isset($recipe.description)}{$recipe.description}{/if}</textarea>
</div>

<div id="ingredientsModule" style="display:none" class="tabContainer">
<div id="setSection" >
</div>
<input type="button" id="addIngrField" value="{getMessage key=AddIngredientSet}"/>
</div>

<div id="stepModule"   class="tabContainer">
<div class="helptext">{getMessage key=sortStepsHelp}</div>
<ul id="steps">
</ul>
<br/>

<input id="addStepButton" type="button" value="{getMessage key=AddStep}"/>

</div>

<div id="noteModule" style="display:none" class="tabContainer">
<textarea style="margin-top: 1em; margin-bottom: 1em;" rows="3" cols="70" name="note" id="noteTextArea">{if isset($recipe.note)}{$recipe.note}{/if}</textarea>
</div>

<div class="buttonRow rightButtonRow" >
{if not $isNew}<input name="discardAndView" type="submit" value="{getMessage key=DiscardAndView}"/>{/if}

<input name="saveAndEdit" type="submit" value="{getMessage key=Save}"/>

<input name="saveAndView" type="submit" value="{getMessage key=SaveAndView}"/>
<input id="selectedTab" name="selectedTab" type="hidden" value="information"/>

</div>
{/form}
{section name=stepdiv loop=$recipe.steps}
<div style="display:none" id="steptext{$smarty.section.stepdiv.index}">{$recipe.steps[stepdiv]}</div>
{/section}
<script type="text/javascript" language="Javascript">
{literal}
var rb = {
{/literal}
  Ingredients : '{getMessage key=Ingredients}',
  deleteButton : '{getMessage key=delete}',
  Description : '{getMessage key=Description}',
  Amount : '{getMessage key=Amount}',
  upButton : '{getMessage key=upButton}',
  downButton : '{getMessage key=downButton}',    
  Add : '{getMessage key=AddIngredient}',
  SortIngredient: '{getMessage key=sortIngredientsHelp}',
  deleteIngredientSetTooltip : '{getMessage key=deleteIngredientSetTooltip}'
{literal}
};
var options = {
{/literal}
    ingredientLength: '{$ingredient_description_length}',
    amountLength: '{$ingredient_amount_length}',
	buttonDisplayMode : '{#buttonDisplay#}',
	skinImages : '{$skin_img}',
	deleteIcon : {if #deleteIcon#}true{else}false{/if},
	selectedTab : '{if $selectedTab == 'note' or $selectedTab == 'step' or $selectedTab == 'ingredients'}{$selectedTab}{else}information{/if}'
{literal}
};

var re = new RecipeEditor(rb, options);
window.onload = function() {
  re.initEditor.call(re);
}
$('addStepButton').onclick = function(evt) {
	re.addStep.call(re);
}
$('addCatButton').onclick = function(evt) {
	re.addCat.call(re);	
}
$('addIngrField').onclick = function(evt) {
  re.addIngredientSet.call(re);
}
{/literal}
var sets;
{section name=set loop=$recipe.ingredients}
sets = [
{section name=row loop=$recipe.ingredients[set].rows}
['{$recipe.ingredients[set].rows[row].amount}','{$recipe.ingredients[set].rows[row].description}']
{if not $smarty.section.row.last } , {/if}
{/section}];
re.editIngredientSet("{$recipe.ingredients[set].id}", "{$recipe.ingredients[set].name}", sets); 
{/section}

var steps;
{section name=step loop=$recipe.steps}
re.addStepWithText($("steptext{$smarty.section.step.index}").innerHTML, false);
{/section}
re.makeStepsSortable();

</script>
{include file="skin:footer.tpl"}
