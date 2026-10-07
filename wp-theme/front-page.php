<?php
/**
 * Homepage. Always renders the designed home unless the page chosen as
 * "Homepage" has a different TopicalBacklink template assigned.
 *
 * @package TopicalBacklink
 */

$tb_front_tpl = is_page() ? get_page_template_slug( get_queried_object_id() ) : '';

if ( $tb_front_tpl && 0 === strpos( $tb_front_tpl, 'templates/' ) && 'templates/home.php' !== $tb_front_tpl && file_exists( TB_DIR . '/' . $tb_front_tpl ) ) {
	require TB_DIR . '/' . $tb_front_tpl;
	return;
}

require TB_DIR . '/templates/home.php';
