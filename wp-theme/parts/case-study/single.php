<?php
/**
 * Case study detail page, built from the "Case study content" fields
 * (inc/fields-defs.php). Each section is skipped when its content is empty;
 * the "On this page" list follows whichever sections are present.
 *
 * @package TopicalBacklink
 */

the_post();

$f      = tbt_fields();
$id     = get_the_ID();
$title  = get_the_title();
$arrow  = tbt_svg( 'arrow' );
$hero_h = tbt_v( $f, 'hero_heading', $title );
$lead   = tbt_v( $f, 'hero_lead', get_the_excerpt() );
$tags   = array_filter( array( tbt_v( $f, 'industry' ), tbt_v( $f, 'service_name' ), tbt_v( $f, 'market' ) ) );
$chips  = array_slice( tbt_filled( $f['hero_chips'] ), 0, 4 );
$kpis   = array_slice( tbt_filled( $f['kpis'] ), 0, 4 );
$crumb  = tbt_v( $f, 'client', $title );

$chip_ico = array( array( 'trend', '' ), array( 'chart', ' hchip__ico--gain' ), array( 'link', ' hchip__ico--sky' ), array( 'check', '' ) );
$chip_dep = array( 14, -12, 10, -16 );

// Body sections, in order, with the content that decides whether each shows.
$cards    = tbt_filled( $f['strategy_cards'] );
$timeline = tbt_filled( $f['timeline'] );
$series   = array_values( array_filter( tbt_filled( $f['chart_series'] ), function ( $s ) {
	return '' !== trim( (string) tbt_v( $s, 'values' ) );
} ) );
$bars     = tbt_filled( $f['bars'] );
$places   = tbt_filled( $f['placements'] );

$sections = array();
if ( tbt_v( $f, 'challenge_heading' ) || tbt_v( $f, 'challenge_text' ) ) {
	$sections['challenge'] = array( 'The challenge', tbt_v( $f, 'challenge_heading', 'The challenge' ) );
}
if ( tbt_v( $f, 'strategy_heading' ) || tbt_v( $f, 'strategy_text' ) || $cards ) {
	$sections['strategy'] = array( 'Our strategy', tbt_v( $f, 'strategy_heading', 'Our strategy' ) );
}
if ( tbt_v( $f, 'execution_heading' ) || $timeline ) {
	$sections['execution'] = array( 'Execution', tbt_v( $f, 'execution_heading', 'Execution' ) );
}
if ( tbt_v( $f, 'results_heading' ) || $series || $bars ) {
	$sections['results'] = array( 'Results', tbt_v( $f, 'results_heading', 'Results' ) );
}
if ( $places ) {
	$sections['placements'] = array( 'Sample placements', tbt_v( $f, 'placements_heading', 'Sample placements' ) );
}

$glance = array_filter( array(
	'CLIENT'   => tbt_v( $f, 'client' ),
	'INDUSTRY' => tbt_v( $f, 'industry' ),
	'SERVICE'  => tbt_v( $f, 'service_name' ),
	'MARKET'   => tbt_v( $f, 'market' ),
	'DURATION' => tbt_v( $f, 'duration' ),
) );

$sec_n = 0;
$label = function ( $key ) use ( &$sec_n, $sections ) {
	$sec_n++;
	return sprintf( '%02d · %s', $sec_n, strtoupper( $sections[ $key ][0] ) );
};
?>
<main id="top">

