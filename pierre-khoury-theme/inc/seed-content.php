<?php
/**
 * One-time content seeding: creates the Pages, blog posts and menus that
 * reproduce the approved Claude Design prototype, using real WordPress
 * content (editable afterwards in the block editor) instead of hard-coded
 * markup. Every insert is guarded so re-running (e.g. reactivating the
 * theme) never overwrites content someone has already edited.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pk_html_block( $raw ) {
	return "<!-- wp:html -->\n" . $raw . "\n<!-- /wp:html -->\n\n";
}

/* ---------------------------------------------------------------------
 * Section builders — each returns a block-markup string ready to be
 * concatenated into a page's post_content.
 * ------------------------------------------------------------------- */

function pk_section_page_header( $crumb, $title, $subtitle = '' ) {
	$html  = '<section class="pk-page-header">';
	$html .= '<span class="pk-page-header__crumb">' . esc_html( $crumb ) . '</span>';
	$html .= '<h1>' . esc_html( $title ) . '</h1>';
	if ( $subtitle ) {
		$html .= '<p>' . esc_html( $subtitle ) . '</p>';
	}
	$html .= '</section>';
	return pk_html_block( $html );
}

function pk_section_hero_home() {
	$slides = pk_slides_data();
	$html   = '<section class="pk-hero"><div class="pk-hero__inner"><div class="pk-hero__slides">';
	foreach ( $slides as $i => $s ) {
		$active = ( 0 === $i ) ? ' is-active' : '';
		$target = 0 === $i ? 'services' : ( 1 === $i ? 'service:gen-z-workplace-expertise' : 'service:training-center-launch-advisory' );
		$html  .= '<div class="pk-hero__slide' . $active . '">';
		$html  .= '<span class="pk-hero__eyebrow">' . esc_html( $s['eyebrow'] ) . '</span>';
		$html  .= '<h1>' . esc_html( $s['head'] ) . '</h1>';
		$html  .= '<p>' . esc_html( $s['sub'] ) . '</p>';
		$html  .= '<div class="pk-hero__actions">';
		$html  .= do_shortcode( '[pk_cta target="' . esc_attr( $target ) . '" label="' . esc_attr( $s['cta'] ) . '" style="solid"]' );
		$html  .= do_shortcode( '[pk_cta target="contact" label="Book a Consultation" style="outline"]' );
		$html  .= '</div></div>';
	}
	$html .= '</div>';
	$html .= '<div class="pk-hero__dots">';
	foreach ( $slides as $i => $s ) {
		$html .= '<button type="button" class="pk-hero__dot' . ( 0 === $i ? ' is-active' : '' ) . '" data-slide="' . (int) $i . '" aria-label="Go to slide ' . (int) ( $i + 1 ) . '"></button>';
	}
	$html .= '</div></div></section>';
	return pk_html_block( $html );
}

function pk_section_stats() {
	$html = '<section class="pk-stats">';
	foreach ( pk_counters_data() as $c ) {
		$html .= '<div class="pk-stat"><span class="pk-stat__num">' . esc_html( $c['value'] ) . '</span><span class="pk-stat__label">' . esc_html( $c['label'] ) . '</span></div>';
	}
	$html .= '</section>';
	return pk_html_block( $html );
}

function pk_section_positioning() {
	$html  = '<section class="pk-section pk-split">';
	$html .= '<div class="pk-media-placeholder"><span class="pk-media-placeholder__label">Photo / video, Pierre training</span></div>';
	$html .= '<div>';
	$html .= '<div class="pk-eyebrow-row"><span class="pk-rule"></span><p class="pk-eyebrow">Background</p></div>';
	$html .= '<h2>Five disciplines, because institutions rarely face one problem at a time.</h2>';
	$html .= '<p>Gen Z friction, financial blind spots, career planning, blockchain and training capacity tend to arrive together. Pierre Khoury works across all five because he has spent three decades moving between them: researching at central banks, consulting for businesses and ministries, teaching finance and blockchain at graduate level, and building training programmes for institutions across the region. The breadth comes from the work, not from a single title.</p>';
	$html .= do_shortcode( '[pk_cta target="about" label="Read the Full Background" style="navy"]' );
	$html .= '</div></section>';
	return pk_html_block( $html );
}

