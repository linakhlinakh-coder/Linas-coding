<?php
/**
 * A page written in the WordPress editor (not Elementor): styled like the inner pages.
 *
 * @package MaisonDigitale
 */
?>
<header class="sec" style="padding-top: clamp(40px, 6cqw, 88px); padding-bottom: clamp(32px, 4cqw, 56px)">
	<div class="in" style="display: flex; flex-direction: column; gap: clamp(16px, 2cqw, 28px)">
		<div>
			<div aria-hidden="true" class="fh md-q" style="font-size: clamp(60px, 8.4cqw, 120px); line-height: .62; color: #a67c2e">“</div>
			<h1 class="fh" style="font-size: clamp(40px, 7.2cqw, 104px)"><?php the_title(); ?></h1>
		</div>
	</div>
</header>
<section class="sec" style="padding-top: 0">
	<div class="md-article"><?php the_content(); ?></div>
</section>
