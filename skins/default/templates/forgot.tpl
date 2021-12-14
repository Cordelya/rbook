{include file="header.tpl"}

<p>{getMessage key=emailToRecover}</p>
{form action=retrieve_password controller=user}
<label for="email">{getMessage key=Email}:</label>&nbsp;&nbsp;<input id="email" size="30" type="text" name="email"/>
<div id="loginButtonDiv">
<input type="submit" value="{getMessage key=Submit}"/>
</div>
{/form}

{include file="footer.tpl"}