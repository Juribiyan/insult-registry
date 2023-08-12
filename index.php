<?php
require_once 'config.php';
require_once 'inc/registry.php';

$reg = new Registry();

$names = $reg->getNames();
$datalist = '<datalist id="persons">';
foreach($names as $name) {
	$datalist .= '<option value="'. $name['name'] .'"></option>';
}
$datalist .= '</datalist>';

require 'inc/blocks/global_header.php'; 

$nav_state = "index";
require 'inc/blocks/header.php';

?>
<h1>Регистратор оскорблений<sup>β</sup></h1>
<h3>Зарегистрировано оскорблений: <a href="/all"><?= $reg->totalEntryCount() ?></a></h3>
<form action="/api.php?action=new" method="POST" id="entry-form" data-ajaxfn="submitEntry">
	<?= $datalist ?>
	<div class="form-block">
		<label for="offender">Кто был оскорблён?</label>
		<input type="text" name="victim" list="persons" id="victim" placeholder="Человек или социальная группа" required max="100">
		<i class="form-hint">Отдавайте предпочтение нейтральным или наиболее общеупотребимым никнеймам</i>
	</div>
	<div class="form-block">
		<label for="offender">Кем был оскорблён?</label>
		<input type="text" name="offender" list="persons" id="offender" placeholder="Никнейм или иной идентификатор" max="100">
		<i class="form-hint">Значение по умолчанию: «Аноним»</i>
	</div>
	<div class="form-block">
		<label for="link">Пруфлинк:</label>
		<input type="url" name="link" id="link" placeholder="Ссылка на пост" max="2048">
	</div>
	<div class="form-block">
		<label for="link">Пруфпик:</label>
		<input type="url" name="pic" id="pic" placeholder="Ссылка на изображение" max="2048" pattern="^<?= PIC_URL_PATTERN ?>$">
		<i class="form-hint">Используйте <a href="https://catbox.moe" target="_blank">Catbox</a> или <a href="https://imgur.com" target="_blank">Imgur</a></i>
	</div>
	<div class="form-block">
		<label for="link">Комментарий:</label>
		<textarea name="comment" id="comment"></textarea>
	</div>
	<div class="form-block captcha-block">
		<?php if (ENABLE_CAPTCHA) include 'inc/blocks/captcha.php' ?>
		<button type="submit">
			<span>Зарегистрировать</span> <span>оскробление</span>
		</button>
	</div>
</form>
<?php if (ENABLE_CAPTCHA) include 'inc/blocks/butt.php' ?> 