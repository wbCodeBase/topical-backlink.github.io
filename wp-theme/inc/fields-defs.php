<?php
/**
 * Editor fields for Services, Case studies and the two listing pages.
 * Field names are part of the stored data: rename one and its saved
 * content stops showing. Add new fields freely.
 *
 * @package TopicalBacklink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tbt_field_group_defs() {
	$icon = function ( $default = 'check' ) {
		return tbt_f( 'select', 'icon', 'Icon', array( 'choices' => tbt_feature_icons(), 'default_value' => $default, 'return_format' => 'value', 'wrapper' => array( 'width' => '25' ) ) );
	};
	$img = function ( $name, $label, $extra = array() ) {
		return tbt_f( 'image', $name, $label, array_merge( array( 'return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all' ), $extra ) );
	};
	$hl  = array( 'instructions' => 'Shown with the violet brush stroke after the heading. Optional.' );
	$w   = function ( $n ) {
		return array( 'wrapper' => array( 'width' => (string) $n ) );
	};
	$num = function ( $extra = array() ) use ( $w ) {
		return array(
			tbt_f( 'number', 'value', 'Number', array_merge( $w( 25 ), array( 'instructions' => 'e.g. 3500' ) ) ),
			tbt_f( 'text', 'prefix', 'Before', array_merge( $w( 15 ), array( 'instructions' => 'e.g. +' ) ) ),
			tbt_f( 'text', 'suffix', 'After', array_merge( $w( 15 ), array( 'instructions' => 'e.g. % or +' ) ) ),
		);
	};

	/* ---------------- Service ---------------- */
	$service = array(
		tbt_tab( 'Listing card' ),
		tbt_f( 'message', '', 'How this page works', array( 'message' => 'The <b>title</b> above is the service name. <b>Excerpt</b> (box below) is the short description on the Services page, <b>Featured image</b> is its picture, and <b>Page Attributes &rarr; Order</b> sets its position. Every section below is hidden when left empty.' ) ),
		tbt_f( 'textarea', 'card_points', 'Bullet points on the Services page', array( 'rows' => 4, 'instructions' => 'One point per line.' ) ),
		tbt_rep( 'card_stats', 'Figures under the picture', array(
			tbt_f( 'text', 'label', 'Label', $w( 60 ) ),
			tbt_f( 'text', 'value', 'Value', array_merge( $w( 40 ), array( 'instructions' => 'e.g. 94% or 3,500+' ) ) ),
		), array( 'max' => 4, 'layout' => 'table', 'button_label' => 'Add figure' ) ),
		tbt_f( 'text', 'card_note', 'Small note under the figures', array( 'instructions' => 'e.g. // figures are programme averages' ) ),

		tbt_tab( 'Hero' ),
		tbt_f( 'text', 'short_name', 'Short name for the breadcrumb', array( 'instructions' => 'e.g. White-label. Empty = page title.' ) ),
		tbt_f( 'text', 'hero_heading', 'Heading', array_merge( $w( 60 ), array( 'instructions' => 'Empty = page title.' ) ) ),
		tbt_f( 'text', 'hero_highlight', 'Highlighted words', array_merge( $w( 40 ), $hl ) ),
		tbt_f( 'textarea', 'hero_lead', 'Intro paragraph', array( 'rows' => 3, 'instructions' => 'Empty = the excerpt.' ) ),
		tbt_f( 'text', 'hero_btn_label', 'Main button text', array_merge( $w( 50 ), array( 'default_value' => 'Book a call' ) ) ),
		tbt_f( 'text', 'hero_btn_url', 'Main button link', array_merge( $w( 50 ), array( 'instructions' => 'Empty = Contact page.' ) ) ),
		tbt_f( 'text', 'hero_btn2_label', 'Second button text', array_merge( $w( 50 ), array( 'instructions' => 'Empty = no second button.' ) ) ),
		tbt_f( 'text', 'hero_btn2_url', 'Second button link', array_merge( $w( 50 ), array( 'instructions' => 'e.g. #estimate to jump to the price estimator.' ) ) ),
		tbt_rep( 'hero_stats', 'Counters under the buttons', array_merge( array( tbt_f( 'text', 'label', 'Label', $w( 45 ) ) ), $num() ), array( 'max' => 4, 'layout' => 'table', 'button_label' => 'Add counter' ) ),
		tbt_rep( 'hero_chips', 'Floating badges (desktop)', array(
			tbt_f( 'text', 'title', 'Bold line', $w( 50 ) ),
			tbt_f( 'text', 'text', 'Small line', $w( 50 ) ),
		), array( 'max' => 4, 'layout' => 'table', 'button_label' => 'Add badge' ) ),

		tbt_tab( 'Overview' ),
		tbt_f( 'true_false', 'show_trust', 'Show the client logo strip', array( 'ui' => 1, 'default_value' => 1 ) ),
		tbt_f( 'text', 'overview_kicker', 'Small label', array( 'default_value' => 'What you get' ) ),
		tbt_f( 'text', 'overview_heading', 'Heading', $w( 60 ) ),
		tbt_f( 'text', 'overview_highlight', 'Highlighted words', array_merge( $w( 40 ), $hl ) ),
		tbt_f( 'textarea', 'overview_lead', 'Paragraph', array( 'rows' => 3 ) ),
		tbt_f( 'textarea', 'overview_points', 'Tick list', array( 'rows' => 4, 'instructions' => 'One point per line.' ) ),
		tbt_f( 'select', 'overview_visual', 'Picture beside it', array( 'choices' => array( 'image' => 'An image (choose below)', 'dashboard' => 'The white-label dashboard illustration', 'none' => 'Nothing' ), 'default_value' => 'image' ) ),
		$img( 'overview_image', 'Image', array( 'instructions' => 'Empty = the featured image.' ) ),

		tbt_tab( 'Features' ),
		tbt_f( 'text', 'features_kicker', 'Small label', array( 'default_value' => 'Inside every order' ) ),
		tbt_f( 'text', 'features_heading', 'Heading', $w( 60 ) ),
		tbt_f( 'text', 'features_highlight', 'Highlighted words', array_merge( $w( 40 ), $hl ) ),
		tbt_f( 'text', 'features_lead', 'Line under the heading' ),
		tbt_rep( 'features', 'Feature cards', array( $icon(), tbt_f( 'text', 'title', 'Title', $w( 75 ) ), tbt_f( 'textarea', 'text', 'Text', array( 'rows' => 2 ) ) ), array( 'button_label' => 'Add feature' ) ),

		tbt_tab( 'Process' ),
		tbt_f( 'text', 'process_kicker', 'Small label', array( 'default_value' => 'How it works' ) ),
		tbt_f( 'text', 'process_heading', 'Heading', $w( 60 ) ),
		tbt_f( 'text', 'process_highlight', 'Highlighted words', array_merge( $w( 40 ), $hl ) ),
		tbt_f( 'text', 'process_lead', 'Line under the heading', array( 'default_value' => 'Hover to pause, or pick any step to jump to it.' ) ),
		tbt_rep( 'steps', 'Steps', array(
			tbt_f( 'text', 'title', 'Step name (left list)', $w( 50 ) ),
			tbt_f( 'text', 'when', 'When', array_merge( $w( 50 ), array( 'instructions' => 'e.g. WITHIN 72 HOURS' ) ) ),
			tbt_f( 'text', 'heading', 'Panel heading' ),
			tbt_f( 'textarea', 'text', 'Panel text', array( 'rows' => 3 ) ),
			tbt_f( 'textarea', 'outputs', 'Deliverables', array( 'rows' => 2, 'instructions' => 'One per line.' ) ),
		), array( 'button_label' => 'Add step' ) ),

		tbt_tab( 'Price estimator' ),
		tbt_f( 'true_false', 'show_estimator', 'Show the price estimator', array( 'ui' => 1, 'default_value' => 0 ) ),
		tbt_f( 'text', 'est_kicker', 'Small label', array( 'default_value' => 'Transparent pricing' ) ),
		tbt_f( 'text', 'est_heading', 'Heading', array_merge( $w( 60 ), array( 'default_value' => 'Estimate a programme' ) ) ),
		tbt_f( 'text', 'est_highlight', 'Highlighted words', array_merge( $w( 40 ), array( 'default_value' => 'in ten seconds' ) ) ),
		tbt_f( 'text', 'est_lead', 'Line under the heading', array( 'default_value' => 'Same rate whether you buy one placement or fifty.' ) ),
		tbt_rep( 'est_bands', 'Authority bands', array(
			tbt_f( 'text', 'label', 'Band', array_merge( $w( 35 ), array( 'instructions' => 'e.g. DR 45-60' ) ) ),
			tbt_f( 'number', 'rate', 'Price per placement ($)', $w( 30 ) ),
			tbt_f( 'text', 'lead_time', 'Lead time', array_merge( $w( 35 ), array( 'instructions' => 'e.g. 21-30 days' ) ) ),
		), array( 'layout' => 'table', 'button_label' => 'Add band', 'instructions' => 'Empty = the standard rate card.' ) ),
		tbt_f( 'textarea', 'est_points', 'Tick list', array( 'rows' => 2, 'instructions' => 'One per line.' ) ),
		tbt_f( 'textarea', 'est_note', 'Small print', array( 'rows' => 2 ) ),

		tbt_tab( 'Comparison' ),
		tbt_f( 'text', 'compare_kicker', 'Small label', array( 'default_value' => 'The difference' ) ),
		tbt_f( 'text', 'compare_heading', 'Heading', array( 'default_value' => 'Why agencies switch to us' ) ),
		tbt_f( 'text', 'compare_them', 'Competitor column title', array( 'default_value' => 'Typical link vendor' ) ),
		tbt_rep( 'compare_rows', 'Rows', array(
			tbt_f( 'text', 'feature', 'What you get', $w( 50 ) ),
			tbt_f( 'text', 'us', 'Us', array_merge( $w( 25 ), array( 'default_value' => 'yes' ) ) ),
			tbt_f( 'text', 'them', 'Them', $w( 25 ) ),
		), array( 'layout' => 'table', 'button_label' => 'Add row', 'instructions' => 'Type yes or no for a tick or cross, or any short text (e.g. 90 days). Empty table = section hidden.' ) ),

		tbt_tab( 'Case study & reviews' ),
		tbt_f( 'post_object', 'featured_case', 'Featured case study', array( 'post_type' => array( 'tb_case_study' ), 'return_format' => 'id', 'allow_null' => 1, 'ui' => 1 ) ),
		tbt_f( 'true_false', 'show_testimonials', 'Show testimonials', array( 'ui' => 1, 'default_value' => 1 ) ),
		tbt_rep( 'testimonials', 'Testimonials', array(
			tbt_f( 'textarea', 'quote', 'Quote', array( 'rows' => 2 ) ),
			tbt_f( 'text', 'name', 'Name', $w( 40 ) ),
			tbt_f( 'text', 'role', 'Role, company', $w( 40 ) ),
			$img( 'photo', 'Photo', $w( 20 ) ),
		), array( 'max' => 3, 'button_label' => 'Add testimonial', 'instructions' => 'Empty = the standard three client quotes.' ) ),

		tbt_tab( 'FAQ' ),
		tbt_f( 'text', 'faq_heading', 'Heading', array_merge( $w( 60 ), array( 'instructions' => 'e.g. White-label,' ) ) ),
		tbt_f( 'text', 'faq_highlight', 'Highlighted words', array_merge( $w( 40 ), array( 'default_value' => 'answered' ) ) ),
		tbt_rep( 'faqs', 'Questions', array(
			tbt_f( 'text', 'question', 'Question' ),
			tbt_f( 'wysiwyg', 'answer', 'Answer', array( 'toolbar' => 'basic', 'media_upload' => 0, 'tabs' => 'visual' ) ),
		), array( 'button_label' => 'Add question' ) ),

		tbt_tab( 'Call to action' ),
		tbt_f( 'text', 'cta_heading', 'Heading', array( 'default_value' => 'Get started today. Earn powerful backlinks. Grow every client.' ) ),
		tbt_f( 'textarea', 'cta_text', 'Text', array( 'rows' => 2, 'default_value' => 'Book a call and get your free link audit. Review recommended placements and track every order from outreach to live link.' ) ),
	);

	/* ---------------- Case study ---------------- */
	$case = array(
		tbt_tab( 'Card & facts' ),
		tbt_f( 'message', '', 'How this page works', array( 'message' => 'The <b>title</b> above is the case study headline. <b>Excerpt</b> is the summary on the Case studies page, <b>Featured image</b> is the main picture, and <b>Page Attributes &rarr; Order</b> sets its position. Every section below is hidden when left empty.' ) ),
		tbt_f( 'text', 'client', 'Client name', $w( 50 ) ),
		tbt_f( 'text', 'industry', 'Industry', $w( 50 ) ),
		tbt_f( 'text', 'service_name', 'Service used', $w( 50 ) ),
		tbt_f( 'text', 'market', 'Market', array_merge( $w( 50 ), array( 'instructions' => 'e.g. United States' ) ) ),
		tbt_f( 'number', 'dr_before', 'DR before', $w( 25 ) ),
		tbt_f( 'number', 'dr_after', 'DR after', $w( 25 ) ),
		tbt_f( 'text', 'duration', 'Duration', array_merge( $w( 25 ), array( 'instructions' => 'e.g. 10 months' ) ) ),
		tbt_f( 'text', 'result', 'Headline result', array_merge( $w( 25 ), array( 'instructions' => 'e.g. +156% enquiries' ) ) ),
		tbt_rep( 'card_stats', 'Three figures on the card', array(
			tbt_f( 'text', 'label', 'Label', $w( 60 ) ),
			tbt_f( 'text', 'value', 'Value', $w( 40 ) ),
		), array( 'max' => 3, 'layout' => 'table', 'button_label' => 'Add figure', 'instructions' => 'Empty = headline result, DR and duration.' ) ),

		tbt_tab( 'Hero' ),
		tbt_f( 'text', 'hero_heading', 'Heading', array_merge( $w( 60 ), array( 'instructions' => 'Empty = page title.' ) ) ),
		tbt_f( 'text', 'hero_highlight', 'Highlighted words', array_merge( $w( 40 ), $hl ) ),
		tbt_f( 'textarea', 'hero_lead', 'Intro paragraph', array( 'rows' => 3, 'instructions' => 'Empty = the excerpt.' ) ),
		tbt_rep( 'hero_chips', 'Floating badges around the picture (desktop)', array(
			tbt_f( 'text', 'title', 'Bold line', $w( 50 ) ),
			tbt_f( 'text', 'text', 'Small line', $w( 50 ) ),
		), array( 'max' => 4, 'layout' => 'table', 'button_label' => 'Add badge' ) ),

		tbt_tab( 'Key numbers' ),
		tbt_rep( 'kpis', 'Big numbers under the hero', array_merge(
			$num(),
			array(
				tbt_f( 'number', 'from', 'Count up from', array_merge( $w( 15 ), array( 'instructions' => 'Optional' ) ) ),
				tbt_f( 'text', 'label', 'Label', $w( 50 ) ),
				tbt_f( 'text', 'note', 'Small note', array_merge( $w( 50 ), array( 'instructions' => 'e.g. FROM 19' ) ) ),
			)
		), array( 'max' => 4, 'button_label' => 'Add number' ) ),

		tbt_tab( '1. Challenge' ),
		tbt_f( 'text', 'challenge_heading', 'Heading' ),
		tbt_f( 'wysiwyg', 'challenge_text', 'Text', array( 'toolbar' => 'basic', 'media_upload' => 0 ) ),
		tbt_f( 'textarea', 'challenge_brief', 'The brief (highlighted box)', array( 'rows' => 2 ) ),

		tbt_tab( '2. Strategy' ),
		tbt_f( 'text', 'strategy_heading', 'Heading' ),
		tbt_f( 'wysiwyg', 'strategy_text', 'Text', array( 'toolbar' => 'basic', 'media_upload' => 0 ) ),
		tbt_rep( 'strategy_cards', 'Strategy cards', array( $icon( 'target' ), tbt_f( 'text', 'title', 'Title', $w( 75 ) ), tbt_f( 'textarea', 'text', 'Text', array( 'rows' => 2 ) ) ), array( 'max' => 6, 'button_label' => 'Add card' ) ),

		tbt_tab( '3. Execution' ),
		tbt_f( 'text', 'execution_heading', 'Heading' ),
		tbt_f( 'textarea', 'execution_text', 'Text', array( 'rows' => 2 ) ),
		tbt_rep( 'timeline', 'Timeline', array(
			tbt_f( 'text', 'when', 'When', array_merge( $w( 30 ), array( 'instructions' => 'e.g. MONTHS 2-4' ) ) ),
			tbt_f( 'text', 'title', 'Title', $w( 70 ) ),
			tbt_f( 'textarea', 'text', 'Text', array( 'rows' => 2 ) ),
		), array( 'button_label' => 'Add stage' ) ),

		tbt_tab( '4. Results' ),
		tbt_f( 'text', 'results_heading', 'Heading' ),
		tbt_f( 'textarea', 'results_text', 'Text', array( 'rows' => 2 ) ),
		tbt_f( 'text', 'chart_labels', 'Chart: x-axis labels', array( 'instructions' => 'Comma separated, one per point, e.g. Month 0,Month 1,Month 2' ) ),
		tbt_rep( 'chart_series', 'Chart: metrics (one tab each)', array(
			tbt_f( 'text', 'name', 'Metric name', $w( 30 ) ),
			tbt_f( 'text', 'values', 'Values', array_merge( $w( 70 ), array( 'instructions' => 'Comma separated, same count as the labels, e.g. 19,21,24' ) ) ),
			tbt_f( 'number', 'max', 'Chart top', array_merge( $w( 20 ), array( 'instructions' => 'Empty = automatic' ) ) ),
			tbt_f( 'text', 'prefix', 'Before', $w( 15 ) ),
			tbt_f( 'text', 'suffix', 'After', $w( 15 ) ),
			tbt_f( 'text', 'aria', 'Description for screen readers', $w( 50 ) ),
		), array( 'max' => 4, 'button_label' => 'Add metric' ) ),
		tbt_rep( 'bars', 'Before / after bars', array(
			tbt_f( 'text', 'label', 'Label', $w( 40 ) ),
			tbt_f( 'number', 'before', 'Before', $w( 20 ) ),
			tbt_f( 'number', 'after', 'After', $w( 20 ) ),
			tbt_f( 'text', 'change', 'Change', array_merge( $w( 20 ), array( 'instructions' => 'e.g. +392%' ) ) ),
		), array( 'layout' => 'table', 'button_label' => 'Add bar' ) ),

		tbt_tab( '5. Placements' ),
		tbt_f( 'text', 'placements_heading', 'Heading' ),
		tbt_f( 'textarea', 'placements_text', 'Text', array( 'rows' => 2 ) ),
		tbt_rep( 'placements', 'Placements table', array(
			tbt_f( 'text', 'type', 'Publication type', $w( 30 ) ),
			tbt_f( 'text', 'dr', 'DR', $w( 10 ) ),
			tbt_f( 'text', 'traffic', 'Monthly traffic', $w( 15 ) ),
			tbt_f( 'text', 'target', 'Target page', $w( 25 ) ),
			tbt_f( 'select', 'status', 'Status', array_merge( $w( 20 ), array( 'choices' => array( 'live' => 'Live', 'editorial' => 'Editorial', 'pending' => 'Pending' ), 'default_value' => 'live' ) ) ),
		), array( 'layout' => 'table', 'button_label' => 'Add placement' ) ),

		tbt_tab( 'Client quote' ),
		tbt_f( 'textarea', 'quote', 'Quote', array( 'rows' => 3 ) ),
		tbt_f( 'text', 'quote_name', 'Name', $w( 40 ) ),
		tbt_f( 'text', 'quote_role', 'Role, company', $w( 40 ) ),
		$img( 'quote_photo', 'Photo', $w( 20 ) ),

		tbt_tab( 'Call to action' ),
		tbt_f( 'text', 'cta_heading', 'Heading', array( 'default_value' => 'Want a result like this for your site?' ) ),
		tbt_f( 'textarea', 'cta_text', 'Text', array( 'rows' => 2, 'default_value' => 'Tell us the domain. Within 72 hours you get the competitor link gap and the specific placements that would close it.' ) ),
	);

	/* ---------------- Services / Case studies listing pages ---------------- */
	$listing = array(
		tbt_f( 'message', '', 'About this page', array( 'message' => 'The list on this page comes from <b>Services</b> / <b>Case studies</b> in the left menu. Here you can change the headings around it. Empty fields keep the design text. Wrap words in &lt;span class="mark"&gt;...&lt;/span&gt; to highlight them.' ) ),
		tbt_f( 'text', 'hero_heading', 'Page heading' ),
		tbt_f( 'textarea', 'hero_sub', 'Text under the heading', array( 'rows' => 2 ) ),
		tbt_f( 'text', 'list_kicker', 'Small label above the list', $w( 30 ) ),
		tbt_f( 'text', 'list_heading', 'List heading', $w( 70 ) ),
		tbt_f( 'textarea', 'list_lead', 'Text under the list heading', array( 'rows' => 2 ) ),
		tbt_f( 'text', 'cta_heading', 'Call to action heading' ),
		tbt_f( 'textarea', 'cta_text', 'Call to action text', array( 'rows' => 2 ) ),
	);

	return array(
		'tb_service'    => array(
			'title'    => 'Service page content',
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'tb_service' ) ) ),
			'fields'   => $service,
		),
		'tb_case_study' => array(
			'title'    => 'Case study content',
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'tb_case_study' ) ) ),
			'fields'   => $case,
		),
		'tb_listing'    => array(
			'title'    => 'Page headings',
			'location' => array(
				array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'templates/services.php' ) ),
				array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'templates/case-studies.php' ) ),
			),
			'fields'   => $listing,
		),
	);
}
