<?php
if ($comments) {
	foreach($comments as $comment) {
		require 'inc/blocks/comment.php';
	}
}