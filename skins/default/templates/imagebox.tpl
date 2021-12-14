{if isset($recipe.images) && count($recipe.images) > 1}
{literal}
<script type="text/javascript">
<!--
ss = new slideshow("ss");
ss.timeout = 4000;
{/literal}
{section name=img loop=$recipe.images}
s = new slide();
s.src =  "{$recipe.images[img].pic}";
s.text = "{$recipe.images[img].caption}";
ss.add_slide(s);
{/section}
{literal}
function change_pause_play() {
  if (ss.timeoutid > 0) {
    document.getElementById("pauseplay").innerHTML = ">"; ss.pause();
	} else {
    document.getElementById("pauseplay").innerHTML = "||"; ss.play(); ss.next();
}}
-->
</script>
{/literal}
{/if}
<div id="recipeImage" style="width: 310px; height: {$imageboxheight}px;">
<div style="width: 310px; height: {$imageheight}px;">
<img alt="{$recipe.images[0].caption}" class="recipePic" id="recipepic" src="{$recipe.images[0].pic}"/>
</div>
{if $imagetext}
<div id="imagetext">{$recipe.images[0].caption}</div>
{/if}
{if count($recipe.images) > 1}
<div id="sscontrols">
  <a href="javascript:ss.previous()">&lt;&lt;</a>&nbsp;
  <a href="javascript:change_pause_play();" id="pauseplay">||</a>&nbsp;
  <a href="javascript:ss.next()">&gt;&gt;</a>
</div>
{/if}
</div>

{if count($recipe.images) > 1}
{literal}
<script type="text/javascript">
<!--
if (document.images)
{
ss.set_image(document.images.recipepic);
ss.set_textid("imagetext");

var fadein_opacity = 0.04;
var fadein_img = ss.image;
function fadein(opacity) {
  if (typeof opacity != 'undefined') fadein_opacity = opacity;
  if (fadein_opacity < 0.99 && fadein_img && fadein_img.style &&
	  typeof fadein_img.style.MozOpacity != 'undefined') {

	fadein_opacity += .05;
	fadein_img.style.MozOpacity = fadein_opacity;
	setTimeout("fadein()", 50);
  }
}
ss.post_update_hook = function() { fadein(0.8); }

ss.update();
ss.play();
}
-->
</script>
{/literal}
{/if}
