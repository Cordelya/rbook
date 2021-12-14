{include file="skin:header.tpl"}

<h2>{getMessage key=help}</h2>

<p class="help">{getMessage key=helptext1}</p>

<p class="help">{getMessage key=helptext2}</p>

<p class="help">{getMessage key=helptext3}</p>

<p class="help">{getMessage key=helptext4}</p>

<p class="help">{getMessage key=helptext5} <a href="{$purl}" onclick="window.external.AddSearchProvider(this.href, '{$picon}', '{$pname}', ''); return false;">{getMessage key=installplugin}</a></p>

{include file="skin:footer.tpl"}