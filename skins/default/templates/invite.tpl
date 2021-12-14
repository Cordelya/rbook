{include file="skin:header.tpl"}
<script type="text/javascript">
  this.onload = initInvite;
</script>
<div>
{form action=process_invite controller=user}
<table class="loginTable">
<tbody>
<tr><td class="loginTable"><label for="name">{getMessage key=Name}:</label></td><td class="loginTable"><input id="name" type="text" name="name" size="40" maxlength="{$user_name_length}"/></td></tr>

<tr><td class="loginTable"><label for="email">{getMessage key=Email}:</label></td><td class="loginTable"><input id="email" type="text" name="email" size="40" maxlength="{$user_email_length}"/></td></tr>
</tbody>
</table>
<div style="margin-top: 20px">
<div>{getMessage key=personalMessage}</div>
<textarea name="message" cols="47" rows="5">
</textarea>
</div>
<div style="margin-top: 20px">
<input disabled="disabled" id="submitButton" type="submit" value="{getMessage key=Submit}"/>
</div>
{/form}
</div>

{include file="skin:footer.tpl"}
