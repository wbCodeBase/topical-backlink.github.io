<?php
/**
 * Blog: listing and article helpers, query tweaks and the post editor fields.
 *
 * Templates: home.php / archive.php / search.php render parts/blog/archive.php,
 * single.php renders parts/blog/single.php. Assets: css/blog.css, js/blog.js.
 *
 * Editor fields (Posts > Add New: a "Card & article settings" panel in the
 * block editor sidebar, js/editor.js; meta boxes in the Classic Editor):
 *   Card summary          -> the post excerpt
 *   Key takeaways         -> _tb_takeaways (one point per line)
 *   Hide table of contents -> _tb_hide_toc
 *
 * @package TopicalBacklink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TB_BLOG_PER_PAGE', 9 );

/* ------------------------------------------------------------------
 * URLs and page state
 * ------------------------------------------------------------------ */

/** Blog index URL: the "Posts page" set in Settings > Reading. */
function tbt_blog_url() {
	$id = (int) get_option( 'page_for_posts' );
	if ( $id && 'publish' === get_post_status( $id ) ) {
		return get_permalink( $id );
	}
	return home_url( '/blog/' );
}

/** Echo the escaped blog index URL. */
function tbt_blog_link() {
	echo esc_url( tbt_blog_url() );
}

/** True on the blog index, post archives, search and single posts. */
function tbt_is_blog_view() {
	return is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date() || is_search();
}

/**
 * Create the "Blog" page and set it as the Posts page, unless one is already
 * set. Runs once automatically and again from Appearance > TopicalBacklink setup.
 */
function tbt_setup_blog() {
	$id = (int) get_option( 'page_for_posts' );

	if ( ! $id || ! get_post( $id ) ) {
		$page = get_page_by_path( 'blog', OBJECT, 'page' );
		if ( $page ) {
			$id = $page->ID;
		} else {
			$id = wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Blog',
				'post_name'    => 'blog',
				'post_content' => '',
			) );
		}
		if ( $id && ! is_wp_error( $id ) ) {
			update_option( 'page_for_posts', (int) $id );
		}
	}

	if ( $id && ! is_wp_error( $id ) && 'publish' !== get_post_status( $id ) ) {
		wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
	}

	update_option( 'tb_blog_setup', 1 );
}
add_action( 'admin_init', function () {
	if ( ! get_option( 'tb_blog_setup' ) && current_user_can( 'manage_options' ) ) {
		tbt_setup_blog();
	}
} );

/* ------------------------------------------------------------------
 * Queries
 * ------------------------------------------------------------------ */

/**
 * The post shown large at the top of the blog (or of a category): the newest
 * sticky post ("Stick to the top of the blog" in the editor), else the newest post.
 *
 * @param WP_Query|null $q Main query while it is being set up; null in templates.
 */
function tbt_blog_featured_id( $q = null ) {
	static $cache = array();

	$cat = 0;
	if ( $q ? $q->is_category() : is_category() ) {
		$term = $q ? $q->get_queried_object() : get_queried_object();
		$cat  = $term && isset( $term->term_id ) ? (int) $term->term_id : 0;
	}
	if ( isset( $cache[ $cat ] ) ) {
		return $cache[ $cat ];
	}

	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
		'fields'              => 'ids',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	if ( $cat ) {
		$args['cat'] = $cat;
	}

	$ids    = array();
	$sticky = array_filter( array_map( 'intval', (array) get_option( 'sticky_posts', array() ) ) );
	if ( $sticky ) {
		$ids = get_posts( $args + array( 'post__in' => $sticky ) );
	}
	if ( ! $ids ) {
		$ids = get_posts( $args );
	}

	$cache[ $cat ] = $ids ? (int) $ids[0] : 0;
	return $cache[ $cat ];
}

add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() || ! $q->is_main_query() || $q->is_feed() ) {
		return;
	}

	// Site search only covers articles (the designed pages have no body text).
	if ( $q->is_search() ) {
		$q->set( 'post_type', 'post' );
	}

	if ( $q->is_home() || $q->is_archive() || $q->is_search() ) {
		$q->set( 'posts_per_page', TB_BLOG_PER_PAGE );
	}

	// The featured post sits above the grid on page 1, so the grid skips it on
	// every page; that keeps each page a full 3x3 and pagination exact.
	if ( ( $q->is_home() || $q->is_category() ) && ! $q->is_search() ) {
		$q->set( 'ignore_sticky_posts', true );
		$featured = tbt_blog_featured_id( $q );
		if ( $featured ) {
			$q->set( 'post__not_in', array( $featured ) );
		}
	}
} );

/**
 * IDs of up to $n posts related to $post_id: same categories first, then newest.
 */
