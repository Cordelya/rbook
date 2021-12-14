{include file="skin:header.tpl"}
<p>{getMessage key=exportMsg}</p>
{form action=export controller=exporter}
<div class="formBlock">
<label for="nameField">{getMessage key=Name}</label>&nbsp;<input id="nameField" name="exportFile" type="text" maxlength="100" size="30" value="{$exportFile}"/></div>
<input type="submit" name="export" value="{getMessage key=Export}"/>
{/form}
{include file="skin:footer.tpl"}
