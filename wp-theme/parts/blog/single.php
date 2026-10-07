<?php
/**
 * Blog article: hero, cover image, key takeaways, auto table of contents,
 * content, share, author box, related posts and CTA, plus the reading
 * progress bar, floating "Contents" button and back-to-top (js/blog.js).
 *
 * @package TopicalBacklink
 */

global $post;

while ( have_posts() ) :
	the_post();

	list( $tb_html, $tb_toc ) = tbt_content_with_toc();
	$tb_show_toc  = count( $tb_toc ) >= 2 && ! get_post_meta( get_the_ID(), '_tb_hide_toc', true );
	$tbt_takeaways = tbt_takeaways();
	$tb_cat       = tbt_post_cat();
	$tb_author    = (int) get_the_author_meta( 'ID' );
	$tb_post_id   = get_the_ID();

	// Rendered twice: in the sidebar and in the floating "Contents" sheet.
	$tb_toc_list = '';
	foreach ( $tb_toc as $item ) {
		$tb_toc_list .= '<li class="is-h' . (int) $item['level'] . '"><a href="#' . esc_attr( $item['id'] ) . '" data-id="' . esc_attr( $item['id'] ) . '">' . esc_html( $item['text'] ) . '</a></li>';
	}
	?>
<div class="rprog" aria-hidden="true"><i id="rprog"></i></div>

<main id="top">

<section class="phero bhero">
  <div class="phero__wash" aria-hidden="true"></div>
  <div class="shell phero__inner">
    <p class="crumb">
      <a href="<?php tb_link( 'home' ); ?>">Home</a> <span>/</span>
      <a href="<?php tbt_blog_link(); ?>">Blog</a>
      <?php if ( $tb_cat ) : ?>
        <span>/</span> <a href="<?php echo esc_url( get_category_link( $tb_cat ) ); ?>"><?php echo esc_html( $tb_cat->name ); ?></a>
      <?php endif; ?>
    </p>
    <?php if ( $tb_cat ) : ?>
      <a class="bpill" href="<?php echo esc_url( get_category_link( $tb_cat ) ); ?>"><?php echo esc_html( $tb_cat->name ); ?></a>
    <?php endif; ?>
    <h1 class="phero__h bhero__h"><?php the_title(); ?></h1>
    <?php tbt_post_meta( 'bmeta--hero', 40, true ); ?>
  </div>
</section>

<section class="bpost">
  <div class="shell">

    <?php if ( has_post_thumbnail() ) : ?>
      <figure class="bpost__cover">
        <?php the_post_thumbnail( 'large', array( 'fetchpriority' => 'high', 'loading' => 'eager', 'sizes' => '(min-width:1140px) 1080px, 100vw' ) ); ?>
      </figure>
    <?php endif; ?>

    <div class="bpost__grid<?php echo $tb_show_toc ? '' : ' bpost__grid--solo'; ?>">

      <?php if ( $tb_show_toc ) : ?>
      <aside class="bpost__aside">
        <nav class="btoc" id="btoc" aria-label="Table of contents">
          <span class="btoc__k">Contents</span>
          <div class="btoc__prog" aria-hidden="true"><i></i></div>
          <ol class="tocl"><?php echo $tb_toc_list; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></ol>
        </nav>
        <?php tbt_share( 'bshare--aside' ); ?>
      </aside>
      <?php endif; ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class( 'bpost__main' ); ?>>

        <?php if ( $tbt_takeaways ) : ?>
        <div class="btake">
          <p class="btake__k"><?php echo tbt_icon( 'bulb' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Key takeaways</p>
          <ul>
            <?php foreach ( $tbt_takeaways as $line ) : ?>
              <li><span class="btake__tick"><?php echo tbt_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><?php echo esc_html( $line ); ?></span></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <div class="bprose" id="bprose">
          <?php echo $tb_html; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output. ?>
        </div>
        <?php wp_link_pages( array( 'before' => '<nav class="bpages">Pages:', 'after' => '</nav>' ) ); ?>

        <div class="bpost__foot">
          <?php
          $tb_tags = get_the_tags();
          if ( $tb_tags ) {
              echo '<div class="btags">';
              foreach ( $tb_tags as $t ) {
                  echo '<a href="' . esc_url( get_tag_link( $t ) ) . '">#' . esc_html( $t->name ) . '</a>';
              }
              echo '</div>';
          }
          tbt_share();
          ?>
        </div>

        <div class="bauthor">
          <?php echo get_avatar( $tb_author, 144, '', '', array( 'class' => 'bauthor__img', 'width' => 72, 'height' => 72 ) ); ?>
          <div>
            <span class="bauthor__k">Written by</span>
            <h2 class="bauthor__h"><a href="<?php echo esc_url( get_author_posts_url( $tb_author ) ); ?>"><?php the_author(); ?></a></h2>
            <p class="bauthor__b">
              <?php
              $tb_bio = get_the_author_meta( 'description' );
              echo esc_html( $tb_bio ? $tb_bio : 'Part of the TopicalBacklink team, writing about link building, digital PR and search visibility from the work we do for clients every day.' );
              ?>
            </p>
            <a class="bauthor__more" href="<?php echo esc_url( get_author_posts_url( $tb_author ) ); ?>">More from <?php the_author(); ?> <?php echo tbt_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
          </div>
        </div>

      </article>
    </div>
  </div>
</section>

<?php $tb_related = tbt_related_ids( $tb_post_id, 3 ); ?>
<?php if ( $tb_related ) : ?>
<section class="band brel">
  <div class="shell">
    <div class="sec-head">
      <p class="kicker">Keep reading</p>
      <h2 class="h2">Related articles</h2>
    </div>
    <div class="bgrid">
      <?php
      foreach ( $tb_related as $tb_id ) {
          $post = get_post( $tb_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
          setup_postdata( $post );
          get_template_part( 'parts/blog/card', null, array( 'h' => 'h3' ) );
      }
      wp_reset_postdata();
      ?>
    </div>
    <p class="brel__all"><a class="btn btn--ghost" href="<?php tbt_blog_link(); ?>">View all articles <?php echo tbt_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
  </div>
</section>
<?php endif; ?>

<?php get_template_part( 'parts/blog/cta' ); ?>

</main>

<div class="bfloat" id="bfloat">
  <?php if ( $tb_show_toc ) : ?>
  <button type="button" class="bfloat__toc" id="bfloatToc" aria-expanded="false" aria-controls="bsheet">
    <?php echo tbt_icon( 'list' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> Contents
  </button>
  <?php endif; ?>
  <button type="button" class="bfloat__top" id="btop" aria-label="Back to top"><?php echo tbt_icon( 'up' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</div>

<?php if ( $tb_show_toc ) : ?>
<div class="bsheet" id="bsheet" hidden>
  <div class="bsheet__panel" role="dialog" aria-modal="true" aria-labelledby="bsheetTitle">
    <div class="bsheet__top">
      <b id="bsheetTitle">Contents</b>
      <button type="button" class="bsheet__x" id="bsheetClose" aria-label="Close contents"><?php echo tbt_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
    </div>
    <ol class="tocl"><?php echo $tb_toc_list; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></ol>
  </div>
</div>
<?php endif; ?>

<p class="screen-reader-text" aria-live="polite" id="bshareMsg"></p>
	<?php
endwhile;
