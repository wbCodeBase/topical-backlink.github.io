<?php
/**
 * Services page. The list comes from Services in the dashboard (menu order);
 * headings come from the "Page headings" fields, falling back to the design copy.
 *
 * @package TopicalBacklink
 */

$f     = tbt_fields( get_queried_object_id(), 'tb_listing' );
$arrow = tbt_svg( 'arrow' );
$tick  = '<span class="tick">' . tbt_svg( 'check' ) . '</span>';
$items = tbt_items( 'tb_service' );
// Anchors the footer and older links use (services.html#local etc.).
$legacy = array(
	'white-label-link-building'   => 'white-label',
	'multi-lingual-link-building' => 'multi-lingual',
	'local-link-building'         => 'local',
	'media-placements'            => 'media',
	'ai-search-optimization'      => 'ai-search',
);
$words = array( 'Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten' );
?>
<main id="top">

<section class="phero">
  <div class="phero__wash" aria-hidden="true"></div>
  <div class="shell phero__inner">
  <p class="crumb"><a href="<?php tb_link( 'home' ); ?>">Home</a> <span>/</span> Services</p>
    <h1 class="phero__h" data-rise><?php tb_heading( tbt_v( $f, 'hero_heading', 'Link building services for <span class="mark">organic</span> and <span class="mark">AI visibility</span>' ) ); ?></h1>
    <p class="phero__sub" data-rise><?php echo esc_html( tbt_v( $f, 'hero_sub', '41% of marketers consider link building time-consuming and costly. We take care of backlinks for agencies, brands, local and international markets, and AI search visibility, so you can stay on on-page SEO.' ) ); ?></p>
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
      <p class="kicker"><?php echo esc_html( tbt_v( $f, 'list_kicker', ( isset( $words[ count( $items ) ] ) ? $words[ count( $items ) ] : count( $items ) ) . ' ways we work' ) ); ?></p>
      <h2 class="h2"><?php tb_heading( tbt_v( $f, 'list_heading', 'Pick the programme that matches the gap' ) ); ?></h2>
      <p class="lead"><?php echo esc_html( tbt_v( $f, 'list_lead', 'Every service runs on the same spine: map the graph, pitch real editors, publish content that deserves the placement, and report it live.' ) ); ?></p>
    </div>
    <?php
    foreach ( $items as $i => $s ) :
        $sf     = tbt_fields( $s->ID );
        $points = tbt_lines( $sf['card_points'] );
        $stats  = array_slice( tbt_filled( $sf['card_stats'] ), 0, 4 );
        ?>
      <div class="srow" id="<?php echo esc_attr( $s->post_name ); ?>">
        <?php if ( isset( $legacy[ $s->post_name ] ) ) : ?><span id="<?php echo esc_attr( $legacy[ $s->post_name ] ); ?>" class="srow__anchor"></span><?php endif; ?>
        <div class="srow__copy" data-rise>
          <p class="srow__k"><b><?php echo esc_html( sprintf( 'SERVICE %02d', $i + 1 ) ); ?></b></p>
          <h2 class="srow__h"><a href="<?php echo esc_url( get_permalink( $s ) ); ?>"><?php echo esc_html( get_the_title( $s ) ); ?></a></h2>
          <?php if ( has_excerpt( $s ) ) : ?><p class="srow__b"><?php echo esc_html( get_the_excerpt( $s ) ); ?></p><?php endif; ?>
          <?php if ( $points ) : ?>
          <ul class="srow__list">
            <?php foreach ( $points as $p ) : ?><li><?php echo $tick . esc_html( $p ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li><?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <p class="row" style="margin-top:1.75rem"><a href="<?php tb_link( 'contact' ); ?>" class="btn btn--primary">Discuss this service <?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput ?></a><a href="<?php echo esc_url( get_permalink( $s ) ); ?>" class="btn btn--ghost">Full service details</a></p>
        </div>
        <div class="srow__media srow__media--img" data-rise>
          <figure class="shot shot--wide">
            <?php echo tbt_has_image( $s->ID ) ? tbt_post_image( $s->ID, 'large', array( 'loading' => 'lazy' ) ) : tbt_blog_ph(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
          </figure>
          <?php if ( $stats ) : ?>
          <dl class="shotstats">
            <?php foreach ( $stats as $st ) : ?><div><dt><?php echo esc_html( tbt_v( $st, 'label' ) ); ?></dt><dd><?php echo esc_html( tbt_v( $st, 'value' ) ); ?></dd></div><?php endforeach; ?>
          </dl>
          <?php endif; ?>
          <?php if ( tbt_v( $sf, 'card_note' ) ) : ?><p class="shotnote"><?php echo esc_html( $sf['card_note'] ); ?></p><?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section>
  <div class="shell">
    <div class="sec-head" data-rise>
      <p class="kicker">Included with everything</p>
      <h2 class="h2">The parts we never charge extra for</h2>
    </div>
    <div class="grid3" data-rise>
      <div class="card">
        <span class="card__ico"><?php echo tbt_svg( 'chart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <h3 class="card__h">Live reporting dashboard</h3>
        <p class="card__b">Placement URL, DR, estimated traffic, anchor, target page and live date
          for every link, visible the moment it publishes, not in a monthly PDF.</p>
      </div>
      <div class="card">
        <span class="card__ico"><?php echo tbt_svg( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <h3 class="card__h">12-month replacement guarantee</h3>
        <p class="card__b">If an eligible link drops, is nofollowed or the page is pulled, we
          replace it on a comparable domain at no charge.</p>
      </div>
      <div class="card">
        <span class="card__ico"><?php echo tbt_svg( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <h3 class="card__h">Vetting before we pitch</h3>
        <p class="card__b">Every prospect is scored on authority, topical fit, outbound-link
          hygiene and real traffic before it ever reaches your approval queue.</p>
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
