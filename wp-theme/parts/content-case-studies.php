<?php
/**
 * Case studies page. The cards come from Case studies in the dashboard (menu
 * order); headings come from the "Page headings" fields, falling back to the design copy.
 *
 * @package TopicalBacklink
 */

$f     = tbt_fields( get_queried_object_id(), 'tb_listing' );
$arrow = tbt_svg( 'arrow' );
$items = tbt_items( 'tb_case_study' );
?>
<main id="top">

<section class="phero">
  <div class="phero__wash" aria-hidden="true"></div>
  <div class="shell phero__inner">
  <p class="crumb"><a href="<?php tb_link( 'home' ); ?>">Home</a> <span>/</span> Case studies</p>
    <h1 class="phero__h" data-rise><?php tb_heading( tbt_v( $f, 'hero_heading', 'Results we can <span class="mark">show you the links for</span>' ) ); ?></h1>
    <p class="phero__sub" data-rise><?php echo esc_html( tbt_v( $f, 'hero_sub', 'Every figure below came from a real programme. On a call we walk you through the live dashboards and the actual placement URLs.' ) ); ?></p>
      <div class="phero__cta">
        <a href="<?php tb_link( 'contact' ); ?>" class="btn btn--primary btn--lg">Book a call <?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
        <a href="<?php tb_link( 'home', '#audit' ); ?>" class="btn btn--ghost btn--lg">Free link audit</a>
      </div>
  </div>
</section>

<?php if ( $items ) : ?>
<section class="band">
  <div class="shell">
    <div class="sec-head" data-rise>
      <p class="kicker"><?php echo esc_html( tbt_v( $f, 'list_kicker', 'Selected work' ) ); ?></p>
      <h2 class="h2"><?php tb_heading( tbt_v( $f, 'list_heading', 'Different programmes, different problems' ) ); ?></h2>
      <p class="lead"><?php echo esc_html( tbt_v( $f, 'list_lead', 'Link building is not one service. What moved the needle in each of these was a different part of the graph.' ) ); ?></p>
    </div>
    <div class="cases" data-rise>
      <?php
      foreach ( $items as $c ) :
          $cf   = tbt_fields( $c->ID );
          $nums = tbt_case_card_stats( $cf );
          $dr   = tbt_case_dr( $cf );
          ?>
      <article class="case case--link">
        <?php if ( tbt_has_image( $c->ID ) ) : ?>
        <figure class="case__img"><?php echo tbt_post_image( $c->ID, 'medium_large', array( 'alt' => '', 'loading' => 'lazy' ) ); ?></figure>
        <?php endif; ?>
        <div class="case__top">
          <span class="case__cat"><?php echo esc_html( tbt_v( $cf, 'industry', tbt_v( $cf, 'client' ) ) ); ?></span>
          <?php if ( $dr ) : ?><span class="case__dr"><?php echo esc_html( $dr ); ?></span><?php endif; ?>
        </div>
        <div class="case__body">
          <h3 class="case__h"><a href="<?php echo esc_url( get_permalink( $c ) ); ?>"><?php echo esc_html( get_the_title( $c ) ); ?></a></h3>
          <?php if ( has_excerpt( $c ) ) : ?><p class="case__b"><?php echo esc_html( get_the_excerpt( $c ) ); ?></p><?php endif; ?>
          <span class="case__go">Read the case study <?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
          <?php if ( $nums ) : ?>
          <dl class="case__nums">
            <?php foreach ( $nums as $n ) : ?><div><dt><?php echo esc_html( strtoupper( tbt_v( $n, 'label' ) ) ); ?></dt><dd><?php echo esc_html( tbt_v( $n, 'value' ) ); ?></dd></div><?php endforeach; ?>
          </dl>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section>
  <div class="shell">
    <div class="sec-head" data-rise>
      <p class="kicker">How we measure</p>
      <h2 class="h2">What these numbers do and don't mean</h2>
    </div>
    <div class="grid3" data-rise>
      <div class="card">
        <span class="card__ico"><?php echo tbt_svg( 'chart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <h3 class="card__h">Measured against a baseline</h3>
        <p class="card__b">Every figure is the delta from the month before the programme started,
          not a cherry-picked peak.</p>
      </div>
      <div class="card">
        <span class="card__ico"><?php echo tbt_svg( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <h3 class="card__h">Links are not the only variable</h3>
        <p class="card__b">Most of these clients also shipped content and technical fixes. We
          claim the links, not the whole result.</p>
      </div>
      <div class="card">
        <span class="card__ico"><?php echo tbt_svg( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <h3 class="card__h">Verifiable on request</h3>
        <p class="card__b">We will show you the live placement URLs for any case here, under NDA,
          on the first call.</p>
      </div>
    </div>
  </div>
</section>

<?php
get_template_part( 'parts/shared/cta', null, array(
    'heading' => tbt_v( $f, 'cta_heading', 'Get started today. Earn powerful backlinks. Grow every client.' ),
    'text'    => tbt_v( $f, 'cta_text', 'Book a call and get your free link audit. Review recommended placements and track every order from outreach to live link.' ),
) );
?>

</main>
