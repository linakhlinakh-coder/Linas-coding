<?php
/**
 * Pages. Pages built with Elementor are printed full width, exactly as designed.
 *
 * @package MaisonDigitale
 */

get_header();

while ( have_posts() ) :
	the_post();
	if ( md_built_with_elementor( get_the_ID() ) ) {
		the_content();
	} else {
		get_template_part( 'template-parts/content', 'page' );
	}
endwhile;

get_footer();
