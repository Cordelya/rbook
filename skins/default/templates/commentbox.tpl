<div id="commentsDiv" class="box">
<h2>{getMessage key="Comments"}</h2>
{if not $userHasComment and $loggedin}
<a href="#" id="addACommentLink">{getMessage key=AddComment}&nbsp;&raquo;</a>
<div style="display:none" id="addCommentSection">
{form action=create_comment controller=recipe}
<div class="controlRow">
<label for="starRating">{getMessage key=Rating}</label><select id="starRating" name="starRating"><option value="notspecified">{getMessage key=notSpecified}</option><option value="0">{getMessage key="0stars"}</option><option value="1">{getMessage key="1star"}</option><option value="2">{getMessage key="2stars"}</option><option value="3">{getMessage key="3stars"}</option><option value="4">{getMessage key="4stars"}</option><option value="5">{getMessage key="5stars"}</option></select>
</div>
<textarea id="addCommentArea" name="commentArea">
</textarea>
<div id="addCommentButtonRow">
<input type="submit" value="{getMessage key=AddComment}" name="addComment"/>
</div>
{/form}
</div>
{/if}

{section name=comment loop=$comments}
<div class="commentSection">
{if isset($comments[comment].rating)}
<div class="starRating">{starRating number=$comments[comment].rating}</div>
{/if}
<p class="comment">{$comments[comment].comment}</p>
<div>{displayUserLink userid=$comments[comment].userid} {$comments[comment].submitted}</div>
{if $user_id == $comments[comment].userid or $user_admin}
{link controller=recipe action=delete_comment arg=$comments[comment].id}({getMessage key="delete"}){/link}
{/if}
</div>
{if not $smarty.section.comment.last}
<hr/>
{/if}
{/section}
</div>
