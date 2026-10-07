<?php
/**
 * TopicalBacklink theme functions.
 *
 * The page designs live in parts/content-*.php (generated from the static
 * HTML by build-wp-theme.py; the homepage is hand-written in parts/home/
 * and driven by ACF, see inc/home-fields.php). This file wires them into WordPress: assets,
 * page URLs, SEO title/description, first-run page setup and the lead form.
 *
 * @package TopicalBacklink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TB_VERSION', '1.0.0' );
define( 'TB_DIR', get_template_directory() );
define( 'TB_URI', get_template_directory_uri() );

require_once TB_DIR . '/inc/pages.php';
require_once TB_DIR . '/inc/home-fields.php';
require_once TB_DIR . '/inc/blog.php';
require_once TB_DIR . '/inc/fields.php';
require_once TB_DIR . '/inc/content-types.php';

/* ------------------------------------------------------------------
 * Page registry helpers
 * ------------------------------------------------------------------ */

/**
 * Key of the designed page being rendered ('' for normal WP pages/posts).
 */
function tb_current_key() {
	return isset( $GLOBALS['tb_current_page'] ) ? $GLOBALS['tb_current_page'] : '';
}

/**
 * Called at the top of each page template, before get_header().
 */
function tb_set_page( $key ) {
	$GLOBALS['tb_current_page'] = $key;
}

/**
 * URL of a designed page, e.g. tb_page_url( 'services', '#local' ).
 * Uses the real permalink when the page exists, so renamed slugs keep working.
 */
function tb_page_url( $key, $anchor = '' ) {
	$pages = tb_pages();

	if ( 'home' === $key || ! isset( $pages[ $key ] ) ) {
		return home_url( '/' ) . $anchor;
	}

	// Entries that are now Services / Case studies posts.
	if ( ! empty( $pages[ $key ]['post_type'] ) ) {
		$post = get_page_by_path( $pages[ $key ]['slug'], OBJECT, $pages[ $key ]['post_type'] );
		if ( $post && 'publish' === $post->post_status ) {
			return get_permalink( $post ) . $anchor;
		}
	}

	$ids = get_option( 'tb_page_ids', array() );
	if ( ! empty( $ids[ $key ] ) && 'publish' === get_post_status( $ids[ $key ] ) ) {
		$url = get_permalink( $ids[ $key ] );
	} else {
		$url = home_url( '/' . $pages[ $key ]['path'] . '/' );
	}

	return $url . $anchor;
}

/** Echo an escaped page URL. */
function tb_link( $key, $anchor = '' ) {
	echo esc_url( tb_page_url( $key, $anchor ) );
}

/** Echo the theme asset base URL (with trailing slash) for images/css/js paths. */
function tb_asset() {
	echo esc_url( TB_URI . '/' );
}

/** Echo the "current page" attributes on nav links. */
function tb_here( $key ) {
	if ( 'blog' === $key ) {
		if ( tbt_is_blog_view() ) {
			echo is_home() ? ' class="is-here" aria-current="page"' : ' class="is-here"';
		}
		return;
	}
	if ( ( 'services' === $key && is_singular( 'tb_service' ) ) || ( 'case-studies' === $key && is_singular( 'tb_case_study' ) ) ) {
		echo ' class="is-here"';
		return;
	}
	$current = tb_current_key();
	if ( ! $current ) {
		return;
	}
	if ( $current === $key ) {
		echo ' class="is-here" aria-current="page"';
		return;
	}
	$pages = tb_pages();
	if ( isset( $pages[ $current ] ) && $pages[ $current ]['parent'] === $key ) {
		echo ' class="is-here"';
	}
}

/** Privacy link: WP's privacy page if one is set, otherwise '#'. */
function tb_privacy_url() {
	$url = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
	echo esc_url( $url ? $url : '#' );
}

/** True when an SEO plugin handles titles and meta descriptions. */
function tb_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/* ------------------------------------------------------------------
 * Theme setup
 * ------------------------------------------------------------------ */

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
} );

/* ------------------------------------------------------------------
 * Assets
 * ------------------------------------------------------------------ */

