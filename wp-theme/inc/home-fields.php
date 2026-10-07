<?php
/**
 * Homepage ACF helpers.
 *
 * The "Home Page" field group (acf-json/group_6ac09b34bc648.json) feeds the
 * template parts in parts/home/. Every field is optional: an empty field
 * falls back to the original design copy, so the page renders in full with
 * ACF switched off or before any content has been entered.
 *
 * @package TopicalBacklink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post ID that holds the homepage fields (the page set as "Homepage").
 */
function tb_home_id() {
	$id = (int) get_option( 'page_on_front' );
	return $id ? $id : (int) get_queried_object_id();
}

/**
 * A top-level ACF group on the homepage, as an array ('' / [] when unset).
 */
function tb_home_group( $name ) {
	$id = tb_home_id();
	if ( ! $id || ! function_exists( 'get_field' ) ) {
		return array();
	}
	$value = get_field( $name, $id );
	return is_array( $value ) ? $value : array();
}

/**
 * $arr[ $key ] if it holds something, otherwise $fallback.
 */
function tb_val( $arr, $key, $fallback = '' ) {
	if ( ! is_array( $arr ) || ! isset( $arr[ $key ] ) ) {
		return $fallback;
	}
	$v = $arr[ $key ];
	if ( is_string( $v ) ) {
		$v = trim( $v );
	}
	return ( '' === $v || null === $v || false === $v || array() === $v ) ? $fallback : $v;
}

/**
 * Non-empty repeater rows, or $fallback when the repeater is empty.
 */
function tb_rows( $arr, $key, $fallback ) {
	$rows = tb_val( $arr, $key, array() );
	if ( ! is_array( $rows ) ) {
		return $fallback;
	}
	$rows = array_values( array_filter( $rows, function ( $row ) {
		return is_array( $row ) && array_filter( $row, function ( $v ) {
			return '' !== $v && null !== $v && false !== $v;
		} );
	} ) );
	return $rows ? $rows : $fallback;
}

/**
 * Escape a heading that may contain the design's inline markup:
 * <span class="mark">, <br>, <em>, <strong> and the hero swoosh.
 */
function tb_kses_heading( $html ) {
	return wp_kses(
		$html,
		array(
			'span'   => array( 'class' => true ),
			'i'      => array( 'class' => true, 'aria-hidden' => true ),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
		)
	);
}

/** Echo an escaped heading. */
function tb_heading( $html ) {
	echo tb_kses_heading( $html ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by wp_kses.
}

/**
 * Hero visibility. The ACF switch defaults to "on"; the hero is hidden only
 * after someone has explicitly saved it as off.
 */
function tb_home_hero_on() {
	$id = tb_home_id();
	return ! $id || '0' !== (string) get_post_meta( $id, 'hero_section_section_on', true );
}

/** Default service cards (mirrors the original design). */
function tb_home_default_services() {
	return array(
		array( 'title' => 'White-Label Link Building', 'is_new' => false, 'description' => 'Scalable, agency-ready link building with genuine editorial outreach and zero PBNs. We become your link-building department and offer complete transparency. Every placement is tracked live. Every link is guaranteed for a year.', 'button' => '' ),
		array( 'title' => 'Multi-Lingual Link Building', 'is_new' => true, 'description' => 'Native-language outreach across 28 markets, run by in-country editors rather than translation tools. Anchor strategy, local relevance and tone are handled per market, so the link reads as though it was always meant to be there.', 'button' => '' ),
		array( 'title' => 'Local Link Building (USA)', 'is_new' => false, 'description' => 'City and state-level authority for multi-location brands. Chamber listings, regional press, local resource pages and genuine community partnerships, the citations and links that move the map pack, not just the blue links.', 'button' => '' ),
		array( 'title' => 'Media Placements', 'is_new' => true, 'description' => 'Editorial coverage in publications your buyers already read. Journalist-led pitching against live queries, with placements on titles that carry real newsroom standards and real traffic, never sponsored-content farms.', 'button' => '' ),
		array( 'title' => 'AI Search Optimization', 'is_new' => true, 'description' => "We track your brand's visibility across ChatGPT, Perplexity, Gemini, Copilot, Grok, Claude, DeepSeek and AI Overviews, then build the signals that get you recommended. Includes LLM monitoring, AI search progression, sentiment tracking, AEO for specific platforms and AI trust-signal engineering.", 'button' => '' ),
	);
}

/**
 * The services tabs are drawn by js/main.js; hand it the ACF rows as JSON.
 * main.js escapes every string before inserting it.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( 'home' !== tb_current_key() && ! is_front_page() ) {
		return;
	}
	$group = tb_home_group( 'link_building_services' );
	$rows  = tb_rows( $group, 'svc_items', tb_home_default_services() );
	$icons = array( 'brief', 'lang', 'pin', 'news', 'bulb' );
	$out   = array();

	foreach ( $rows as $i => $row ) {
		$item = array(
			't' => (string) tb_val( $row, 'title' ),
			'b' => (string) tb_val( $row, 'description' ),
			'i' => $icons[ $i % count( $icons ) ],
			'n' => (bool) tb_val( $row, 'is_new', false ),
			'u' => esc_url_raw( (string) tb_val( $row, 'button', '#pricing' ) ),
		);
		// Keep the original "AI search" card treatment when using the defaults.
		if ( 'AI Search Optimization' === $item['t'] ) {
			$item['tag']  = 'FOR AI VISIBILITY → GEO & AEO';
			$item['show'] = true;
		}
		$out[] = $item;
	}

	wp_add_inline_script( 'tb-main', 'window.TB_SERVICES=' . wp_json_encode( $out ) . ';', 'before' );
}, 20 );

/** Nudge admins to install ACF PRO (repeaters need PRO). */
add_action( 'admin_notices', function () {
	if ( class_exists( 'ACF' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'TopicalBacklink: install and activate Advanced Custom Fields PRO to edit the homepage, Services and Case studies content. Until then those pages show their saved or default content.', 'topicalbacklink' ) . '</p></div>';
} );
