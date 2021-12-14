{include file="skin:header.tpl"}
{if $newuser }
<h2>{getMessage key=addUser}</h2>
{elseif $editprofile}
<h2>{$user_name}'s {getMessage key=UProfile}</h2>
{else}
<h2>{getMessage key=editUser}</h2>
{/if}
<form action="{$action}" method="post">
<table class="formTable">
<tbody>
<tr>
<td id="usernameFieldLabel" class="nameField">{getMessage key=UserName}</td>
<td>{if $newuser }<input size="50" maxlength="50" id="usernameField" type="text" name="username" value="{$user_username}"/>{else}{$user_username}{/if}</td>
</tr>
<tr>
<td class="nameField"><label for="nameField">{getMessage key=Name}</label></td><td class="loginTable"><input size="50" maxlength="{$user_name_length}" id="nameField" type="text" name="name" value="{$user_name}"/></td>
</tr>
{if isset($pageErrors.user_email)}
<tr>
<td colspan="2" class="error">
{getMessage key=$pageErrors.user_email}
</td>
</tr>
{/if}
<tr>
<td class="nameField"><label for="emailField">{getMessage key=Email}</label></td><td class="loginTable"><input size="50" maxlength="{$user_email_length}" id="emailField" type="text" name="email" value="{$user_email}"/></td>
</tr>
{if $editprofile}
<tr>
<td class="nameField"><label for="favorite">{getMessage key=Favorite}</label></td><td><input size="50" maxlength="100" id="favorite" name="favorite" value="{$profile_favorite}"/></td>
</tr>
<tr>
{if isset($pageErrors.website)}
<tr>
<td colspan="2" class="error">
{getMessage key=$pageErrors.website}
</td>
</tr>
{/if}
<td class="nameField"><label for="website">{getMessage key=Website}</label></td><td><input size="50" maxlength="100" id="website" name="website" value="{$profile_website}"/></td>
</tr>
{/if}
</tbody>
</table>
{if not $editprofile }
<div class="formBlock">
{if $newuser}
{getMessage key=initialPassword}
{else}
{getMessage key=resetPasswordDesc}<input type="submit" name="reset" value="{getMessage key=resetPassword}"/>
{/if}
</div>
<div>
<h2>{getMessage key=rightsLevel}</h2>
<dl>
<dt><input type="radio" {if $user_readonly} checked="checked" {/if} id="readonlyLevel" name="rightsLevel" value="readonly"/>&nbsp;<label for="readonlyLevel">{getMessage key=Readonly}</label></dt>
<dd>{getMessage key=readonlyDesc}</dd>
<dt><input type="radio" {if !$user_readonly && !$user_admin} checked="checked" {/if} id="normalLevel" name="rightsLevel" value="normal"/>&nbsp;<label for="normalLevel">{getMessage key=Normal}</label></dt>
<dd>{getMessage key=normalDesc}</dd>
<dt><input type="radio" {if !$user_readonly && $user_admin} checked="checked {/if} id="adminLevel" name="rightsLevel" value="admin"/>&nbsp;<label for="adminLevel">{getMessage key=Admin}</label></dt>
<dd>{getMessage key=adminDesc}</dd>
</dl>
<input type="checkbox" name="disabled" id="disabledField" {if $user_disabled } checked="checked" {/if}/>&nbsp;</label for="disabledField">{getMessage key=disabled}</label>
</div>
{else}

<h2>{getMessage key=Passwords}</h2>
<p style="margin-top: 20px">{getMessage key=leaveBlank}</p>
{include file="skin:cpForm.tpl"}
{/if}
<div class="buttonRow">
{if not $editprofile}
<input type="hidden" name="id" value="{$user_id}"/>
{/if}
<input type="submit" name="save" value="{getMessage key=Save}"/>
</div>

</form>
<script type="text/javascript">
{literal}
window.onload = function() {
  var f = $("nameField");
  if(f) {
    f.focus();
  }
  if($('usernameField')) {
	  new UserNameValidator('usernameField', '{/literal}{$root_path}{literal}');
  }
}
{/literal}
</script>
{include file="skin:footer.tpl"}
