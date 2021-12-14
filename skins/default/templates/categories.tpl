{include file="skin:header.tpl"}
{literal}
<script type="text/javascript">
window.onload = function() {
  $("nameField").onkeypress = newCategoryOnReturn;
}
</script>
{/literal}
{form id=categories action=delete controller=categories}
<table class="withborder" id="categoryTable">
<thead>
<tr>
<th>&nbsp;</th><th>{getMessage key=Category}</th>
</tr>
</thead>
<tbody>
{section name=cat loop=$editCategories}
<tr>
<td><input type="checkbox" name="cat{$categories[cat].id}"/></td>
<td>{$categories[cat].name}</td>
</tr>
{/section}
</tbody>
</table>
<div style="text-align: left;" class="buttonRow">
<input type="button" value="{getMessage key=Delete}" id="deleteCategoryButton"/>{ays id=ays button=deleteCategoryButton evt=deleteCategory}
</div>
{/form}
{form action=add controller=categories}
<h2>{getMessage key=newCategory}</h2>
<div>
<label for="nameField">{getMessage key=categoryName}:</label>&nbsp;<input maxlength="{$category_name_length}" type="text" name="name" id="nameField"/>&nbsp;<input id="newButton" type="submit" name="new" value="{getMessage key=categoryNew}"/>
</div>
<input type="hidden" id="actionField" name="action" value=""/>
{/form}
{include file="skin:footer.tpl"}
