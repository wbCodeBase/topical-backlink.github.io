<?php
/**
 * Article card for the blog grid and related posts. Expects the post to be set up.
 *
 * @package TopicalBacklink
 *
 * @var array $args { h: heading tag, 'h2' or 'h3' }
 */

$tb_h   = isset( $args['h'] ) && 'h3' === $args['h'] ? 'h3' : 'h2';
$tb_cat = tbt_post_cat();
?>
<article <?php post_class( 'bcard' ); ?>>
  <a class="bcard__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
    <?php
    if ( has_post_thumbnail() ) {
        the_post_thumbnail( 'medium_large', array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(min-width:1024px) 390px, (min-width:680px) 50vw, 100vw' ) );
    } else {
        echo tbt_blog_ph(); // phpcs:ignore WordPress.Security.EscapeOutput
    }
    ?>
  </a>
  <div class="bcard__body">
    <?php if ( $tb_cat ) : ?>
      <a class="bpill" href="<?php echo esc_url( get_category_link( $tb_cat ) ); ?>"><?php echo esc_html( $tb_cat->name ); ?></a>
    <?php endif; ?>
    <<?php echo $tb_h; ?> class="bcard__h"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo $tb_h; ?>>
    <p class="bcard__ex"><?php echo esc_html( html_entity_decode( tbt_card_excerpt(), ENT_QUOTES, 'UTF-8' ) ); ?></p>
    <?php tbt_post_meta(); ?>
  </div>
</article>
