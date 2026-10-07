<?php
/**
 * Case study tile (image, industry, DR, client, result). $args['id']: case study post ID.
 *
 * @package TopicalBacklink
 */

$id = (int) $args['id'];
$c  = tbt_fields( $id );
?>
<a class="wtile" href="<?php echo esc_url( get_permalink( $id ) ); ?>">
  <figure class="shot shot--wide shot--contain">
    <?php echo tbt_has_image( $id ) ? tbt_post_image( $id, 'large', array( 'loading' => 'lazy' ) ) : tbt_blog_ph(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
  </figure>
  <div class="wtile__body">
    <div class="wtile__meta">
      <span class="wtile__cat"><?php echo esc_html( tbt_v( $c, 'industry' ) ); ?></span>
      <?php if ( tbt_case_dr( $c ) ) : ?><span class="wtile__dr"><?php echo esc_html( tbt_case_dr( $c ) ); ?></span><?php endif; ?>
    </div>
    <h3 class="wtile__h"><?php echo esc_html( tbt_v( $c, 'client', get_the_title( $id ) ) ); ?></h3>
    <div class="wtile__r"><span><?php echo esc_html( tbt_v( $c, 'result' ) ); ?></span><span><?php echo esc_html( tbt_v( $c, 'duration' ) ); ?></span></div>
  </div>
</a>
