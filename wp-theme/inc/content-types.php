<?php
/**
 * Services and Case studies: post types, URLs, navigation state and the
 * one-time setup that moves the original designed content into them.
 *
 * URLs stay as they were: /services/<slug>/ and /case-studies/<slug>/.
 * Templates: single-tb_service.php, single-tb_case_study.php, and the
 * listing bodies parts/content-services.php / parts/content-case-studies.php.
 *
 * @package TopicalBacklink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {
	$common = array(
		'public'             => true,
		'show_in_rest'       => true,
		'has_archive'        => false, // the designed Services / Case studies pages are the archives
		'hierarchical'       => false,
		'supports'           => array( 'title', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
		'menu_position'      => 21,
		'publicly_queryable' => true,
	);

	register_post_type( 'tb_service', array_merge( $common, array(
		'labels'    => array(
			'name'               => 'Services',
			'singular_name'      => 'Service',
			'add_new_item'       => 'Add new service',
			'edit_item'          => 'Edit service',
			'all_items'          => 'All services',
			'search_items'       => 'Search services',
			'not_found'          => 'No services yet',
			'featured_image'     => 'Service image',
			'set_featured_image' => 'Set service image',
		),
		'menu_icon' => 'dashicons-admin-links',
		'rewrite'   => array( 'slug' => 'services', 'with_front' => false ),
	) ) );

	register_post_type( 'tb_case_study', array_merge( $common, array(
		'labels'    => array(
			'name'               => 'Case studies',
			'singular_name'      => 'Case study',
			'add_new_item'       => 'Add new case study',
			'edit_item'          => 'Edit case study',
			'all_items'          => 'All case studies',
			'search_items'       => 'Search case studies',
			'not_found'          => 'No case studies yet',
			'featured_image'     => 'Main image',
			'set_featured_image' => 'Set main image',
		),
		'menu_icon' => 'dashicons-chart-line',
		'rewrite'   => array( 'slug' => 'case-studies', 'with_front' => false ),
	) ) );
} );

// The editor for these types is fields, not blocks.
add_filter( 'use_block_editor_for_post_type', function ( $use, $type ) {
	return in_array( $type, array( 'tb_service', 'tb_case_study' ), true ) ? false : $use;
}, 10, 2 );

// The excerpt is the card text on these types, so never hide its box by default.
add_filter( 'default_hidden_meta_boxes', function ( $hidden, $screen ) {
	if ( $screen && in_array( $screen->post_type, array( 'tb_service', 'tb_case_study' ), true ) ) {
		$hidden = array_diff( $hidden, array( 'postexcerpt' ) );
	}
	return $hidden;
}, 10, 2 );

// Clearer excerpt box label on these screens.
add_filter( 'gettext', function ( $text, $original ) {
	if ( 'Excerpt' !== $original || ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
		return $text;
	}
	$s = get_current_screen();
	if ( $s && in_array( $s->post_type, array( 'tb_service', 'tb_case_study' ), true ) ) {
		return 'tb_service' === $s->post_type ? 'Short description (Services page)' : 'Summary (Case studies page)';
	}
	return $text;
}, 10, 2 );

/** Published posts of a type in dashboard order. */
function tbt_items( $type, $args = array() ) {
	return get_posts( array_merge( array(
		'post_type'      => $type,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	), $args ) );
}

/** Card figures for a case study: the repeater, else result / DR / duration. */
function tbt_case_card_stats( $f ) {
	$rows = tbt_filled( tbt_v( $f, 'card_stats', array() ) );
	if ( $rows ) {
		return $rows;
	}
	$rows = array();
	if ( tbt_v( $f, 'result' ) ) {
		$rows[] = array( 'label' => 'RESULT', 'value' => $f['result'] );
	}
	if ( '' !== tbt_v( $f, 'dr_before' ) && '' !== tbt_v( $f, 'dr_after' ) ) {
		$rows[] = array( 'label' => 'DOMAIN RATING', 'value' => $f['dr_before'] . ' → ' . $f['dr_after'] );
	}
	if ( tbt_v( $f, 'duration' ) ) {
		$rows[] = array( 'label' => 'TIMEFRAME', 'value' => $f['duration'] );
	}
	return $rows;
}

/** "DR 19 → 52" for a case study, or ''. */
function tbt_case_dr( $f ) {
	return ( '' !== tbt_v( $f, 'dr_before' ) && '' !== tbt_v( $f, 'dr_after' ) ) ? 'DR ' . $f['dr_before'] . ' → ' . $f['dr_after'] : '';
}

/** Link field value: absolute URL, #anchor or site path. */
function tbt_href( $url, $fallback ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return $fallback;
	}
	if ( '#' === $url[0] || preg_match( '#^(https?:|mailto:|tel:)#', $url ) ) {
		return $url;
	}
	return home_url( '/' . ltrim( $url, '/' ) );
}

/* ------------------------------------------------------------------
 * Page state: nav highlight, no block CSS, SEO description
 * ------------------------------------------------------------------ */

