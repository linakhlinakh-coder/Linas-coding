<?php
/**
 * Pierre Khoury theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PK_THEME_VERSION', '1.0.0' );

require get_template_directory() . '/inc/seed-data.php';
require get_template_directory() . '/inc/blocks.php';
require get_template_directory() . '/inc/seed-content.php';
require get_template_directory() . '/inc/import-legacy-posts.php';
require get_template_directory() . '/inc/admin-settings.php';

/**
 * Theme setup.
 */
function pk_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_editor_style( 'assets/css/pierre-khoury.css' );

	// Full Site Editing theme — block templates live in /templates and /parts.
	add_theme_support( 'block-templates' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'pierre-khoury' ),
			'footer-services' => __( 'Footer — Services', 'pierre-khoury' ),
			'footer-resources' => __( 'Footer — Resources', 'pierre-khoury' ),
		)
	);

	set_post_thumbnail_size( 1200, 750, true );
}
add_action( 'after_setup_theme', 'pk_setup' );

/**
 * Styles & scripts.
 */
function pk_assets() {
	wp_enqueue_style(
		'pk-google-fonts',
		'https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;1,6..72,400&family=Space+Grotesk:wght@300;400;500;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'pierre-khoury-style',
		get_template_directory_uri() . '/assets/css/pierre-khoury.css',
		array(),
		PK_THEME_VERSION
	);
	// style.css is the theme's identifying stylesheet; keep it enqueued too.
	wp_enqueue_style( 'pierre-khoury-theme', get_stylesheet_uri(), array( 'pierre-khoury-style' ), PK_THEME_VERSION );

	wp_enqueue_script(
		'pierre-khoury-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		PK_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'pk_assets' );

/**
 * Notice in wp-admin if Contact Form 7 isn't active yet — full setup
 * happens at Settings → Pierre Khoury.
 */
function pk_cf7_notice() {
	if ( ! class_exists( 'WPCF7' ) && current_user_can( 'activate_plugins' ) ) {
		echo '<div class="notice notice-warning"><p>' .
			sprintf(
				/* translators: %s: link to Settings → Pierre Khoury */
				esc_html__( 'The Pierre Khoury theme uses Contact Form 7 to power the Contact page. Install & activate it, then finish setup at %s.', 'pierre-khoury' ),
				'<a href="' . esc_url( admin_url( 'options-general.php?page=pierre-khoury-settings' ) ) . '">' . esc_html__( 'Settings → Pierre Khoury', 'pierre-khoury' ) . '</a>'
			) .
			'</p></div>';
	}
}
add_action( 'admin_notices', 'pk_cf7_notice' );

/**
 * WhatsApp number used across CTAs. Configurable at Settings → Pierre
 * Khoury; still filterable for anyone who prefers to set it in code.
 * Falls back to a placeholder Lebanese number until it's configured.
 */
function pk_whatsapp_url() {
	$number = get_option( 'pk_whatsapp_number', '' );
	$number = apply_filters( 'pk_whatsapp_number', $number ? $number : '96170000000' );
	$number = preg_replace( '/[^0-9]/', '', (string) $number );
	return 'https://wa.me/' . $number;
}
