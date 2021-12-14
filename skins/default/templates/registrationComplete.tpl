{include file="skin:header.tpl"}

{if isset($registrationCode)}
{link action=respond controller=user arg=$registrationCode}Test Link{/link}<br/>
{/if}
<p id="regComplete">{getMessage key=registrationCompleteP}</p>

{include file="skin:footer.tpl"}