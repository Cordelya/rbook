{include file="skin:header.tpl"}
<h2>{getMessage key=addPictureTo} {$recipe.title}</h2>
{form enctype="multipart/form-data" action=save_picture controller=recipe}
    <table class="recipeTable">
        <tbody>
            <tr>
                <td><label for="captionName">{getMessage key=addPictureCaption}</label></td>
                <td style="text-align: left;"><input size="50" type="text" id="captionName" name="caption"/></td>
            </tr>
            <tr>
                <td><label for="imageFile">{getMessage key=addPicturePathToImage}</label></td>
                <td><input id="imageFile" size="50" type="file" name="imagefile"/></td>
            </tr>
        </tbody>
    </table>
<div style="margin-top: 10px">
<input type="submit" value="{getMessage key=addPictureAddPicture}"/>
</div>
{/form}
{include file="skin:footer.tpl"}

