{config_load file="skin.conf"}
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
{if $showrss == 'true'}
<link rel="alternate" type="application/rss+xml" title="RSS Feed" href="{$root_path}rss/" />
{/if}
<link rel="search" type="application/opensearchdescription+xml" title="{$appTitle}" href="{$root_path}searchplugin.src"/>
<link type="text/css" media="all" href="{$skin_style}style.css{if #usePHPStylesheet#}.php{/if}?cache={$rbook_version}" rel="stylesheet"/>
<link type="text/css" href="{$skin_style}style-print.css?cache={$rbook_version}" rel="stylesheet" media="print"/>
<link type="text/css" href="{$skin_style}style-handheld.css?cache={$rbook_version}" rel="stylesheet" media="handheld"/>
<link rel="Shortcut Icon" href="{$skin_img}favicon.ico" type="image/x-icon" />
{section name=js loop=$javascripts}
<script type="text/javascript" src="{$javascripts[js]}?cache={$rbook_version}"></script>
{/section}
</head>
<body class="{$pageClass}">
<div id="bodydiv">
<div class="notprintable" id="headerTitle">{$appTitle}</div>
{if $is_mobile && isset($email)}
<div id="loginheader">
{$email} &nbsp;{link action=logout controller=user}[{getMessage key=logout}]{/link}
</div>
{/if}
{include file="skin:topbar.tpl"}
<div id="container">
{if $showNavBar}
{include file="skin:nav.tpl"}
{/if}
<div {if not $showNavBar}style="margin-left:0 !important;" {/if}id="content">
<div id="errorDiv" {if !isset($pageError) }style="display:none"{/if} class="errormessage">{$pageError}</div>
{if isset($pageError)}
<script type="text/javascript">
//<![CDATA[
Element.hide("errorDiv");
new Effect.Appear("errorDiv");
//]]>
</script>
{/if}
{if isset($pageTitle)}
  <h2>{$pageTitle}</h2>
{/if}