function pk_section_pillar_grid( $heading = 'Five areas of practice, one point of contact.', $eyebrow = 'Our solutions', $show_cta = true, $bg_alt = false ) {
	$html  = '<section class="pk-section' . ( $bg_alt ? ' pk-section--alt' : '' ) . '">';
	$html .= '<div class="pk-section-head"><div><div class="pk-eyebrow-row"><span class="pk-rule"></span><p class="pk-eyebrow">' . esc_html( $eyebrow ) . '</p></div><h2>' . esc_html( $heading ) . '</h2></div>';
	if ( $show_cta ) {
		$html .= do_shortcode( '[pk_cta target="services" label="See All Services" style="outline-navy"]' );
	}
	$html .= '</div>';
	$html .= '<div class="pk-pillar-grid">';
	foreach ( pk_pillars_data() as $p ) {
		$html .= '<a class="pk-pillar-card" href="' . esc_url( pk_resolve_url( 'service:' . $p['slug'] ) ) . '">';
		$html .= '<span class="pk-pillar-card__num">' . esc_html( $p['num'] ) . '</span>';
		$html .= '<span class="pk-pillar-card__title">' . esc_html( $p['title'] ) . '</span>';
		$html .= '<span class="pk-pillar-card__sell">' . esc_html( $p['sell'] ) . '</span>';
		$html .= '<ul class="pk-pillar-card__outcomes">';
		foreach ( $p['outcomes'] as $o ) {
			$html .= '<li>' . esc_html( $o ) . '</li>';
		}
		$html .= '</ul>';
		$html .= '<span class="pk-pillar-card__cta">' . esc_html( $p['shortCta'] ) . ' →</span>';
		$html .= '</a>';
	}
	$html .= '</div></section>';
	return pk_html_block( $html );
}

function pk_section_process() {
	$html  = '<section class="pk-section pk-section--alt">';
	$html .= '<div class="pk-eyebrow-row"><span class="pk-rule"></span><p class="pk-eyebrow">How we work</p></div>';
	$html .= '<h2>No guesswork. Just a clear path to results.</h2>';
	$html .= '<div class="pk-process">';
	foreach ( pk_process_data() as $s ) {
		$html .= '<div class="pk-process__step"><span class="pk-process__num">' . esc_html( $s['num'] ) . '</span><span class="pk-process__title">' . esc_html( $s['title'] ) . '</span><span class="pk-process__body">' . esc_html( $s['body'] ) . '</span></div>';
	}
	$html .= '</div>';
	$html .= do_shortcode( '[pk_cta target="contact" label="Request a Call" style="solid"]' );
	$html .= '</section>';
	return pk_html_block( $html );
}

function pk_section_credentials( $with_eyebrow = true, $bg_alt = false ) {
	$html = '<section class="pk-section' . ( $bg_alt ? ' pk-section--alt' : '' ) . ' pk-split pk-split--top">';
	if ( $with_eyebrow ) {
		$html .= '<div><div class="pk-eyebrow-row"><span class="pk-rule"></span><p class="pk-eyebrow">Behind the work</p></div><h2>Background and current roles</h2></div>';
	} else {
		$html .= '<h2>Background and current roles</h2>';
	}
	$html .= '<div class="pk-credentials">';
	foreach ( pk_credentials_data() as $c ) {
		$html .= '<div class="pk-credentials__row"><span class="pk-credentials__num">' . esc_html( $c['num'] ) . '</span><span class="pk-credentials__text">' . esc_html( $c['text'] ) . '</span></div>';
	}
	$html .= '<div class="pk-credentials__end"></div></div></section>';
	return pk_html_block( $html );
}

