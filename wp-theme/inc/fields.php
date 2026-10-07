<?php
/**
 * Field engine for the Services and Case studies editors.
 *
 * Field groups are defined in PHP (inc/fields-defs.php) and registered with
 * ACF as local groups, so they appear in the editor but can't be broken from
 * Custom Fields > Field Groups. Values are stored in ACF's own format, and
 * the templates read them straight from post meta (tbt_fields()), so pages
 * still render if ACF is switched off; ACF PRO is only needed to edit them.
 *
 * @package TopicalBacklink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once TB_DIR . '/inc/fields-defs.php';

/* ------------------------------------------------------------------
 * Definition helpers
 * ------------------------------------------------------------------ */

/** One field definition. Keys are added later by tbt_field_keys(). */
function tbt_f( $type, $name, $label, $extra = array() ) {
	return array_merge( array( 'type' => $type, 'name' => $name, 'label' => $label ), $extra );
}

/** A tab separator in the editor. */
function tbt_tab( $label ) {
	return array( 'type' => 'tab', 'name' => '', 'label' => $label, 'placement' => 'left' );
}

/** A repeater. */
function tbt_rep( $name, $label, $subs, $extra = array() ) {
	return tbt_f( 'repeater', $name, $label, array_merge( array(
		'sub_fields'   => $subs,
		'layout'       => 'block',
		'button_label' => 'Add row',
	), $extra ) );
}

/** Stable ACF keys derived from each field's path. */
function tbt_field_keys( $fields, $path ) {
	foreach ( $fields as $i => $f ) {
		$p                 = $path . '/' . ( '' !== $f['name'] ? $f['name'] : 'tab' . $i );
		$fields[ $i ]['key'] = 'field_tb' . substr( md5( $p ), 0, 13 );
		if ( ! empty( $f['sub_fields'] ) ) {
			$fields[ $i ]['sub_fields'] = tbt_field_keys( $f['sub_fields'], $p );
		}
	}
	return $fields;
}

/** The registered field groups: id => [ title, location, fields ]. */
function tbt_field_groups() {
	static $groups = null;
	if ( null === $groups ) {
		$groups = array();
		foreach ( tbt_field_group_defs() as $id => $g ) {
			$g['fields']   = tbt_field_keys( $g['fields'], $id );
			$groups[ $id ] = $g;
		}
	}
	return $groups;
}

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	foreach ( tbt_field_groups() as $id => $g ) {
		acf_add_local_field_group( array(
			'key'                   => 'group_' . $id,
			'title'                 => $g['title'],
			'fields'                => $g['fields'],
			'location'              => $g['location'],
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'menu_order'            => 0,
			'active'                => true,
		) );
	}
} );

/* ------------------------------------------------------------------
 * Reading and writing values (ACF storage format)
 * ------------------------------------------------------------------ */

function tbt_read_fields( $post_id, $fields, $prefix = '' ) {
	$out = array();
	foreach ( $fields as $f ) {
		if ( 'tab' === $f['type'] || 'message' === $f['type'] ) {
			continue;
		}
		$meta = $prefix . $f['name'];
		if ( 'repeater' === $f['type'] ) {
			$rows = array();
			$n    = (int) get_post_meta( $post_id, $meta, true );
			for ( $i = 0; $i < $n; $i++ ) {
				$rows[] = tbt_read_fields( $post_id, $f['sub_fields'], $meta . '_' . $i . '_' );
			}
			$out[ $f['name'] ] = $rows;
			continue;
		}
		if ( ! metadata_exists( 'post', $post_id, $meta ) ) {
			$out[ $f['name'] ] = isset( $f['default_value'] ) ? $f['default_value'] : '';
			continue;
		}
		$out[ $f['name'] ] = get_post_meta( $post_id, $meta, true );
	}
	return $out;
}

function tbt_write_fields( $post_id, $fields, $values, $prefix = '' ) {
	foreach ( $fields as $f ) {
		if ( 'tab' === $f['type'] || 'message' === $f['type'] || ! array_key_exists( $f['name'], $values ) ) {
			continue;
		}
		$meta = $prefix . $f['name'];
		$v    = $values[ $f['name'] ];
		if ( 'repeater' === $f['type'] ) {
			$rows = array_values( (array) $v );
			foreach ( $rows as $i => $row ) {
				tbt_write_fields( $post_id, $f['sub_fields'], $row, $meta . '_' . $i . '_' );
			}
			$v = count( $rows );
		}
		update_post_meta( $post_id, $meta, is_string( $v ) ? wp_slash( $v ) : $v );
		update_post_meta( $post_id, '_' . $meta, $f['key'] );
	}
}

/** All field values of a post for the group attached to its type (or a named group). */
function tbt_fields( $post_id = 0, $group = '' ) {
	static $cache = array();
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $group ) {
		$group = tbt_group_for_post( $post_id );
	}
	$k = $post_id . ':' . $group;
	if ( ! isset( $cache[ $k ] ) ) {
		$groups      = tbt_field_groups();
		$cache[ $k ] = isset( $groups[ $group ] ) ? tbt_read_fields( $post_id, $groups[ $group ]['fields'] ) : array();
	}
	return $cache[ $k ];
}

/** Write a set of values into a post using its group's definitions. */
function tbt_seed_fields( $post_id, $values, $group = '' ) {
	$groups = tbt_field_groups();
	$group  = $group ? $group : tbt_group_for_post( $post_id );
	if ( isset( $groups[ $group ] ) ) {
		tbt_write_fields( $post_id, $groups[ $group ]['fields'], $values );
	}
}

