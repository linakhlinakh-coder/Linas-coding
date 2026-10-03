<?php
/**
 * Small helpers shared by templates and widgets.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme option (Appearance → Maison Digitale).
 *
 * @param string $key     Key.
 * @param mixed  $default Default.
 * @return mixed
 */
function md_opt( $key, $default = '' ) {
	$opts = get_option( 'md_options', array() );
	return ( is_array( $opts ) && isset( $opts[ $key ] ) && '' !== $opts[ $key ] ) ? $opts[ $key ] : $default;
}

/**
 * The post whose page settings apply to the current request.
 *
 * @return int
 */
function md_context_id() {
	if ( isset( $GLOBALS['md_context_id'] ) ) {
		return (int) $GLOBALS['md_context_id'];
	}
	if ( is_singular() || is_front_page() ) {
		return (int) get_queried_object_id();
	}
	if ( is_home() ) {
		return (int) get_option( 'page_for_posts' );
	}
	return 0;
}

/**
 * Read a Maison page setting saved from Elementor's page settings panel.
 *
 * @param string   $key     Setting key.
 * @param mixed    $default Default.
 * @param int|null $post_id Post.
 * @return mixed
 */
function md_page_setting( $key, $default = '', $post_id = null ) {
	$post_id = null === $post_id ? md_context_id() : (int) $post_id;
	if ( ! $post_id ) {
		return $default;
	}
	$settings = get_post_meta( $post_id, '_elementor_page_settings', true );
	if ( is_array( $settings ) && isset( $settings[ $key ] ) && '' !== $settings[ $key ] ) {
		return $settings[ $key ];
	}
	return $default;
}

/**
 * Navigation mode for the current page: home | light | dark | overlay | minimal.
 *
 * @return string
 */
function md_nav_mode() {
	$mode = md_page_setting( 'md_nav', '' );
	$allowed = array( 'home', 'light', 'dark', 'overlay', 'minimal' );
	return in_array( $mode, $allowed, true ) ? $mode : 'light';
}

/**
 * Is the request the Elementor editor preview?
 *
 * @return bool
 */
function md_is_editor() {
	if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return true;
	}
	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
		return true;
	}
	return false;
}

/**
 * Right-to-left for the current page (site language or per-page switch).
 *
 * @return bool
 */
function md_is_rtl() {
	return is_rtl() || 'rtl' === md_page_setting( 'md_dir', '' );
}

/**
 * Escape text, turn *words* into the gold emphasis and new lines into <br>.
 *
 * @param string $text Text.
 * @return string
 */
function md_hl( $text ) {
	$text = esc_html( (string) $text );
	$text = preg_replace( '/\*(.+?)\*/s', '<span class="md-gold">$1</span>', $text );
	return nl2br( $text, false );
}

/**
 * Escape text and keep line breaks.
 *
 * @param string $text Text.
 * @return string
 */
function md_br( $text ) {
	return nl2br( esc_html( (string) $text ), false );
}

/**
 * Link attributes from an Elementor URL control value (or a plain string).
 *
 * @param array|string $link     Link.
 * @param string       $fallback Fallback URL.
 * @return string
 */
