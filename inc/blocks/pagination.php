<?php
require_once 'inc/io.php';
list($base_route, $ajax_base_route) = recreateRoute();
$pagination = "";
for ($p=0; $p < $pages; $p++) { 
	$url = $base_route .  ($p==0 ? '' : $p);
	$pagination .= $p == $page
		? "<span class='page-link current-page'>$p</span>"
		: "<a href='$url' class='page-link'>$p</a>";
}
?>
<nav class="pagination">
	<div class="pagination-buttons"><?= $pagination ?></div>
</nav>
<script>const baseURI = `<?= $_SERVER['DOCUMENT_URI'] . '?' . $ajax_base_route ?>`</script>