function tb_ver( $rel ) {
	$file = TB_DIR . '/' . $rel;
	return file_exists( $file ) ? TB_VERSION . '.' . filemtime( $file ) : TB_VERSION;
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'tb-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap', array(), null );
	wp_enqueue_style( 'tb-style', TB_URI . '/css/style.css', array( 'tb-fonts' ), tb_ver( 'css/style.css' ) );
	wp_enqueue_style( 'tb-wp', TB_URI . '/css/wp.css', array( 'tb-style' ), tb_ver( 'css/wp.css' ) );

	wp_enqueue_script( 'tb-gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'tb-gsap-st', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', array( 'tb-gsap' ), '3.12.5', true );
	wp_enqueue_script( 'tb-main', TB_URI . '/js/main.js', array( 'tb-gsap', 'tb-gsap-st' ), tb_ver( 'js/main.js' ), true );
	wp_add_inline_script( 'tb-main', 'window.TB=' . wp_json_encode( array( 'ajax' => admin_url( 'admin-ajax.php' ) ) ) . ';', 'before' );
} );

// The designed pages don't use blocks; drop block CSS there so it can't clash.
add_action( 'wp_enqueue_scripts', function () {
	if ( ! tb_current_key() ) {
		return;
	}
	foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'global-styles', 'classic-theme-styles' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
}, 100 );

add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}, 10, 2 );

// Theme favicon, unless a Site Icon is set in Appearance > Customize.
add_action( 'wp_head', function () {
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" href="' . esc_url( TB_URI . '/images/favicon.svg' ) . '" type="image/svg+xml">' . "\n";
	}
}, 5 );

/* ------------------------------------------------------------------
 * SEO: per-page title + meta description (skipped if an SEO plugin is on)
 * ------------------------------------------------------------------ */

add_filter( 'pre_get_document_title', function ( $title ) {
	$key   = tb_current_key();
	$pages = tb_pages();
	if ( $key && isset( $pages[ $key ] ) && ! tb_seo_plugin_active() ) {
		return $pages[ $key ]['doc_title'];
	}
	return $title;
} );

add_action( 'wp_head', function () {
	$key   = tb_current_key();
	$pages = tb_pages();
	if ( $key && isset( $pages[ $key ] ) && ! tb_seo_plugin_active() && $pages[ $key ]['description'] ) {
		echo '<meta name="description" content="' . esc_attr( $pages[ $key ]['description'] ) . '">' . "\n";
	}
}, 1 );

/* ------------------------------------------------------------------
 * First-run setup: create pages, assign templates, set homepage
 * ------------------------------------------------------------------ */

function tb_setup_site() {
	$ids = get_option( 'tb_page_ids', array() );

	foreach ( tb_pages() as $key => $p ) {
		if ( empty( $p['create'] ) ) {
			continue;
		}

		$parent_id = 0;
		if ( $p['parent'] && ! empty( $ids[ $p['parent'] ] ) ) {
			$parent_id = (int) $ids[ $p['parent'] ];
		}

		$existing = get_page_by_path( $p['path'], OBJECT, 'page' );
		if ( $existing ) {
			$id = $existing->ID;
			if ( 'publish' !== $existing->post_status ) {
				wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
			}
		} else {
			$id = wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $p['title'],
				'post_name'    => $p['slug'],
				'post_parent'  => $parent_id,
				'post_content' => '',
			) );
		}

		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_wp_page_template', $p['template'] );
			$ids[ $key ] = (int) $id;
		}
	}

	update_option( 'tb_page_ids', $ids );

	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}

	tbt_setup_blog();
	tbt_setup_content();

	// Pretty URLs (/services/ instead of /?page_id=12).
	if ( '' === get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'tb_setup_site' );

// Manual re-run: Appearance > TopicalBacklink setup.
add_action( 'admin_menu', function () {
	add_theme_page( 'TopicalBacklink setup', 'TopicalBacklink setup', 'manage_options', 'tb-setup', 'tb_setup_screen' );
} );

