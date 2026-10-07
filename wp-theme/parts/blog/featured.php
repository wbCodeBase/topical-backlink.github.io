<?php
/**
 * Large featured article at the top of the blog. Expects the post to be set up.
 *
 * @package TopicalBacklink
 */

$tb_cat = tbt_post_cat();
?>
<article <?php post_class( 'bfeat' ); ?>>
  <a class="bfeat__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
    <?php
    if ( has_post_thumbnail() ) {
        the_post_thumbnail( 'large', array( 'alt' => '', 'fetchpriority' => 'high', 'loading' => 'eager', 'sizes' => '(min-width:900px) 680px, 100vw' ) );
    } else {
        echo tbt_blog_ph(); // phpcs:ignore WordPress.Security.EscapeOutput
    }
    ?>
  </a>
  <div class="bfeat__body">
    <div class="bfeat__tags">
      <span class="bfeat__flag">Featured</span>
      <?php if ( $tb_cat ) : ?>
        <a class="bpill" href="<?php echo esc_url( get_category_link( $tb_cat ) ); ?>"><?php echo esc_html( $tb_cat->name ); ?></a>
      <?php endif; ?>
    </div>
    <h2 class="bfeat__h"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <p class="bfeat__ex"><?php echo esc_html( html_entity_decode( tbt_card_excerpt( null, 40 ), ENT_QUOTES, 'UTF-8' ) ); ?></p>
    <?php tbt_post_meta( '', 32 ); ?>
    <a class="btn btn--primary" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">Read article <?php echo tbt_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
  </div>
</article>
