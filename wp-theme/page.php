<?php
/**
 * Default template for ordinary WordPress pages (Privacy, Terms, etc.).
 *
 * @package TopicalBacklink
 */

get_header();
?>
<main id="top">
<?php while ( have_posts() ) : the_post(); ?>
<section class="phero">
  <div class="phero__wash" aria-hidden="true"></div>
  <div class="shell phero__inner">
    <p class="crumb"><a href="<?php tb_link( 'home' ); ?>">Home</a> <span>/</span> <?php the_title(); ?></p>
    <h1 class="phero__h"><?php the_title(); ?></h1>
  </div>
</section>

<section class="wpc">
  <div class="shell">
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'wpc__entry' ); ?>>
      <?php the_content(); ?>
      <?php wp_link_pages(); ?>
    </article>
  </div>
</section>
<?php endwhile; ?>
</main>
<?php
get_footer();