function pk_section_packages() {
	$html  = '<section class="pk-section">';
	$html .= '<div class="pk-eyebrow-row"><span class="pk-rule"></span><p class="pk-eyebrow">Ways to work together</p></div>';
	$html .= '<h2>Pick the level of engagement that fits</h2>';
	$html .= '<div class="pk-packages">';
	foreach ( pk_packages_data() as $k ) {
		$dark  = 'dark' === $k['style'];
		$html .= '<div class="pk-package' . ( $dark ? ' pk-package--dark' : '' ) . '">';
		$html .= '<span class="pk-package__tier">' . esc_html( $k['tier'] ) . '</span>';
		$html .= '<span class="pk-package__name">' . esc_html( $k['name'] ) . '</span>';
		$html .= '<span class="pk-package__body">' . esc_html( $k['body'] ) . '</span>';
		$html .= '<ul class="pk-package__items">';
		foreach ( $k['items'] as $i ) {
			$html .= '<li>' . esc_html( $i ) . '</li>';
		}
		$html .= '</ul>';
		$html .= do_shortcode( '[pk_cta target="contact" label="' . esc_attr( $k['cta'] ) . '" style="' . ( $dark ? 'white' : 'navy' ) . '"]' );
		$html .= '</div>';
	}
	$html .= '</div>';
	$html .= '<p class="pk-package-note">Pricing left unlisted — get in touch and we will follow up with a tailored proposal.</p>';
	$html .= '</section>';
	return pk_html_block( $html );
}

function pk_section_blog_teaser() {
	$html  = '<section class="pk-section pk-section--alt">';
	$html .= '<div class="pk-section-head"><div><div class="pk-eyebrow-row"><span class="pk-rule"></span><p class="pk-eyebrow">From the blog</p></div><h2>Strategy and perspective from the front lines of regional change</h2></div>';
	$html .= do_shortcode( '[pk_cta target="blog" label="All Posts" style="outline-navy"]' );
	$html .= '</div>';
	$html .= do_shortcode( '[pk_blog_teaser count="3"]' );
	$html .= '</section>';
	return pk_html_block( $html );
}

function pk_section_cta_band_full() {
	$html  = '<section class="pk-cta-band">';
	$html .= '<h2>Tell us what your institution needs, we will come back with a plan.</h2>';
	$html .= "<p>Whether it's one workshop or a multi-year training center build, the conversation starts with a message.</p>";
	$html .= '<div class="pk-cta-band__actions">';
	$html .= do_shortcode( '[pk_cta target="contact" label="Book a Consultation" style="white"]' );
	$html .= do_shortcode( '[pk_cta target="whatsapp" label="Message on WhatsApp" style="outline-white"]' );
	$html .= '</div></section>';
	return pk_html_block( $html );
}

function pk_section_cta_band_split( $heading, $cta_label ) {
	$html  = '<section class="pk-cta-band--split">';
	$html .= '<h2>' . esc_html( $heading ) . '</h2>';
	$html .= do_shortcode( '[pk_cta target="contact" label="' . esc_attr( $cta_label ) . '" style="white"]' );
	$html .= '</section>';
	return pk_html_block( $html );
}

function pk_section_two_up( $for_whom, $included ) {
	$html  = '<div class="pk-two-up">';
	$html .= '<div class="pk-two-up__col"><span class="pk-two-up__label">Who it\'s for</span><ul>';
	foreach ( $for_whom as $f ) {
		$html .= '<li>' . esc_html( $f ) . '</li>';
	}
	$html .= '</ul></div>';
	$html .= '<div class="pk-two-up__col pk-two-up__col--alt"><span class="pk-two-up__label">What\'s included</span><ul>';
	foreach ( $included as $f ) {
		$html .= '<li>' . esc_html( $f ) . '</li>';
	}
	$html .= '</ul></div></div>';
	return pk_html_block( $html );
}

function pk_section_service_body( $body ) {
	return pk_html_block( '<section class="pk-section"><p style="font-size:clamp(16.5px,1.6vw,20px);line-height:1.65;color:var(--pk-ink-5);max-width:62ch;">' . esc_html( $body ) . '</p></section>' );
}

function pk_section_track_groups() {
	$html = '';
	foreach ( pk_track_groups_data() as $g ) {
		$row   = '<section class="pk-track-group"><span class="pk-track-group__label">' . esc_html( $g['label'] ) . '</span><ul class="pk-track-group__items">';
		foreach ( $g['items'] as $i ) {
			$row .= '<li>' . esc_html( $i ) . '</li>';
		}
		$row  .= '</ul></section>';
		$html .= pk_html_block( $row );
	}
	return $html;
}

