{include file="skin:header.tpl"}
<div style="padding-bottom: 10px" id="newUserContainer">
{link action=edit controller=user}{getMessage key=NewUser}{/link}
</div>
{form id=users action=deleteSelected controller=user}
<table class="withborder" id="userMgmtTable">
<thead>
<tr>
<th>&nbsp;</th><th>{getMessage key=UserName}<th>{getMessage key=Name}</th><th>{getMessage key=Email}</th><th>{getMessage key=Type}</th><th>{getMessage key=Status}</th>
</tr>
</thead>
<tbody>
{section name="user" loop="$users"}
<tr>
<td><input {if $users[user].id == 1} disabled="yes" {/if} type="checkbox" name="selectUser{$users[user].id}"/></td>
<td>{link controller=user action=edit arg=$users[user].id}{$users[user].username}{/link}</td>
<td>{$users[user].name}</td>
<td>{$users[user].email}</td>
<td>{if $users[user].admin}{getMessage key=admin}{elseif $users[user].readonly}{getMessage key=readonly}{else}{getMessage key=normal}{/if}</td>
<td>{if $users[user].disabled}{getMessage key=disabled}{elseif $users[user].invited}{getMessage key=invited}{else}{getMessage key=active}{/if}</td>
</tr> 
{/section}
</tbody>
</table>
<input type="hidden" id="actionField" name="action" value=""/>
<div class="buttonRow">
<input type="button" value="{getMessage key=DeleteSelected}" id="deleteUserButton"/>{ays id=ays button=deleteUserButton evt=deleteUser}
</div>
{/form}


{include file="skin:footer.tpl"}
