<?php
/**
 * Service detail page, built from the "Service page content" fields
 * (inc/fields-defs.php). Each section is skipped when its content is empty.
 *
 * @package TopicalBacklink
 */

the_post();

$f     = tbt_fields();
$title = get_the_title();
$tick  = '<span class="tick">' . tbt_svg( 'check' ) . '</span>';
$arrow = tbt_svg( 'arrow' );

$hero_h   = tbt_v( $f, 'hero_heading', $title );
$hero_l   = tbt_v( $f, 'hero_lead', get_the_excerpt() );
$btn_url  = tbt_href( tbt_v( $f, 'hero_btn_url' ), tb_page_url( 'contact' ) );
$stats    = tbt_filled( $f['hero_stats'] );
$chips    = array_slice( tbt_filled( $f['hero_chips'] ), 0, 4 );
$chip_ico = array( array( 'brief', '' ), array( 'shield', ' hchip__ico--gain' ), array( 'link', ' hchip__ico--sky' ), array( 'clock', '' ) );
$chip_dep = array( 16, -14, 12, -18 );
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

  <?php if ( $chips ) : ?>
  <div class="hchips" aria-hidden="true">
    <?php foreach ( $chips as $i => $c ) : ?>
    <div class="hchip hchip--<?php echo (int) $i + 1; ?>" data-depth="<?php echo (int) $chip_dep[ $i ]; ?>">
      <div class="hchip__in">
        <span class="hchip__ico<?php echo esc_attr( $chip_ico[ $i ][1] ); ?>"><?php echo tbt_svg( $chip_ico[ $i ][0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <span><b><?php echo esc_html( tbt_v( $c, 'title' ) ); ?></b><i><?php echo esc_html( tbt_v( $c, 'text' ) ); ?></i></span>
        <?php if ( 2 === $i ) : ?><span class="hchip__live"></span><?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="shell roi__inner">
    <p class="crumb" data-rise><a href="<?php tb_link( 'home' ); ?>">Home</a> <span>/</span> <a href="<?php tb_link( 'services' ); ?>">Services</a> <span>/</span> <?php echo esc_html( tbt_v( $f, 'short_name', $title ) ); ?></p>

    <h1 class="h2 h2--hero center" data-rise><?php echo tbt_swoosh( $hero_h, tbt_v( $f, 'hero_highlight' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>

    <?php if ( $hero_l ) : ?>
    <p class="lead lead--center" data-rise><?php echo esc_html( $hero_l ); ?></p>
    <?php endif; ?>

    <div class="showcase" data-rise>
      <a href="<?php echo esc_url( $btn_url ); ?>" class="btn btn--violet btn--lg showcase__cta"><?php echo esc_html( tbt_v( $f, 'hero_btn_label', 'Book a call' ) ); ?> <?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
      <?php if ( tbt_v( $f, 'hero_btn2_label' ) ) : ?>
      <a href="<?php echo esc_url( tbt_href( tbt_v( $f, 'hero_btn2_url' ), tb_page_url( 'pricing' ) ) ); ?>" class="btn btn--vghost btn--lg showcase__cta"><?php echo esc_html( $f['hero_btn2_label'] ); ?></a>
      <?php endif; ?>
    </div>

    <?php if ( $stats ) : ?>
    <dl class="hstats" data-rise>
      <?php foreach ( $stats as $s ) : ?>
      <div><dt><?php echo esc_html( tbt_v( $s, 'label' ) ); ?></dt><dd<?php echo tbt_count_attrs( $s ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( tbt_num( tbt_v( $s, 'value', 0 ), tbt_v( $s, 'prefix' ), tbt_v( $s, 'suffix' ) ) ); ?></dd></div>
      <?php endforeach; ?>
    </dl>
    <?php endif; ?>
  </div>
</section>

<?php
if ( $f['show_trust'] ) {
    get_template_part( 'parts/home/trust' );
}

/* ============================== OVERVIEW ============================== */
$ov_points = tbt_lines( $f['overview_points'] );
$ov_visual = tbt_v( $f, 'overview_visual', 'image' );
$ov_image  = tbt_v( $f, 'overview_image' );
$ov_html   = $ov_image ? tbt_image( $ov_image, 'large', array( 'loading' => 'lazy' ) ) : tbt_post_image( get_the_ID(), 'large', array( 'loading' => 'lazy' ) );
if ( tbt_v( $f, 'overview_heading' ) || tbt_v( $f, 'overview_lead' ) || $ov_points ) :
    $has_viz = 'dashboard' === $ov_visual || ( 'image' === $ov_visual && $ov_html );
    ?>
<section class="band" id="overview">
  <div class="shell">
    <div class="split<?php echo $has_viz ? '' : ' split--solo'; ?>">
      <div>
        <p class="kicker kicker--left" data-rise><?php echo esc_html( tbt_v( $f, 'overview_kicker', 'What you get' ) ); ?></p>
        <h2 class="h2" data-rise><?php echo tbt_swoosh( tbt_v( $f, 'overview_heading' ), tbt_v( $f, 'overview_highlight' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
        <?php if ( tbt_v( $f, 'overview_lead' ) ) : ?><p class="lead" data-rise><?php echo esc_html( $f['overview_lead'] ); ?></p><?php endif; ?>
        <?php if ( $ov_points ) : ?>
        <ul class="ticks" data-rise>
          <?php foreach ( $ov_points as $p ) : ?><li><?php echo $tick . esc_html( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li><?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <?php if ( 'dashboard' === $ov_visual ) : ?>
      <div class="split__viz" data-rise>
        <p class="sr-only">An example of the white-label client dashboard, showing live links, referring domains and recent placements under the agency's own brand.</p>
        <div class="dash" aria-hidden="true">
          <div class="dash__chrome">
            <i class="d d--r"></i><i class="d"></i><i class="d"></i>
            <span class="mono">reports.youragency.com</span>
            <b class="dash__brand">YOUR AGENCY</b>
          </div>
          <div class="dash__body">
            <div class="tiles">
              <div class="tile"><div class="tile__k mono">LINKS LIVE</div><div class="tile__v">64</div><div class="tile__d"><?php echo tbt_svg( 'trend' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>8 this month</div></div>
              <div class="tile"><div class="tile__k mono">AVG. DR</div><div class="tile__v">58</div><div class="tile__d"><?php echo tbt_svg( 'trend' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>steady</div></div>
              <div class="tile"><div class="tile__k mono">CLIENTS</div><div class="tile__v">12</div><div class="tile__d"><?php echo tbt_svg( 'trend' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>2 new</div></div>
            </div>
            <div class="rows">
              <div class="prow"><span>industry-mag.com <i>&rsaquo; resources</i></span><b class="pill pill--live">LIVE &middot; DR 58</b></div>
              <div class="prow"><span>niche-reviews.io <i>&rsaquo; guides</i></span><b class="pill pill--mid">CONTENT APPROVED</b></div>
              <div class="prow"><span>trade-journal.com <i>&rsaquo; insights</i></span><b class="pill pill--live">LIVE &middot; DR 64</b></div>
              <div class="prow"><span>regional-business.news <i>&rsaquo; features</i></span><b class="pill pill--soft">OUTREACH</b></div>
            </div>
          </div>
        </div>
        <div class="hchip hchip--a" aria-hidden="true">
          <div class="hchip__in">
            <span class="hchip__ico hchip__ico--gain"><?php echo tbt_svg( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
            <span><b>Client-ready report</b><i>exported under your domain</i></span>
          </div>
        </div>
        <div class="hchip hchip--b" aria-hidden="true">
          <div class="hchip__in">
            <span class="hchip__ico"><?php echo tbt_svg( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
            <span><b>Zero client contact</b><i>we stay invisible</i></span>
          </div>
        </div>
      </div>
      <?php elseif ( $has_viz ) : ?>
      <div class="split__viz" data-rise>
        <figure class="shot shot--wide svc-shot"><?php echo $ov_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></figure>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
    <?php
endif;

/* ============================== FEATURES ============================== */
$features = tbt_filled( $f['features'] );
if ( $features ) :
    ?>
<section id="features">
  <div class="shell">
    <div class="sec-head" data-rise>
      <p class="kicker"><?php echo esc_html( tbt_v( $f, 'features_kicker', 'Inside every order' ) ); ?></p>
      <h2 class="h2"><?php echo tbt_swoosh( tbt_v( $f, 'features_heading', 'What we do' ), tbt_v( $f, 'features_highlight' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
      <?php if ( tbt_v( $f, 'features_lead' ) ) : ?><p class="lead"><?php echo esc_html( $f['features_lead'] ); ?></p><?php endif; ?>
    </div>
    <div class="fgrid" data-rise>
      <?php foreach ( $features as $i => $c ) : ?>
      <article class="fcard">
        <span class="fcard__n"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
        <span class="fcard__ico"><?php echo tbt_svg( tbt_v( $c, 'icon', 'check' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <h3 class="fcard__h"><?php echo esc_html( tbt_v( $c, 'title' ) ); ?></h3>
        <p class="fcard__b"><?php echo esc_html( tbt_v( $c, 'text' ) ); ?></p>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
    <?php
endif;

/* ============================== PROCESS ============================== */
$steps = tbt_filled( $f['steps'] );
if ( $steps ) :
    ?>
<section class="band" id="process">
  <div class="shell">
    <div class="sec-head" data-rise>
      <p class="kicker"><?php echo esc_html( tbt_v( $f, 'process_kicker', 'How it works' ) ); ?></p>
      <h2 class="h2"><?php echo tbt_swoosh( tbt_v( $f, 'process_heading', 'How it works' ), tbt_v( $f, 'process_highlight' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
      <?php if ( tbt_v( $f, 'process_lead' ) ) : ?><p class="lead"><?php echo esc_html( $f['process_lead'] ); ?></p><?php endif; ?>
    </div>

    <div class="proc" id="proc" data-rise>
      <div class="proc__rail" role="tablist" aria-label="Process steps">
        <?php foreach ( $steps as $i => $s ) : $n = $i + 1; ?>
        <button class="proc__step" role="tab" id="proc-t<?php echo (int) $n; ?>" aria-controls="proc-p<?php echo (int) $n; ?>">
          <span class="proc__n"><?php echo esc_html( sprintf( '%02d', $n ) ); ?></span><span class="proc__t"><b><?php echo esc_html( tbt_v( $s, 'title' ) ); ?></b><i><?php echo esc_html( tbt_v( $s, 'when' ) ); ?></i></span><span class="proc__bar"><i></i></span>
        </button>
        <?php endforeach; ?>
      </div>
      <div class="proc__panes">
        <?php foreach ( $steps as $i => $s ) : $n = $i + 1; ?>
        <div class="proc__pane" id="proc-p<?php echo (int) $n; ?>" role="tabpanel" aria-labelledby="proc-t<?php echo (int) $n; ?>"<?php echo $i ? ' hidden' : ''; ?>>
          <span class="proc__big" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $n ) ); ?></span>
          <?php if ( tbt_v( $s, 'when' ) ) : ?><span class="proc__k mono"><?php echo esc_html( $s['when'] ); ?></span><?php endif; ?>
          <h3 class="proc__h"><?php echo esc_html( tbt_v( $s, 'heading', tbt_v( $s, 'title' ) ) ); ?></h3>
          <?php if ( tbt_v( $s, 'text' ) ) : ?><p class="proc__b"><?php echo esc_html( $s['text'] ); ?></p><?php endif; ?>
          <?php $outs = tbt_lines( tbt_v( $s, 'outputs' ) ); if ( $outs ) : ?>
          <ul class="proc__out">
            <?php foreach ( $outs as $o ) : ?><li><?php echo tbt_svg( 'check' ) . esc_html( $o ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li><?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
    <?php
endif;

/* ============================== ESTIMATOR ============================== */
if ( $f['show_estimator'] ) :
    $bands = tbt_filled( $f['est_bands'] );
    if ( ! $bands ) {
        require_once TB_DIR . '/inc/seed.php';
        $bands = tbt_seed_rate_card();
    }
    $on       = min( 1, count( $bands ) - 1 );
    $rate     = (float) tbt_v( $bands[ $on ], 'rate', 0 );
    $e_points = tbt_lines( $f['est_points'] );
    ?>
<section id="estimate">
  <div class="shell">
    <div class="sec-head" data-rise>
      <p class="kicker"><?php echo esc_html( tbt_v( $f, 'est_kicker', 'Transparent pricing' ) ); ?></p>
      <h2 class="h2"><?php echo tbt_swoosh( tbt_v( $f, 'est_heading', 'Estimate a programme' ), tbt_v( $f, 'est_highlight' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
      <?php if ( tbt_v( $f, 'est_lead' ) ) : ?><p class="lead"><?php echo esc_html( $f['est_lead'] ); ?></p><?php endif; ?>
    </div>

    <div class="calc" id="calc" data-rise>
      <div class="calc__form">
        <div>
          <div class="calc__lab"><label for="calcRange">Placements per month</label><output id="calcQty" for="calcRange">12</output></div>
          <input type="range" class="range" id="calcRange" min="1" max="50" value="12">
          <div class="calc__ticks mono" aria-hidden="true"><span>1</span><span>25</span><span>50</span></div>
        </div>
        <div>
          <span class="calc__lab" id="bandLab">Authority band</span>
          <div class="seg" role="radiogroup" aria-labelledby="bandLab">
            <?php foreach ( $bands as $i => $b ) : ?>
            <button type="button" class="seg__b" role="radio" aria-checked="<?php echo $i === $on ? 'true' : 'false'; ?>" data-rate="<?php echo esc_attr( (float) tbt_v( $b, 'rate', 0 ) ); ?>" data-lead="<?php echo esc_attr( tbt_v( $b, 'lead_time' ) ); ?>"><?php echo esc_html( tbt_v( $b, 'label' ) ); ?><small><?php echo esc_html( '$' . number_format( (float) tbt_v( $b, 'rate', 0 ) ) ); ?></small></button>
            <?php endforeach; ?>
          </div>
        </div>
        <?php if ( $e_points ) : ?>
        <ul class="ticks">
          <?php foreach ( $e_points as $p ) : ?><li><?php echo $tick . esc_html( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li><?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <div class="calc__out" aria-live="polite">
        <span class="mono">ESTIMATED MONTHLY INVESTMENT</span>
        <b class="calc__total" id="calcTotal"><?php echo esc_html( '$' . number_format( 12 * $rate ) ); ?><small> / month</small></b>
        <dl class="calc__meta">
          <div><dt>Per placement</dt><dd id="calcRate"><?php echo esc_html( '$' . number_format( $rate ) ); ?></dd></div>
          <div><dt>Typical lead time</dt><dd id="calcLead"><?php echo esc_html( tbt_v( $bands[ $on ], 'lead_time' ) ); ?></dd></div>
          <div><dt>Closest programme</dt><dd id="calcPlan">Growth</dd></div>
        </dl>
        <div class="calc__cta">
          <a href="<?php tb_link( 'contact' ); ?>" class="btn btn--violet btn--lg btn--block">Get an exact quote <?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
          <?php if ( tbt_v( $f, 'est_note' ) ) : ?><p class="calc__note"><?php echo esc_html( $f['est_note'] ); ?></p><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
    <?php
endif;

/* ============================== COMPARISON ============================== */
$rows = tbt_filled( $f['compare_rows'] );
if ( $rows ) :
    $cell = function ( $v ) {
        $v = trim( (string) $v );
        $l = strtolower( $v );
        if ( in_array( $l, array( 'yes', 'y', '✓', 'true' ), true ) ) {
            return '<span class="yn yn--y" aria-label="Yes">' . tbt_svg( 'check' ) . '</span>';
        }
        if ( in_array( $l, array( 'no', 'n', '✗', 'x', 'false' ), true ) ) {
            return '<span class="yn yn--n" aria-label="No">' . tbt_svg( 'x' ) . '</span>';
        }
        return '' === $v ? '' : '<span class="yn yn--m">' . esc_html( $v ) . '</span>';
    };
    ?>
<section class="band" id="compare">
  <div class="shell narrow">
    <div class="sec-head" data-rise>
      <p class="kicker"><?php echo esc_html( tbt_v( $f, 'compare_kicker', 'The difference' ) ); ?></p>
      <h2 class="h2"><?php echo esc_html( tbt_v( $f, 'compare_heading', 'Why agencies switch to us' ) ); ?></h2>
    </div>
    <div class="vswrap" data-rise>
      <table class="vs">
        <thead>
          <tr><th scope="col">What you get</th><th scope="col" class="vs__us">TopicalBacklink</th><th scope="col"><?php echo esc_html( tbt_v( $f, 'compare_them', 'Typical link vendor' ) ); ?></th></tr>
        </thead>
        <tbody>
          <?php foreach ( $rows as $r ) : ?>
          <tr><th scope="row"><?php echo esc_html( tbt_v( $r, 'feature' ) ); ?></th><td class="vs__us"><?php echo $cell( tbt_v( $r, 'us', 'yes' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td><td><?php echo $cell( tbt_v( $r, 'them' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
    <?php
endif;

/* ============================== FEATURED CASE ============================== */
$case_id = (int) tbt_v( $f, 'featured_case', 0 );
if ( $case_id && 'publish' === get_post_status( $case_id ) && 'tb_case_study' === get_post_type( $case_id ) ) :
    $c     = tbt_fields( $case_id );
    $nums  = tbt_case_card_stats( $c );
    $quote = tbt_v( $c, 'quote' );
    ?>
<section id="proof">
  <div class="shell">
    <a href="<?php echo esc_url( get_permalink( $case_id ) ); ?>" class="fcase" data-rise>
      <div class="fcase__copy">
        <span class="fcase__tag"><?php echo esc_html( 'CASE STUDY' . ( tbt_v( $c, 'industry' ) ? ' · ' . strtoupper( $c['industry'] ) : '' ) ); ?></span>
        <h2 class="fcase__h"><?php echo esc_html( get_the_title( $case_id ) ); ?></h2>
        <?php if ( $quote ) : ?>
        <p class="fcase__q">&ldquo;<?php echo esc_html( $quote ); ?>&rdquo;</p>
        <p class="fcase__who"><?php echo esc_html( trim( tbt_v( $c, 'quote_name' ) . ( tbt_v( $c, 'quote_role' ) ? ' · ' . $c['quote_role'] : '' ) ) ); ?></p>
        <?php endif; ?>
        <?php if ( $nums ) : ?>
        <dl class="fcase__nums">
          <?php foreach ( $nums as $n ) : ?><div><dt><?php echo esc_html( strtoupper( tbt_v( $n, 'label' ) ) ); ?></dt><dd><?php echo esc_html( tbt_v( $n, 'value' ) ); ?></dd></div><?php endforeach; ?>
        </dl>
        <?php endif; ?>
        <span class="fcase__go">Read the case study <i><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></i></span>
      </div>
      <?php if ( tbt_has_image( $case_id ) ) : ?>
      <div class="fcase__media"><?php echo tbt_post_image( $case_id, 'large', array( 'alt' => '', 'loading' => 'lazy' ) ); ?></div>
      <?php endif; ?>
    </a>
  </div>
</section>
    <?php
endif;

/* ============================== TESTIMONIALS ============================== */
if ( $f['show_testimonials'] ) {
    get_template_part( 'parts/shared/testimonials', null, array( 'rows' => tbt_filled( $f['testimonials'] ) ) );
}

/* ============================== FAQ ============================== */
$faqs = tbt_filled( $f['faqs'] );
if ( $faqs ) :
    ?>
<section class="faq" id="faq">
  <div class="shell faq__inner">
    <div class="faq__head" data-rise>
      <p class="kicker">Questions</p>
      <h2 class="h2 center"><?php echo tbt_swoosh( tbt_v( $f, 'faq_heading', 'Questions,' ), tbt_v( $f, 'faq_highlight' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
    </div>
    <div class="faq__list" data-rise>
      <?php foreach ( $faqs as $i => $q ) : ?>
      <div class="qa<?php echo 0 === $i ? ' is-open' : ''; ?>">
        <button class="qa__q" type="button" aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>"><span><?php echo esc_html( tbt_v( $q, 'question' ) ); ?></span><span class="qa__sign" aria-hidden="true"></span></button>
        <div class="qa__panel"><div class="qa__a"><?php echo wp_kses_post( wpautop( tbt_v( $q, 'answer' ) ) ); ?></div></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
    <?php
endif;

/* ============================== OTHER SERVICES ============================== */
$others = tbt_items( 'tb_service', array( 'post__not_in' => array( get_the_ID() ), 'posts_per_page' => 4 ) );
if ( $others ) :
    $all = tbt_items( 'tb_service', array( 'fields' => 'ids' ) );
    ?>
<section class="band">
  <div class="shell">
    <div class="sec-head" data-rise>
      <p class="kicker">Pair it with</p>
      <h2 class="h2">Other ways we build authority</h2>
    </div>
    <div class="rgrid" data-rise>
      <?php foreach ( $others as $o ) : ?>
      <a class="rcard" href="<?php echo esc_url( get_permalink( $o ) ); ?>">
        <span class="rcard__k"><?php echo esc_html( sprintf( 'SERVICE %02d', array_search( $o->ID, $all, true ) + 1 ) ); ?></span>
        <h3 class="rcard__h"><?php echo esc_html( get_the_title( $o ) ); ?></h3>
        <p class="rcard__b"><?php echo esc_html( wp_trim_words( get_the_excerpt( $o ), 14, '…' ) ); ?></p>
        <span class="rcard__go"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
    <?php
endif;

get_template_part( 'parts/shared/cta', null, array(
    'heading' => tbt_v( $f, 'cta_heading', 'Get started today. Earn powerful backlinks. Grow every client.' ),
    'text'    => tbt_v( $f, 'cta_text' ),
) );
?>

</main>
