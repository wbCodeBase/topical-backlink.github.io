<?php
/**
 * Three testimonial cards. $args['rows']: [ quote, name, role, photo ]; empty = the standard quotes.
 *
 * @package TopicalBacklink
 */

$rows = ! empty( $args['rows'] ) ? array_slice( $args['rows'], 0, 3 ) : array();
if ( ! $rows ) {
	require_once TB_DIR . '/inc/seed.php';
	$rows = tbt_seed_testimonial_defaults();
}
?>
<section class="band">
  <div class="shell">
    <div class="sec-head" data-rise>
      <p class="kicker">In their words</p>
      <h2 class="h2">What partners say</h2>
    </div>
    <div class="tgrid" data-rise>
      <?php foreach ( $rows as $r ) : ?>
      <figure class="tcard">
        <blockquote class="tcard__q"><?php echo esc_html( tbt_v( $r, 'quote' ) ); ?></blockquote>
        <figcaption class="tcard__who">
          <span class="tcard__av"><?php echo tbt_image( tbt_v( $r, 'photo', 'images/people/avatar-m.svg' ), 'thumbnail', array( 'alt' => '', 'width' => 120, 'height' => 120, 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
          <span><p class="tcard__n"><?php echo esc_html( tbt_v( $r, 'name' ) ); ?></p><p class="tcard__r"><?php echo esc_html( tbt_v( $r, 'role' ) ); ?></p></span>
        </figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
