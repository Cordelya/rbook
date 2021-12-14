{include file="skin:header.tpl"}
<p>{getMessage key=replacementMsg}</p>
{form id=pick action=pick_replacement controller=categories}

<table style="width: 85%">
<thead>
<tr>
<th>&nbsp;</th><th>{getMessage key=Category}</th>
</tr>
</thead>
<tbody>
{section name=cat loop=$editCategories}
<tr>
<td><input {if $smarty.section.cat.first} checked="checked" {/if} type="radio" name="category" value="{$editCategories[cat].id}"/></td>
<td>{$editCategories[cat].name}</td>
</tr>
{/section}
</tbody>
</table>
<div class="buttonRow">
<input type="submit" name="continue" value="{getMessage key=Continue}>>"/>
</div>
{/form}
{include file="skin:footer.tpl"}
