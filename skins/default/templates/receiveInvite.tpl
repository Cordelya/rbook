{include file="skin:header.tpl"}
<script type="text/javascript">
  this.onload = initProcessInvite;
</script>
<div class="standalone">
<form method="POST">
<input type="hidden" name="code" value="{$invitation_code}"/>
<table class="loginTable">
<tbody>
<tr>
<td class="loginTable"><label for="email">{getMessage key=verifyEmail}</label></td><td class="loginTable"><input name="email" id="email" type="text" maxlength="{$user_email_length}" size="25"/></td>
</tr>
<tr>
<td class="loginTable"><label for="password">{getMessage key=NewPassword}</label></td><td class="loginTable"><input name="password" id="password" type="password" maxlength="{$user_password_length}" size="25"/></td>
</tr>
<tr>
<td class="loginTable"><label for="vpassword">{getMessage key=ConfirmNewPassword}</label></td><td class="loginTable"><input name="vpassword" id="vpassword" type="password" maxlength="{$user_password_length}" size="25"/></td>
</tr>
</tbody>
</table>
<div>
<input id="submitButton" type="submit" value="Submit"/>
</div>
</form>
{include file="skin:footer.tpl"}
