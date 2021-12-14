{include file="skin:header.tpl"}

<script type="text/javascript">
{literal}
onload=guestbookAddComment;
{/literal}
</script>

<h2>{getMessage key=guestbook}</h2>

<div class="commentAdd">
<a href="#" id="addACommentLink">{getMessage key=AddComment}&nbsp;&raquo;</a>
<div style="display:none" id="addCommentSection">
{form action=create_comment controller=guestbook}
<div>{getMessage key=Name}: <input type="text" id="name" name="name" /></div>
<div>{getMessage key=antispam}: {$no1} + {$no2} = <input type="text" id="antispam" name="antispam" size="2" /></div>
<div><textarea id="addCommentArea" name="commentArea" rows="5" cols="50"></textarea></div>
<div id="addCommentButtonRow">
<input type="submit" value="{getMessage key=AddComment}" name="addComment"/>
</div>
{/form}
</div>
</div>

{section name=comment loop=$comments}
<div class="commentHead">
<strong>{$comments[comment].name}</strong>
{$comments[comment].postdate}
{if $user_admin}
&nbsp;{link controller=guestbook action=delete_comment arg=$comments[comment].id}({getMessage key="delete"}){/link}
{/if}
</div>
<div class="commentText">
{$comments[comment].comment}
</div>
{/section}
{include file="skin:footer.tpl"}
