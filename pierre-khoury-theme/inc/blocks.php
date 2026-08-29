<?php
/**
 * Small server-rendered blocks used inside template parts and seeded page
 * content. Neither has an editor UI (no "edit"/"save" script) — they exist
 * so links and menus stay correct even after pages are renamed or their
 * permalinks change, instead of being hard-coded into static HTML.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve one of the theme's known internal link targets to a real URL.
 * Falls back to '#' (rather than fataling) if the seeded page/menu is
 * missing, so a site that hasn't run the seeder yet doesn't break.
 */
function pk_resolve_url( $target ) {
	if ( 0 === strpos( $target, 'service:' ) ) {
		$slug = substr( $target, 8 );
		$id   = get_option( 'pk_page_service_' . $slug . '_id' );
		return $id ? get_permalink( $id ) : '#';
	}

	if ( 'whatsapp' === $target ) {
		return pk_whatsapp_url();
	}

	$map = array(
		'home'     => 'pk_page_home_id',
		'about'    => 'pk_page_about_id',
		'services' => 'pk_page_services_id',
		'track'    => 'pk_page_track_id',
		'contact'  => 'pk_page_contact_id',
	);

	if ( 'blog' === $target ) {
		$id = get_option( 'page_for_posts' );
		return $id ? get_permalink( $id ) : '#';
	}

	if ( isset( $map[ $target ] ) ) {
		$id = get_option( $map[ $target ] );
		return $id ? get_permalink( $id ) : '#';
	}

	return '#';
}

function pk_render_cta_button_block( $attributes ) {
	$target = isset( $attributes['target'] ) ? sanitize_text_field( $attributes['target'] ) : 'contact';
	$label  = isset( $attributes['label'] ) ? $attributes['label'] : __( 'Book a Consultation', 'pierre-khoury' );
	$style  = isset( $attributes['style'] ) ? sanitize_html_class( $attributes['style'] ) : 'solid';
	$extra  = isset( $attributes['className'] ) ? ' ' . sanitize_html_class( $attributes['className'] ) : '';

	$styles = array(
		'solid'        => 'pk-btn',
		'navy'         => 'pk-btn pk-btn--navy',
		'outline'      => 'pk-btn pk-btn--outline',
		'outline-navy' => 'pk-btn pk-btn--outline-navy',
		'white'        => 'pk-btn pk-btn--white',
		'outline-white' => 'pk-btn pk-btn--outline-white',
	);
	$class = isset( $styles[ $style ] ) ? $styles[ $style ] : $styles['solid'];

	$url    = pk_resolve_url( $target );
	$target_attr = ( 'whatsapp' === $target ) ? ' target="_blank" rel="noopener noreferrer"' : '';

	// $label may arrive HTML-entity-encoded when this was invoked via the
	// [pk_cta] shortcode (its attribute value is taken verbatim from the
	// shortcode text, which we build with esc_attr()) — decode once before
	// re-escaping for output so it isn't double-encoded.
	$label = html_entity_decode( $label, ENT_QUOTES, 'UTF-8' );

	return sprintf(
		'<a class="%1$s%2$s" href="%3$s"%4$s>%5$s</a>',
		esc_attr( $class ),
		esc_attr( $extra ),
		esc_url( $url ),
		$target_attr,
		esc_html( $label )
	);
}

function pk_render_nav_menu_block( $attributes ) {
	$location = isset( $attributes['location'] ) ? sanitize_key( $attributes['location'] ) : 'primary';
	$class    = isset( $attributes['className'] ) ? sanitize_html_class( $attributes['className'] ) : 'pk-nav-menu';

	if ( ! has_nav_menu( $location ) ) {
		return '';
	}

	return wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => $class,
			'depth'          => 2,
			'fallback_cb'    => false,
			'echo'           => false,
		)
	);
}

function pk_render_logo_block( $attributes ) {
	$class = isset( $attributes['className'] ) ? sanitize_html_class( $attributes['className'] ) : 'pk-logo';
	return sprintf(
		'<a href="%1$s" class="%2$s">pierre khoury<span class="pk-logo-dot">.</span></a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $class )
	);
}

function pk_render_footer_bottom_block() {
	return sprintf(
		'<div class="pk-footer-bottom"><span>&copy; %1$s %2$s</span><span>Beirut, Lebanon &middot; Serving MENA</span></div>',
		esc_html( gmdate( 'Y' ) ),
		esc_html( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Pierre Khoury' )
	);
}

function pk_render_blog_categories_block() {
	$cats = get_categories( array( 'hide_empty' => false ) );
	if ( empty( $cats ) ) {
		return '';
	}
	$out = '<div class="pk-category-pills">';
	foreach ( $cats as $cat ) {
		$out .= sprintf( '<a href="%1$s">%2$s</a>', esc_url( get_category_link( $cat ) ), esc_html( $cat->name ) );
	}
	$out .= '</div>';
	return $out;
}

/**
 * Maps the theme's five blog categories to their matching service pillar,
 * so a single blog post can link to the relevant service page.
 */
function pk_category_to_pillar_map() {
	return array(
		'Gen Z in the Workplace'         => 'genz',
		'Financial Literacy for Leaders' => 'finance',
		'Career & Lifelong Learning'     => 'career',
		'Blockchain in the Arab World'   => 'blockchain',
		'Training Center Strategy'       => 'center',
	);
}

function pk_render_related_service_block() {
	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return '';
	}
	$cats = get_the_category( $post_id );
	if ( empty( $cats ) ) {
		return '';
	}
	$map    = pk_category_to_pillar_map();
	$pillar = null;
	foreach ( $cats as $cat ) {
		if ( isset( $map[ $cat->name ] ) ) {
			$pillar = $map[ $cat->name ];
			break;
		}
	}
	if ( ! $pillar ) {
		return '';
	}
	$pillars = pk_pillars_data();
	$data    = null;
	foreach ( $pillars as $p ) {
		if ( $p['id'] === $pillar ) {
			$data = $p;
			break;
		}
	}
	if ( ! $data ) {
		return '';
	}
	$url = pk_resolve_url( 'service:' . $data['slug'] );

	return sprintf(
		'<div class="pk-related-service"><span class="pk-related-service__label">%1$s</span><span class="pk-related-service__title">%2$s</span><a class="pk-btn pk-btn--white" href="%3$s">%4$s &rarr;</a></div>',
		esc_html__( 'Related service', 'pierre-khoury' ),
		esc_html( $data['title'] ),
		esc_url( $url ),
		esc_html__( 'Learn More', 'pierre-khoury' )
	);
}

