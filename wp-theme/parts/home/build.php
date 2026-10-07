<?php
/**
 * Home: how we build links. ACF group "how_we_build_links".
 *
 * @package TopicalBacklink
 */

$f       = tb_home_group( 'how_we_build_links' );
$heading = tb_val( $f, 'heading', 'How we build links <span class="mark">that last</span>' );
$steps   = tb_rows( $f, 'steps_items', array(
	array( 'step_title' => 'Outreach that earns the placement', 'step_para' => "We pitch real editors on real publications with content that fits their audience. If a site won\u{2019}t accept a genuine pitch, it isn\u{2019}t a site we want to place on anyway." ),
	array( 'step_title' => 'Content that deserves to be there', 'step_para' => "Every piece is written for the publication it\u{2019}s going to, on the right topic, for the target audience, with your link incorporated naturally. That\u{2019}s what makes placements stick long-term." ),
	array( 'step_title' => 'Delivery you can see and verify',   'step_para' => 'When a link goes live, your dashboard shows the placement URL, DR, estimated traffic, anchor text, target page and live date. Every eligible placement is backed by a 12-month replacement guarantee.' ),
) );
?>
<!-- ============================== HOW WE BUILD ============================== -->
<section class="build" id="build">
  <div class="shell">
    <h2 class="h2" data-rise><?php tb_heading( $heading ); ?></h2>

    <div class="build__grid">
      <ol class="steps" data-rise>
        <?php foreach ( $steps as $i => $step ) : ?>
        <li class="step">
          <span class="step__k mono"><?php echo esc_html( sprintf( 'STEP %02d', $i + 1 ) ); ?></span>
          <h3 class="step__h"><?php echo esc_html( tb_val( $step, 'step_title' ) ); ?></h3>
          <p class="step__b"><?php echo esc_html( tb_val( $step, 'step_para' ) ); ?></p>
        </li>
        <?php endforeach; ?>
      </ol>

      <div class="build__viz" data-rise>
        <p class="sr-only">
          An illustration of the ordering dashboard: a destination URL and anchor are
          entered, then the order moves through queued, outreach, content and live.
        </p>

        <div class="order" id="order" aria-hidden="true">
          <div class="dash__chrome">
            <i class="d d--r"></i><i class="d"></i><i class="d"></i>
          </div>

          <div class="order__body">
            <h4 class="order__title">Place a new order</h4>
            <p class="order__kicker mono">ORDER / DESTINATION URL &amp; ANCHOR</p>

            <div class="oinput">
              <span class="oinput__text" id="orderText"></span><span class="oinput__caret"></span>
            </div>

            <div class="oboxes">
              <div class="obox">
                <span class="obox__k mono">STATUS</span>
                <span class="obox__v" id="orderStatus">Queued</span>
              </div>
              <div class="obox">
                <span class="obox__k mono">DELIVERY</span>
                <span class="obox__v">30 days</span>
              </div>
              <div class="obox">
                <span class="obox__k mono">REPORTING</span>
                <span class="obox__v">Real-time</span>
              </div>
            </div>

            <div class="ocols" id="orderCols">
              <div class="ocol" data-stage="0">
                <span class="ocol__n">6</span>
                <span class="ocol__bar"><i style="height:50%"></i></span>
                <span class="ocol__l mono">QUEUED</span>
              </div>
              <div class="ocol" data-stage="1">
                <span class="ocol__n">9</span>
                <span class="ocol__bar"><i style="height:75%"></i></span>
                <span class="ocol__l mono">OUTREACH</span>
              </div>
              <div class="ocol" data-stage="2">
                <span class="ocol__n">4</span>
                <span class="ocol__bar"><i style="height:34%"></i></span>
                <span class="ocol__l mono">CONTENT</span>
              </div>
              <div class="ocol" data-stage="3">
                <span class="ocol__n">12</span>
                <span class="ocol__bar"><i style="height:100%"></i></span>
                <span class="ocol__l mono">LIVE</span>
              </div>
            </div>

            <p class="order__note mono">// dashboard updates instantly at every stage</p>
          </div>
        </div>

        <div class="portal">
          <span class="portal__t">Experience our premium portal</span>
          <a href="#audit" class="btn btn--primary">
            Sign up
            <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
