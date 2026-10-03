<?php
/**
 * Header and footer output.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

/**
 * Site header: Elementor Pro location → page override → site template → built-in default.
 */
function md_site_header() {
	if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) {
		return;
	}
	$mode     = md_nav_mode();
	$override = (int) md_page_setting( 'md_header_tpl', 0 );

	if ( 'minimal' === $mode ) {
		echo '<header class="md-header md-hm-minimal">';
		if ( ! md_print_template( $override ? $override : md_opt( 'header_min_tpl' ) ) ) {
			md_print_widget( 'md-mini-header' );
		}
		echo '</header>';
		return;
	}

	echo '<header class="md-header md-hm-' . esc_attr( $mode ) . '" id="md-header">';
	if ( ! md_print_template( $override ? $override : md_opt( 'header_tpl' ) ) ) {
		if ( ! md_print_widget( 'md-nav' ) ) {
			md_basic_header();
		}
	}
	echo '</header>';
}

/**
 * Site footer.
 */
function md_site_footer() {
	if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) {
		return;
	}
	$footer   = md_page_setting( 'md_footer', 'minimal' === md_nav_mode() ? 'minimal' : ( 'home' === md_nav_mode() ? 'none' : 'full' ) );
	$override = (int) md_page_setting( 'md_footer_tpl', 0 );
	if ( 'none' === $footer ) {
		return;
	}
	if ( 'minimal' === $footer ) {
		if ( ! md_print_template( $override ? $override : md_opt( 'footer_min_tpl' ) ) ) {
			md_print_widget( 'md-mini-footer' );
		}
		return;
	}
	md_full_footer( $override );
}

/**
 * The full footer, also used inside the homepage closing panel.
 *
 * @param int $override Template override.
 */
function md_full_footer( $override = 0 ) {
	if ( ! $override ) {
		$override = (int) md_page_setting( 'md_footer_tpl', 0 );
	}
	if ( ! md_print_template( $override ? $override : md_opt( 'footer_tpl' ) ) ) {
		if ( ! md_print_widget( 'md-footer' ) ) {
			echo '<footer class="ft"><div class="ft-in"><p style="margin:0;font-size:13px;color:#b9ad9d">&copy; ' . esc_html( gmdate( 'Y' ) ) . ' ' . esc_html( get_bloginfo( 'name' ) ) . '</p></div></footer>';
		}
	}
}

/**
 * Header without Elementor (only shown until Elementor is activated).
 */
function md_basic_header() {
	?>
	<div class="nv"><nav class="bar-d" aria-label="<?php esc_attr_e( 'Main', 'maison-digitale' ); ?>" style="display:flex">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( md_asset( 'logos/logo-horizontal.svg' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"></a>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => false,
				'depth'          => 1,
				'items_wrap'     => '<div class="nv-links">%3$s</div>',
			)
		);
		?>
	</nav></div>
	<?php
}

/**
 * Was this post built with Elementor?
 *
 * @param int $post_id Post.
 * @return bool
 */
function md_built_with_elementor( $post_id ) {
	return $post_id && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/**
 * First category name of a post.
 *
 * @param int $post_id Post.
 * @return string
 */
function md_post_category( $post_id ) {
	$cats = get_the_category( $post_id );
	return $cats ? $cats[0]->name : '';
}

/**
 * Article card (Blog.dc.html / Article.dc.html "Related notes").
 *
 * @param int    $post_id    Post.
 * @param string $title_size CSS font size of the title.
 * @param string $extra      Extra attributes on the card.
 */
function md_post_card( $post_id, $title_size = '26px', $extra = '' ) {
	$thumb = get_post_thumbnail_id( $post_id );
	$media = $thumb ? array( 'id' => $thumb, 'url' => wp_get_attachment_image_url( $thumb, 'large' ) ) : '';
	$mins  = md_reading_time( $post_id );
	$min_l = md_is_rtl() ? 'دقائق قراءة' : __( 'min read', 'maison-digitale' );
	?>
	<a class="card rv" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" <?php echo $extra; // phpcs:ignore ?>>
		<div class="im" style="aspect-ratio: 4 / 3"><?php echo md_box( $media, '[Article image]', '', '', 'div', get_the_title( $post_id ) ); // phpcs:ignore ?></div>
		<p class="ov" style="color: #a67c2e; margin-top: 16px"><?php echo esc_html( md_post_category( $post_id ) ); ?></p>
		<p class="fh tt" style="font-size: <?php echo esc_attr( $title_size ); ?>; margin-top: 8px"><?php echo esc_html( get_the_title( $post_id ) ); ?></p>
		<p class="ov" style="color: #62574d; margin-top: 12px"><?php echo esc_html( $mins . ' ' . $min_l . ' · ' . get_the_date( '', $post_id ) ); ?></p>
	</a>
	<?php
}

/**
 * URL of a page by its slug, or the homepage.
 *
 * @param string $slug Slug.
 * @return string
 */
function md_page_url_by_slug( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' );
}
