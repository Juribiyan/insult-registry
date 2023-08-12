<?php
require_once "inc/formatting.php";

$comment['date'] = formatDateShort(strtotime($comment['timestamp']));
?>
<div class="comment" id="comment_<?= $comment['id'] ?>" data-id="<?= $comment['id'] ?>">
	<div class="comment-head post-head">
		<div class="comment-id post-id">#<?= $comment['id'] ?></div>
		<date><?= $comment['date'] ?></date>
	</div>
	<div class="comment-body"><?= $comment['content'] ?></div>
</div>