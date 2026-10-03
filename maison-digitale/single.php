<?php
/**
 * Blog note (Article.dc.html). Write notes in the normal WordPress editor;
 * the theme sets them in the article design. Quotes become pull quotes with the gold mark.
 *
 * @package MaisonDigitale
 */

get_header();

while ( have_posts() ) :
	the_post();
	$md_id      = get_the_ID();
	$md_blog    = (int) get_option( 'md_blog_page' );
	$md_blogurl = $md_blog ? get_permalink( $md_blog ) : ( get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' ) );
	$md_thumb   = get_post_thumbnail_id();
	$md_avatar  = get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => 88 ) );
	$md_rtl     = md_is_rtl();
	?>
	<header class="sec" style="padding-top: clamp(40px, 6cqw, 88px); padding-bottom: clamp(32px, 4cqw, 56px)">
		<div class="in" style="display: flex; flex-direction: column; gap: clamp(16px, 2cqw, 24px); max-width: 1040px">
			<a class="lk" href="<?php echo esc_url( $md_blogurl ); ?>" style="align-self: flex-start"><?php echo md_svg( 'arrow-back' ); // phpcs:ignore ?><?php echo esc_html( $md_rtl ? 'كل الملاحظات' : __( 'All notes', 'maison-digitale' ) ); ?></a>
			<p class="ov" style="color: #a67c2e"><?php echo esc_html( md_post_category( $md_id ) ); ?></p>
			<h1 class="fh" style="font-size: clamp(34px, 5.6cqw, 80px)"><?php the_title(); ?></h1>
			<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px 24px">
				<span style="display: flex; align-items: center; gap: 12px">
					<?php if ( $md_avatar && get_option( 'show_avatars' ) ) : ?>
						<img src="<?php echo esc_url( $md_avatar ); ?>" alt="" width="44" height="44" style="width: 44px; height: 44px; object-fit: cover; display: block">
					<?php else : ?>
						<span class="ph" style="width: 44px; height: 44px; padding: 0; font-size: 9px">[Photo]</span>
					<?php endif; ?>
					<span style="font-weight: 600"><?php the_author(); ?></span>
				</span>
				<span class="ov" style="color: #62574d"><?php echo esc_html( get_the_date() ); ?></span>
				<span class="ov" style="color: #62574d"><?php echo esc_html( md_reading_time( $md_id ) . ' ' . ( $md_rtl ? 'دقائق قراءة' : __( 'min read', 'maison-digitale' ) ) ); ?></span>
			</div>
		</div>
	</header>

	<div style="padding-inline: clamp(20px, 5cqw, 80px)">
		<?php echo md_box( $md_thumb ? array( 'id' => $md_thumb, 'url' => wp_get_attachment_image_url( $md_thumb, 'full' ) ) : '', '[Cover image]', 'in rv', 'aspect-ratio: 21 / 9', 'div', get_the_title() ); // phpcs:ignore ?>
	</div>

	<article class="sec" style="padding-top: clamp(40px, 5cqw, 72px)">
		<div class="md-article">
			<?php the_content(); ?>
			<?php
			wp_link_pages(
				array(
					'before' => '<p class="ov">',
					'after'  => '</p>',
				)
			);
			$md_url   = rawurlencode( get_permalink() );
			$md_title = rawurlencode( get_the_title() );
			?>
			<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; border-top: 1px solid #d8cdbc; padding-top: 20px; margin-top: 18px">
				<p class="ov" style="color: #62574d"><?php echo esc_html( $md_rtl ? 'شاركوا هذه الملاحظة' : __( 'Share this note', 'maison-digitale' ) ); ?></p>
				<div style="display: flex; gap: 8px">
					<a class="sh" href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . $md_url ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'maison-digitale' ); ?>"><?php echo md_social_svg( 'linkedin' ); // phpcs:ignore ?></a>
					<a class="sh" href="<?php echo esc_url( 'https://wa.me/?text=' . $md_title . '%20' . $md_url ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'maison-digitale' ); ?>"><?php echo md_social_svg( 'whatsapp' ); // phpcs:ignore ?></a>
					<a class="sh" href="<?php echo esc_url( 'mailto:?subject=' . $md_title . '&body=' . $md_url ); ?>" aria-label="<?php esc_attr_e( 'Share by email', 'maison-digitale' ); ?>"><?php echo md_social_svg( 'email' ); // phpcs:ignore ?></a>
					<button class="sh" type="button" data-md-copy="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php esc_attr_e( 'Copy link', 'maison-digitale' ); ?>"><?php echo md_social_svg( 'link' ); // phpcs:ignore ?></button>
				</div>
			</div>
		</div>
	</article>

	<?php
	$md_cats    = wp_get_post_categories( $md_id );
	$md_related = get_posts(
		array(
			'post__not_in'        => array( $md_id ),
			'posts_per_page'      => 3,
			'category__in'        => $md_cats,
			'ignore_sticky_posts' => true,
		)
	);
	if ( count( $md_related ) < 3 ) {
		$md_more    = get_posts(
			array(
				'post__not_in'        => array_merge( array( $md_id ), wp_list_pluck( $md_related, 'ID' ) ),
				'posts_per_page'      => 3 - count( $md_related ),
				'ignore_sticky_posts' => true,
			)
		);
		$md_related = array_merge( $md_related, $md_more );
	}
	if ( $md_related ) :
		?>
		<section class="sec" style="background: #ece4d6">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(24px, 3cqw, 40px)">
				<div><p class="ov" style="color: #62574d"><?php echo esc_html( $md_rtl ? 'تابعوا القراءة' : __( 'Keep reading', 'maison-digitale' ) ); ?></p><h2 class="fh" style="font-size: clamp(30px, 4cqw, 56px); margin-top: 12px"><?php echo esc_html( $md_rtl ? 'ملاحظات ذات صلة' : __( 'Related notes', 'maison-digitale' ) ); ?></h2></div>
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr)); gap: clamp(28px, 3cqw, 40px) clamp(16px, 2cqw, 28px)">
					<?php
					foreach ( $md_related as $md_r ) {
						md_post_card( $md_r->ID, '26px' );
					}
					?>
				</div>
			</div>
		</section>
		<?php
	endif;

	// Call to action under every note: an Elementor template (Appearance → Maison Digitale).
	if ( ! md_print_template( md_opt( 'article_cta_tpl' ) ) ) {
		md_print_widget(
			'md-cta-band',
			array(
				'heading' => 'Want this applied to your brand? Start with a free audit.',
				'text'    => '',
				'buttons' => array(
					array(
						'_id'   => 'a1',
						'label' => 'Get your free brand audit',
						'style' => 'b-ink',
						'link'  => array( 'url' => md_page_url_by_slug( 'free-brand-audit' ) ),
					),
				),
				'heading_width' => '13em',
				'heading_size'  => 'm',
			)
		);
	}
endwhile;

get_footer();
