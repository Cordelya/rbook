{include file="skin:header.tpl"}

{form action=submit_registration controller=user}
<table class="formTable">
<tbody>
<tr>
<td class="nameField">Name:</td>
<td><input type="text" name="name"/></td>
</tr>
<tr>
<td class="nameField">User name:</td>
<td><input type="text" name="username"/></td>
<tr>
<td class="nameField">Email:</td>
<td><input type="text" name="email"/></td>
</tr>
</tbody>
</table>
<input type="submit" value="{getMessage key=Register}"/>
{/form}

{include file="skin:footer.tpl"}