function md_href( $link, $fallback = '#' ) {
	$url = is_array( $link ) ? ( isset( $link['url'] ) ? $link['url'] : '' ) : (string) $link;
	if ( '' === trim( $url ) ) {
		$url = $fallback;
	}
	$out = 'href="' . esc_url( md_resolve_url( $url ) ) . '"';
	if ( is_array( $link ) ) {
		if ( ! empty( $link['is_external'] ) ) {
			$out .= ' target="_blank"';
		}
		$rel = array();
		if ( ! empty( $link['is_external'] ) ) {
			$rel[] = 'noopener';
		}
		if ( ! empty( $link['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}
		if ( $rel ) {
			$out .= ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"';
		}
	}
	return $out;
}

/**
 * URL string from a link control value.
 *
 * @param array|string $link Link.
 * @return string
 */
function md_url( $link ) {
	return md_resolve_url( is_array( $link ) ? ( isset( $link['url'] ) ? (string) $link['url'] : '' ) : (string) $link );
}

/**
 * Media URL from a media control value.
 *
 * @param array|string $media Media.
 * @return string
 */
function md_media_url( $media ) {
	if ( is_array( $media ) ) {
		return isset( $media['url'] ) ? (string) $media['url'] : '';
	}
	return (string) $media;
}

/**
 * Image box: the photo when one is chosen, otherwise the visible placeholder from the design.
 *
 * @param array|string $media       Media control value.
 * @param string       $placeholder Placeholder text, kept visibly marked.
 * @param string       $class       Extra classes on the box.
 * @param string       $style       Inline style on the box.
 * @param string       $tag         Wrapper tag.
 * @param string       $alt         Alt text.
 * @return string
 */
function md_box( $media, $placeholder, $class = '', $style = '', $tag = 'div', $alt = '' ) {
	$url   = md_media_url( $media );
	$cls   = trim( 'ph ' . $class . ( $url ? ' has-media' : '' ) );
	$attrs = 'class="' . esc_attr( $cls ) . '"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' );
	if ( $url ) {
		$id    = is_array( $media ) && ! empty( $media['id'] ) ? (int) $media['id'] : 0;
		$inner = '';
		if ( preg_match( '/\.(mp4|webm|mov)(\?.*)?$/i', $url ) ) {
			$inner = '<video src="' . esc_url( $url ) . '" muted loop playsinline autoplay preload="metadata"></video>';
		} elseif ( $id ) {
			$inner = wp_get_attachment_image( $id, 'large', false, array( 'alt' => $alt, 'loading' => 'lazy' ) );
		}
		if ( ! $inner ) {
			$inner = '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy">';
		}
		return '<' . $tag . ' ' . $attrs . '>' . $inner . '</' . $tag . '>';
	}
	return '<' . $tag . ' ' . $attrs . '>' . esc_html( $placeholder ) . '</' . $tag . '>';
}

/**
 * Theme asset URL.
 *
 * @param string $rel Relative path.
 * @return string
 */
function md_asset( $rel ) {
	return MD_URI . '/assets/' . ltrim( $rel, '/' );
}

/**
 * Logo URL: the chosen image or the bundled brand file.
 *
 * @param array|string $media    Media.
 * @param string       $fallback File in assets/logos.
 * @return string
 */
function md_logo( $media, $fallback ) {
	$url = md_media_url( $media );
	return $url ? $url : md_asset( 'logos/' . $fallback );
}

/**
 * Arrow icons used across the designs.
 *
 * @param string $kind Kind.
 * @return string
 */
function md_svg( $kind ) {
	switch ( $kind ) {
		case 'arrow-s':
			return '<svg width="24" height="12" viewBox="0 0 24 12" aria-hidden="true"><path d="M0 6h22M17 1l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.5"></path></svg>';
		case 'arrow-back':
			return '<svg class="md-back" width="24" height="12" viewBox="0 0 24 12" aria-hidden="true" style="transform: scaleX(-1)"><path d="M0 6h22M17 1l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.5"></path></svg>';
		case 'arrow-row':
			return '<svg class="ar" width="32" height="14" viewBox="0 0 32 14" aria-hidden="true"><path d="M0 7h30M24 1l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.5"></path></svg>';
		case 'arrow-l':
			return '<svg width="72" height="24" viewBox="0 0 72 24" aria-hidden="true"><path d="M0 12h70M60 2l10 10-10 10" fill="none" stroke="currentColor" stroke-width="2"></path></svg>';
		case 'arrow-m':
			return '<svg width="56" height="20" viewBox="0 0 56 20" aria-hidden="true"><path d="M0 10h54M46 2l8 8-8 8" fill="none" stroke="currentColor" stroke-width="1.5"></path></svg>';
		case 'chev':
			return '<svg width="10" height="10" viewBox="0 0 10 10" aria-hidden="true" style="margin-inline-start: 8px"><path d="M1 3l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5"></path></svg>';
		case 'burger':
			return '<svg width="26" height="18" viewBox="0 0 26 18" aria-hidden="true"><path d="M0 1h26M0 9h26M8 17h18" fill="none" stroke="currentColor" stroke-width="2"></path></svg>';
		case 'close':
			return '<svg width="22" height="22" viewBox="0 0 22 22" aria-hidden="true"><path d="M2 2l18 18M20 2L2 20" fill="none" stroke="currentColor" stroke-width="2"></path></svg>';
		case 'close-s':
			return '<svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M1 1l12 12M13 1L1 13" fill="none" stroke="currentColor" stroke-width="1.5"></path></svg>';
		case 'play':
			return '<svg width="16" height="18" viewBox="0 0 16 18" aria-hidden="true"><path d="M1 1l14 8-14 8z" fill="currentColor"></path></svg>';
		case 'warn':
			return '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M8 1l7 13H1z" fill="none" stroke="currentColor" stroke-width="1.5"></path><path d="M8 6v4M8 12v.5" stroke="currentColor" stroke-width="1.5"></path></svg>';
		case 'ok':
			return '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M2 8.5l4 4 8-9" fill="none" stroke="currentColor" stroke-width="1.5"></path></svg>';
		case 'whatsapp':
			return '<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 20l1.3-4A8 8 0 1 1 8 18.7z"></path></svg>';
	}
	return '';
}

/**
 * Square-cornered social icons from the designs.
 *
 * @param string $network Network key.
 * @param int    $size    Pixel size.
 * @return string
 */
function md_social_svg( $network, $size = 20 ) {
	$a = 'width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5"';
	switch ( $network ) {
		case 'instagram':
			return '<svg ' . $a . '><rect x="3" y="3" width="18" height="18"></rect><circle cx="12" cy="12" r="4.2"></circle><path d="M17.2 6.8h.01"></path></svg>';
		case 'linkedin':
			return '<svg ' . $a . '><rect x="3" y="3" width="18" height="18"></rect><path d="M7.5 10.5v6M7.5 7.4v.1M11.5 16.5v-6M11.5 13c0-1.6 1-2.6 2.5-2.6s2.5 1 2.5 2.6v3.5"></path></svg>';
		case 'facebook':
			return '<svg ' . $a . '><rect x="3" y="3" width="18" height="18"></rect><path d="M15.5 7.5h-1.8c-1.2 0-1.7.7-1.7 1.8V21M9.5 12.5h6"></path></svg>';
		case 'tiktok':
			return '<svg ' . $a . '><path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5"></path><path d="M14 3c.4 2.6 2.2 4.3 5 4.5"></path></svg>';
		case 'youtube':
			return '<svg ' . $a . '><rect x="2.5" y="5.5" width="19" height="13"></rect><path d="M10 9.2v5.6l4.8-2.8z"></path></svg>';
		case 'x':
			return '<svg ' . $a . '><path d="M4 4l16 16M20 4L4 20"></path></svg>';
		case 'behance':
			return '<svg ' . $a . '><path d="M3 6h5.2a2.6 2.6 0 0 1 0 5.2H3zM3 11.2h5.8a2.9 2.9 0 0 1 0 5.8H3zM14 13.5h7a3.5 3.5 0 1 0-1 2.5M15 7.5h5"></path></svg>';
		case 'whatsapp':
			return '<svg ' . $a . '><path d="M4 20l1.3-4A8 8 0 1 1 8 18.7z"></path></svg>';
		case 'email':
			return '<svg ' . $a . '><rect x="3" y="5" width="18" height="14"></rect><path d="M3 6l9 7 9-7"></path></svg>';
		case 'link':
			return '<svg ' . $a . '><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"></path></svg>';
	}
	return '';
}

/**
 * Social networks offered in the social repeaters.
 *
 * @return array
 */
function md_social_networks() {
	return array(
		'instagram' => 'Instagram',
		'linkedin'  => 'LinkedIn',
		'facebook'  => 'Facebook',
		'tiktok'    => 'TikTok',
		'youtube'   => 'YouTube',
		'behance'   => 'Behance',
		'x'         => 'X',
		'whatsapp'  => 'WhatsApp',
		'email'     => 'Email',
		'text'      => __( 'Text only (placeholder)', 'maison-digitale' ),
	);
}

/**
 * Split a textarea into clean lines.
 *
 * @param string $text Text.
 * @return array
 */
function md_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * "Label | URL" lines into pairs.
 *
 * @param string $text Text.
 * @return array
 */
function md_link_lines( $text ) {
	$out = array();
	foreach ( md_lines( $text ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$out[] = array(
			'label' => $parts[0],
			'url'   => isset( $parts[1] ) && '' !== $parts[1] ? md_resolve_url( $parts[1] ) : '#',
		);
	}
	return $out;
}

/**
 * Accept full URLs, anchors or site paths like /about/.
 *
 * @param string $url URL.
 * @return string
 */
function md_resolve_url( $url ) {
	if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) {
		return home_url( $url );
	}
	return $url;
}

/**
 * Print an Elementor template (header, footer, article call to action).
 *
 * @param int $template_id Template.
 * @return bool True when something was printed.
 */
function md_print_template( $template_id ) {
	$template_id = (int) $template_id;
	if ( ! $template_id || ! class_exists( '\Elementor\Plugin' ) || 'publish' !== get_post_status( $template_id ) ) {
		return false;
	}
	$html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, true );
	if ( '' === trim( (string) $html ) ) {
		return false;
	}
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- Elementor output.
	return true;
}

/**
 * Render one Maison widget directly with its default content (used as a fallback
 * when no Elementor template has been created yet).
 *
 * @param string $type     Widget name.
 * @param array  $settings Settings.
 * @return bool
 */
function md_print_widget( $type, $settings = array() ) {
	if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance(
		array(
			'id'         => substr( md5( $type ), 0, 7 ),
			'elType'     => 'widget',
			'widgetType' => $type,
			'settings'   => $settings,
		)
	);
	if ( ! $widget ) {
		return false;
	}
	ob_start();
	$widget->print_element();
	echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput
	return true;
}

/**
 * Estimated reading time in minutes.
 *
 * @param int $post_id Post.
 * @return int
 */
function md_reading_time( $post_id ) {
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( strip_shortcodes( $content ) ) );
	if ( $words < 50 ) {
		$data = get_post_meta( $post_id, '_elementor_data', true );
		if ( $data ) {
			$words = str_word_count( wp_strip_all_tags( is_string( $data ) ? $data : wp_json_encode( $data ) ) ) / 3;
		}
	}
	return max( 1, (int) round( $words / 200 ) );
}

