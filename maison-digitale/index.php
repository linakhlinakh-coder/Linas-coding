<?php
/**
 * Fallback listing (archives, search, the posts page when no Elementor blog page is used).
 *
 * @package MaisonDigitale
 */

get_header();

if ( is_home() && ! is_front_page() && md_built_with_elementor( (int) get_option( 'page_for_posts' ) ) ) {
	// The Blog page was built in Elementor: show it instead of a plain list.
	$GLOBALS['post'] = get_post( (int) get_option( 'page_for_posts' ) ); // phpcs:ignore
	setup_postdata( $GLOBALS['post'] );
	the_content();
	wp_reset_postdata();
	get_footer();
	return;
}

if ( is_search() ) {
	/* translators: %s: search query */
	$md_title = sprintf( __( 'Results for “%s”', 'maison-digitale' ), get_search_query() );
} elseif ( is_archive() ) {
	$md_title = wp_strip_all_tags( get_the_archive_title() );
} else {
	$md_title = __( 'Notes from the house.', 'maison-digitale' );
}
?>
<header class="sec" style="padding-top: clamp(40px, 6cqw, 88px); padding-bottom: clamp(32px, 4cqw, 56px)">
	<div class="in" style="display: flex; flex-direction: column; gap: clamp(16px, 2cqw, 28px)">
		<p class="ov" style="color: #62574d"><?php esc_html_e( 'Blog', 'maison-digitale' ); ?></p>
		<div>
			<div aria-hidden="true" class="fh md-q" style="font-size: clamp(60px, 8.4cqw, 120px); line-height: .62; color: #a67c2e">“</div>
			<h1 class="fh" style="font-size: clamp(40px, 7.2cqw, 104px)"><?php echo esc_html( $md_title ); ?></h1>
		</div>
	</div>
</header>
<section style="padding: 0 clamp(20px, 5cqw, 80px) clamp(56px, 8cqw, 120px)">
	<div class="in" style="display: flex; flex-direction: column; gap: clamp(24px, 3cqw, 40px); border-top: 1px solid #d8cdbc; padding-top: 24px">
		<?php if ( have_posts() ) : ?>
			<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 320px), 1fr)); gap: clamp(32px, 4cqw, 56px) clamp(16px, 2cqw, 28px)">
				<?php
				while ( have_posts() ) :
					the_post();
					md_post_card( get_the_ID(), 'clamp(22px, 2.1cqw, 30px)' );
				endwhile;
				?>
			</div>
			<div class="md-pagination" style="display: flex; gap: 12px; flex-wrap: wrap">
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'type'      => 'plain',
							'prev_text' => __( 'Newer', 'maison-digitale' ),
							'next_text' => __( 'Older', 'maison-digitale' ),
						)
					)
				);
				?>
			</div>
		<?php else : ?>
			<p style="margin: 0; padding: 48px 0; font-size: 20px; color: #62574d"><?php esc_html_e( 'Nothing here yet.', 'maison-digitale' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
