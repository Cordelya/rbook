<?php
header('Content-type: application/xml; charset="utf-8"',true);
require_once("./init.php");
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
  <ShortName><?php echo(APPTITLE); ?></ShortName>
  <Description><?php echo(APPTITLE); ?> search plugin.</Description>
  <Url type="text/html" method="get" template="<?php echo("http://" . $_SERVER['SERVER_NAME'] . APPROOT); ?>recipe/search/?search={searchTerms}" />
  <Image width="16" height="16"><?php echo("http://" . $_SERVER['SERVER_NAME'] . APPROOT . "skins/" . SKIN . "/images/favicon.ico"); ?></Image>
  <Developer>Maschine</Developer>
  <InputEncoding>UTF-8</InputEncoding>
  <moz:SearchForm><?php echo("http://" . $_SERVER['SERVER_NAME'] . APPROOT); ?></moz:SearchForm>
  <moz:UpdateUrl><?php echo("http://" . $_SERVER['SERVER_NAME'] . APPROOT); ?>searchplugin.src</moz:UpdateUrl>
  <moz:UpdateInterval>14</moz:UpdateInterval>
</OpenSearchDescription>