/**
 * Shortcodes — used instead of the blocks above wherever we need dynamic
 * links or forms *inside* a hand-authored HTML section of seeded page
 * content (a "Custom HTML" block does not re-parse nested block comments,
 * but `do_shortcode()` still runs on the assembled page content, so a
 * shortcode dropped into raw HTML resolves correctly).
 */
function pk_shortcode_cta( $atts ) {
	$atts = shortcode_atts(
		array(
			'target' => 'contact',
			'label'  => __( 'Book a Consultation', 'pierre-khoury' ),
			'style'  => 'solid',
			'class'  => '',
		),
		$atts,
		'pk_cta'
	);
	return pk_render_cta_button_block(
		array(
			'target'    => $atts['target'],
			'label'     => $atts['label'],
			'style'     => $atts['style'],
			'className' => $atts['class'],
		)
	);
}
add_shortcode( 'pk_cta', 'pk_shortcode_cta' );

function pk_shortcode_blog_teaser( $atts ) {
	$atts = shortcode_atts( array( 'count' => 3 ), $atts, 'pk_blog_teaser' );
	$q    = new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => absint( $atts['count'] ),
			'post_status'    => 'publish',
			'ignore_sticky_posts' => true,
		)
	);

	if ( ! $q->have_posts() ) {
		return '<p><em>' . esc_html__( 'No posts published yet.', 'pierre-khoury' ) . '</em></p>';
	}

	$out = '<div class="pk-post-grid">';
	while ( $q->have_posts() ) {
		$q->the_post();
		$cats = get_the_category();
		$cat  = ! empty( $cats ) ? $cats[0]->name : '';
		$out .= sprintf(
			'<a class="pk-post-card" href="%1$s">%2$s<span class="pk-post-card__body"><span class="pk-post-card__cat">%3$s</span><span class="pk-post-card__title">%4$s</span><span class="pk-post-card__meta">%5$s</span></span></a>',
			esc_url( get_permalink() ),
			has_post_thumbnail() ? get_the_post_thumbnail( null, 'medium_large', array( 'class' => 'pk-post-card__thumb' ) ) : '<span class="pk-post-card__thumb"></span>',
			esc_html( $cat ),
			esc_html( get_the_title() ),
			esc_html( get_the_date() ) . ' · Read More →'
		);
	}
	wp_reset_postdata();
	$out .= '</div>';
	return $out;
}
add_shortcode( 'pk_blog_teaser', 'pk_shortcode_blog_teaser' );

function pk_shortcode_contact_form() {
	$form_id = get_option( 'pk_contact_form_id' );
	if ( $form_id && shortcode_exists( 'contact-form-7' ) ) {
		return do_shortcode( sprintf( '[contact-form-7 id="%d"]', absint( $form_id ) ) );
	}
	if ( current_user_can( 'edit_theme_options' ) ) {
		return '<p><em>' . esc_html__( 'Contact Form 7 is not installed/configured yet. Install & activate it and this form will appear automatically.', 'pierre-khoury' ) . '</em></p>';
	}
	return '<p><em>' . esc_html__( 'Please email or WhatsApp us — the contact form is being set up.', 'pierre-khoury' ) . '</em></p>';
}
add_shortcode( 'pk_contact_form', 'pk_shortcode_contact_form' );

function pk_register_blocks() {
	register_block_type(
		'pierre-khoury/blog-categories',
		array(
			'render_callback' => 'pk_render_blog_categories_block',
		)
	);

	register_block_type(
		'pierre-khoury/related-service',
		array(
			'render_callback' => 'pk_render_related_service_block',
		)
	);

	register_block_type(
		'pierre-khoury/footer-bottom',
		array(
			'render_callback' => 'pk_render_footer_bottom_block',
		)
	);

	register_block_type(
		'pierre-khoury/logo',
		array(
			'attributes'      => array(
				'className' => array( 'type' => 'string', 'default' => 'pk-logo' ),
			),
			'render_callback' => 'pk_render_logo_block',
		)
	);

	register_block_type(
		'pierre-khoury/nav-menu',
		array(
			'attributes'      => array(
				'location'  => array( 'type' => 'string', 'default' => 'primary' ),
				'className' => array( 'type' => 'string', 'default' => 'pk-nav-menu' ),
			),
			'render_callback' => 'pk_render_nav_menu_block',
		)
	);

	register_block_type(
		'pierre-khoury/cta-button',
		array(
			'attributes'      => array(
				'target'    => array( 'type' => 'string', 'default' => 'contact' ),
				'label'     => array( 'type' => 'string', 'default' => 'Book a Consultation' ),
				'style'     => array( 'type' => 'string', 'default' => 'solid' ),
				'className' => array( 'type' => 'string', 'default' => '' ),
			),
			'render_callback' => 'pk_render_cta_button_block',
		)
	);
}
add_action( 'init', 'pk_register_blocks' );