function tbt_related_ids( $post_id, $n = 3 ) {
	$base = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'fields'              => 'ids',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	$ids  = array();
	$cats = wp_get_post_categories( $post_id );
	if ( $cats ) {
		$ids = get_posts( $base + array(
			'category__in'   => $cats,
			'post__not_in'   => array( $post_id ),
			'posts_per_page' => $n,
		) );
	}
	if ( count( $ids ) < $n ) {
		$ids = array_merge( $ids, get_posts( $base + array(
			'post__not_in'   => array_merge( array( $post_id ), $ids ),
			'posts_per_page' => $n - count( $ids ),
		) ) );
	}
	return $ids;
}

/* ------------------------------------------------------------------
 * Post data helpers
 * ------------------------------------------------------------------ */

/** Minutes to read, at ~225 words a minute. */
function tbt_read_time( $post = null ) {
	$text  = wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', $post ) ) );
	$words = count( preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY ) );
	return max( 1, (int) ceil( $words / 225 ) );
}

/** Card summary: the excerpt field, or the opening words of the article. */
function tbt_card_excerpt( $post = null, $words = 26 ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	if ( has_excerpt( $post ) ) {
		return wp_trim_words( $post->post_excerpt, 45, '&hellip;' );
	}
	$text = wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $post->post_content ) ) );
	return wp_trim_words( $text, $words, '&hellip;' );
}

/** The category shown on a card: the first one that isn't "Uncategorized". */
function tbt_post_cat( $post = null ) {
	$cats = get_the_category( $post ? get_post( $post )->ID : false );
	foreach ( $cats as $c ) {
		if ( 'uncategorized' !== $c->slug ) {
			return $c;
		}
	}
	return $cats ? $cats[0] : null;
}

