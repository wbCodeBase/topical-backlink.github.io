<?php
/**
 * Homepage body. Each section is its own template part in parts/home/;
 * sections backed by the "Home Page" ACF group read it via inc/home-fields.php.
 *
 * @package TopicalBacklink
 */

$tb_home_sections = array(
	'hero',         // ACF: hero_section
	'trust',        // static
	'platform',     // ACF: the_platform
	'stats',        // ACF: stat_section
	'services',     // ACF: link_building_services
	'build',        // ACF: how_we_build_links
	'work',         // ACF: case_studies
	'testimonials', // static
	'cta',          // ACF: cta_section
	'faq',          // ACF: faq_section
);
?>
<main id="top">
<?php
foreach ( $tb_home_sections as $tb_section ) {
	get_template_part( 'parts/home/' . $tb_section );
}
?>
</main>
