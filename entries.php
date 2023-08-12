<?php
require_once "config.php";
require_once "inc/formatting.php";
require_once "inc/io.php";

$ajax = @$_GET['ajax'];

$heading = "Все записи";

require_once 'inc/registry.php';
$reg = new Registry();

$params = sanitizeOutput($_GET, ['offender', 'victim', 'site'], true);
$page = (int)(@$_GET['page']);

if (count($params)) {
	$heading = (isset($params['offender'])
		? ($params['offender'] == '_' ? DEFAULT_NAME : $params['offender'])
		: "*") . " → ";

	$heading .= isset($params['victim'])
		? $params['victim']
		: "*";

	if (isset($params['site']))
		$heading .= " @ " . $params['site'];
}

list($entries, $pages) = $reg->getEntries($page, $ajax);

if (!$entries) {
	http_response_code(404);
}

if (!$ajax) {
	$title = $heading;
	require 'inc/blocks/global_header.php';

	$nav_state = "entries";
	require 'inc/blocks/header.php';

	echo "<div class='entry-list'>";
}

if (!$entries) {
	if (!$ajax)
		echo '<h3>Ничего не найдено.</h3>';
}
else foreach($entries as $entry) {
	require 'inc/blocks/entry.php';
	echo '<hr class="entry-divider">';
}

if (!$ajax) {
	echo "</div>"; // /entry-list

	if ($pages > 1)
		require 'inc/blocks/pagination.php';

	require 'inc/blocks/butt.php'; 
}