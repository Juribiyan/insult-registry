<?php
$title = "Ошибка! - Регистратор оскорблений";
require 'inc/blocks/global_header.php';
$heading = "Ошибка!";
$nav_state = "error";
require 'inc/blocks/header.php';
?>
<b><?= $error_message ?></b>
<?php include 'inc/blocks/butt.php' ?> 