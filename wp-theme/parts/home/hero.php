<?php
/**
 * Home: hero. ACF group "hero_section".
 *
 * @package TopicalBacklink
 */

if ( ! tb_home_hero_on() ) {
	return;
}

$f       = tb_home_group( 'hero_section' );
$eyebrow = tb_val( $f, 'eye_brow_text', 'AI search optimization: get cited by ChatGPT & Perplexity' );
$heading = tb_val( $f, 'heading', 'The link building agency <span class="swoosh">companies hire<i class="swoosh__ink" aria-hidden="true"></i></span><br> when ROI is priority&nbsp;#1' );
$para    = tb_val( $f, 'para', 'Finally, a link building agency that actually acquires high-authority, world-class links that improve rankings, LLM visibility, and most importantly: produce ROI you can measure.' );
$work    = tb_val( $f, 'work_with_us', tb_page_url( 'contact' ) );
$cases   = tb_val( $f, 'see_case_study_link', '#work' );
?>
<!-- ============================== HERO / ROI ============================== -->
<section class="roi" id="hero">
  <div class="hfx" aria-hidden="true">
    <span class="hfx__grid"></span>
    <span class="hfx__orb hfx__orb--a"></span>
    <span class="hfx__orb hfx__orb--b"></span>
    <span class="hfx__orb hfx__orb--c"></span>
    <span class="hfx__spot"></span>
  </div>

  <div class="hchips" aria-hidden="true">
    <div class="hchip hchip--1" data-depth="18">
      <div class="hchip__in">
        <span class="hchip__ico"><svg class="ico" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg></span>
        <span><b>DR 22 &rarr; 78</b><i>B2B client &middot; authority</i></span>
      </div>
    </div>
    <div class="hchip hchip--2" data-depth="-14">
      <div class="hchip__in">
        <span class="hchip__ico hchip__ico--gain"><svg class="ico" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="m7 14 4-4 4 4 5-6"/></svg></span>
        <span><b>+340% organic</b><i>Nimble CRM &middot; 18 months</i></span>
        <svg class="hchip__spark" viewBox="0 0 78 26"><path d="M1 23 C 12 22, 18 20, 26 17 S 40 14, 48 10 S 64 5, 77 2" fill="none" stroke="#6d28d9" stroke-width="2" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div class="hchip hchip--3" data-depth="12">
      <div class="hchip__in">
        <span class="hchip__ico hchip__ico--sky"><svg class="ico" viewBox="0 0 24 24"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/></svg></span>
        <span><b>34% AI citation share</b><i>across five answer engines</i></span>
      </div>
    </div>
    <div class="hchip hchip--4" data-depth="-20">
      <div class="hchip__in">
        <span class="hchip__ico"><svg class="ico" viewBox="0 0 24 24"><path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 1 1 0 10h-2"/><path d="M8 12h8"/></svg></span>
        <span><b>Link live &middot; DR 58</b><i>industry-mag.com &rsaquo; resources</i></span>
        <span class="hchip__live"></span>
      </div>
    </div>
  </div>

  <div class="shell roi__inner">
    <a href="<?php tb_link( 'services', '#ai-search' ); ?>" class="hpill" data-rise>
      <span class="hpill__tag">NEW</span>
      <?php echo esc_html( $eyebrow ); ?>
      <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
    </a>

    <h1 class="h2 h2--hero center" data-rise><?php tb_heading( $heading ); ?></h1>

    <p class="lead lead--center" data-rise><?php echo esc_html( $para ); ?></p>

    <div class="showcase" data-rise>
      <a href="<?php echo esc_url( $work ); ?>" class="btn btn--violet btn--lg showcase__cta">
        Work with us
        <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
      <a href="<?php echo esc_url( $cases ); ?>" class="btn btn--vghost btn--lg showcase__cta">See case studies</a>
    </div>

    <ul class="hassure" data-rise>
      <li><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>No PBNs, ever</li>
      <li><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>12-month link guarantee</li>
      <li><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>Every placement tracked live</li>
    </ul>
  </div>
</section>
