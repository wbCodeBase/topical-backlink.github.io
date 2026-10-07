<?php
/**
 * Home: platform. ACF group "the_platform".
 *
 * @package TopicalBacklink
 */

$f        = tb_home_group( 'the_platform' );
$eyebrow  = tb_val( $f, 'eye_brow', 'THE PLATFORM' );
$heading  = tb_val( $f, 'heading', 'One link building agency to win rankings &amp; <span class="mark">own the answer</span>' );
$sub      = tb_val( $f, 'sub_heading', 'Genuine outreach. Real placements.' );
$ticks    = array(
	tb_val( $f, 'list_item_1', 'Every placement tracked live in your dashboard' ),
	tb_val( $f, 'list_item_2', 'Editorial sites with real traffic, never PBNs' ),
	tb_val( $f, 'list_item_3', 'Every link guaranteed for twelve months' ),
);
$started  = tb_val( $f, 'get_started_link', '#audit' );
$strategy = tb_val( $f, 'book_strategy_call', '#call' );
?>
<!-- ============================== PLATFORM ============================== -->
<section class="platform" id="platform">
  <div class="platform__wash" aria-hidden="true"></div>

  <div class="shell platform__inner">
    <div class="platform__copy">
      <span class="eyebrow" data-rise><i></i><?php echo esc_html( $eyebrow ); ?></span>

      <h2 class="h2 h2--xl" data-rise><?php tb_heading( $heading ); ?></h2>

      <p class="lead lead--strong" data-rise><?php echo esc_html( $sub ); ?></p>

      <ul class="ticks" data-rise>
        <?php foreach ( $ticks as $tick ) : ?>
        <li>
          <span class="tick"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>
          <?php echo esc_html( $tick ); ?>
        </li>
        <?php endforeach; ?>
      </ul>

      <div class="row row--cta" data-rise>
        <a href="<?php echo esc_url( $started ); ?>" class="btn btn--primary btn--lg">
          Get started free
          <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
        <a href="<?php echo esc_url( $strategy ); ?>" class="btn btn--solid btn--lg">
          Book a strategy call
          <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </div>
    </div>

    <div class="platform__viz" data-rise>
      <div class="dash">
        <div class="dash__chrome">
          <i class="d d--r"></i><i class="d"></i><i class="d"></i>
          <span class="mono">app.topicalbacklinks.com · client dashboard</span>
        </div>

        <div class="dash__body">
          <div class="tiles">
            <div class="tile">
              <div class="tile__k mono">LINKS LIVE</div>
              <div class="tile__v">128</div>
              <div class="tile__d"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>12 this week</div>
            </div>
            <div class="tile">
              <div class="tile__k mono">REFERRING DOMAINS</div>
              <div class="tile__v">86</div>
              <div class="tile__d"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>9 new</div>
            </div>
            <div class="tile">
              <div class="tile__k mono">AVG. DR</div>
              <div class="tile__v">52</div>
              <div class="tile__d"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>steady</div>
            </div>
          </div>

          <div class="chart" id="chart">
            <div class="chart__tip" id="chartTip" hidden></div>
          </div>

          <div class="legend">
            <span><i class="bar bar--live"></i>Links live</span>
            <span><i class="bar bar--prev"></i>Previous period</span>
          </div>

          <div class="rows">
            <div class="prow"><span>industry-mag.com <i>› resources</i></span><b class="pill pill--live">LIVE · DA 58</b></div>
            <div class="prow"><span>niche-reviews.io <i>› guides</i></span><b class="pill pill--mid">CONTENT APPROVED</b></div>
            <div class="prow"><span>trade-journal.com <i>› insights</i></span><b class="pill pill--soft">OUTREACH</b></div>
          </div>
        </div>
      </div>

      <div class="float float--traffic">organic traffic <b>▲ 38.8%</b></div>
      <div class="float float--hot">42 new referring domains</div>
      <div class="float float--icon float--tl">
        <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 1 1 0 10h-2"/><path d="M8 12h8"/></svg>
      </div>
      <div class="float float--icon float--br">
        <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
      </div>
    </div>
  </div>
</section>
