<?php
/**
 * Fallback template: blog index, archives, search and single posts.
 *
 * @package TopicalBacklink
 */

get_header();

if ( is_singular() ) {
	$tb_heading = single_post_title( '', false );
} elseif ( is_search() ) {
	$tb_heading = sprintf( 'Search: %s', get_search_query() );
} elseif ( is_archive() ) {
	$tb_heading = wp_strip_all_tags( get_the_archive_title() );
} else {
	$tb_heading = 'Blog';
}
?>
<main id="top">
<section class="phero">
  <div class="phero__wash" aria-hidden="true"></div>
  <div class="shell phero__inner">
    <p class="crumb"><a href="<?php tb_link( 'home' ); ?>">Home</a> <span>/</span> <?php echo esc_html( $tb_heading ); ?></p>
    <h1 class="phero__h"><?php echo esc_html( $tb_heading ); ?></h1>
  </div>
</section>

<section class="wpc">
  <div class="shell">
  <?php if ( have_posts() ) : ?>
    <?php if ( is_singular() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class( 'wpc__entry' ); ?>>
        <?php if ( has_post_thumbnail() ) : ?><figure class="wpc__thumb"><?php the_post_thumbnail( 'large' ); ?></figure><?php endif; ?>
        <p class="wpc__meta"><?php echo esc_html( get_the_date() ); ?></p>
        <?php the_content(); ?>
        <?php wp_link_pages(); ?>
      </article>
      <?php
      if ( comments_open() || get_comments_number() ) {
          echo '<div class="wpc__entry">';
          comments_template();
          echo '</div>';
      }
      endwhile;
      ?>
    <?php else : ?>
      <div class="wpc__list">
      <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'wpc__card' ); ?>>
          <p class="wpc__meta"><?php echo esc_html( get_the_date() ); ?></p>
          <h2 class="wpc__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <div class="wpc__excerpt"><?php the_excerpt(); ?></div>
          <a class="btn btn--ghost" href="<?php the_permalink(); ?>">Read more</a>
        </article>
      <?php endwhile; ?>
      </div>
      <div class="wpc__pager"><?php the_posts_pagination(); ?></div>
    <?php endif; ?>
  <?php else : ?>
    <div class="wpc__entry"><p>Nothing found here yet.</p></div>
  <?php endif; ?>
  </div>
</section>
</main>
<?php
get_footer();
