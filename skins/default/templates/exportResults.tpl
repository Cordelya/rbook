{include file=skin:header.tpl}
<table id="ert">
    <tbody>
        <tr>
            <td class="ert_name">{getMessage key=ExportFile}</td>
            <td class="ert_value">{link controller=exporter action=download_export arg=$exportFileId}{$exportFile}{/link}</td>
        </tr>
        <tr>
            <td class="ert_name">{getMessage key=numORecipes}</td>
            <td class='ert_value'>{$numRecipes}</td>
        </tr>
        <tr>
            <td class="ert_name">{getMessage key=numOCategories}</td>
            <td class="ert_value">{$numCategories}</td>
        </tr>
        <tr>
            <td class="ert_name">{getMessage key=numOUsers}</td>
            <td class="ert_value">{$numUsers}</td>
        </tr>
    </tbody>
</table>
{include file=skin:footer.tpl}