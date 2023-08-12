<?php
require_once 'config.php';
require_once 'inc/io.php';
require_once "inc/formatting.php";
require_once "inc/comments.php";
$comments_class = new Comments();

if (!isset($entry)) {
	$input = validate($_GET, [
		'id' => ['numeric'=>true]
	]);

	require_once 'inc/registry.php';
	$reg = new Registry();

	$entry = $reg->getEntry($input['id']);
}

$entry['id_padded'] = entryNoFull($entry['id']);

$title="Запись №" . $entry['id']
	. " (".(@$entry['offender'] ? $entry['offender'] : "Аноним")
	.' → '.$entry['victim'].")";
require 'inc/blocks/global_header.php';

$nav_state = "entry";
require 'inc/blocks/header.php'; 

$entry_single = true;
require 'inc/blocks/entry.php';

echo '<div class="comments">';
$comments = $comments_class->getComments($entry['id']);
require 'inc/blocks/comments.php';
echo '</div>';

require 'inc/blocks/comment-form.php';

require 'inc/blocks/butt.php'; 
