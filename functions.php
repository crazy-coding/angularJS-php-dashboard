<?php
global $Hooks;
$Hooks->add_action('Before_Update_Product','after_update_product');

function after_update_product($p_id) {
	write_file(DIR_STORAGE.'hook_test.txt', 'After Update Product: ' . $p_id);
}