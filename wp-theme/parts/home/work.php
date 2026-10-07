<?php
/**
 * Home: our work / case studies. ACF group "case_studies".
 * Each card (cs_items) has: image, industry, dr_before, dr_after,
 * client_name, result, duration, link. When cs_items is empty the tiles
 * come from Case studies in the dashboard.
 *
 * @package TopicalBacklink
 */

$f       = tb_home_group( 'case_studies' );
$eyebrow = tb_val( $f, 'eye_brow', 'Our work' );
$heading = tb_val( $f, 'work_heading', 'Programmes we can show you the links for' );
$para    = tb_val( $f, 'work_para', 'Real clients, named with their permission. Every figure is measured against the month before the programme started and on a call we will open the live dashboard so you can check any placement URL yourself.' );

$all_url = tb_page_url( 'case-studies' );

// Without hand-picked cards (cs_items), show the case studies from the dashboard.
$tb_case_ids = tb_rows( $f, 'cs_items', array() ) ? array() : tbt_items( 'tb_case_study', array( 'fields' => 'ids', 'posts_per_page' => 6 ) );

$cards   = tb_rows( $f, 'cs_items', array(
	array( 'image' => 'nimble-crm.webp',        'alt' => 'Nimble CRM dashboard views',                       'industry' => 'SaaS / CRM',          'dr_before' => 38, 'dr_after' => 71, 'client_name' => 'Nimble CRM',                 'result' => '+340% organic',   'duration' => '18 months', 'link' => $all_url ),
	array( 'image' => 'dwlc.webp',              'alt' => 'Doctors Weight Loss Center campaign creative',     'industry' => 'Healthcare',          'dr_before' => 24, 'dr_after' => 58, 'client_name' => 'Doctors Weight Loss Center', 'result' => '+212% enquiries', 'duration' => '12 months', 'link' => $all_url ),
	array( 'image' => 'nri-remittance.webp',    'alt' => 'NRI Remittance money transfer app screen',         'industry' => 'Fintech',             'dr_before' => 31, 'dr_after' => 64, 'client_name' => 'NRI Remittance',             'result' => '+180% signups',   'duration' => '14 months', 'link' => $all_url ),
	array( 'image' => 'collab-management.webp', 'alt' => 'Collab Management site team reviewing plans',      'industry' => 'Construction',        'dr_before' => 19, 'dr_after' => 52, 'client_name' => 'Collab Management',          'result' => '+156% enquiries', 'duration' => '10 months', 'link' => tb_page_url( 'case-study-collab' ) ),
	array( 'image' => 'mn-brow-lash.webp',      'alt' => 'MN Brow Lash Academy training cohort',             'industry' => 'Beauty & training',   'dr_before' => 16, 'dr_after' => 49, 'client_name' => 'MN Brow Lash Academy',       'result' => '+290% bookings',  'duration' => '9 months',  'link' => $all_url ),
	array( 'image' => 'ag-com.webp',            'alt' => 'AG-Com agricultural equipment',                    'industry' => 'Agriculture',         'dr_before' => 27, 'dr_after' => 61, 'client_name' => 'AG-Com LLC',                 'result' => '+124% RFQs',      'duration' => '11 months', 'link' => $all_url ),
) );
?>
<!-- ============================== OUR WORK ============================== -->
<section id="work">
  <div class="shell">
    <div class="sec-head" data-rise>
      <p class="kicker"><?php echo esc_html( $eyebrow ); ?></p>
      <h2 class="h2"><?php tb_heading( $heading ); ?></h2>
      <p class="lead"><?php echo esc_html( $para ); ?></p>
    </div>

    <div class="work__grid" data-rise>
      <?php
      foreach ( $tb_case_ids as $tb_case_id ) {
          get_template_part( 'parts/shared/work-tile', null, array( 'id' => $tb_case_id ) );
      }
      foreach ( ( $tb_case_ids ? array() : $cards ) as $card ) :
          $name   = tb_val( $card, 'client_name' );
          $image  = tb_val( $card, 'image' );
          $before = tb_val( $card, 'dr_before' );
          $after  = tb_val( $card, 'dr_after' );
          ?>
      <a class="wtile" href="<?php echo esc_url( tb_val( $card, 'link', $all_url ) ); ?>">
        <figure class="shot shot--wide shot--contain">
          <?php
          if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
              // ACF image (return format: array). wp_get_attachment_image escapes its output.
              echo wp_get_attachment_image( $image['ID'], 'large', false, array(
                  'loading' => 'lazy',
                  'alt'     => tb_val( $image, 'alt', $name ),
              ) );
          } elseif ( is_numeric( $image ) ) {
              echo wp_get_attachment_image( (int) $image, 'large', false, array( 'loading' => 'lazy' ) );
          } elseif ( is_string( $image ) && $image ) {
              $src = preg_match( '#^https?://#', $image ) ? $image : TB_URI . '/images/work/' . $image;
              printf(
                  '<img src="%s" alt="%s" width="900" height="506" loading="lazy">',
                  esc_url( $src ),
                  esc_attr( tb_val( $card, 'alt', $name ) )
              );
          }
          ?>
        </figure>
        <div class="wtile__body">
          <div class="wtile__meta">
            <span class="wtile__cat"><?php echo esc_html( tb_val( $card, 'industry' ) ); ?></span>
            <?php if ( '' !== $before && '' !== $after ) : ?>
            <span class="wtile__dr"><?php echo esc_html( 'DR ' . $before ); ?> &rarr; <?php echo esc_html( $after ); ?></span>
            <?php endif; ?>
          </div>
          <h3 class="wtile__h"><?php echo esc_html( $name ); ?></h3>
          <div class="wtile__r"><span><?php echo esc_html( tb_val( $card, 'result' ) ); ?></span><span><?php echo esc_html( tb_val( $card, 'duration' ) ); ?></span></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <div class="work__foot" data-rise>
      <a href="<?php echo esc_url( $all_url ); ?>" class="btn btn--primary btn--lg">See all case studies <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
    </div>
  </div>
</section>
