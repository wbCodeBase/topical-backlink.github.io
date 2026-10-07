<?php
/**
 * Home: call to action. ACF group "cta_section".
 *
 * @package TopicalBacklink
 */

$f       = tb_home_group( 'cta_section' );
$heading = tb_val( $f, 'cta_heading', 'Get started today. Earn powerful backlinks. Grow every client.' );
$sub     = tb_val( $f, 'cta_subheading', 'Schedule a call and get your free link audit. Review recommended placements and track every order from outreach to live link.' );
$one     = tb_val( $f, 'button_one', '#audit' );
$two     = tb_val( $f, 'button_two', '#call' );
?>
<!-- ============================== CTA ============================== -->
<section class="cta" id="audit">
  <div class="shell">
    <div class="cta__panel" data-rise>
      <div class="cta__grid" aria-hidden="true"></div>

      <div class="cta__body">
        <h2 class="cta__h"><?php tb_heading( $heading ); ?></h2>
        <p class="cta__sub"><?php echo esc_html( $sub ); ?></p>

        <div class="cta__row">
          <a href="<?php echo esc_url( $one ); ?>" class="btn btn--ink btn--lg">
            Place an order
            <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
          <a href="<?php echo esc_url( $two ); ?>" class="btn btn--outline btn--lg">
            Schedule a meeting
            <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
