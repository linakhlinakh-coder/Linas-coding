<?php
/**
 * Not found.
 *
 * @package MaisonDigitale
 */

get_header();
?>
<section class="sec" style="padding-top: clamp(40px, 6cqw, 88px); flex: 1 1 auto">
	<div class="in" style="display: flex; flex-direction: column; gap: clamp(16px, 2cqw, 28px)">
		<p class="ov" style="color: #62574d">404</p>
		<div>
			<div aria-hidden="true" class="fh md-q" style="font-size: clamp(60px, 8.4cqw, 120px); line-height: .62; color: #a67c2e">“</div>
			<h1 class="fh" style="font-size: clamp(40px, 7.2cqw, 104px)"><?php esc_html_e( 'This page has moved out.', 'maison-digitale' ); ?></h1>
		</div>
		<p style="margin: 0; font-size: clamp(19px, 1.7cqw, 24px); line-height: 1.5; max-width: 34em"><?php esc_html_e( 'The page you were looking for is not here. Start again from the homepage.', 'maison-digitale' ); ?></p>
		<div><a class="bt b-ink" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the site', 'maison-digitale' ); ?></a></div>
	</div>
</section>
<?php
get_footer();
