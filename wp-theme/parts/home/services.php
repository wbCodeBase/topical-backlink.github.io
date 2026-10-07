<?php
/**
 * Home: link building services. ACF group "link_building_services".
 * The service tabs (svc_items) are drawn by js/main.js from window.TB_SERVICES,
 * printed in inc/home-fields.php.
 *
 * @package TopicalBacklink
 */

$f       = tb_home_group( 'link_building_services' );
$heading = tb_val( $f, 'svc_heading', 'Link building services for <span class="mark">organic</span> and <span class="mark">AI visibility</span>' );
$lead    = tb_val( $f, 'svc_lead', '41% of marketers consider link building to be time-consuming and costly. We take care of backlinks for agencies, brands, local and international markets, and AI search visibility. You dedicate your efforts to on-page SEO and leave off-page to us.' );
$button  = tb_val( $f, 'svc_button', tb_page_url( 'services' ) );
$count   = count( tb_rows( $f, 'svc_items', tb_home_default_services() ) );
?>
<!-- ============================== SERVICES / METHOD ============================== -->
<section class="services" id="method">
  <div class="shell">
    <div class="panel">
      <div class="panel__hatch" aria-hidden="true"></div>

      <div class="panel__head">
        <div>
          <h2 class="h2" data-rise><?php tb_heading( $heading ); ?></h2>
          <p class="lead" data-rise><?php echo esc_html( $lead ); ?></p>
        </div>
        <a href="<?php echo esc_url( $button ); ?>" class="btn btn--card" data-rise>
          All services
          <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </div>

      <div class="panel__body">
        <div class="svc__index" role="tablist" aria-label="Services" id="svcIndex"></div>
        <div class="svc__panel" id="svcPanel">
          <div class="dash__chrome">
            <i class="d d--r"></i><i class="d"></i><i class="d"></i>
            <span class="mono" id="svcCrumb"><?php echo esc_html( sprintf( 'services / 01 of %02d', $count ) ); ?></span>
          </div>
          <div class="svc__stage" id="svcStage"></div>
        </div>
      </div>
    </div>
  </div>
</section>
