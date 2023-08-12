<?php 
header('Content-Type:text/html;charset:utf-8');
header('Cache-Control:no-cache');

$title = (@$title ? "$title — " : "") . "Регистратор оскорблений";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="apple-touch-icon" sizes="180x180" href="/static/fav/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/static/fav/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/static/fav/favicon-16x16.png">
	<link rel="manifest" href="/static/fav/site.webmanifest">
	<noscript><style>.yesscript { display: none!important; }</style></noscript>
	<link rel="stylesheet" href="/static/main.css">
	<script src="/static/main.js"></script>
	<title><?= $title ?></title>
</head>
<body>