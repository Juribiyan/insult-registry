<?php
require_once 'config.php';
require_once 'inc/io.php';

$actions = array('new', 'new_comment', 'get_comments');
if (!in_array($_GET['action'], $actions)) {
	exitWithError(400, 'Недопустимое действие');
}

$fn = '_'.$_GET['action'];
$fn();

function _new() {
	require_once 'inc/registry.php';
	$reg = new Registry();

	if (ENABLE_CAPTCHA)
		checkCaptcha();

	$input = validate($_POST, [
		'offender' => ['minLength'=>1, 'maxLength'=>100, 'optional'=>true],
		'victim' => ['minLength'=>1, 'maxLength'=>100],
		'link' => ['url'=>true, 'maxLength'=>2048, 'optional'=>true], // but only optional if the pic is set, check later
		'comment' => ['markup'=>true],
		'pic' => [
			'pattern' => '/^'.PIC_URL_PATTERN.'$/',
			'extract_match' => 1,
			'optional' => true]
	]);

	if (!@$input['link'] && !@$input['pic']) {
		exitWithError(400, "Либо пруфпик, либо пруфлинк должен присутствовать");
	}

	$site = mb_strtolower(parse_url($input['link'], PHP_URL_HOST));

	$entry_id = $reg->submitEntry([
		'offender' => mb_strtolower($input['offender'])==mb_strtolower(DEFAULT_NAME) ? "" : $input['offender'],
		'victim' => $input['victim'],
		'link' => $input['link'],
		'pic' => $input['pic'],
		'site' => $site,
		'comment' => $input['comment']
	]);

	if (@$_GET['ajax']) {
		exitWithJSON(["entry_id" => $entry_id]);
	}

	header("Location: /entry/$entry_id");
	die();
}

function _new_comment() {
	require_once 'inc/comments.php';
	$comments_class = new Comments();

	if (ENABLE_CAPTCHA_COMMENTS)
		checkCaptcha();

	$input = validate($_POST, [
		'entry_id' => ['numeric'=>true],
		'comment' => ['markup'=>true]
	]);

	$comment_id = $comments_class->addComment([
		'entry_id' => $input['entry_id'],
		'content' => $input['comment']
	]);

	if (@$_GET['ajax']) {
		exitWithJSON(['id' => $comment_id]);
	}

	header("Location: /entry/".$input['entry_id']);
	die();
}

function _get_comments() {
	require_once 'inc/comments.php';
	$comments_class = new Comments();

	$input = validate($_GET, [
		'entry_id' => ['numeric'=>true],
		'after' => ['numeric'=>true]
	]);

	$comments = $comments_class->getComments($input['entry_id'], $input['after']);
	require 'inc/blocks/comments.php';
}


?>