/** Categories for the filter row: every non-empty one except "Uncategorized". */
function tbt_blog_cats() {
	return array_values( array_filter(
		get_categories( array( 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC' ) ),
		function ( $c ) {
			return 'uncategorized' !== $c->slug;
		}
	) );
}

/** Key takeaways for a post, as an array of lines. */
function tbt_takeaways( $post_id = 0 ) {
	$raw   = (string) get_post_meta( $post_id ? $post_id : get_the_ID(), '_tb_takeaways', true );
	$lines = array();
	foreach ( preg_split( '/\R/u', $raw ) as $line ) {
		$line = trim( preg_replace( '/^\s*(?:[-*\x{2022}]|\d+[.)])\s*/u', '', $line ) );
		if ( '' !== $line ) {
			$lines[] = $line;
		}
	}
	return $lines;
}

/**
 * The current post's content, with an id on every H2/H3 and the list of those
 * headings for the table of contents.
 *
 * @return array{0:string,1:array} [ html, [ [ 'level', 'id', 'text' ], ... ] ]
 */
function tbt_content_with_toc() {
	$html = apply_filters( 'the_content', get_the_content() );
	$html = str_replace( ']]>', ']]&gt;', $html );
	$toc  = array();
	$used = array();
	$n    = 0;

	$html = preg_replace_callback( '#<h([23])(\s[^>]*)?>(.*?)</h\1>#is', function ( $m ) use ( &$toc, &$used, &$n ) {
		$attrs = isset( $m[2] ) ? $m[2] : '';
		$text  = trim( html_entity_decode( wp_strip_all_tags( $m[3] ), ENT_QUOTES, 'UTF-8' ) );
		if ( '' === $text ) {
			return $m[0];
		}
		$n++;

		if ( preg_match( '/\sid\s*=\s*(["\'])(.*?)\1/i', $attrs, $idm ) && '' !== $idm[2] ) {
			$id = $idm[2];
		} else {
			$base = sanitize_title( $text );
			if ( '' === $base || false !== strpos( $base, '%' ) ) {
				$base = 'section-' . $n;
			}
			$id = $base;
			for ( $i = 2; isset( $used[ $id ] ); $i++ ) {
				$id = $base . '-' . $i;
			}
			$attrs .= ' id="' . esc_attr( $id ) . '"';
		}
		$used[ $id ] = true;

		$toc[] = array( 'level' => (int) $m[1], 'id' => $id, 'text' => $text );
		return '<h' . $m[1] . $attrs . '>' . $m[3] . '</h' . $m[1] . '>';
	}, $html );

	return array( $html, $toc );
}

/* ------------------------------------------------------------------
 * Markup helpers
 * ------------------------------------------------------------------ */

/** Inline SVG icon (stroke icons share the site's .ico class). */
function tbt_icon( $name ) {
	$p = array(
		'arrow'    => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'up'       => '<path d="M12 19V5"/><path d="m5 12 7-7 7 7"/>',
		'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'list'     => '<path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/>',
		'close'    => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
		'check'    => '<path d="M20 6 9 17l-5-5"/>',
		'bulb'     => '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>',
		'link'     => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',
		'linkedin' => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/>',
		'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
	);
	if ( 'x' === $name ) {
		return '<svg class="ico ico--fill" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.4l-5.8-7.58-6.64 7.58H.47l8.6-9.83L0 1.15h7.6l5.24 6.93zm-1.29 19.5h2.04L6.49 3.24H4.3z"/></svg>';
	}
	return isset( $p[ $name ] ) ? '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">' . $p[ $name ] . '</svg>' : '';
}

/** Branded stand-in for posts without a featured image. */
function tbt_blog_ph() {
	return '<span class="bph" aria-hidden="true"><svg viewBox="0 0 40 40" fill="none"><g transform="rotate(-45 20 20)" stroke="#fff" stroke-width="2.6" stroke-linecap="round"><rect x="7" y="15" width="15" height="10" rx="5"/><rect x="18" y="15" width="15" height="10" rx="5" stroke-opacity=".7"/></g></svg></span>';
}

/** Author / date / read-time row for the current post. */
function tbt_post_meta( $class = '', $avatar = 28, $link_author = false ) {
	$author_id = (int) get_the_author_meta( 'ID' );
	$name      = esc_html( get_the_author() );
	if ( $link_author ) {
		$name = '<a href="' . esc_url( get_author_posts_url( $author_id ) ) . '">' . $name . '</a>';
	}
	echo '<div class="bmeta ' . esc_attr( $class ) . '">';
	echo get_avatar( $author_id, $avatar * 2, '', '', array( 'width' => $avatar, 'height' => $avatar ) );
	echo '<span class="bmeta__by">' . $name . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
	echo '<span class="bmeta__sep" aria-hidden="true"></span>';
	echo '<time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date( 'M j, Y' ) ) . '</time>';
	echo '<span class="bmeta__sep" aria-hidden="true"></span>';
	echo '<span>' . esc_html( tbt_read_time() ) . ' min read</span>';
	echo '</div>';
}

/** Share buttons for the current post. */
function tbt_share( $class = '' ) {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ) );
	$links = array(
		array( 'x', 'Share on X', 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title ),
		array( 'linkedin', 'Share on LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url ),
		array( 'facebook', 'Share on Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . $url ),
	);
	echo '<div class="bshare ' . esc_attr( $class ) . '"><span class="bshare__k">Share</span>';
	foreach ( $links as $l ) {
		echo '<a href="' . esc_url( $l[2] ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $l[1] ) . '">' . tbt_icon( $l[0] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
	}
	echo '<button type="button" data-copy="' . esc_url( get_permalink() ) . '" aria-label="Copy link">' . tbt_icon( 'link' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
	echo '</div>';
}

/* ------------------------------------------------------------------
 * Assets and head
 * ------------------------------------------------------------------ */

add_action( 'wp_enqueue_scripts', function () {
	if ( ! tbt_is_blog_view() ) {
		return;
	}
	wp_enqueue_style( 'tbt-blog', TB_URI . '/css/blog.css', array( 'tb-wp' ), tb_ver( 'css/blog.css' ) );
	if ( is_singular( 'post' ) ) {
		wp_enqueue_script( 'tbt-blog', TB_URI . '/js/blog.js', array(), tb_ver( 'js/blog.js' ), true );
	}
}, 20 );

// Meta description for articles and the blog index (skipped if an SEO plugin is on).
add_action( 'wp_head', function () {
	if ( tb_seo_plugin_active() ) {
		return;
	}
	$desc = '';
	if ( is_singular( 'post' ) ) {
		$desc = tbt_card_excerpt( get_queried_object_id(), 30 );
	} elseif ( is_home() && ! is_paged() ) {
		$desc = 'Practical guides on link building, guest posting, digital PR and AI citation building from the TopicalBacklink team.';
	} elseif ( is_category() ) {
		$desc = wp_strip_all_tags( category_description() );
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( html_entity_decode( $desc, ENT_QUOTES, 'UTF-8' ) ) . '">' . "\n";
	}
}, 1 );

/* ------------------------------------------------------------------
 * Post editor fields
 * ------------------------------------------------------------------ */

add_action( 'init', function () {
	$auth = function ( $allowed, $key, $post_id ) {
		return current_user_can( 'edit_post', $post_id );
	};
	register_post_meta( 'post', '_tb_takeaways', array(
		'type'              => 'string',
		'single'            => true,
		'default'           => '',
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_textarea_field',
		'auth_callback'     => $auth,
	) );
	register_post_meta( 'post', '_tb_hide_toc', array(
		'type'          => 'boolean',
		'single'        => true,
		'default'       => false,
		'show_in_rest'  => true,
		'auth_callback' => $auth,
	) );
} );

// Classic Editor only; the block editor uses the sidebar panel in js/editor.js.
add_action( 'add_meta_boxes_post', function () {
	remove_meta_box( 'postexcerpt', 'post', 'normal' );
	$compat = array( '__back_compat_meta_box' => true );
	add_meta_box( 'tb_card_summary', 'Card summary (excerpt)', 'tbt_box_summary', 'post', 'normal', 'high', $compat );
	add_meta_box( 'tb_article_extras', 'Key takeaways & table of contents', 'tbt_box_extras', 'post', 'normal', 'high', $compat );
} );

function tbt_box_summary( $post ) {
	wp_nonce_field( 'tb_summary', 'tb_summary_nonce' );
	?>
	<textarea name="tb_excerpt" id="tb_excerpt" rows="3" style="width:100%" placeholder="One or two sentences that make people want to read this article."><?php echo esc_textarea( $post->post_excerpt ); ?></textarea>
	<p class="description">
		Shown under the title on the blog cards and the featured article, and used as the Google description when no SEO plugin is installed.
		Aim for about 140&ndash;160 characters: <strong id="tb_excerpt_n">0</strong> so far.
		Leave empty to use the first lines of the article.
	</p>
	<script>
	(function(){var t=document.getElementById('tb_excerpt'),n=document.getElementById('tb_excerpt_n');
	function u(){n.textContent=t.value.length;n.style.color=t.value.length>170?'#b32d2e':'';}t.addEventListener('input',u);u();})();
	</script>
	<?php
}

function tbt_box_extras( $post ) {
	wp_nonce_field( 'tb_extras', 'tb_extras_nonce' );
	$takeaways = (string) get_post_meta( $post->ID, '_tb_takeaways', true );
	$hide_toc  = (bool) get_post_meta( $post->ID, '_tb_hide_toc', true );
	?>
	<p style="margin-top:0"><label for="tbt_takeaways"><strong>Key takeaways</strong></label></p>
	<textarea name="tbt_takeaways" id="tbt_takeaways" rows="5" style="width:100%" placeholder="Topical relevance beats raw domain rating.&#10;Build links to the pages that earn revenue.&#10;Five editorial links outperform fifty directory links."><?php echo esc_textarea( $takeaways ); ?></textarea>
	<p class="description">One point per line. Shown in a highlighted box at the top of the article. Leave empty to hide the box.</p>
	<hr style="margin:1.25em 0">
	<label><input type="checkbox" name="tb_hide_toc" value="1" <?php checked( $hide_toc ); ?>> <strong>Hide the table of contents on this post</strong></label>
	<p class="description">The contents list is built automatically from the article's H2 and H3 headings. It only appears when there are at least two.</p>
	<?php
}

function tbt_verify( $field, $action ) {
	return isset( $_POST[ $field ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $field ] ) ), $action );
}

// Card summary -> post_excerpt. Done as the post is written, so no second save is needed.
add_filter( 'wp_insert_post_data', function ( $data, $postarr ) {
	if ( 'post' !== $data['post_type'] || empty( $postarr['ID'] ) || ! isset( $_POST['tb_excerpt'], $_POST['post_ID'] ) ) {
		return $data;
	}
	if ( (int) $_POST['post_ID'] !== (int) $postarr['ID'] || ! tbt_verify( 'tb_summary_nonce', 'tb_summary' ) || ! current_user_can( 'edit_post', $postarr['ID'] ) ) {
		return $data;
	}
	$data['post_excerpt'] = sanitize_textarea_field( wp_unslash( $_POST['tb_excerpt'] ) );
	return $data;
}, 10, 2 );

add_action( 'save_post_post', function ( $post_id ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! tbt_verify( 'tb_extras_nonce', 'tb_extras' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$takeaways = isset( $_POST['tbt_takeaways'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tbt_takeaways'] ) ) : '';
	if ( '' !== trim( $takeaways ) ) {
		update_post_meta( $post_id, '_tb_takeaways', $takeaways );
	} else {
		delete_post_meta( $post_id, '_tb_takeaways' );
	}

	if ( ! empty( $_POST['tb_hide_toc'] ) ) {
		update_post_meta( $post_id, '_tb_hide_toc', '1' );
	} else {
		delete_post_meta( $post_id, '_tb_hide_toc' );
	}
} );

// Block editor: the "Card & article settings" sidebar panel.
add_action( 'enqueue_block_editor_assets', function () {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_script(
		'tbt-editor',
		TB_URI . '/js/editor.js',
		array( 'wp-plugins', 'wp-editor', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-dom-ready' ),
		tb_ver( 'js/editor.js' ),
		true
	);
} );
