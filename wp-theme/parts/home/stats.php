<?php
/**
 * Home: stats / network figures. ACF group "stat_section".
 *
 * @package TopicalBacklink
 */

$f       = tb_home_group( 'stat_section' );
$heading = tb_val( $f, 'stats_heading', 'The reason to pick <span class="mark">TopicalBacklink</span> this season' );
$items   = tb_rows( $f, 'stats_items', array(
	array( 'number' => 3500,  'label' => 'Trusted agencies',   'hover_text' => 'Agencies white-labelling us since 2019' ),
	array( 'number' => 11000, 'label' => 'Projects delivered', 'hover_text' => 'Across 183 industries and 52 nations' ),
	array( 'number' => 80000, 'label' => 'Links live',         'hover_text' => '94% still live at twelve months' ),
	array( 'number' => 7000,  'label' => 'Domains boosted',    'hover_text' => 'Average lift of +38 DR in six months' ),
) );

// Icons cycle in the original order: briefcase, chart, link, trend.
$icons = array(
	'<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
	'<path d="M3 3v18h18"/><path d="m7 14 4-4 4 4 5-6"/>',
	'<path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 1 1 0 10h-2"/><path d="M8 12h8"/>',
	'<polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>',
);
?>
<!-- ============================== STATS / NETWORK ============================== -->
<section class="stats" id="network">
  <div class="ticker" id="ticker">
    <div class="ticker__track" id="tickerTrack"></div>
    <div class="ticker__fade ticker__fade--l" aria-hidden="true"></div>
    <div class="ticker__fade ticker__fade--r" aria-hidden="true"></div>
  </div>

  <div class="shell stats__inner">
    <div class="stats__wash" aria-hidden="true"></div>

    <h2 class="h2 center" data-rise><?php tb_heading( $heading ); ?></h2>

    <div class="figures" id="figures">
      <?php
      foreach ( $items as $i => $item ) :
          $num   = absint( tb_val( $item, 'number', 0 ) );
          $label = tb_val( $item, 'label' );
          $hover = tb_val( $item, 'hover_text' );
          ?>
      <button class="fig" type="button" data-to="<?php echo esc_attr( $num ); ?>" data-detail="<?php echo esc_attr( $hover ); ?>">
        <span class="fig__bar" aria-hidden="true"></span>
        <span class="fig__ico">
          <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><?php echo $icons[ $i % count( $icons ) ]; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG from this file. ?></svg>
        </span>
        <span class="fig__v" data-val><?php echo esc_html( number_format( $num ) . '+' ); ?></span>
        <span class="fig__l"><?php echo esc_html( $label ); ?></span>
        <span class="fig__d"><?php echo esc_html( $hover ); ?></span>
      </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>
