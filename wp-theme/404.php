<?php
/**
 * 404 page.
 *
 * @package TopicalBacklink
 */

get_header();
?>
<main id="top">
<section class="phero">
  <div class="phero__wash" aria-hidden="true"></div>
  <div class="shell phero__inner">
    <p class="crumb"><a href="<?php tb_link( 'home' ); ?>">Home</a> <span>/</span> 404</p>
    <h1 class="phero__h">This page has no inbound links.</h1>
    <p class="phero__sub">The page you were looking for doesn&rsquo;t exist or has moved.</p>
    <div class="phero__cta">
      <a href="<?php tb_link( 'home' ); ?>" class="btn btn--primary btn--lg">Back to home</a>
      <a href="<?php tb_link( 'contact' ); ?>" class="btn btn--ghost btn--lg">Contact us</a>
    </div>
  </div>
</section>
</main>
<?php
get_footer();
