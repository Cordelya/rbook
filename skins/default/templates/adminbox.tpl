<div class="box">
<h2>{getMessage key=Admin}</h2>
<ul class="menulist">
<li class="menuitem">{link controller=user action=index}{getMessage key=Users}{/link}</li>
<li class="menuitem">{link controller=categories action=index}{getMessage key=Categories}{/link}</li>
{if $useImport}
<li class="menuitem">{link controller=exporter action=index}{getMessage key=ExportImport}{/link}</li>
{/if}
</ul>
{if !$useImport}
<div class="boxText">{getMessage key=ImpExpDisabled}</div>
{/if}
</div>
