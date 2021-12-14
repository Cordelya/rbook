{include file="skin:header.tpl"}
<h2>{getMessage key=deletePictures}: {$recipe.title}</h2>

{section name=img loop=$recipe.images}
<p style="margin-bottom: 10px">
<img src="{$recipe.images[img].pic}" alt="{$recipe.images[img].caption}" />
{if isset($recipe.images[img].caption) && $recipe.images[img].caption != ''}
<br />
{$recipe.images[img].caption}
{/if}
<br />
{link controller=recipe action=remove_picture arg=$recipe.images[img].id}{getMessage key=deleteThisImage}{/link}
</p>
{/section}

<div style="margin-top: 10px">
{link controller=recipe action=view arg=$recipe.id}{getMessage key=backToRecipe} {$recipe.title}{/link}
</div>

{include file="skin:footer.tpl"}

