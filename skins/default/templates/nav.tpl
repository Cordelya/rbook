<div id="leftbox">
<div class="box">
<h2>{getMessage key=Profile}</h2>
{if isset($email) }
<div class="boxText">
{getMessage key=User}: {link action=edit_profile controller=user}{$username}{/link}
</div>
<div class="boxText">
{getMessage key=recipecount}: {link action=author controller=recipe arg=$userId}{$user_recipe_count}{/link}
</div>
<div class="boxText">
{link controller=mine action=index}{getMessage key=myRecipes}{/link}
</div>
<div class="boxText">
{link controller=user action=logout class_name=ebutton}[{getMessage key=logout}]{/link}
</div>
{else}
{form controller=user action=login method=post}
<div>
<label for="nameField">{getMessage key=UserName}</label>:<br/>
<input size="23" type="text" name="user" id="nameField" value="{$userName}"/>
</div>
<div><label for="passwordField">{getMessage key=Password}</label>:<br/>
<input size="23" maxlength="{$user_password_length}" type="password" name="password" id="passwordField"/>
</div>
<div>
  <input type="checkbox" name="saveid" id="saveIdButton" checked="checked"/><label for="saveIdButton">{getMessage key=rememberme}</label><br/>
</div>
<div id="loginButtonDiv">
<input id="loginButton" type="submit" value="{getMessage key=Login}"/>
</div>
<div>
{link controller=user action=forgot}{getMessage key=forgotPassword}{/link} <br/>{if $allow_registration}
{link controller=user action=register}{getMessage key=newUser}{/link}{/if}
</div>
{/form}
{/if}
</div>
<div class="box">
<h2>{getMessage key=Categories}</h2>
{include file="skin:categorybox.tpl"}
</div>
{if isset($email) and ($enable_invitations or $admin)}
<div class="box">
<h2>{getMessage key=Invitations}</h2>
{if $enable_invitations}
<div class="boxText">
{getMessage key=remainingInvitations}: {$remaining_invitations}
</div>
{if $remaining_invitations != 0}
<div class="boxText">
{link class_name=ebutton action=invite controller=user}[{getMessage key=invite}]{/link}
</div>
{/if}
{else}
<div class="boxText">
{getMessage key=invitationsNotEnabled}
</div>
{/if}
</div>
{/if}
{if $admin}
{include file="skin:adminbox.tpl"}
{/if}
<div class="box">
<h2>{getMessage key=About}</h2>
<div style="text-align: center; margin: auto; " ><a style="text-align: center;" href="http://rbook.sourceforge.net/">rbook version {$rbook_version}</a></div>
</div>
</div>
