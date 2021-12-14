{include file="skin:header.tpl"}
<div>
<img id="captchaImg" src="{buildLink controller=user action=captcha_image cache_defeat=true}" alt="captcha image"/>
</div>
{form action=submit_captcha controller=user}
<p id="captchaParagraph">{getMessage key=pleaseEnterCaptcha}</p>
<input id="captchaInput" type="text" name="phrase" maxlength="5" /><input id="captchaSubmit" value="{getMessage key="submitChallenge"}" type="submit"/>
{/form}
{include file="skin:footer.tpl"}