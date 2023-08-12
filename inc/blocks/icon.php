<?php
function make_icon($id, $_class="icon") {
	return "<svg class='$_class'><use xlink:href='/static/icons.svg#i-$id'></use></svg>";
}
