<form action="/api.php?action=new_comment" method="post" id="comment-form" data-ajaxfn="makeComment">
	<input type="hidden" name="entry_id" value="<?= $entry['id'] ?>">
	<div class="form-block">
		<textarea name="comment" id="comment" required placeholder="Комментарий"></textarea>
		<i class="form-hint">Для вставки картинок используйте полные ссылки на <a href="https://catbox.moe" target="_blank">Catbox</a> или <a href="https://imgur.com" target="_blank">Imgur</a> в [квадратных скобках]</i>
	</div>
	<div class="form-block captcha-block">
		<?php if (ENABLE_CAPTCHA_COMMENTS) include 'inc/blocks/captcha.php' ?>
		<button type="submit">
			<span>Запостить</span> <span>комментарий</span>
		</button>
	</div>
</form>