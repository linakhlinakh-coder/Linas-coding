<?php
/**
 * Maison Digitale theme bootstrap.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

define( 'MD_VERSION', '1.0.0' );
define( 'MD_DIR', get_template_directory() );
define( 'MD_URI', get_template_directory_uri() );

require_once MD_DIR . '/inc/helpers.php';
require_once MD_DIR . '/inc/settings.php';
require_once MD_DIR . '/inc/template-tags.php';
require_once MD_DIR . '/inc/forms.php';
require_once MD_DIR . '/inc/elementor.php';
require_once MD_DIR . '/inc/demo.php';

/**
 * Theme supports.
 */
function md_setup() {
	load_theme_textdomain( 'maison-digitale', MD_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'site-icon' );
	register_nav_menus(
		array(
			'primary' => __( 'Primary (used only when no Elementor header exists)', 'maison-digitale' ),
		)
	);
}
add_action( 'after_setup_theme', 'md_setup' );

/**
 * Elementor content width: the designs are full bleed.
 */
function md_content_width() {
	$GLOBALS['content_width'] = 1280;
}
add_action( 'after_setup_theme', 'md_content_width', 0 );

/**
 * Styles and scripts.
 */
function md_assets() {
	wp_enqueue_style( 'md-fonts', 'https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600&family=IBM+Plex+Sans+Arabic:wght@400;500;600&display=swap', array(), null );
	wp_enqueue_style( 'maison-digitale', MD_URI . '/assets/css/maison.css', array( 'md-fonts' ), md_asset_version( 'assets/css/maison.css' ) );
	wp_enqueue_script( 'maison-digitale', MD_URI . '/assets/js/maison.js', array(), md_asset_version( 'assets/js/maison.js' ), true );
	wp_localize_script(
		'maison-digitale',
		'MD',
		array(
			'ajax'  => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'md_form' ),
			'i18n'  => array(
				'required'  => md_is_rtl() ? 'هذا الحقل مطلوب.' : __( 'This field is required.', 'maison-digitale' ),
				'email'     => md_is_rtl() ? 'أدخلوا عنوان بريد إلكتروني كاملاً.' : __( 'Enter a complete email address.', 'maison-digitale' ),
				'summary1'  => md_is_rtl() ? 'حقل واحد يحتاج إلى انتباه قبل الإرسال.' : __( 'One field needs attention before we can send this.', 'maison-digitale' ),
				'summaryN'  => md_is_rtl() ? '%d حقول تحتاج إلى انتباه قبل الإرسال.' : __( '%d fields need attention before we can send this.', 'maison-digitale' ),
				'failed'    => md_is_rtl() ? 'تعذّر الإرسال. حاولوا مرة أخرى.' : __( 'Something went wrong while sending. Please try again.', 'maison-digitale' ),
				'copied'    => md_is_rtl() ? 'تم نسخ الرابط' : __( 'Link copied', 'maison-digitale' ),
			),
		)
	);
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'md_assets' );

/**
 * Cache-busting version from the file's modification time.
 *
 * @param string $rel Relative path inside the theme.
 * @return string
 */
function md_asset_version( $rel ) {
	$file = MD_DIR . '/' . $rel;
	return file_exists( $file ) ? MD_VERSION . '.' . filemtime( $file ) : MD_VERSION;
}

/**
 * Preconnect for Google Fonts and preload the display face.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function md_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'md_resource_hints', 10, 2 );

function md_head_extras() {
	echo '<link rel="preload" href="' . esc_url( MD_URI . '/assets/fonts/Flyhead-Regular.otf' ) . '" as="font" type="font/otf" crossorigin>' . "\n";
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" href="' . esc_url( MD_URI . '/assets/logos/app-icon.svg' ) . '" type="image/svg+xml">' . "\n";
	}
	// Runs before paint. Optionally skips the homepage loader after the first visit of a session.
	$once = md_opt( 'loader_once' ) ? 1 : 0;
	echo "<script>(function(d,o){var h=d.documentElement;h.classList.add('md-js');if(o){try{if(sessionStorage.getItem('md-ld')){h.classList.add('md-ld-seen');}sessionStorage.setItem('md-ld','1');}catch(e){}}})(document," . $once . ");</script>\n";
}
add_action( 'wp_head', 'md_head_extras', 1 );

/**
 * Body classes drive the page frame: navigation mode and background.
 *
 * @param array $classes Classes.
 * @return array
 */
function md_body_class( $classes ) {
	$classes[] = 'md-site';
	$classes[] = 'md-mode-' . md_nav_mode();
	if ( 'ink' === md_page_setting( 'md_bg', 'ground' ) ) {
		$classes[] = 'md-bg-ink';
	}
	return $classes;
}
add_filter( 'body_class', 'md_body_class' );

/**
 * Per-page right-to-left switch (for the Arabic pages) on top of the site language.
 *
 * @param string $output Attributes.
 * @return string
 */
function md_language_attributes( $output ) {
	$classes = array();
	if ( 'home' === md_nav_mode() && ! md_is_editor() ) {
		$classes[] = 'md-snap';
	}
	if ( md_is_editor() ) {
		$classes[] = 'md-editor';
	}
	if ( 'rtl' === md_page_setting( 'md_dir', '' ) && ! is_rtl() ) {
		$output = preg_replace( '/\s?dir="[^"]*"/', '', $output );
		$output = preg_replace( '/\s?lang="[^"]*"/', '', $output );
		$output .= ' dir="rtl" lang="ar"';
	}
	if ( $classes ) {
		$output .= ' class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
	return $output;
}
add_filter( 'language_attributes', 'md_language_attributes', 20 );

/**
 * Elementor Pro theme builder: let Pro users override header and footer if they want to.
 *
 * @param object $manager Locations manager.
 */
function md_register_elementor_locations( $manager ) {
	$manager->register_location( 'header' );
	$manager->register_location( 'footer' );
}
add_action( 'elementor/theme/register_locations', 'md_register_elementor_locations' );

/**
 * Blog excerpts: short and without the default "[...]".
 */
add_filter( 'excerpt_more', function () {
	return '…';
} );
add_filter( 'excerpt_length', function () {
	return 24;
} );
