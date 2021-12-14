{include file="skin:header.tpl"}
{literal}
<script type="text/javascript">
this.onload = function() {
  $('nameField').focus()

}
</script>
{/literal}
<div id="loginModule" class="module">
<div class="moduleHeader">
<h2>Login</h2>
</div>
<div class="innerModule">
{form action=login controller=user}
{if isset($redirectTo) }
<input type="hidden" name="redirectto" value="{$redirectTo}"/>
{/if}
{include file="skin:loginTable.tpl"}
{/form}
</div>
<div class="moduleFooter">&nbsp;</div>
</div>
{include file="skin:footer.tpl"}
