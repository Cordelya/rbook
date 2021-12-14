<table class="loginTable">
<tr>
<td class="loginTable"><label for="nameField">{getMessage key=UserName}</label>:</td>
<td class="loginTable"><input size="30" type="text" name="user" id="nameField" value="{$userName}"/></td>
</tr>
<tr>
<td class="loginTable"><label for="passwordField">{getMessage key=Password}</label>:</td>
<td class="loginTable"><input size="30" type="password" name="password" id="passwordField"/></td>
</tr>
</table>
<br/>
<div>
<input type="checkbox" name="saveid" id="saveIdButton" checked="checked"/><label class="right" for="saveIdButton">{getMessage key=rememberme}</label><br/><br/>
</div>
<div class="buttonRow">
<input type="submit" value="{getMessage key=Login}"/>
</div>
<div style="margin-top: 20px">
{link controller=user action=forgot}{getMessage key=forgotPassword}{/link}
</div>
