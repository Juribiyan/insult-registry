<?php
function formatDayLong($timestamp) {
	$timestamp = (int)$timestamp;
	$d = date('j', $timestamp);
	$month = [
		'Января',
		'Февраля',
		'Марта',
		'Апреля',
		'Мая',
		'Июня',
		'Июля',
		'Августа',
		'Сентября',
		'Октября',
		'Ноября',
		'Декабря',
	][date('n', $timestamp) - 1];
	$year = date('Y', $timestamp);
	return "$d $month $year г";
}

function formatDateShort($timestamp) {
	return date('d.m.y @ H:i:s', (int)$timestamp);
}

function entryNoFull($num) {
	return str_repeat("0", 8-strlen($num)) . $num;
}