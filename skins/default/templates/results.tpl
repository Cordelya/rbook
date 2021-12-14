{if $controller == 'recipe' and $action == 'index'}
<div id="sortResults">
{getParameterizedMessage key=SortBy}{param}{if $sortType == 'popular'}{getMessage key="popular"}{else}{link controller=recipe action=sort_by arg=popular}{getMessage key=popular}{/link}{/if}{/param}{param}{if $sortType == 'recent'}{getMessage key="recent"}{else}{link controller=recipe action=sort_by arg=recent}{getMessage key=recent}{/link}{/if}{/param}{/getParameterizedMessage}
</div>
{/if}
<div id="results">
{if count($results) > 0}
{section name=res loop=$results}
{if $smarty.section.customer.index_prev < 0}
<h2 class="results">{$result_name}</h2>
{if $display_count}
<p>{getMessage key=searchResults1} {$result_count} {getMessage key=searchResults2}.</p>
{/if}
{/if}
{include file="skin:result.tpl"}
{sectionelse}
{/section}
{if $number_of_pages > 1}
<div class="resultNav">
  {if $has_previous }
	{if $controller == 'mine'}
		{link controller=mine action=results arg=$prev_page}&laquo;&nbsp;{getMessage key=prev}{/link}&nbsp;
	{else}
		{link controller=recipe action=results arg=$prev_page}&laquo;&nbsp;{getMessage key=prev}{/link}&nbsp;
	{/if}
  {else}
    &laquo;&nbsp;&nbsp;{getMessage key=prev}
  {/if}
  &nbsp;&nbsp;
  {section name=page loop=$pages}
     {if $pages[page].id == $page}
       {$pages[page].id}
     {else}
	 	{if $controller == 'mine'}
		  {link controller=mine action=results arg=$pages[page].id}{$pages[page].id}{/link}
		{else}
		  {link controller=recipe action=results arg=$pages[page].id}{$pages[page].id}{/link}
		{/if}
     {/if}
     &nbsp;&nbsp;
  {/section}
  &nbsp;&nbsp;
  {if $has_next}
		{if $controller == 'mine'}
		  {link controller=mine action=results arg=$next_page}{getMessage key=next}&nbsp;&nbsp;&raquo;{/link}&nbsp;
		{else}
		  {link controller=recipe action=results arg=$next_page}{getMessage key=next}&nbsp;&nbsp;&raquo;{/link}&nbsp;
		{/if}		  
  {else}
     {getMessage key=next}&nbsp&nbsp;&raquo;
  {/if}
</div>
{/if}
{else}
<h2 class="results">{getMessage key=sorry}</h2>
<p>{getMessage key=noresults}</p>
{/if}
</div>