function tb_setup_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$done = false;
	if ( isset( $_POST['tb_run_setup'] ) && check_admin_referer( 'tb_run_setup' ) ) {
		tb_setup_site();
		$done = true;
	}
	$ids = get_option( 'tb_page_ids', array() );
	echo '<div class="wrap"><h1>TopicalBacklink setup</h1>';
	if ( $done ) {
		echo '<div class="notice notice-success"><p>Pages created/updated and homepage set.</p></div>';
	}
	echo '<p>Creates any missing site pages (including the Blog), assigns their templates and sets the homepage. Safe to run more than once.</p><table class="widefat striped" style="max-width:720px"><tbody>';
	foreach ( tb_pages() as $key => $p ) {
		if ( empty( $p['create'] ) ) {
			continue;
		}
		$ok = ! empty( $ids[ $key ] ) && 'publish' === get_post_status( $ids[ $key ] );
		echo '<tr><td>' . esc_html( $p['title'] ) . '</td><td>' . ( $ok ? '<a href="' . esc_url( get_permalink( $ids[ $key ] ) ) . '" target="_blank">' . esc_html( get_permalink( $ids[ $key ] ) ) . '</a>' : '<em>missing</em>' ) . '</td></tr>';
	}
	$blog = (int) get_option( 'page_for_posts' );
	echo '<tr><td>Blog</td><td>' . ( $blog && 'publish' === get_post_status( $blog ) ? '<a href="' . esc_url( get_permalink( $blog ) ) . '" target="_blank">' . esc_html( get_permalink( $blog ) ) . '</a>' : '<em>missing</em>' ) . '</td></tr>';
	foreach ( array( 'tb_service' => 'Services', 'tb_case_study' => 'Case studies' ) as $type => $label ) {
		$n = (int) wp_count_posts( $type )->publish;
		echo '<tr><td>' . esc_html( $label ) . ' (posts)</td><td><a href="' . esc_url( admin_url( 'edit.php?post_type=' . $type ) ) . '">' . esc_html( $n . ' published' ) . '</a></td></tr>';
	}
	echo '</tbody></table><form method="post" style="margin-top:1em">';
	wp_nonce_field( 'tb_run_setup' );
	echo '<button class="button button-primary" name="tb_run_setup" value="1">Run setup</button></form></div>';
}

/* ------------------------------------------------------------------
 * Lead form: saves to Leads (admin) and emails the notification address
 * ------------------------------------------------------------------ */

add_action( 'init', function () {
	register_post_type( 'tb_lead', array(
		'labels'          => array(
			'name'          => 'Leads',
			'singular_name' => 'Lead',
			'all_items'     => 'All leads',
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_position'   => 26,
		'menu_icon'       => 'dashicons-email-alt',
		'supports'        => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
} );

// Settings > General > "Lead notification email".
add_action( 'admin_init', function () {
	register_setting( 'general', 'tb_lead_email', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ) );
	add_settings_field( 'tb_lead_email', 'Lead notification email', function () {
		printf(
			'<input type="email" name="tb_lead_email" class="regular-text" value="%s" placeholder="%s"><p class="description">Contact-form leads are sent here. Empty = Administration Email Address.</p>',
			esc_attr( get_option( 'tb_lead_email', '' ) ),
			esc_attr( get_option( 'admin_email' ) )
		);
	}, 'general' );
} );

function tb_handle_lead() {
	// Honeypot: bots fill the hidden "website" field; pretend success.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success();
	}

	// Basic rate limit: 5 submissions per IP per 10 minutes.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'tb_rl_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 5 ) {
		wp_send_json_error( array( 'message' => 'Too many requests. Please try again in a few minutes.' ), 429 );
	}
	set_transient( $key, $n + 1, 10 * MINUTE_IN_SECONDS );

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$domain  = isset( $_POST['domain'] ) ? sanitize_text_field( wp_unslash( $_POST['domain'] ) ) : '';
	$type    = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $domain ) {
		wp_send_json_error( array( 'message' => 'Please fill in your name, a valid email and the domain.' ), 400 );
	}

	$body = "Name: {$name}\nEmail: {$email}\nDomain: {$domain}\nNeeds: {$type}\n\nMessage:\n{$message}\n";

	$post_id = wp_insert_post( array(
		'post_type'    => 'tb_lead',
		'post_status'  => 'private',
		'post_title'   => $name . ' - ' . $domain,
		'post_content' => $body,
	) );

	$to   = get_option( 'tb_lead_email' );
	$to   = $to ? $to : get_option( 'admin_email' );
	$sent = wp_mail( $to, 'New link map request: ' . $domain, $body, array( 'Reply-To: ' . $name . ' <' . $email . '>' ) );

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_tb_mail_sent', $sent ? 'yes' : 'no' );
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_tb_lead', 'tb_handle_lead' );
add_action( 'wp_ajax_nopriv_tb_lead', 'tb_handle_lead' );