/**
 * The nine services, as listed in the brief. Used as default content.
 *
 * @return array
 */
function md_services() {
	return array(
		array( 'slug' => 'digital-marketing', 'name' => 'Digital Marketing', 'short' => 'Digital Marketing', 'chip' => 'Digital Marketing', 'promise' => 'Strategy and campaigns that turn attention into growth.', 'home' => "Strategy, campaigns and growth, measured.\nPaid campaigns, search, email and reporting.", 'ph' => '[Full-bleed image — Digital Marketing project]', 'included' => array( 'Marketing strategy', 'Paid campaigns', 'Search engine optimisation', 'Email marketing', 'Analytics and reporting' ) ),
		array( 'slug' => 'social-media-management', 'name' => 'Social Media Management', 'short' => 'Social Media', 'chip' => 'Social Media', 'promise' => 'A consistent, considered presence on every platform that matters to you.', 'home' => "Daily presence, planned and consistent.\nCalendars, publishing, community and monthly reports.", 'ph' => '[Full-bleed image — Social Media project]', 'included' => array( 'Platform strategy', 'Monthly content calendar', 'Publishing', 'Community management', 'Monthly performance report' ) ),
		array( 'slug' => 'content-creation', 'name' => 'Content Creation', 'short' => 'Content Creation', 'chip' => 'Content', 'promise' => 'Content with an idea behind it.', 'home' => "Ideas made to be watched, read and shared.\nSeries, photography, reels, graphics and articles.", 'ph' => '[Full-bleed image — Content project]', 'included' => array( 'Content concepts and series', 'Photography', 'Short-form video and reels', 'Graphics and carousels', 'Articles' ) ),
		array( 'slug' => 'design-services', 'name' => 'Design Services', 'short' => 'Design', 'chip' => 'Design', 'promise' => 'Visual identity with a point of view.', 'home' => "Identities, layouts and visuals with a point of view.\nLogos, brand books, templates, print and web.", 'ph' => '[Full-bleed image — Design project]', 'included' => array( 'Logo and brand identity', 'Brand books', 'Social media templates', 'Print and packaging', 'Presentations', 'Website and interface design' ) ),
		array( 'slug' => 'production-videography', 'name' => 'Production & Videography', 'short' => 'Production', 'chip' => 'Production', 'promise' => 'From shot list to final cut.', 'home' => "From shot list to final cut.\nBrand films, adverts, motion graphics and event coverage.", 'ph' => '[Full-bleed image or loop — Production project]', 'included' => array( 'Concept and pre-production', 'Filming', 'Editing and colour', 'Motion graphics', 'Brand films', 'Adverts', 'Event coverage' ) ),
		array( 'slug' => 'copywriting', 'name' => 'Copywriting', 'short' => 'Copywriting', 'chip' => 'Copywriting', 'promise' => 'Words that sound like you and sell for you.', 'home' => "Words that sound like you and sell for you.\nBrand voice, websites, campaigns, in English and Arabic.", 'ph' => '[Full-bleed image — Copywriting project]', 'included' => array( 'Brand voice', 'Website copy', 'Campaign and advert copy', 'Captions', 'Newsletters', 'English and Arabic' ) ),
		array( 'slug' => 'scriptwriting-cinematic-work', 'name' => 'Scriptwriting & Cinematic Work', 'short' => 'Scriptwriting', 'chip' => 'Scriptwriting', 'promise' => 'Stories written for the screen.', 'home' => "Stories written for the screen.\nScripts, storyboards and creative direction on set.", 'ph' => '[Full-bleed film still — Scriptwriting project]', 'included' => array( 'Scripts for adverts, brand films and long-form video', 'Storyboards', 'Creative direction on set', 'Documentary and narrative pieces' ) ),
		array( 'slug' => 'crm-creation', 'name' => 'CRM Creation', 'short' => 'CRM Creation', 'chip' => 'CRM', 'promise' => 'Every lead captured, every client remembered.', 'home' => "Every lead captured, every client remembered.\nSetup, pipelines, follow-ups and team training.", 'ph' => '[Full-bleed image — CRM project]', 'included' => array( 'CRM setup and customisation', 'Sales pipeline design', 'Lead capture forms', 'Automated follow-ups', 'Team training' ) ),
		array( 'slug' => 'ai-automation-management', 'name' => 'AI Automation & Management', 'short' => 'AI Automation', 'chip' => 'AI Automation', 'promise' => 'Smart systems that take the repetitive work off your team.', 'home' => "Smart systems that take the repetitive work off your team.\nWorkflows, assistants, reporting and monitoring.", 'ph' => '[Full-bleed image — AI automation project]', 'included' => array( 'Workflow automation', 'AI assistants and chatbots', 'Automated reporting', 'Content workflows', 'Ongoing monitoring and management' ) ),
	);
}

/**
 * Site path of a service page.
 *
 * @param int $i Index 0–8.
 * @return string
 */
function md_service_path( $i ) {
	$s = md_services();
	return isset( $s[ $i ] ) ? '/services/' . $s[ $i ]['slug'] . '/' : '/services/';
}

/**
 * YouTube / Vimeo page URL into an embed URL.
 *
 * @param string $url URL.
 * @return string
 */
function md_embed_url( $url ) {
	if ( preg_match( '~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([\w-]{6,})~', $url, $m ) ) {
		return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
	}
	if ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~', $url, $m ) ) {
		return 'https://player.vimeo.com/video/' . $m[1];
	}
	return $url;
}
