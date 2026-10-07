<?php
/**
 * Starting content for Services and Case studies: the copy and figures from
 * the original designed pages. Used once by tbt_setup_content(); after that
 * everything is edited in the dashboard.
 *
 * @package TopicalBacklink
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tbt_seed_testimonial_defaults() {
	return array(
		array( 'quote' => 'Happy with the services in 2021. We are on the right track and the team is able to adapt very well. Looking forward to 2022, congratulations.', 'name' => 'Jeff Wright', 'role' => 'CEO, LuxVan', 'photo' => 'images/people/jeff.webp' ),
		array( 'quote' => 'Impressive team, fast execution, and growth-driven strategies delivering exceptional content consistently.', 'name' => 'Randy Cheng', 'role' => 'Founder & CEO, Collab Management Group', 'photo' => 'images/people/avatar-m.svg' ),
		array( 'quote' => 'Their team invested deeply in our growth. The performance was beyond what we expected.', 'name' => 'Christina', 'role' => 'Co-Founder, Segwise.ai', 'photo' => 'images/people/avatar-f.svg' ),
	);
}

function tbt_seed_services( $collab_id ) {
	$cta_h = 'Get started today. Earn powerful backlinks. Grow every client.';
	$cta_t = 'Book a call and get your free link audit. Review recommended placements and track every order from outreach to live link.';

	$stats = function ( $pairs ) {
		$out = array();
		foreach ( $pairs as $p ) {
			$out[] = array( 'label' => $p[0], 'value' => $p[1] );
		}
		return $out;
	};

	$simple = function ( $title, $slug, $short, $excerpt, $points, $card_stats, $note, $image ) use ( $stats, $cta_h, $cta_t ) {
		return array(
			'title'   => $title,
			'slug'    => $slug,
			'excerpt' => $excerpt,
			'image'   => $image,
			'fields'  => array(
				'card_points'       => implode( "\n", $points ),
				'card_stats'        => $stats( $card_stats ),
				'card_note'         => $note,
				'short_name'        => $short,
				'hero_heading'      => $title,
				'hero_lead'         => $excerpt,
				'hero_btn_label'    => 'Book a call',
				'hero_btn2_label'   => 'See pricing',
				'hero_btn2_url'     => '/pricing/',
				'show_trust'        => 1,
				'overview_kicker'   => 'What you get',
				'overview_heading'  => 'What is included',
				'overview_points'   => implode( "\n", $points ),
				'overview_visual'   => 'image',
				'show_estimator'    => 0,
				'show_testimonials' => 1,
				'cta_heading'       => $cta_h,
				'cta_text'          => $cta_t,
			),
		);
	};

	return array(
		array(
			'title'   => 'White-label link building',
			'slug'    => 'white-label-link-building',
			'excerpt' => 'Scalable, agency-ready link building with genuine editorial outreach and zero PBNs. We become your link-building department: reports carry your logo, we never contact your client, and we never appear in the placement trail.',
			'image'   => '',
			'fields'  => array(
				'card_points'       => "Your branding on every report and dashboard\nLive placement tracking your client can log into\nEvery link guaranteed for twelve months\nNo minimum order, test with a single placement",
				'card_stats'        => $stats( array( array( 'AVG. DR PLACED', '58' ), array( 'RETENTION 12MO', '94%' ), array( 'TYPICAL TURNAROUND', '30d' ), array( 'AGENCIES SERVED', '3,500+' ) ) ),
				'card_note'         => '// figures are programme averages, not a guarantee',

				'short_name'        => 'White-label',
				'hero_heading'      => 'White-label link building your',
				'hero_highlight'    => 'clients will love',
				'hero_lead'         => 'Scalable, agency-ready link building with genuine editorial outreach and zero PBNs. We become your link-building department: your brand on every report, and we never contact your client.',
				'hero_btn_label'    => 'Start a white-label trial',
				'hero_btn_url'      => '',
				'hero_btn2_label'   => 'Estimate your cost',
				'hero_btn2_url'     => '#estimate',
				'hero_stats'        => array(
					array( 'label' => 'AVG. DR PLACED', 'value' => 58, 'prefix' => '', 'suffix' => '' ),
					array( 'label' => 'RETENTION AT 12 MONTHS', 'value' => 94, 'prefix' => '', 'suffix' => '%' ),
					array( 'label' => 'TYPICAL TURNAROUND', 'value' => 30, 'prefix' => '', 'suffix' => 'd' ),
					array( 'label' => 'AGENCIES SERVED', 'value' => 3500, 'prefix' => '', 'suffix' => '+' ),
				),
				'hero_chips'        => array(
					array( 'title' => 'Your logo, not ours', 'text' => 'on every report & dashboard' ),
					array( 'title' => '94% retention', 'text' => 'still live at twelve months' ),
					array( 'title' => 'Link live · DR 64', 'text' => 'trade-journal.com › insights' ),
					array( 'title' => '30-day delivery', 'text' => 'typical order turnaround' ),
				),

				'show_trust'        => 1,
				'overview_kicker'   => 'What you get',
				'overview_heading'  => 'A link department that',
				'overview_highlight' => 'wears your badge',
				'overview_lead'     => 'Hiring outreach specialists, editors and a vetting analyst costs more than most retainers bring in. We already have the team. You sell the programme under your name, we deliver it, and your client only ever sees your brand.',
				'overview_points'   => "Your branding on every report and dashboard\nLive placement tracking your client can log into\nEvery link guaranteed for twelve months\nNo minimum order, test with a single placement",
				'overview_visual'   => 'dashboard',

				'features_kicker'   => 'Inside every order',
				'features_heading'  => 'Six things your clients',
				'features_highlight' => 'never see us do',
				'features_lead'     => 'They see the result under your name. Behind it, this is the work.',
				'features'          => array(
					array( 'icon' => 'search', 'title' => 'Link-gap mapping', 'text' => "We map which domains link to your client's competitors and not to them, then rank those gaps by authority and topical fit." ),
					array( 'icon' => 'shield', 'title' => 'Four-point vetting', 'text' => 'Every prospect is scored on authority, topical fit, outbound-link hygiene and real traffic before it reaches your approval queue.' ),
					array( 'icon' => 'send', 'title' => 'Human outreach', 'text' => "Real editors pitched individually. If a site won't accept a genuine pitch, it isn't a site worth placing on." ),
					array( 'icon' => 'pen', 'title' => 'Publication-grade content', 'text' => "Every piece is written for the publication it's going to, with your client's link placed where an editor would put it." ),
					array( 'icon' => 'chart', 'title' => 'Branded live reporting', 'text' => 'Placement URL, DR, traffic, anchor, target page and live date for every link, on a dashboard carrying your logo and domain.' ),
					array( 'icon' => 'refresh', 'title' => '12-month replacement', 'text' => 'If an eligible link drops, is nofollowed or the page is pulled, we replace it on a comparable domain at no charge.' ),
				),

				'process_kicker'    => 'How it works',
				'process_heading'   => 'From brief to live link in',
				'process_highlight' => 'five steps',
				'process_lead'      => 'Hover to pause, or pick any step to jump to it.',
				'steps'             => array(
					array( 'title' => 'Brief & link-gap map', 'when' => 'WITHIN 72 HOURS', 'heading' => 'Tell us the domain. We map the gap.', 'text' => "Send the client domain, target pages and any anchors or topics to avoid. We map their link graph against their three closest competitors and return the exact domains they're missing.", 'outputs' => "Competitor link-gap report, white-labelled\nRecommended target pages and anchor mix" ),
					array( 'title' => 'Prospect approval', 'when' => 'YOU SIGN OFF', 'heading' => 'See every site before we pitch it.', 'text' => 'The domain, its authority, traffic and price land in your approval queue first. Nothing is pitched without a yes from you, so nothing surprises your client.', 'outputs' => "Vetted prospect list with DR, traffic and price\nOne-click approve or reject per domain" ),
					array( 'title' => 'Outreach & content', 'when' => 'WEEKS 1–3', 'heading' => 'Real pitches, written for each publication.', 'text' => "Our outreach team pitches editors one by one, and our writers produce content that fits each site's audience. Your client's link sits where an editor would naturally place it.", 'outputs' => "Individually pitched, never templated blasts\nDrafts available for review on request" ),
					array( 'title' => 'Links go live', 'when' => 'FROM WEEK 3', 'heading' => 'Links land, and your dashboard shows them.', 'text' => 'When a placement publishes, it appears on the branded dashboard immediately, with the live URL, DR, estimated traffic, anchor text and target page. A typical order completes inside 30 days.', 'outputs' => "Real-time status: queued, outreach, content, live\nClient login under your own domain" ),
					array( 'title' => 'Report & guarantee', 'when' => 'ONGOING', 'heading' => 'Monitored for a year, replaced if lost.', 'text' => 'Every live link is monitored continuously. If one drops, is nofollowed or the page is pulled, we replace it on a comparable domain at no charge, and your client never has to ask.', 'outputs' => "12-month replacement guarantee\nMonthly branded PDF if your client prefers one" ),
				),

				'show_estimator'    => 1,
				'est_kicker'        => 'Transparent pricing',
				'est_heading'       => 'Estimate a programme',
				'est_highlight'     => 'in ten seconds',
				'est_lead'          => 'Same rate whether you buy one placement or fifty. Resell at whatever margin you set.',
				'est_bands'         => tbt_seed_rate_card(),
				'est_points'        => "White-label reporting included at every volume\nPause or cancel with 30 days' notice",
				'est_note'          => 'Estimate from our standing rate card. DR 85+ and national media are quoted per story. Your exact quote follows the free gap analysis.',

				'compare_kicker'    => 'The difference',
				'compare_heading'   => 'Why agencies switch to us',
				'compare_them'      => 'Typical link vendor',
				'compare_rows'      => array(
					array( 'feature' => 'Placements on real editorial sites', 'us' => 'yes', 'them' => 'Sometimes' ),
					array( 'feature' => 'No PBNs or link farms', 'us' => 'yes', 'them' => 'no' ),
					array( 'feature' => "Approve every site before it's pitched", 'us' => 'yes', 'them' => 'no' ),
					array( 'feature' => 'Fully white-label dashboard', 'us' => 'yes', 'them' => 'PDF only' ),
					array( 'feature' => '12-month replacement guarantee', 'us' => 'yes', 'them' => '90 days' ),
					array( 'feature' => 'No contract, no minimum', 'us' => 'yes', 'them' => 'no' ),
				),

				'featured_case'     => $collab_id,
				'show_testimonials' => 1,

				'faq_heading'       => 'White-label,',
				'faq_highlight'     => 'answered',
				'faqs'              => array(
					array( 'question' => 'Will my client ever know you exist?', 'answer' => 'No. Reports carry your logo and domain, we never contact your client, and we never appear in the placement trail. Most of our volume is agencies reselling under their own brand.' ),
					array( 'question' => 'Can I set my own prices?', 'answer' => 'Yes. You pay our standing rate per placement and charge your client whatever you like. We never publish a client-facing price anywhere they could find it.' ),
					array( 'question' => 'Is there a minimum order or contract?', 'answer' => "Neither. You can run a single placement before committing to a programme, and monthly programmes can be paused or cancelled with 30 days' notice." ),
					array( 'question' => 'How fast will my client see links?', 'answer' => 'The gap analysis lands within 72 hours of kickoff. Placements begin going live from week three, and a typical order completes inside 30 days.' ),
					array( 'question' => 'What happens if a link is removed?', 'answer' => 'Every eligible placement carries a 12-month replacement guarantee. If one drops, is nofollowed or the page is pulled, we replace it on a comparable domain at no charge.' ),
				),

				'cta_heading'       => 'Add a link department to your agency this week.',
				'cta_text'          => "Book a call and get a free white-labelled gap analysis for one of your clients. If it's useful, run a single placement. If not, keep the report.",
			),
		),
		$simple(
			'Multi-lingual link building',
			'multi-lingual-link-building',
			'Multi-lingual',
			'Native-language outreach across 28 markets, run by in-country editors rather than translation tools. Anchor strategy, local relevance and tone are handled per market, so the link reads as though it was always meant to be there.',
			array( 'In-country editors, never machine translation', 'Market-specific anchor and topic strategy', 'Local publications with genuine domestic traffic', 'Reporting split by market and language' ),
			array( array( 'MARKETS LIVE', '28' ), array( 'LANGUAGES', '19' ), array( 'NATIVE EDITORS', '40+' ), array( 'AVG. DR PLACED', '54' ) ),
			'// coverage expands quarterly, ask for the current list',
			''
		),
		$simple(
			'Local link building (USA)',
			'local-link-building',
			'Local (USA)',
			'City and state-level authority for multi-location brands. Chamber listings, regional press, local resource pages and genuine community partnerships, the citations and links that move the map pack, not just the blue links.',
			array( 'Per-location link and citation strategy', 'Regional press and community partnerships', 'NAP-consistent citations across directories', 'Map-pack tracking alongside organic' ),
			array( array( 'METROS COVERED', '310' ), array( 'AVG. LOCATIONS', '12' ), array( 'MAP-PACK LIFT', '+6' ), array( 'TYPICAL TURNAROUND', '45d' ) ),
			'// map-pack lift measured as average position change',
			''
		),
		$simple(
			'Media placements',
			'media-placements',
			'Media placements',
			'Editorial coverage in publications your buyers already read. Journalist-led pitching against live queries, with placements on titles that carry real newsroom standards and real traffic, never sponsored-content farms.',
			array( 'Journalist-led pitching against live queries', 'Titles with real newsrooms and real readers', 'Expert commentary and data-led story angles', 'Full disclosure of paid vs. earned placements' ),
			array( array( 'AVG. DR PLACED', '81' ), array( 'EARNED SHARE', '72%' ), array( 'AVG. LEAD TIME', '21d' ), array( 'TITLES ON ROSTER', '900+' ) ),
			'// earned share = placements secured without a fee',
			'images/services/media-placements.jpg'
		),
		$simple(
			'AI search optimization',
			'ai-search-optimization',
			'AI search',
			"We track your brand's visibility across ChatGPT, Perplexity, Gemini, Copilot, Grok, Claude, DeepSeek and AI Overviews, then build the signals that get you recommended, so your brand appears inside the answer rather than beneath it.",
			array( 'LLM monitoring across eight answer engines', 'AI search progression and sentiment tracking', 'AEO tuned per platform, not one generic pass', 'Trust-signal engineering: entities, citations, consistency' ),
			array( array( 'ENGINES TRACKED', '8' ), array( 'CITATION SHARE', '34%' ), array( 'PROMPTS MONITORED', '2.4k' ), array( 'REPORTING', 'Live' ) ),
			'// citation share is the best-performing client to date',
			'images/services/ai-search.jpg'
		),
	);
}

/** The standing rate card (also the estimator default). */
function tbt_seed_rate_card() {
	return array(
		array( 'label' => 'DR 30–45', 'rate' => 310, 'lead_time' => '14–21 days' ),
		array( 'label' => 'DR 45–60', 'rate' => 480, 'lead_time' => '21–30 days' ),
		array( 'label' => 'DR 60–75', 'rate' => 790, 'lead_time' => '30–45 days' ),
		array( 'label' => 'DR 75–85', 'rate' => 1450, 'lead_time' => '45–60 days' ),
	);
}