<!-- ============================== HERO ============================== -->
<section class="roi">
  <div class="hfx" aria-hidden="true">
    <span class="hfx__grid"></span>
    <span class="hfx__orb hfx__orb--a"></span>
    <span class="hfx__orb hfx__orb--b"></span>
    <span class="hfx__orb hfx__orb--c"></span>
    <span class="hfx__spot"></span>
  </div>

  <div class="shell roi__inner">
    <p class="crumb" data-rise><a href="<?php tb_link( 'home' ); ?>">Home</a> <span>/</span> <a href="<?php tb_link( 'case-studies' ); ?>">Case studies</a> <span>/</span> <?php echo esc_html( $crumb ); ?></p>

    <?php if ( $tags ) : ?>
    <div class="ctags" data-rise>
      <?php foreach ( $tags as $t ) : ?><span class="ctag"><?php echo esc_html( $t ); ?></span><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <h1 class="h2 h2--hero center" data-rise><?php echo tbt_swoosh( $hero_h, tbt_v( $f, 'hero_highlight' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>

    <?php if ( $lead ) : ?><p class="lead lead--center" data-rise><?php echo esc_html( $lead ); ?></p><?php endif; ?>

    <?php if ( tbt_has_image( $id ) ) : ?>
    <div class="cshow" data-rise>
      <div class="frame">
        <div class="dash__chrome">
          <i class="d d--r"></i><i class="d"></i><i class="d"></i>
          <span class="mono"><?php echo esc_html( strtolower( $crumb ) . ' · programme overview' ); ?></span>
        </div>
        <figure class="shot shot--wide shot--contain"><?php echo tbt_post_image( $id, 'large', array( 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
      </div>
      <?php foreach ( $chips as $i => $c ) : ?>
      <div class="hchip hchip--<?php echo (int) $i + 1; ?>" data-depth="<?php echo (int) $chip_dep[ $i ]; ?>" aria-hidden="true">
        <div class="hchip__in">
          <span class="hchip__ico<?php echo esc_attr( $chip_ico[ $i ][1] ); ?>"><?php echo tbt_svg( $chip_ico[ $i ][0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
          <span><b><?php echo esc_html( tbt_v( $c, 'title' ) ); ?></b><i><?php echo esc_html( tbt_v( $c, 'text' ) ); ?></i></span>
          <?php if ( 1 === $i ) : ?><svg class="hchip__spark" viewBox="0 0 78 26"><path d="M1 23 C 14 22, 20 21, 28 18 S 44 12, 52 9 S 66 4, 77 2" fill="none" stroke="#6d28d9" stroke-width="2" stroke-linecap="round"/></svg><?php endif; ?>
          <?php if ( 3 === $i ) : ?><span class="hchip__live"></span><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if ( $kpis ) : ?>
<!-- ============================== KPIs ============================== -->
<section>
  <div class="shell">
    <div class="kpis" data-rise>
      <?php foreach ( $kpis as $k ) : ?>
      <div class="kpi">
        <b class="kpi__v"<?php echo tbt_count_attrs( $k, tbt_v( $k, 'from' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( tbt_num( tbt_v( $k, 'value', 0 ), tbt_v( $k, 'prefix' ), tbt_v( $k, 'suffix' ) ) ); ?></b>
        <span class="kpi__l"><?php echo esc_html( tbt_v( $k, 'label' ) ); ?></span>
        <?php if ( tbt_v( $k, 'note' ) ) : ?><span class="kpi__d"><?php echo esc_html( $k['note'] ); ?></span><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ( $sections || $glance ) : ?>
<!-- ============================== BODY ============================== -->
<section>
  <div class="shell">
    <div class="csbody">

      <aside class="csaside">
        <?php if ( count( $sections ) > 1 ) : ?>
        <nav class="toc" aria-label="On this page">
          <span class="toc__k">ON THIS PAGE</span>
          <div class="toc__prog" aria-hidden="true"><i></i></div>
          <ol>
            <?php foreach ( $sections as $key => $s ) : ?><li><a href="#<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $s[0] ); ?></a></li><?php endforeach; ?>
          </ol>
        </nav>
        <?php endif; ?>

        <div class="glance">
          <?php if ( $glance ) : ?>
          <dl>
            <?php foreach ( $glance as $k => $v ) : ?><div><dt><?php echo esc_html( $k ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div><?php endforeach; ?>
          </dl>
          <?php endif; ?>
          <a href="<?php tb_link( 'contact' ); ?>" class="btn btn--violet">Get results like this</a>
        </div>
      </aside>

      <article class="prose">

        <?php if ( isset( $sections['challenge'] ) ) : ?>
        <section id="challenge">
          <span class="prose__k"><?php echo esc_html( $label( 'challenge' ) ); ?></span>
          <h2><?php echo esc_html( $sections['challenge'][1] ); ?></h2>
          <?php echo wp_kses_post( wpautop( tbt_v( $f, 'challenge_text' ) ) ); ?>
          <?php if ( tbt_v( $f, 'challenge_brief' ) ) : ?>
          <div class="callout" data-rise>
            <b>THE BRIEF</b>
            <p><?php echo esc_html( $f['challenge_brief'] ); ?></p>
          </div>
          <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if ( isset( $sections['strategy'] ) ) : ?>
        <section id="strategy">
          <span class="prose__k"><?php echo esc_html( $label( 'strategy' ) ); ?></span>
          <h2><?php echo esc_html( $sections['strategy'][1] ); ?></h2>
          <?php echo wp_kses_post( wpautop( tbt_v( $f, 'strategy_text' ) ) ); ?>
          <?php if ( $cards ) : ?>
          <div class="fgrid" data-rise>
            <?php foreach ( $cards as $c ) : ?>
            <div class="fcard">
              <span class="fcard__ico"><?php echo tbt_svg( tbt_v( $c, 'icon', 'target' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
              <h3 class="fcard__h"><?php echo esc_html( tbt_v( $c, 'title' ) ); ?></h3>
              <p class="fcard__b"><?php echo esc_html( tbt_v( $c, 'text' ) ); ?></p>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if ( isset( $sections['execution'] ) ) : ?>
        <section id="execution">
          <span class="prose__k"><?php echo esc_html( $label( 'execution' ) ); ?></span>
          <h2><?php echo esc_html( $sections['execution'][1] ); ?></h2>
          <?php if ( tbt_v( $f, 'execution_text' ) ) : ?><p><?php echo esc_html( $f['execution_text'] ); ?></p><?php endif; ?>
          <?php if ( $timeline ) : ?>
          <div class="tline">
            <span class="tline__fill" aria-hidden="true"></span>
            <?php foreach ( $timeline as $t ) : ?>
            <div class="tl">
              <span class="tl__k"><?php echo esc_html( tbt_v( $t, 'when' ) ); ?></span>
              <h3 class="tl__h"><?php echo esc_html( tbt_v( $t, 'title' ) ); ?></h3>
              <p class="tl__b"><?php echo esc_html( tbt_v( $t, 'text' ) ); ?></p>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if ( isset( $sections['results'] ) ) : ?>
        <section id="results">
          <span class="prose__k"><?php echo esc_html( $label( 'results' ) ); ?></span>
          <h2><?php echo esc_html( $sections['results'][1] ); ?></h2>
          <?php if ( tbt_v( $f, 'results_text' ) ) : ?><p><?php echo esc_html( $f['results_text'] ); ?></p><?php endif; ?>

          <?php if ( $series ) : ?>
          <?php
          $labels = array_map( 'trim', explode( ',', (string) tbt_v( $f, 'chart_labels' ) ) );
          $first  = array_map( 'floatval', explode( ',', $series[0]['values'] ) );
          ?>
          <div class="cschart" id="csChart" data-labels="<?php echo esc_attr( implode( ',', $labels ) ); ?>" data-rise>
            <div class="cschart__top">
              <div class="cschart__now" aria-hidden="true"><?php echo esc_html( end( $first ) ); ?></div>
              <div class="mtabs" role="tablist" aria-label="Results metric">
                <?php
                foreach ( $series as $i => $s ) :
                    $vals = array_map( 'floatval', array_filter( array_map( 'trim', explode( ',', $s['values'] ) ), 'strlen' ) );
                    $max  = (float) tbt_v( $s, 'max', 0 );
                    if ( $max <= 0 && $vals ) {
                        $max = max( $vals ) * 1.12;
                    }
                    ?>
                <button class="mtab" role="tab" aria-selected="<?php echo $i ? 'false' : 'true'; ?>"<?php echo $i ? ' tabindex="-1"' : ''; ?>
                  data-vals="<?php echo esc_attr( implode( ',', $vals ) ); ?>" data-min="0" data-max="<?php echo esc_attr( $max ); ?>"
                  <?php if ( tbt_v( $s, 'prefix' ) ) : ?>data-prefix="<?php echo esc_attr( $s['prefix'] ); ?>" <?php endif; ?>
                  <?php if ( tbt_v( $s, 'suffix' ) ) : ?>data-suffix="<?php echo esc_attr( $s['suffix'] ); ?>" <?php endif; ?>
                  data-aria="<?php echo esc_attr( tbt_v( $s, 'aria', tbt_v( $s, 'name' ) ) ); ?>"><?php echo esc_html( tbt_v( $s, 'name', 'Metric' ) ); ?></button>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="cschart__plot"></div>
          </div>
          <?php endif; ?>

          <?php if ( $bars ) : ?>
          <div class="ba" data-inview data-rise>
            <div class="ba__key"><span><i></i>Before</span><span><i class="post"></i>After<?php echo tbt_v( $f, 'duration' ) ? ' ' . esc_html( $f['duration'] ) : ''; ?></span></div>
            <?php
            foreach ( $bars as $b ) :
                $pre  = (float) tbt_v( $b, 'before', 0 );
                $post = (float) tbt_v( $b, 'after', 0 );
                $top  = max( $pre, $post, 1 ) / .88;
                ?>
            <div class="ba__row">
              <div class="ba__lab"><?php echo esc_html( tbt_v( $b, 'label' ) ); ?> <?php if ( tbt_v( $b, 'change' ) ) : ?><em><?php echo esc_html( $b['change'] ); ?></em><?php endif; ?></div>
              <div class="ba__track">
                <span class="ba__bar ba__bar--pre" style="--w:<?php echo esc_attr( max( 6, round( $pre / $top * 100 ) ) ); ?>%"><?php echo esc_html( tbt_num( $pre ) ); ?></span>
                <span class="ba__bar ba__bar--post" style="--w:<?php echo esc_attr( max( 6, round( $post / $top * 100 ) ) ); ?>%"><?php echo esc_html( tbt_num( $post ) ); ?></span>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if ( isset( $sections['placements'] ) ) : ?>
        <section id="placements">
          <span class="prose__k"><?php echo esc_html( $label( 'placements' ) ); ?></span>
          <h2><?php echo esc_html( $sections['placements'][1] ); ?></h2>
          <?php if ( tbt_v( $f, 'placements_text' ) ) : ?><p><?php echo esc_html( $f['placements_text'] ); ?></p><?php endif; ?>
          <div class="tblwrap" data-rise>
            <table class="ctable">
              <thead><tr><th>Publication type</th><th>DR</th><th>Monthly traffic</th><th>Target page</th><th>Status</th></tr></thead>
              <tbody>
                <?php
                $st = array( 'live' => array( 'spill--live', 'LIVE' ), 'editorial' => array( 'spill--ed', 'EDITORIAL' ), 'pending' => array( 'spill--ed', 'PENDING' ) );
                foreach ( $places as $p ) :
                    $s = isset( $st[ tbt_v( $p, 'status', 'live' ) ] ) ? $st[ tbt_v( $p, 'status', 'live' ) ] : $st['live'];
                    ?>
                <tr><th><?php echo esc_html( tbt_v( $p, 'type' ) ); ?></th><td class="num"><?php echo esc_html( tbt_v( $p, 'dr' ) ); ?></td><td><?php echo esc_html( tbt_v( $p, 'traffic' ) ); ?></td><td><?php echo esc_html( tbt_v( $p, 'target' ) ); ?></td><td><span class="spill <?php echo esc_attr( $s[0] ); ?>"><?php echo esc_html( $s[1] ); ?></span></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
        <?php endif; ?>

      </article>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ( tbt_v( $f, 'quote' ) ) : ?>
<!-- ============================== QUOTE ============================== -->
<section class="band">
  <div class="shell narrow">
    <figure class="bquote" data-rise>
      <blockquote><?php echo esc_html( $f['quote'] ); ?></blockquote>
      <figcaption>
        <?php echo tbt_image( tbt_v( $f, 'quote_photo', 'images/people/avatar-m.svg' ), 'thumbnail', array( 'alt' => '', 'width' => 48, 'height' => 48 ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        <span><b><?php echo esc_html( tbt_v( $f, 'quote_name' ) ); ?></b><span><?php echo esc_html( tbt_v( $f, 'quote_role' ) ); ?></span></span>
      </figcaption>
    </figure>
  </div>
</section>
<?php endif; ?>

<?php
/* ============================== NEXT / MORE ============================== */
$all = tbt_items( 'tb_case_study', array( 'fields' => 'ids' ) );
$pos = array_search( $id, $all, true );
if ( count( $all ) > 1 ) :
    $next   = $all[ ( false === $pos ? 0 : $pos + 1 ) % count( $all ) ];
    $nf     = tbt_fields( $next );
    $more   = array_slice( array_values( array_diff( $all, array( $id, $next ) ) ), 0, 3 );
    $n_meta = implode( ' · ', array_filter( array( strtoupper( tbt_v( $nf, 'industry' ) ), tbt_case_dr( $nf ) ) ) );
    ?>
<section>
  <div class="shell">
    <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="nextcase" data-rise>
      <div>
        <span class="nextcase__k">NEXT CASE STUDY</span>
        <h2 class="nextcase__h"><?php echo esc_html( get_the_title( $next ) ); ?></h2>
        <?php if ( $n_meta ) : ?><p class="nextcase__r"><?php echo esc_html( $n_meta ); ?></p><?php endif; ?>
      </div>
      <?php echo tbt_post_image( $next, 'large', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
      <span class="rcard__go"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
    </a>

    <?php if ( $more ) : ?>
    <div class="work__grid" data-rise>
      <?php
      foreach ( $more as $m ) {
          get_template_part( 'parts/shared/work-tile', null, array( 'id' => $m ) );
      }
      ?>
    </div>
    <?php endif; ?>
  </div>
</section>
    <?php
endif;

get_template_part( 'parts/shared/cta', null, array(
    'heading'    => tbt_v( $f, 'cta_heading', 'Want a result like this for your site?' ),
    'text'       => tbt_v( $f, 'cta_text' ),
    'btn_label'  => 'Get my free gap analysis',
    'btn2_label' => 'More case studies',
    'btn2_url'   => tb_page_url( 'case-studies' ),
) );
?>

</main>
