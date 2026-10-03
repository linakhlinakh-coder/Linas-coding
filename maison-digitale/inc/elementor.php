<?php
/**
 * Elementor integration: widget category and widget registration.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

/**
 * "Maison Digitale" category at the top of the Elementor panel.
 *
 * @param \Elementor\Elements_Manager $manager Manager.
 */
function md_elementor_category( $manager ) {
	$manager->add_category(
		'maison-digitale',
		array(
			'title' => __( 'Maison Digitale', 'maison-digitale' ),
			'icon'  => 'eicon-site-identity',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'md_elementor_category' );

/**
 * The widget classes, file by file.
 *
 * @return array file => class names
 */
function md_widget_map() {
	return array(
		'global'   => array( 'MD_W_Nav', 'MD_W_Footer', 'MD_W_Mini_Header', 'MD_W_Mini_Footer' ),
		'home'     => array( 'MD_W_Home_Hero', 'MD_W_Home_Service', 'MD_W_Home_About', 'MD_W_Home_Audit', 'MD_W_Home_Closing' ),
		'sections' => array(
			'MD_W_Page_Hero',
			'MD_W_Dark_Hero',
			'MD_W_Image_Band',
			'MD_W_Split_Text',
			'MD_W_Statements',
			'MD_W_Steps',
			'MD_W_Cards',
			'MD_W_Team',
			'MD_W_Recognition',
			'MD_W_CTA_Band',
			'MD_W_Service_Index',
			'MD_W_Service_Groups',
			'MD_W_Work_Cards',
			'MD_W_Accordion',
			'MD_W_Next_Link',
			'MD_W_Meta_Row',
			'MD_W_Gallery',
			'MD_W_Results',
			'MD_W_Quote',
			'MD_W_Contact',
			'MD_W_Route_Cards',
			'MD_W_Newsletter',
		),
		'apps'     => array( 'MD_W_Portfolio', 'MD_W_Blog', 'MD_W_Brand_Audit', 'MD_W_Enquiry' ),
	);
}

/**
 * Register every widget.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
 */
function md_register_widgets( $widgets_manager ) {
	require_once MD_DIR . '/inc/widgets/class-md-widget.php';
	foreach ( md_widget_map() as $file => $classes ) {
		require_once MD_DIR . '/inc/widgets/' . $file . '.php';
		foreach ( $classes as $class ) {
			if ( class_exists( $class ) ) {
				$widgets_manager->register( new $class() );
			}
		}
	}
}
add_action( 'elementor/widgets/register', 'md_register_widgets' );

/**
 * Editor styles: brand colours on the Maison widget tiles.
 */
function md_editor_styles() {
	wp_add_inline_style(
		'elementor-editor',
		'#elementor-panel-category-maison-digitale .elementor-element .icon{color:#a67c2e}#elementor-panel-category-maison-digitale .elementor-element:hover .icon{color:#17120f}'
	);
}
add_action( 'elementor/editor/after_enqueue_styles', 'md_editor_styles' );

/**
 * Theme fonts inside the editor preview too.
 */
function md_preview_assets() {
	md_assets();
}
add_action( 'elementor/preview/enqueue_styles', 'md_preview_assets' );
