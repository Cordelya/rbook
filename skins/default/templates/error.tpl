{include file="skin:header.tpl"}

<div id="errorBlock">
{getMessage key=AnErrorHasOccurred}
</div>
<div class="errorDetails">
<table>
<tbody>
<tr>
<td>{getMessage key=ErrorNumber}</td><td>{$errorNumber}</td></tr>
<td>{getMessage key=ErrorMessage}</td><td>{$errorString}</td></tr>
<td>{getMessage key=ErrorFile}</td><td>{$errorFile}</td></tr>
<td>{getMessage key=ErrorLine}</td><td>{$errorLine}</td></tr>
</tr>
</tbody>
</table>
</div>
{include file="skin:footer.tpl"}