add_action( 'template_redirect', function () {
	if ( is_singular( 'tb_service' ) ) {
		tb_set_page( 'service-single' );
	} elseif ( is_singular( 'tb_case_study' ) ) {
		tb_set_page( 'case-study-single' );
	}
} );

add_action( 'wp_head', function () {
	if ( tb_seo_plugin_active() || ! is_singular( array( 'tb_service', 'tb_case_study' ) ) ) {
		return;
	}
	$desc = wp_strip_all_tags( get_the_excerpt( get_queried_object_id() ) );
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( wp_trim_words( $desc, 32, '…' ) ) . '">' . "\n";
	}
}, 1 );

/* ------------------------------------------------------------------
 * One-time setup: create the posts from the original design content
 * ------------------------------------------------------------------ */

/**
 * Creates the services and case studies (only if none exist yet), retires
 * the two old hand-built detail pages and refreshes URLs. Safe to re-run.
 */
function tbt_setup_content() {
	require_once TB_DIR . '/inc/seed.php';

	$none = function ( $type ) {
		return ! get_posts( array( 'post_type' => $type, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
	};

	// Case studies first, so a service can point at its featured case.
	if ( $none( 'tb_case_study' ) ) {
		foreach ( tbt_seed_case_studies() as $i => $c ) {
			tbt_seed_post( 'tb_case_study', $c, $i );
		}
	}

	if ( $none( 'tb_service' ) ) {
		$p      = get_page_by_path( 'collab-management', OBJECT, 'tb_case_study' );
		$collab = $p ? $p->ID : 0;
		foreach ( tbt_seed_services( $collab ) as $i => $s ) {
			tbt_seed_post( 'tb_service', $s, $i );
		}
	}

	// The old fixed-design child pages now live at the same URLs as posts.
	$ids = get_option( 'tb_page_ids', array() );
	foreach ( array( 'service-white-label', 'case-study-collab' ) as $key ) {
		if ( ! empty( $ids[ $key ] ) && get_post( $ids[ $key ] ) ) {
			wp_update_post( array( 'ID' => (int) $ids[ $key ], 'post_status' => 'draft' ) );
		}
		unset( $ids[ $key ] );
	}
	update_option( 'tb_page_ids', $ids );

	update_option( 'tb_content_setup', 2 );
	flush_rewrite_rules();
}

/*
 * Runs once on the first request after the theme is installed or updated
 * (front end or dashboard), so the moved White-label / Collab URLs never 404.
 * It only writes a few rows to the database: no image processing, so it stays
 * well inside shared-hosting time limits. A short lock stops two visitors
 * running it at the same moment.
 */
add_action( 'init', function () {
	if ( (int) get_option( 'tb_content_setup' ) >= 2 || get_transient( 'tb_setup_lock' ) || wp_installing() ) {
		return;
	}
	set_transient( 'tb_setup_lock', 1, 5 * MINUTE_IN_SECONDS );
	tbt_setup_blog();
	tbt_setup_content();
	delete_transient( 'tb_setup_lock' );
}, 99 );

/** Insert one seeded post with its fields. Its starting picture is a theme file (see tbt_post_image()). */
function tbt_seed_post( $type, $data, $order ) {
	$id = wp_insert_post( array(
		'post_type'    => $type,
		'post_status'  => 'publish',
		'post_title'   => $data['title'],
		'post_name'    => $data['slug'],
		'post_excerpt' => $data['excerpt'],
		'menu_order'   => $order + 1,
	) );
	if ( ! $id || is_wp_error( $id ) ) {
		return 0;
	}
	if ( ! empty( $data['image'] ) ) {
		update_post_meta( $id, '_tb_theme_image', $data['image'] );
		update_post_meta( $id, '_tb_theme_image_alt', isset( $data['alt'] ) ? $data['alt'] : $data['title'] );
	}
	tbt_seed_fields( $id, $data['fields'], $type );
	return $id;
}

/** True when a post has a picture: its featured image, or a starting theme image. */
function tbt_has_image( $post_id ) {
	return has_post_thumbnail( $post_id ) || ( get_post_meta( $post_id, '_tb_theme_image', true ) && file_exists( TB_DIR . '/' . get_post_meta( $post_id, '_tb_theme_image', true ) ) );
}

/**
 * <img> for a post: the featured image set in the dashboard, else the
 * starting picture shipped with the theme, else ''.
 */
function tbt_post_image( $post_id, $size = 'large', $attrs = array() ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $size, $attrs );
	}
	$rel = (string) get_post_meta( $post_id, '_tb_theme_image', true );
	if ( '' === $rel || ! file_exists( TB_DIR . '/' . $rel ) ) {
		return '';
	}
	$size_info = function_exists( 'wp_getimagesize' ) ? wp_getimagesize( TB_DIR . '/' . $rel ) : @getimagesize( TB_DIR . '/' . $rel ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	$attrs     = array_merge( array( 'alt' => (string) get_post_meta( $post_id, '_tb_theme_image_alt', true ) ), $size_info ? array( 'width' => $size_info[0], 'height' => $size_info[1] ) : array(), $attrs );
	return tbt_image( $rel, $size, $attrs );
}