function tbt_group_for_post( $post_id ) {
	$type = get_post_type( $post_id );
	if ( 'tb_service' === $type ) {
		return 'tb_service';
	}
	if ( 'tb_case_study' === $type ) {
		return 'tb_case_study';
	}
	return 'tb_listing';
}

/* ------------------------------------------------------------------
 * Value helpers for templates
 * ------------------------------------------------------------------ */

/** Trimmed string value, or $fallback when empty. */
function tbt_v( $arr, $key, $fallback = '' ) {
	$v = isset( $arr[ $key ] ) ? $arr[ $key ] : '';
	$v = is_string( $v ) ? trim( $v ) : $v;
	return ( '' === $v || null === $v || false === $v || array() === $v ) ? $fallback : $v;
}

/** Non-empty lines of a textarea. */
function tbt_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', (string) $text ) ), 'strlen' ) );
}

/** Repeater rows that have at least one filled value. */
function tbt_filled( $rows ) {
	return array_values( array_filter( (array) $rows, function ( $r ) {
		return is_array( $r ) && array_filter( $r, function ( $v ) {
			return '' !== trim( (string) $v );
		} );
	} ) );
}

/** "Heading <swoosh>highlight</swoosh>" as escaped HTML. */
function tbt_swoosh( $text, $highlight ) {
	$out = esc_html( $text );
	if ( '' !== trim( (string) $highlight ) ) {
		$out .= ' <span class="swoosh">' . esc_html( $highlight ) . '<i class="swoosh__ink" aria-hidden="true"></i></span>';
	}
	return trim( $out );
}

/** Format a number with optional prefix/suffix: 3500 + "+" -> "3,500+". */
function tbt_num( $value, $prefix = '', $suffix = '' ) {
	$n   = (float) $value;
	$dec = floor( $n ) == $n ? 0 : 1; // phpcs:ignore WordPress.PHP.StrictComparisons
	return $prefix . number_format( $n, $dec ) . $suffix;
}

/** data-count attributes for the counting animation in main.js. */
function tbt_count_attrs( $row, $from = null ) {
	$a = ' data-count="' . esc_attr( (float) tbt_v( $row, 'value', 0 ) ) . '"';
	if ( null !== $from && '' !== (string) $from ) {
		$a .= ' data-from="' . esc_attr( (float) $from ) . '"';
	}
	if ( tbt_v( $row, 'prefix' ) ) {
		$a .= ' data-prefix="' . esc_attr( $row['prefix'] ) . '"';
	}
	if ( tbt_v( $row, 'suffix' ) ) {
		$a .= ' data-suffix="' . esc_attr( $row['suffix'] ) . '"';
	}
	if ( floor( (float) tbt_v( $row, 'value', 0 ) ) != (float) tbt_v( $row, 'value', 0 ) ) { // phpcs:ignore WordPress.PHP.StrictComparisons
		$a .= ' data-dec="1"';
	}
	return $a;
}

/** <img> for an attachment ID, or a theme-relative/absolute URL. */
function tbt_image( $img, $size = 'large', $attrs = array() ) {
	if ( is_numeric( $img ) && (int) $img > 0 ) {
		return wp_get_attachment_image( (int) $img, $size, false, $attrs );
	}
	if ( is_string( $img ) && '' !== $img ) {
		$src = preg_match( '#^https?://#', $img ) ? $img : TB_URI . '/' . ltrim( $img, '/' );
		$out = '<img src="' . esc_url( $src ) . '"';
		foreach ( $attrs as $k => $v ) {
			$out .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
		}
		return $out . '>';
	}
	return '';
}

/** Feature icons (same artwork as the static design). */
function tbt_feature_icons() {
	return array(
		'search'  => 'Magnifier',
		'shield'  => 'Shield',
		'send'    => 'Paper plane',
		'pen'     => 'Pen',
		'chart'   => 'Chart',
		'refresh' => 'Refresh',
		'target'  => 'Target',
		'link'    => 'Link',
		'globe'   => 'Globe',
		'pin'     => 'Map pin',
		'news'    => 'Newspaper',
		'bulb'    => 'Light bulb',
		'users'   => 'People',
		'clock'   => 'Clock',
		'trend'   => 'Trend up',
		'brief'   => 'Briefcase',
		'check'   => 'Tick',
	);
}

function tbt_svg( $name ) {
	$p = array(
		'search'  => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
		'shield'  => '<path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/>',
		'send'    => '<path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7Z"/>',
		'pen'     => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
		'chart'   => '<path d="M3 3v18h18"/><path d="m7 14 4-4 4 4 5-6"/>',
		'refresh' => '<path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/>',
		'target'  => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r=".5"/>',
		'link'    => '<path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 1 1 0 10h-2"/><path d="M8 12h8"/>',
		'globe'   => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18"/>',
		'pin'     => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
		'news'    => '<path d="M4 4h13v16H6a2 2 0 0 1-2-2Z"/><path d="M17 8h3v10a2 2 0 0 1-2 2"/><path d="M8 8h5"/><path d="M8 12h5"/><path d="M8 16h3"/>',
		'bulb'    => '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>',
		'users'   => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m17 11 2 2 4-4"/>',
		'clock'   => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'trend'   => '<polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>',
		'brief'   => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
		'check'   => '<path d="M20 6 9 17l-5-5"/>',
		'x'       => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
		'arrow'   => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
	);
	$d = isset( $p[ $name ] ) ? $p[ $name ] : $p['check'];
	return '<svg class="ico" viewBox="0 0 24 24" aria-hidden="true">' . $d . '</svg>';
}
