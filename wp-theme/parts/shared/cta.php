<?php
/**
 * Violet call-to-action panel.
 * $args: heading, text, btn2_label, btn2_url (second button defaults to pricing).
 *
 * @package TopicalBacklink
 */

$arrow = tbt_svg( 'arrow' );
?>
<section class="cta">
  <div class="shell">
    <div class="cta__panel" data-rise>
      <div class="cta__grid" aria-hidden="true"></div>
      <div class="cta__body">
        <h2 class="cta__h"><?php echo esc_html( tbt_v( $args, 'heading', 'Get started today. Earn powerful backlinks. Grow every client.' ) ); ?></h2>
        <?php if ( tbt_v( $args, 'text' ) ) : ?><p class="cta__sub"><?php echo esc_html( $args['text'] ); ?></p><?php endif; ?>
        <p class="cta__trust">No contract. No minimum.</p>
        <div class="cta__row">
          <a href="<?php tb_link( 'contact' ); ?>" class="btn btn--ink btn--lg"><?php echo esc_html( tbt_v( $args, 'btn_label', 'Book a call' ) ); ?> <?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
          <a href="<?php echo esc_url( tbt_v( $args, 'btn2_url', tb_page_url( 'pricing' ) ) ); ?>" class="btn btn--outline btn--lg"><?php echo esc_html( tbt_v( $args, 'btn2_label', 'See pricing' ) ); ?> <?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
        </div>
      </div>
    </div>
  </div>
</section>
