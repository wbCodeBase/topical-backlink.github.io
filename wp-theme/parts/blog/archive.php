<?php
/**
 * Blog listing: hero with search, category filter, featured article, card grid,
 * pagination and CTA. Used by home.php, archive.php and search.php.
 *
 * @package TopicalBacklink
 */

global $post, $wp_query;

$tb_search = is_search();
$tb_q      = get_search_query();
$tb_cat    = is_category() ? get_queried_object() : null;
if ( $tb_search && get_query_var( 'category_name' ) ) {
	$tb_cat = get_category_by_slug( get_query_var( 'category_name' ) );
}
$tb_feat = ( ! $tb_search && ! is_paged() && ( is_home() || is_category() ) ) ? tbt_blog_featured_id() : 0;

// Hero copy.
if ( $tb_search ) {
	$tb_crumb = 'Search';
	$tb_h1    = $tb_q ? 'Results for <span class="mark">&ldquo;' . esc_html( $tb_q ) . '&rdquo;</span>' : 'Search the blog';
	$tb_sub   = $tb_q ? sprintf( '%d %s found%s.', (int) $wp_query->found_posts, 1 === (int) $wp_query->found_posts ? 'article' : 'articles', $tb_cat ? ' in ' . esc_html( $tb_cat->name ) : '' ) : 'Type a topic, question or keyword.';
} elseif ( $tb_cat ) {
	$tb_crumb = esc_html( $tb_cat->name );
	$tb_h1    = '<span class="mark">' . esc_html( $tb_cat->name ) . '</span>';
	$tb_sub   = $tb_cat->description ? esc_html( $tb_cat->description ) : 'Every article we have published on ' . esc_html( strtolower( $tb_cat->name ) ) . '.';
} elseif ( is_archive() ) {
	$tb_crumb = esc_html( wp_strip_all_tags( get_the_archive_title() ) );
	$tb_h1    = is_author() ? 'Articles by <span class="mark">' . esc_html( get_the_author_meta( 'display_name', get_queried_object_id() ) ) . '</span>' : $tb_crumb;
	$tb_sub   = is_author() && get_the_author_meta( 'description', get_queried_object_id() ) ? esc_html( get_the_author_meta( 'description', get_queried_object_id() ) ) : 'Articles from the TopicalBacklink team.';
} else {
	$tb_crumb = '';
	$tb_h1    = 'Link building, <span class="mark">explained by practitioners</span>';
	$tb_sub   = 'Practical guides on link building, guest posting, digital PR and getting cited by AI search, written by the team that builds the links.';
}

// Filter URLs keep the search term, so search and category work together.
$tb_all_url = $tb_search ? add_query_arg( 's', rawurlencode( $tb_q ), home_url( '/' ) ) : tbt_blog_url();
?>
<main id="top">

<section class="phero bhero">
  <div class="phero__wash" aria-hidden="true"></div>
  <div class="shell phero__inner">
    <p class="crumb">
      <a href="<?php tb_link( 'home' ); ?>">Home</a> <span>/</span>
      <?php if ( $tb_crumb ) : ?>
        <a href="<?php tbt_blog_link(); ?>">Blog</a> <span>/</span> <?php echo $tb_crumb; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?>
      <?php else : ?>
        Blog
      <?php endif; ?>
    </p>
    <h1 class="phero__h"><?php echo $tb_h1; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></h1>
    <p class="phero__sub"><?php echo $tb_sub; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></p>

    <form class="bsearch" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
      <label class="screen-reader-text" for="bsearch">Search articles</label>
      <?php echo tbt_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
      <input id="bsearch" type="search" name="s" value="<?php echo esc_attr( $tb_q ); ?>" placeholder="<?php echo esc_attr( $tb_cat ? 'Search ' . $tb_cat->name . ' articles' : 'Search articles' ); ?>&hellip;" autocomplete="off">
      <?php if ( $tb_cat ) : ?><input type="hidden" name="category_name" value="<?php echo esc_attr( $tb_cat->slug ); ?>"><?php endif; ?>
      <button class="btn btn--primary" type="submit">Search</button>
    </form>
  </div>
</section>

<section class="blist">
  <div class="shell">

    <?php $tb_cats = tbt_blog_cats(); ?>
    <?php if ( $tb_cats ) : ?>
    <nav class="bfilter" aria-label="Filter by category">
      <div class="bfilter__track">
        <a class="bfilter__b<?php echo $tb_cat ? '' : ' is-on'; ?>" href="<?php echo esc_url( $tb_all_url ); ?>"<?php echo $tb_cat ? '' : ' aria-current="page"'; ?>>All</a>
        <?php foreach ( $tb_cats as $c ) : ?>
          <?php
          $on  = $tb_cat && (int) $tb_cat->term_id === (int) $c->term_id;
          $url = $tb_search ? add_query_arg( array( 's' => rawurlencode( $tb_q ), 'category_name' => $c->slug ), home_url( '/' ) ) : get_category_link( $c );
          ?>
          <a class="bfilter__b<?php echo $on ? ' is-on' : ''; ?>" href="<?php echo esc_url( $url ); ?>"<?php echo $on ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $c->name ); ?> <span><?php echo (int) $c->count; ?></span></a>
        <?php endforeach; ?>
      </div>
    </nav>
    <?php endif; ?>

    <?php
    if ( $tb_feat ) {
        $post = get_post( $tb_feat ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
        setup_postdata( $post );
        get_template_part( 'parts/blog/featured' );
        wp_reset_postdata();
    }
    ?>

    <?php if ( have_posts() ) : ?>
      <?php if ( $tb_feat ) : ?><h2 class="blist__h">Latest articles</h2><?php endif; ?>
      <div class="bgrid">
        <?php
        while ( have_posts() ) {
            the_post();
            get_template_part( 'parts/blog/card', null, array( 'h' => $tb_feat ? 'h3' : 'h2' ) );
        }
        ?>
      </div>
      <?php
      the_posts_pagination( array(
          'class'              => 'bpager',
          'mid_size'           => 1,
          'prev_text'          => tbt_icon( 'arrow' ) . '<span>Previous</span>',
          'next_text'          => '<span>Next</span>' . tbt_icon( 'arrow' ),
          'screen_reader_text' => 'Blog pages',
      ) );
      ?>
    <?php elseif ( ! $tb_feat ) : ?>
      <div class="bempty">
        <span class="bempty__ico"><?php echo tbt_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <?php if ( $tb_search ) : ?>
          <h2 class="bempty__h">No articles match that search</h2>
          <p class="bempty__b">Try a broader word, or browse everything we have written.</p>
        <?php else : ?>
          <h2 class="bempty__h">No articles here yet</h2>
          <p class="bempty__b">New guides are on the way. In the meantime, browse the rest of the blog.</p>
        <?php endif; ?>
        <a class="btn btn--ghost" href="<?php tbt_blog_link(); ?>">View all articles</a>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php get_template_part( 'parts/blog/cta' ); ?>

</main>
