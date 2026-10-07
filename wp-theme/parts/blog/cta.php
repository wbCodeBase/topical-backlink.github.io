<?php
/**
 * Call to action at the bottom of the blog pages (same panel as the other pages).
 *
 * @package TopicalBacklink
 */
?>
<section class="cta">
  <div class="shell">
    <div class="cta__panel" data-rise>
      <div class="cta__grid" aria-hidden="true"></div>
      <div class="cta__body">
        <h2 class="cta__h">Want links like the ones we write about?</h2>
        <p class="cta__sub">
          Tell us your domain and goals. We'll come back with a free link audit and
          the placements we'd build first, from outreach to live link.
        </p>
        <p class="cta__trust">No contract. No minimum.</p>
        <div class="cta__row">
          <a href="<?php tb_link( 'contact' ); ?>" class="btn btn--ink btn--lg">Talk to our team <?php echo tbt_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
          <a href="<?php tb_link( 'pricing' ); ?>" class="btn btn--outline btn--lg">See pricing <?php echo tbt_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
        </div>
      </div>
    </div>
  </div>
</section>