function pk_section_contact_grid() {
	$html  = '<section class="pk-section pk-contact-grid">';
	$html .= '<div>' . do_shortcode( '[pk_contact_form]' ) . '</div>';
	$html .= '<div>';
	$html .= '<div class="pk-side-card"><span class="pk-side-card__label">Prefer WhatsApp?</span><span class="pk-side-card__title">Message us directly and we\'ll pick it up from there.</span>';
	$html .= do_shortcode( '[pk_cta target="whatsapp" label="Open WhatsApp →" style="outline-navy"]' );
	$html .= '</div>';
	$html .= '<div class="pk-side-card pk-side-card--alt"><span class="pk-side-card__label">Based in</span><span class="pk-side-card__title">Beirut, Lebanon, working across the Levant, Gulf and North Africa.</span></div>';
	$html .= '</div></section>';
	return pk_html_block( $html );
}

function pk_section_about_bio() {
	$paras = array(
		'lead' => 'Pierre Khoury currently serves as Assistant Vice President of External Relations at the American University of Technology (AUT) and previously served as Dean of the Faculty of Business Administration at the same institution.',
		'He holds a PhD in Business Administration from the Arab Academy for Banking & Financial Studies, accredited by the Lebanese Ministry of Education & Higher Education in 2015, an MS in Money & Banking from the American University of Beirut, and a BA in Economics from the Lebanese University. Beyond AUT, he holds graduate and doctoral-level teaching appointments at Beirut Arab University (Finance & Economics), Royal Roads University in Canada, and American Imperial University in Florida, and is an adjunct Research and Blockchain for Business Professor at Dayananda Sagar University (DSU) in Bangalore, India. He also serves as a dissertation reader at Heilbronn University in Germany and was Region Eight Secretary for the ACBSP business school accreditation agency in 2022.',
		'His career began in 1986 as a researcher at the Central Bank of Lebanon and the Central Bank of Kuwait, followed by a term as cost and management consultant to Lebanon\'s Ministry of Electricity & Water Resources. Since 1999 he has run an independent consulting practice serving Lebanese and regional businesses in finance and strategy, alongside academic leadership roles including Chairperson of Management Studies at Rafik Hariri University (2006–2013) and Vice President for Development & Dean of the Business School at Lebanese German University (2014–2020).',
		'Alongside his university role, he has built an independent consulting and training practice spanning five specializations: Gen Z workplace strategy, financial literacy training, career and lifelong-learning advisory, blockchain education, and training center feasibility and launch support.',
		'He has trained extensively with regional institutions, including OAPEC (the Organization of Arab Petroleum Exporting Countries), the Kuwait Petroleum Training Center (KOC/KPC), where he has trained across different business disciplines, and the Union of Arab Chambers, and has supported the launch of training centers across the Arab world. His blockchain work includes specialized training at INSEAD in France, a Blockchain for Humanitarian Aid program delivered to the Nigerian Ministry of Foreign Affairs, and a Blockchain for Business Bootcamp that produced 93 completed entrepreneur projects. He is the author of 4 books and more than 40 peer-reviewed research papers spanning education management, economics, finance, and blockchain, and is active in regional media as Publisher of Strategic File, Opinion Editor at Aswak Al Arab, and host of the podcast Hakika Bi Kam Dakika, with blockchain commentary featured on MTV, Al Arabi TV, and OSN.',
	);

	$html  = '<section class="pk-section pk-split pk-split--top">';
	$html .= '<div class="pk-media-placeholder pk-media-placeholder--portrait"><span class="pk-media-placeholder__label">Photo, portrait</span></div>';
	$html .= '<div>';
	$html .= '<p style="font-size:clamp(17px,1.65vw,21px);font-weight:500;line-height:1.5;letter-spacing:-0.02em;color:var(--pk-ink-2);">' . esc_html( $paras['lead'] ) . '</p>';
	unset( $paras['lead'] );
	foreach ( $paras as $p ) {
		$html .= '<p>' . esc_html( $p ) . '</p>';
	}
	$html .= do_shortcode( '[pk_cta target="contact" label="Get My Proposal" style="solid"]' );
	$html .= '</div></section>';
	return pk_html_block( $html );
}

/* ---------------------------------------------------------------------
 * Seeding orchestration
 * ------------------------------------------------------------------- */

/**
 * Insert a page if (and only if) we haven't already created one for this
 * key, and the previously-created page still exists.
 */
