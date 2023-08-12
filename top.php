<?php
require_once 'config.php';
require_once "inc/formatting.php";
require_once "inc/io.php";
require_once 'inc/registry.php';
$reg = new Registry();

$order_options_map = [
	"offender" => " вредителей",
	"victim" => " жертв",
	"site" => " сайтов"
];

$heading = "Топ ";
$by = @$_GET['by'];
if (!$by || !in_array($by, array_keys($order_options_map)))
	exitWithError(401, "Неверный список");

$heading .= $order_options_map[$by];

$top = $reg->getTopList($by);
if (!$top) {
	http_response_code(404);
}

$title = $heading;
require 'inc/blocks/global_header.php';

$nav_state = $by . "s";
require 'inc/blocks/header.php';

?>
<div class="top-list"><?php
if (!$top) {
	echo '<h3>Ничего не найдено.</h3>';
}
else foreach($top as $line) {
	$name = (strlen($line['name']) == 0) 
		? "Аноним"
		: htmlspecialchars($line['name']);
	echo "<div><a href=\"/$by/$name\">$name</a> (" . $line['total'] . ")</div>";
}?></div>
<?php 
require 'inc/blocks/butt.php'; 