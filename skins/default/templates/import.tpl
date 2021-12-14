{include file="skin:header.tpl"}
<div id="createExport">{link controller=exporter action=show_exporter}{getMessage key=exportTheRepository}{/link}</div>
<div  class="warning">{getMessage key=importWarning}</div>
{if $hasImportFiles}
{form action=import controller=exporter}
<p>{getMessage key=selectFileToImport}</p>
<table class="withborder" id="importTable">
<thead>
<tr>
<th>&nbsp;</th>
<th class="fileColumn">{getMessage key=File}</th>
<th class="createdColumn">{getMessage key=Created}</th>
<th class="deleteColumn">{getMessage key=DeleteQuestion}</th>
</tr>
</thead>
<tbody>
{section name=importFile loop=$importFiles}
<tr><td><input type="radio" name="importFile" {if $smarty.section.importFile.first}checked="checked"{/if} value="{$importFiles[importFile].name}"/></td>
<td class="fileColumn">{link action=download_export controller=exporter arg=$importFiles[importFile].id}{$importFiles[importFile].name}{/link}</td>
<td class="createdColumn">{$importFiles[importFile].created}</td>
<td class="deleteColumn">{link class_name=ebutton action=delete_import controller=exporter arg=$importFiles[importFile].id}[{getMessage key=delete}]{/link}</td>
</tr>
{/section}
</tbody>
</table>
<div style="margin-top: 20px">
<input type="submit" value="Import"/>
</div>
{/form}
{else}
{getMessage key=noImportFiles}
{/if}
{include file="skin:footer.tpl"}
