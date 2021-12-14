<li>{link controller=recipe action=index}{getMessage key=home}{/link}</li>
{if not $is_mobile}
<li id="conversionLink">{link controller=conversion action=index}{getMessage key=conversion}{/link}</li>
{/if}
{if ($loggedin && not $readonly) or $is_mobile}
<li>{link controller=grocery action=index}{getMessage key=groceryList}{/link}</li>
{/if}
<li>{link controller=guestbook action=index}{getMessage key=guestbook}{/link}</li>
<li>{link controller=help action=index}{getMessage key=help}{/link}</li>