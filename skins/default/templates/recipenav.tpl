<div id="rightbox">
{if $loggedin}
{include file="skin:grocerybox.tpl"}
{/if}
{if $loggedin or $hasComments}
{include file="skin:commentbox.tpl"}
{/if}
</div>
