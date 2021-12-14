{include file="skin:header.tpl"}
<h2>{getParameterizedMessage key=ProfileHeader}{param}{$profileName}{/param}{/getParameterizedMessage}</h2>

<div id="memberSinceDiv">
{getParameterizedMessage key=MemberSince}{param}{$memberSince}{/param}{/getParameterizedMessage}
</div>

<h2>{getParameterizedMessage key=MostRecentRecipesBy}{param}{$profileName}{/param}{/getParameterizedMessage}</h2>
<ul id="profileMostRecentRecipes" class="nolist">
{section name=res loop=$recipeResults}
<li><a href="{$recipeResults[res].url}">{$recipeResults[res].title}</li>
{/section}
</ul>
<div id="recipeSeeAll">
{if $smarty.section.res.total < $profileRecipeCount}
<strong>{link controller=recipe action=author arg=$profileId}{getParameterizedMessage key=SeeAll}{param}{$profileRecipeCount}{/param}{param}{$profileName} &raquo;{/param}{/getParameterizedMessage}{/link}</strong>
{/if}
</div>

{include file="skin:footer.tpl"}
