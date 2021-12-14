<div class="result">

{if $images_enabled}
<table class="resultTable">
<tr>
<td>
   {if isset($results[res].image)}
		<a href="{$results[res].url}"><img alt="{getMessage key=thumbnail}" style="margin-right: 2em; border: 1px solid black; " src="{$results[res].image}"/></a>
   {else}
		<a href="{$results[res].url}"><img style=" border:none; margin-right: 2em; " alt="{getMessage key=noThumbnail}" src="{$skin_img}{getMessage key=noImage}" height="100" width="100"/></a>
   {/if}
</td>
<td>
{/if}<a class="onpage resultLink" href="{$results[res].url}">{$results[res].title}</a> {section name=bl loop=$results[res].buttons}<a href="{$results[res].buttons[bl].url}">[{$results[res].buttons[bl].name}]</a> {/section}<br/>
<div class="resultDescription"><span class="resultField">{getMessage key=submittedBy}</span><strong>&nbsp;&nbsp;{link action=view_profile controller=user arg=$results[res].username}{$results[res].submittedBy}{/link}</strong></div>
<div class="resultDescription"><span class="resultField">{getMessage key=submittedOn}</span><strong>&nbsp;&nbsp;{$results[res].submittedWhen}</strong></div>
{if isset($results[res].description)}
<div class="resultDescription">{$results[res].description}</div>
{/if}
{if isset($results[res].rating) and $results[res].ratingHits > 0}
<div class="resultDescription">
{starRating number=$results[res].rating} {getParameterizedMessage key=votes}{param}{$results[res].ratingHits}{/param}{/getParameterizedMessage}</div>
{/if}
{if $images_enabled}</td>
</tr>
</table>
{/if}
</div>
{if not $smarty.section.res.last}
    <hr class="recipeSeparator"/>
{/if}

