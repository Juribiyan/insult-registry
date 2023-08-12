<?php
require_once 'config.php';
require_once "inc/blocks/icon.php";

function figure($url, $extension=false) {
	if (!$extension) {
		preg_match('/'.PIC_URL_PATTERN.'/', $url, $matches);
		$extension = $matches[2];
	}
	$url = "https://$url";
	$is_video = in_array($extension, array('mp4', 'webm'));
	$media_tag = $is_video
		? 'video controls autoplay loop muted'
		: 'img';
	$controls = '<div class="figure-controls">' 
		. "<a title='Открыть в новом окне' href='$url' target='_blank'>" . make_icon('arrow', 'icon icon-16') . "</a>"
		. ($is_video ? "<div title='Свернуть'>" . make_icon('x-cross', 'icon icon-16') . "</div>" : "")
		. "</div>";
	return "<figure class='thumb'><label><input type='checkbox'><$media_tag src='$url'></video>$controls</label></figure>";
}