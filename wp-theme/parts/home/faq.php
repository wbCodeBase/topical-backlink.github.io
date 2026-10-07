<?php
/**
 * Home: FAQ accordion. ACF group "faq_section".
 *
 * @package TopicalBacklink
 */

$f       = tb_home_group( 'faq_section' );
$eyebrow = tb_val( $f, 'faq_eyebrow', 'Questions' );
$heading = tb_val( $f, 'faq_heading', 'Everything agencies ask <span class="mark">before starting</span>' );
$items   = tb_rows( $f, 'faq_items', array(
	array( 'is_open' => true,  'question' => 'How do you find placements?', 'answer' => "We start from the link graph of your niche, not from a seller list. We map which domains already rank for your topics, which of those link out editorially, and which your competitors have that you don\u{2019}t. Every prospect is then pitched individually by a human." ),
	array( 'is_open' => false, 'question' => 'Are these guest posts, or genuine editorial links?', 'answer' => "Both, and we label which is which in your dashboard. What we never do is place on PBNs, link farms, expired domains or \u{201C}write for us\u{201D} pages that exist only to sell links. If a site would take anything for a fee, its links are worth nothing to you." ),
	array( 'is_open' => false, 'question' => 'What is the turnaround?', 'answer' => 'Your gap analysis lands within 72 hours of the kickoff call. Placements begin going live from week three, and a typical order completes inside 30 days. You watch every order move through outreach, content and live in the dashboard rather than waiting on a monthly report.' ),
	array( 'is_open' => false, 'question' => 'Do you work white-label?', 'answer' => 'Yes, most of our volume is agencies reselling under their own brand. Reports carry your logo and domain, we never contact your client, and we never appear in the placement trail. Around 3,500 agencies run us as their link-building department.' ),
	array( 'is_open' => false, 'question' => 'What happens if a link gets removed?', 'answer' => 'Every eligible placement carries a 12-month replacement guarantee. We monitor live links continuously; if one drops, is nofollowed or the page is pulled, we replace it on a comparable domain at no charge. Retention sits at 94% at the twelve-month mark.' ),
	array( 'is_open' => false, 'question' => 'What do you need from me to start?', 'answer' => 'A domain, the pages you want to rank, and any anchors or topics to avoid. That is enough for the gap analysis. There is no contract and no minimum order, so you can run a single placement before committing to a programme.' ),
) );

// The accordion shows one answer at a time: only the first "open" row opens.
$opened = false;
?>
<!-- ============================== FAQ ============================== -->
<section class="faq" id="faq">
  <div class="shell faq__inner">
    <div class="faq__head" data-rise>
      <p class="kicker"><?php echo esc_html( $eyebrow ); ?></p>
      <h2 class="h2 center"><?php tb_heading( $heading ); ?></h2>
    </div>

    <div class="faq__list" data-rise>
      <?php
      foreach ( $items as $item ) :
          $open   = ! $opened && (bool) tb_val( $item, 'is_open', false );
          $opened = $opened || $open;
          ?>
      <div class="qa<?php echo $open ? ' is-open' : ''; ?>">
        <button class="qa__q" type="button" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
          <span><?php echo esc_html( tb_val( $item, 'question' ) ); ?></span>
          <span class="qa__sign" aria-hidden="true"></span>
        </button>
        <div class="qa__panel">
          <div class="qa__a"><?php echo wp_kses_post( tb_val( $item, 'answer' ) ); ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
