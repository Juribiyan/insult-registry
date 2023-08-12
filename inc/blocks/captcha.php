<script>captchaTimeout = <?= CAPTCHA_LIFE ?></script>
<link rel="stylesheet" href="/static/captcha.css">
<div class="captchawrap cw-initial yesscript" title="Обновить капчу">
	<div class="captcha-show captcha-msg">Показать капчу</div>
	<img class="captchaimage" valign="middle" border="0" alt="{t}Captcha image{/t}">
	<div class="rotting-indicator"></div>
	<div class="rotten-msg captcha-msg">Капча протухла</div>
</div>
<noscript><iframe class="captchawrap" src="/nojscaptcha.php" frameborder="0" width="150" height="32"></iframe></noscript>
<span>→</span>
<input type="tel" name="captcha" required placeholder="Капча">