function pk_seed_get_or_create_page( $option_key, $title, $slug, $content, $extra_args = array() ) {
	$existing_id = get_option( $option_key );
	if ( $existing_id && get_post( $existing_id ) ) {
		return (int) $existing_id;
	}

	$id = wp_insert_post(
		array_merge(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_type'    => 'page',
			),
			$extra_args
		)
	);

	if ( $id && ! is_wp_error( $id ) ) {
		update_option( $option_key, $id );
		return (int) $id;
	}

	return 0;
}

function pk_seed_menus( $ids ) {
	if ( get_option( 'pk_menus_seeded' ) ) {
		return;
	}

	// Primary navigation: About / Services (+5 sub-items) / Track Record / Blog / Contact.
	$primary_id = wp_create_nav_menu( 'Primary Navigation' );
	if ( ! is_wp_error( $primary_id ) ) {
		wp_update_nav_menu_item( $primary_id, 0, array( 'menu-item-title' => 'About', 'menu-item-object' => 'page', 'menu-item-object-id' => $ids['about'], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		$services_item = wp_update_nav_menu_item( $primary_id, 0, array( 'menu-item-title' => 'Services', 'menu-item-object' => 'page', 'menu-item-object-id' => $ids['services'], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		foreach ( pk_pillars_data() as $p ) {
			wp_update_nav_menu_item(
				$primary_id,
				0,
				array(
					'menu-item-title'     => $p['short'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $ids['service_' . $p['id']],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => $services_item,
				)
			);
		}
		wp_update_nav_menu_item( $primary_id, 0, array( 'menu-item-title' => 'Track Record', 'menu-item-object' => 'page', 'menu-item-object-id' => $ids['track'], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		wp_update_nav_menu_item( $primary_id, 0, array( 'menu-item-title' => 'Blog', 'menu-item-object' => 'page', 'menu-item-object-id' => $ids['blog'], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		wp_update_nav_menu_item( $primary_id, 0, array( 'menu-item-title' => 'Contact', 'menu-item-object' => 'page', 'menu-item-object-id' => $ids['contact'], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );

		$locations             = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary']  = $primary_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// Footer — Services.
	$footer_services_id = wp_create_nav_menu( 'Footer — Services' );
	if ( ! is_wp_error( $footer_services_id ) ) {
		foreach ( pk_pillars_data() as $p ) {
			wp_update_nav_menu_item( $footer_services_id, 0, array( 'menu-item-title' => $p['short'], 'menu-item-object' => 'page', 'menu-item-object-id' => $ids[ 'service_' . $p['id'] ], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		}
		$locations                     = get_theme_mod( 'nav_menu_locations', array() );
		$locations['footer-services']  = $footer_services_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// Footer — Resources.
	$footer_resources_id = wp_create_nav_menu( 'Footer — Resources' );
	if ( ! is_wp_error( $footer_resources_id ) ) {
		wp_update_nav_menu_item( $footer_resources_id, 0, array( 'menu-item-title' => 'About', 'menu-item-object' => 'page', 'menu-item-object-id' => $ids['about'], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		wp_update_nav_menu_item( $footer_resources_id, 0, array( 'menu-item-title' => 'Track Record', 'menu-item-object' => 'page', 'menu-item-object-id' => $ids['track'], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		wp_update_nav_menu_item( $footer_resources_id, 0, array( 'menu-item-title' => 'Blog', 'menu-item-object' => 'page', 'menu-item-object-id' => $ids['blog'], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		wp_update_nav_menu_item( $footer_resources_id, 0, array( 'menu-item-title' => 'Contact', 'menu-item-object' => 'page', 'menu-item-object-id' => $ids['contact'], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		$locations                      = get_theme_mod( 'nav_menu_locations', array() );
		$locations['footer-resources']  = $footer_resources_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	update_option( 'pk_menus_seeded', 1 );
}

function pk_run_content_seed() {
	if ( get_option( 'pk_content_seeded_v1' ) ) {
		return;
	}

	// Seeded content is trusted (authored in this theme's PHP, not user
	// input) and relies on markup kses would otherwise strip for users
	// without unfiltered_html (e.g. multisite admins) — switch it off for
	// the duration of the seed, then restore it exactly as core's own
	// importers do.
	kses_remove_filters();

	$ids = array();

	// 1. Five service pages (self-contained content).
	foreach ( pk_pillars_data() as $p ) {
		$content  = pk_section_page_header( 'Home / Services / ' . $p['label'], $p['hero'], $p['sub'] );
		$content .= pk_section_service_body( $p['body'] );
		$content .= pk_section_two_up( $p['forWhom'], $p['included'] );
		$content .= pk_section_cta_band_split( $p['ctaHead'], $p['cta'] );

		$ids[ 'service_' . $p['id'] ] = pk_seed_get_or_create_page(
			'pk_page_service_' . $p['slug'] . '_id',
			$p['title'],
			$p['slug'],
			$content,
			array( 'menu_order' => (int) $p['num'] )
		);
	}

	// 2. About.
	$about_content  = pk_section_page_header( 'Home / About', 'Three decades bridging academia, business, and what comes next' );
	$about_content .= pk_section_about_bio();
	$about_content .= pk_section_credentials( false, true );
	$ids['about']   = pk_seed_get_or_create_page( 'pk_page_about_id', 'About', 'about', $about_content );

	// 3. Track Record.
	$track_content  = pk_section_page_header( 'Home / Track Record', 'Trusted across academia, energy, and enterprise' );
	$track_content .= pk_section_track_groups();
	$ids['track']   = pk_seed_get_or_create_page( 'pk_page_track_id', 'Track Record', 'track-record', $track_content );

	// 4. Contact.
	$contact_content  = pk_section_page_header( 'Home / Contact', "Let's build something your institution actually needs", "Tell us about your organization and what you're looking for, Gen Z strategy, financial training, career advisory, blockchain education, or training center support, and we'll follow up with a tailored proposal." );
	$contact_content .= pk_section_contact_grid();
	$ids['contact']   = pk_seed_get_or_create_page( 'pk_page_contact_id', 'Contact', 'contact', $contact_content );

	// 5. Services landing (used as the "Services" nav parent + "See All Services" target).
	$services_content = pk_section_page_header( 'Home / Services', 'Five areas of practice, one point of contact.', 'One advisor, five disciplines: Gen Z strategy, financial literacy, career advisory, blockchain training, and training center launch support.' );
	$services_content .= pk_section_pillar_grid( 'Choose the area that fits your institution', 'Our solutions', false );
	$services_content .= pk_section_cta_band_split( 'Not sure which pillar fits? Tell us what you need.', 'Talk to Us' );
	$ids['services']   = pk_seed_get_or_create_page( 'pk_page_services_id', 'Services', 'services', $services_content );

	// 6. Blog (posts page — content unused, home.html template renders the query loop).
	$ids['blog'] = pk_seed_get_or_create_page( 'pk_page_blog_id', 'Blog', 'blog', '' );

	// 7. Home / front page.
	$home_content  = pk_section_hero_home();
	$home_content .= pk_section_stats();
	$home_content .= pk_section_positioning();
	$home_content .= pk_section_pillar_grid();
	$home_content .= pk_section_process();
	$home_content .= pk_section_credentials( true );
	$home_content .= pk_section_packages();
	$home_content .= pk_section_blog_teaser();
	$home_content .= pk_section_cta_band_full();
	$ids['home']   = pk_seed_get_or_create_page( 'pk_page_home_id', 'Home', 'home', $home_content );

	// Reading settings: static front page + dedicated posts page.
	if ( $ids['home'] && $ids['blog'] ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		update_option( 'page_for_posts', $ids['blog'] );
	}

	pk_seed_menus( $ids );

	kses_init_filters();

	update_option( 'pk_content_seeded_v1', 1 );
}
add_action( 'after_switch_theme', 'pk_run_content_seed' );

/**
 * Also offer a manual re-run via wp-admin, in case the theme was active
 * before this seeder shipped, or seeding partially failed.
 */
function pk_maybe_run_seed_from_admin() {
	if ( is_admin() && current_user_can( 'manage_options' ) && isset( $_GET['pk_reseed'] ) && check_admin_referer( 'pk_reseed' ) ) {
		delete_option( 'pk_content_seeded_v1' );
		pk_run_content_seed();
		wp_safe_redirect( remove_query_arg( array( 'pk_reseed', '_wpnonce' ) ) );
		exit;
	}
}
add_action( 'admin_init', 'pk_maybe_run_seed_from_admin' );