function tbt_seed_case_studies() {
	$tile = function ( $title, $slug, $client, $industry, $before, $after, $result, $duration, $image, $alt ) {
		return array(
			'title'   => $title,
			'slug'    => $slug,
			'excerpt' => '',
			'image'   => 'images/work/' . $image,
			'alt'     => $alt,
			'fields'  => array(
				'client'      => $client,
				'industry'    => $industry,
				'dr_before'   => $before,
				'dr_after'    => $after,
				'result'      => $result,
				'duration'    => $duration,
				'cta_heading' => 'Want a result like this for your site?',
				'cta_text'    => 'Tell us the domain. Within 72 hours you get the competitor link gap and the specific placements that would close it.',
			),
		);
	};

	$collab = array(
		'title'   => 'How Collab Management grew enquiries by 156%',
		'slug'    => 'collab-management',
		'excerpt' => 'A construction management firm with strong referrals and almost no search presence. We rebuilt its authority around the pages that sell, and organic became its largest source of new project enquiries.',
		'image'   => 'images/work/collab-management.webp',
		'fields'  => array(
			'client'            => 'Collab Management Group',
			'industry'          => 'Construction',
			'service_name'      => 'White-label link building',
			'market'            => 'United States',
			'dr_before'         => 19,
			'dr_after'          => 52,
			'duration'          => '10 months',
			'result'            => '+156% enquiries',
			'card_stats'        => array(
				array( 'label' => 'DOMAIN RATING', 'value' => '19 → 52' ),
				array( 'label' => 'ENQUIRIES', 'value' => '+156%' ),
				array( 'label' => 'TIMEFRAME', 'value' => '10 mo' ),
			),

			'hero_heading'      => 'How Collab Management grew enquiries',
			'hero_highlight'    => 'by 156%',
			'hero_chips'        => array(
				array( 'title' => 'DR 19 → 52', 'text' => 'domain rating' ),
				array( 'title' => '+156% enquiries', 'text' => 'vs. pre-programme month' ),
				array( 'title' => '74 editorial links', 'text' => 'zero PBNs' ),
				array( 'title' => '100% still live', 'text' => 'at programme end' ),
			),

			'kpis'              => array(
				array( 'value' => 52, 'from' => 19, 'prefix' => '', 'suffix' => '', 'label' => 'Domain rating', 'note' => '▲ FROM 19' ),
				array( 'value' => 156, 'from' => '', 'prefix' => '+', 'suffix' => '%', 'label' => 'Organic enquiries', 'note' => '▲ VS. BASELINE' ),
				array( 'value' => 74, 'from' => '', 'prefix' => '', 'suffix' => '', 'label' => 'Editorial links placed', 'note' => 'AVG. DR 49' ),
				array( 'value' => 212, 'from' => '', 'prefix' => '+', 'suffix' => '%', 'label' => 'Organic traffic', 'note' => '▲ 10 MONTHS' ),
			),

			'challenge_heading' => 'Great work, invisible online',
			'challenge_text'    => "<p>Collab Management won most of its projects through referrals. That kept the order book healthy but capped growth: developers searching for a construction manager in its markets found competitors first, and the firm's service pages sat on page three or lower.</p>\n<p>A previous vendor had built links in volume, but mostly on low-traffic directories that passed little authority. The site had <strong>a domain rating of 19</strong> and almost no editorial links from publications the industry actually reads.</p>",
			'challenge_brief'   => 'Turn organic search into a dependable source of qualified project enquiries, without risking the domain on shortcuts.',

			'strategy_heading'  => 'Authority where buyers look, pointed at pages that sell',
			'strategy_text'     => '<p>We mapped the link graph of the five firms ranking above them and found the gap was not volume but relevance: competitors held links from trade publications, regional business press and industry associations.</p>',
			'strategy_cards'    => array(
				array( 'icon' => 'search', 'title' => 'Close the gap', 'text' => 'Target the trade and regional titles competitors already held links from.' ),
				array( 'icon' => 'target', 'title' => 'Money pages first', 'text' => 'Point links at service and location pages, not just the homepage.' ),
				array( 'icon' => 'shield', 'title' => 'Clean the profile', 'text' => 'Audit and disavow the worst directory links from the previous vendor.' ),
			),

			'execution_heading' => 'Ten months, month by month',
			'execution_text'    => "Every placement was approved before it was pitched and appeared on the client's dashboard the day it went live.",
			'timeline'          => array(
				array( 'when' => 'MONTH 1', 'title' => 'Gap map and profile clean-up', 'text' => 'Competitor link-gap analysis, anchor audit and disavow of the lowest-quality directory links.' ),
				array( 'when' => 'MONTHS 2–4', 'title' => 'Trade publication outreach', 'text' => 'First editorial placements on construction and property trade titles, pointed at core service pages.' ),
				array( 'when' => 'MONTHS 5–7', 'title' => 'Regional press and associations', 'text' => 'Local business press features and association resource links to lift each location page.' ),
				array( 'when' => 'MONTHS 8–10', 'title' => 'Compounding and consolidation', 'text' => 'Higher-DR placements as authority grew; service pages moved onto page one for their core terms.' ),
			),

			'results_heading'   => 'Authority first, then the enquiries followed',
			'results_text'      => 'Switch between metrics, and hover the chart to read any month.',
			'chart_labels'      => 'Month 0,Month 1,Month 2,Month 3,Month 4,Month 5,Month 6,Month 7,Month 8,Month 9,Month 10',
			'chart_series'      => array(
				array( 'name' => 'Domain rating', 'values' => '19,21,24,27,31,35,39,43,46,49,52', 'max' => 60, 'prefix' => '', 'suffix' => '', 'aria' => 'Domain rating rising from 19 to 52 over ten months.' ),
				array( 'name' => 'Enquiries / mo', 'values' => '9,10,10,12,13,15,16,18,20,22,23', 'max' => 25, 'prefix' => '', 'suffix' => '', 'aria' => 'Organic enquiries per month rising from 9 to 23 over ten months.' ),
				array( 'name' => 'Organic visits', 'values' => '1400,1480,1620,1900,2150,2600,2950,3300,3700,4050,4370', 'max' => 4800, 'prefix' => '', 'suffix' => '', 'aria' => 'Monthly organic visits rising from 1,400 to 4,370 over ten months.' ),
			),
			'bars'              => array(
				array( 'label' => 'Page-one keywords', 'before' => 12, 'after' => 59, 'change' => '+392%' ),
				array( 'label' => 'Referring domains', 'before' => 41, 'after' => 133, 'change' => '+224%' ),
				array( 'label' => 'Enquiries per month', 'before' => 9, 'after' => 23, 'change' => '+156%' ),
			),

			'placements_heading' => 'A few of the links behind it',
			'placements_text'   => 'Publication names are withheld here; on a call we open the live dashboard so you can check every URL.',
			'placements'        => array(
				array( 'type' => 'National construction trade title', 'dr' => '71', 'traffic' => '180k', 'target' => 'Services', 'status' => 'live' ),
				array( 'type' => 'Regional business journal', 'dr' => '64', 'traffic' => '95k', 'target' => 'Location page', 'status' => 'live' ),
				array( 'type' => 'Property development magazine', 'dr' => '58', 'traffic' => '42k', 'target' => 'Pre-construction', 'status' => 'live' ),
				array( 'type' => 'Industry association resources', 'dr' => '55', 'traffic' => '18k', 'target' => 'Homepage', 'status' => 'editorial' ),
				array( 'type' => 'Architecture & design blog', 'dr' => '49', 'traffic' => '26k', 'target' => 'Case studies', 'status' => 'live' ),
			),

			'quote'             => 'Impressive team, fast execution, and growth-driven strategies delivering exceptional content consistently.',
			'quote_name'        => 'Randy Cheng',
			'quote_role'        => 'Founder & CEO, Collab Management Group',

			'cta_heading'       => 'Want a result like this for your site?',
			'cta_text'          => 'Tell us the domain. Within 72 hours you get the competitor link gap and the specific placements that would close it.',
		),
	);

	return array(
		$tile( 'Nimble CRM: +340% organic in 18 months', 'nimble-crm', 'Nimble CRM', 'SaaS / CRM', 38, 71, '+340% organic', '18 months', 'nimble-crm.webp', 'Nimble CRM dashboard views' ),
		$tile( 'Doctors Weight Loss Center: +212% enquiries', 'doctors-weight-loss-center', 'Doctors Weight Loss Center', 'Healthcare', 24, 58, '+212% enquiries', '12 months', 'dwlc.webp', 'Doctors Weight Loss Center campaign creative' ),
		$tile( 'NRI Remittance: +180% signups', 'nri-remittance', 'NRI Remittance', 'Fintech', 31, 64, '+180% signups', '14 months', 'nri-remittance.webp', 'NRI Remittance money transfer app screen' ),
		$collab,
		$tile( 'MN Brow Lash Academy: +290% bookings', 'mn-brow-lash-academy', 'MN Brow Lash Academy', 'Beauty & training', 16, 49, '+290% bookings', '9 months', 'mn-brow-lash.webp', 'MN Brow Lash Academy training cohort' ),
		$tile( 'AG-Com LLC: +124% RFQs', 'ag-com', 'AG-Com LLC', 'Agriculture', 27, 61, '+124% RFQs', '11 months', 'ag-com.webp', 'AG-Com agricultural equipment' ),
	);
}
