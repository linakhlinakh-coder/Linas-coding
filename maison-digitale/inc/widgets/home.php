<?php
/**
 * Homepage panels — Main.dc.html.
 * Each widget is one full-screen snap panel. The side dots, the navigation
 * colour switch and the snapping are handled by maison.js from these panels.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared bits for panels.
 */
abstract class MD_Panel extends MD_Widget {

	protected $md_icon = 'eicon-slider-full-screen';

	public function get_keywords() {
		return array( 'maison', 'home', 'panel', 'snap', 'fullscreen' );
	}

	protected function dot_control( $default ) {
		$this->fields(
			array(
				array( 'text', 'dot', __( 'Side dot label', 'maison-digitale' ), $default, array( 'description' => __( 'Read by screen readers on the side dot navigation.', 'maison-digitale' ) ) ),
				array( 'text', 'anchor', __( 'Anchor id (optional)', 'maison-digitale' ), '', array( 'description' => __( 'Lets other links jump to this panel, e.g. "services".', 'maison-digitale' ) ) ),
			)
		);
	}

	protected function panel_attrs( $s, $dark ) {
		$id = ! empty( $s['anchor'] ) ? sanitize_title( $s['anchor'] ) : 'md-pn-' . $this->get_id();
		return 'id="' . esc_attr( $id ) . '" data-md-panel data-md-tone="' . ( $dark ? 'dark' : 'light' ) . '" data-md-dot="' . esc_attr( $s['dot'] ) . '"';
	}
}

/* =========================================================================
 * Panel 1 — Hero with loader, typed word, showreel
 * ====================================================================== */
class MD_W_Home_Hero extends MD_Panel {

	public function get_name() {
		return 'md-home-hero';
	}

	public function get_title() {
		return __( 'Home — Hero panel', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'text', __( 'Text', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'eyebrow', __( 'Eyebrow', 'maison-digitale' ), 'Creative boutique agency, Lebanon' ),
				array( 'area', 'headline', __( 'Headline', 'maison-digitale' ), 'Your brand deserves a house, not a factory.', array( 'description' => __( 'Wrap words in *asterisks* for gold.', 'maison-digitale' ) ) ),
				array( 'text', 'typed_prefix', __( 'Typed line: fixed start', 'maison-digitale' ), 'We craft' ),
				array( 'area', 'typed_words', __( 'Typed words (one per line)', 'maison-digitale' ), "Digital Marketing\nSocial Media\nContent\nDesign\nFilms\nCopy\nScripts\nCRM Systems\nAI Automation", array( 'rows' => 9 ) ),
				array( 'text', 'cue', __( 'Scroll cue', 'maison-digitale' ), 'What we do' ),
				array( 'url', 'cue_link', __( 'Scroll cue link', 'maison-digitale' ), '', array( 'description' => __( 'Leave empty to go to the next panel.', 'maison-digitale' ) ) ),
			)
		);
		$this->end();

		$this->sec( 'video', __( 'Background video', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'media', 'bg_video', __( 'Looping video (MP4, desktop)', 'maison-digitale' ), '', array( 'media_types' => array( 'video' ) ) ),
				array( 'text', 'bg_video_url', __( '…or video file URL', 'maison-digitale' ), '' ),
				array( 'media', 'poster', __( 'Poster image (mobile, and while the video loads)', 'maison-digitale' ), '' ),
				array( 'number', 'dim', __( 'Darken the video (%)', 'maison-digitale' ), 40, array( 'min' => 0, 'max' => 90, 'description' => __( 'A flat black layer so the headline stays readable over footage.', 'maison-digitale' ) ) ),
				array( 'text', 'ph_d', __( 'Placeholder while no video is set', 'maison-digitale' ), '[Looping muted showreel video, full screen — to be supplied]' ),
				array( 'text', 'ph_m', __( 'Placeholder while no poster is set (mobile)', 'maison-digitale' ), '[Poster image on mobile — to be supplied]' ),
			)
		);
		$this->end();

		$this->sec( 'reel', __( 'Showreel player', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'switch', 'show_reel', __( 'Show the showreel button', 'maison-digitale' ), 'yes' ),
				array( 'text', 'reel_label', __( 'Button label', 'maison-digitale' ), 'Play our showreel' ),
				array( 'media', 'reel_video', __( 'Showreel video file', 'maison-digitale' ), '', array( 'media_types' => array( 'video' ) ) ),
				array( 'text', 'reel_url', __( '…or YouTube / Vimeo / video URL', 'maison-digitale' ), '' ),
				array( 'text', 'reel_ph', __( 'Placeholder while no video is set', 'maison-digitale' ), '[Showreel video player, with sound and controls — video to be supplied]' ),
				array( 'text', 'reel_close', __( 'Close label', 'maison-digitale' ), 'Close' ),
				array( 'text', 'reel_aria', __( 'Player name (screen readers)', 'maison-digitale' ), 'Showreel' ),
			)
		);
		$this->end();

		$this->sec( 'loader', __( 'Loading screen', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'switch', 'loader', __( 'Show the loading screen', 'maison-digitale' ), 'yes', array( 'description' => __( 'The wordmark appears in outline, fills in, then lifts away. Never shown to visitors who prefer reduced motion.', 'maison-digitale' ) ) ),
				array( 'text', 'ld1', __( 'Wordmark line 1', 'maison-digitale' ), 'Maison' ),
				array( 'text', 'ld2', __( 'Wordmark line 2', 'maison-digitale' ), 'Digitale' ),
			)
		);
		$this->end();

		$this->sec( 'panel', __( 'Panel', 'maison-digitale' ) );
		$this->dot_control( 'Welcome' );
		$this->end();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$loader = 'yes' === $s['loader'] && ! md_is_editor();
		$words  = md_lines( $s['typed_words'] );
		$first  = $words ? $words[0] : '';
		$video  = md_media_url( $s['bg_video'] );
		$video  = $video ? $video : trim( (string) $s['bg_video_url'] );
		$poster = md_media_url( $s['poster'] );
		$reel   = md_media_url( $s['reel_video'] );
		$reel   = $reel ? $reel : trim( (string) $s['reel_url'] );
		$dim    = max( 0, min( 90, (int) $s['dim'] ) ) / 100;
		$uid    = 'md-reel-' . $this->get_id();
		$d      = function ( $sec ) {
			return 'animation-delay: calc(var(--md-ld, 2.6s) + ' . $sec . 's)';
		};
		$style  = 'background: #1f1915; color: #f6f1e9; display: flex; flex-direction: column; justify-content: flex-end; padding: 140px clamp(24px, 5cqw, 80px) clamp(28px, 3.4cqw, 48px)';
		if ( ! $loader ) {
			$style .= '; --md-ld: 0s';
		}
		$cue = md_url( $s['cue_link'] );
		?>
		<?php if ( $loader ) : ?>
			<div class="md-ld" aria-hidden="true">
				<div class="ld-in">
					<div class="q">“</div>
					<div class="w"><?php echo esc_html( $s['ld1'] ); ?><br><?php echo esc_html( $s['ld2'] ); ?></div>
				</div>
			</div>
		<?php endif; ?>
		<section class="pn dk hero" <?php echo $this->panel_attrs( $s, true ); // phpcs:ignore ?> style="<?php echo esc_attr( $style ); ?>">
			<?php if ( $video ) : ?>
				<div class="bgm vid-d" aria-hidden="true">
					<video src="<?php echo esc_url( $video ); ?>" <?php echo $poster ? 'poster="' . esc_url( $poster ) . '"' : ''; ?> autoplay muted loop playsinline preload="auto" data-md-bgvideo></video>
					<?php if ( $dim > 0 ) : ?><span class="dim" style="opacity: <?php echo esc_attr( $dim ); ?>"></span><?php endif; ?>
				</div>
			<?php else : ?>
				<div class="hph vid-d" style="inset-inline-end: clamp(24px, 5cqw, 80px)"><?php echo esc_html( $s['ph_d'] ); ?></div>
			<?php endif; ?>
			<?php if ( $poster ) : ?>
				<div class="bgm vid-m" aria-hidden="true">
					<img src="<?php echo esc_url( $poster ); ?>" alt="">
					<?php if ( $dim > 0 ) : ?><span class="dim" style="opacity: <?php echo esc_attr( $dim ); ?>"></span><?php endif; ?>
				</div>
			<?php else : ?>
				<div class="hph vid-m" style="inset-inline-end: 20px"><?php echo esc_html( $s['ph_m'] ); ?></div>
			<?php endif; ?>

			<div class="hc" style="display: flex; flex-direction: column; gap: clamp(14px, 1.7cqw, 24px); max-width: 1000px">
				<?php if ( $s['eyebrow'] ) : ?>
					<p class="ov h-in" style="color: #b9ad9d; <?php echo esc_attr( $d( '.3' ) ); ?>"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>
				<div class="h-in" style="<?php echo esc_attr( $d( '.45' ) ); ?>">
					<?php echo $this->q( 'font-size: clamp(56px, 7.4cqw, 106px); line-height: .62; color: #c9a24f' ); // phpcs:ignore ?>
					<h1 class="fh" style="font-size: clamp(38px, 6cqw, 86px)"><?php echo md_hl( $s['headline'] ); // phpcs:ignore ?></h1>
				</div>
				<?php if ( $s['typed_prefix'] || $words ) : ?>
					<p class="h-in ty" style="margin: 0; font-size: clamp(20px, 2.3cqw, 33px); line-height: 1.3; font-weight: 500; <?php echo esc_attr( $d( '.65' ) ); ?>"><?php echo esc_html( $s['typed_prefix'] ); ?> <span style="color: #c9a24f" data-md-typed="<?php echo esc_attr( wp_json_encode( $words ) ); ?>"><?php echo esc_html( $first ); ?></span><span class="caret" aria-hidden="true"></span></p>
				<?php endif; ?>
			</div>
			<div class="h-in hc" style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 20px; margin-top: clamp(24px, 3.4cqw, 48px); <?php echo esc_attr( $d( '.8' ) ); ?>">
				<?php if ( 'yes' === $s['show_reel'] ) : ?>
					<button class="reel" type="button" aria-haspopup="dialog" aria-controls="<?php echo esc_attr( $uid ); ?>" data-md-reel-open="<?php echo esc_attr( $uid ); ?>">
						<span class="sq"><?php echo md_svg( 'play' ); // phpcs:ignore ?></span>
						<?php echo esc_html( $s['reel_label'] ); ?>
					</button>
				<?php else : ?>
					<span></span>
				<?php endif; ?>
				<?php if ( $s['cue'] ) : ?>
					<a class="cue ov" href="<?php echo $cue ? esc_url( $cue ) : '#'; ?>" <?php echo $cue ? '' : 'data-md-next'; ?>><?php echo esc_html( $s['cue'] ); ?><i aria-hidden="true"></i></a>
				<?php endif; ?>
			</div>
		</section>
		<?php if ( 'yes' === $s['show_reel'] ) : ?>
			<div class="md-reel dk" id="<?php echo esc_attr( $uid ); ?>" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $s['reel_aria'] ); ?>" hidden data-md-reel data-src="<?php echo esc_attr( $reel ); ?>">
				<button class="bt b-line rl-x" type="button" data-md-reel-close><?php echo md_svg( 'close-s' ); // phpcs:ignore ?><?php echo esc_html( $s['reel_close'] ); ?></button>
				<div class="rl-v"><?php echo $reel ? '' : esc_html( $s['reel_ph'] ); ?></div>
			</div>
		<?php endif; ?>
		<?php
	}
}

/* =========================================================================
 * Panels 2–10 — one service per panel
 * ====================================================================== */
class MD_W_Home_Service extends MD_Panel {

	public function get_name() {
		return 'md-home-service';
	}

	public function get_title() {
		return __( 'Home — Service panel', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'text', __( 'Text', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), '01 — Service' ),
				array( 'area', 'title', __( 'Service name', 'maison-digitale' ), 'Digital Marketing' ),
				array( 'area', 'lines', __( 'Two or three short lines', 'maison-digitale' ), "Strategy, campaigns and growth, measured.\nPaid campaigns, search, email and reporting." ),
				array( 'text', 'feat_l', __( 'Featured project label', 'maison-digitale' ), 'Featured project' ),
				array( 'text', 'feat', __( 'Featured project / client', 'maison-digitale' ), '[project / client]' ),
				array( 'media', 'client_logo', __( 'Client logo', 'maison-digitale' ), '' ),
				array( 'text', 'logo_ph', __( 'Placeholder while no logo is set', 'maison-digitale' ), '[client logo]' ),
			)
		);
		$this->end();

		$this->sec( 'links', __( 'Buttons', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'btn', __( 'Button', 'maison-digitale' ), 'View case study' ),
				array( 'url', 'btn_link', __( 'Button link', 'maison-digitale' ), '/portfolio/case-study/' ),
				array( 'text', 'lnk', __( 'Text link', 'maison-digitale' ), 'Explore the service' ),
				array( 'url', 'lnk_link', __( 'Text link URL', 'maison-digitale' ), '/services/digital-marketing/' ),
			)
		);
		$this->end();

		$this->sec( 'look', __( 'Image and layout', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'media', 'image', __( 'Full-bleed image or video', 'maison-digitale' ), '', array( 'media_types' => array( 'image', 'video' ) ) ),
				array( 'text', 'ph', __( 'Placeholder while no image is set', 'maison-digitale' ), '[Full-bleed image — Digital Marketing project]' ),
				array(
					'select',
					'tone',
					__( 'Tone', 'maison-digitale' ),
					'light',
					array(
						'options' => array(
							'light' => __( 'Light (ivory text block, ink navigation)', 'maison-digitale' ),
							'dark'  => __( 'Dark (black text block, ivory navigation)', 'maison-digitale' ),
						),
						'description' => __( 'Alternate light and dark panels so the navigation switch stays visible.', 'maison-digitale' ),
					),
				),
				array(
					'select',
					'side',
					__( 'Text block side', 'maison-digitale' ),
					'start',
					array(
						'options' => array(
							'start' => __( 'Left (right in Arabic)', 'maison-digitale' ),
							'end'   => __( 'Right (left in Arabic)', 'maison-digitale' ),
						),
					),
				),
			)
		);
		$this->end();

		$this->sec( 'panel', __( 'Panel', 'maison-digitale' ) );
		$this->dot_control( 'Digital Marketing' );
		$this->end();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$dark  = 'dark' === $s['tone'];
		$start = 'end' !== $s['side'];
		$img   = md_media_url( $s['image'] );
		$logo  = md_media_url( $s['client_logo'] );
		$mut   = $dark ? '#b9ad9d' : '#62574d';
		$style = $dark ? 'background: #3d3324; color: #f6f1e9; justify-content: ' . ( $start ? 'flex-start' : 'flex-end' ) : 'background: #e3d3ad; color: #17120f; justify-content: ' . ( $start ? 'flex-start' : 'flex-end' );
		?>
		<section class="pn sv<?php echo $dark ? ' dk' : ''; ?>" <?php echo $this->panel_attrs( $s, $dark ); // phpcs:ignore ?> style="<?php echo esc_attr( $style ); ?>">
			<?php if ( $img ) : ?>
				<div class="bgm" aria-hidden="true">
					<?php if ( preg_match( '/\.(mp4|webm|mov)(\?.*)?$/i', $img ) ) : ?>
						<video src="<?php echo esc_url( $img ); ?>" autoplay muted loop playsinline preload="metadata"></video>
					<?php else : ?>
						<img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy">
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="hph" style="<?php echo $start ? 'inset-inline-end' : 'inset-inline-start'; ?>: clamp(24px, 5cqw, 80px)"><?php echo esc_html( $s['ph'] ); ?></div>
			<?php endif; ?>
			<div class="tx" style="background: <?php echo $dark ? '#17120f' : '#f6f1e9'; ?>">
				<p class="ov rv" style="color: <?php echo esc_attr( $mut ); ?>"><?php echo esc_html( $s['label'] ); ?></p>
				<h2 class="fh rv" style="font-size: clamp(34px, 4.2cqw, 60px)"><?php echo md_hl( $s['title'] ); // phpcs:ignore ?></h2>
				<p class="rv rv2 lines" style="margin: 0; font-size: clamp(18px, 1.6cqw, 22px); line-height: 1.45; max-width: 30em"><?php echo md_br( $s['lines'] ); // phpcs:ignore ?></p>
				<?php if ( $s['feat'] || $logo || $s['logo_ph'] ) : ?>
					<div class="ft-row rv rv2" style="border-color: <?php echo $dark ? '#3b322a' : '#d8cdbc'; ?>">
						<div><p class="ov" style="color: <?php echo esc_attr( $mut ); ?>"><?php echo esc_html( $s['feat_l'] ); ?></p><p style="margin: 6px 0 0; font-size: 17px; font-weight: 500"><?php echo esc_html( $s['feat'] ); ?></p></div>
						<?php if ( $logo ) : ?>
							<img class="cl-logo" src="<?php echo esc_url( $logo ); ?>" alt="">
						<?php elseif ( $s['logo_ph'] ) : ?>
							<div class="logo-ph"><?php echo esc_html( $s['logo_ph'] ); ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="rv rv3" style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px 28px">
					<?php if ( $s['btn'] ) : ?>
						<a class="bt b-ink" <?php echo md_href( $s['btn_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['btn'] ); ?></a>
					<?php endif; ?>
					<?php if ( $s['lnk'] ) : ?>
						<a class="lk" <?php echo md_href( $s['lnk_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['lnk'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Panel 11 — About teaser
 * ====================================================================== */
class MD_W_Home_About extends MD_Panel {

	public function get_name() {
		return 'md-home-about';
	}

	public function get_title() {
		return __( 'Home — About panel', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'text', __( 'Text', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'eyebrow', __( 'Overline', 'maison-digitale' ), 'About the house' ),
				array( 'area', 'headline', __( 'Headline', 'maison-digitale' ), 'A boutique house for brands with *something to say.*', array( 'description' => __( 'Wrap words in *asterisks* for gold.', 'maison-digitale' ) ) ),
				array( 'area', 'text', __( 'Text', 'maison-digitale' ), 'Maison Digitale is a small senior team in Lebanon that plans, writes, designs, films and automates for brands. One house, every craft, in English and Arabic.' ),
				array(
					'rep',
					'stats',
					__( 'Figures', 'maison-digitale' ),
					$this->rows( array( 'value', 'label' ), array( array( '[00]', 'Years of experience' ), array( '[00]', 'Brands served' ), array( '[00]', 'Awards' ) ) ),
					array(
						'title'  => '{{{ value }}} {{{ label }}}',
						'fields' => array(
							array( 'text', 'value', __( 'Figure', 'maison-digitale' ), '' ),
							array( 'text', 'label', __( 'Label', 'maison-digitale' ), '' ),
						),
					),
				),
				array( 'text', 'btn', __( 'Button', 'maison-digitale' ), 'About us' ),
				array( 'url', 'btn_link', __( 'Button link', 'maison-digitale' ), '/about/' ),
			)
		);
		$this->end();
		$this->sec( 'panel', __( 'Panel', 'maison-digitale' ) );
		$this->dot_control( 'About' );
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="pn dk pn-about" <?php echo $this->panel_attrs( $s, true ); // phpcs:ignore ?> style="background: #17120f; color: #f6f1e9; display: flex; flex-direction: column; justify-content: flex-end; gap: clamp(24px, 3.4cqw, 48px); padding: 130px clamp(24px, 5cqw, 80px) clamp(40px, 5cqw, 72px)">
			<div style="display: flex; flex-direction: column; gap: clamp(14px, 1.7cqw, 24px); max-width: 1080px">
				<p class="ov rv" style="color: #b9ad9d"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<h2 class="fh rv" style="font-size: clamp(34px, 5.2cqw, 76px)"><?php echo md_hl( $s['headline'] ); // phpcs:ignore ?></h2>
				<p class="rv rv2 lines" style="margin: 0; font-size: clamp(18px, 1.6cqw, 22px); line-height: 1.5; max-width: 34em; color: #f6f1e9"><?php echo md_br( $s['text'] ); // phpcs:ignore ?></p>
			</div>
			<?php if ( ! empty( $s['stats'] ) ) : ?>
				<div class="rv rv2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px clamp(20px, 3cqw, 48px); border-top: 1px solid #3b322a; padding-top: 20px; max-width: 900px">
					<?php foreach ( $s['stats'] as $st ) : ?>
						<div><div class="fh" style="font-size: clamp(30px, 3.4cqw, 48px); color: #c9a24f"><?php echo esc_html( $st['value'] ); ?></div><p class="ov" style="color: #b9ad9d; margin-top: 8px"><?php echo esc_html( $st['label'] ); ?></p></div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ( $s['btn'] ) : ?>
				<div class="rv rv3"><a class="bt b-line" <?php echo md_href( $s['btn_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['btn'] ); ?></a></div>
			<?php endif; ?>
		</section>
		<?php
	}
}

/* =========================================================================
 * Panel 12 — Free brand audit (gold)
 * ====================================================================== */
class MD_W_Home_Audit extends MD_Panel {

	public function get_name() {
		return 'md-home-audit';
	}

	public function get_title() {
		return __( 'Home — Brand audit panel', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'text', __( 'Text', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'eyebrow', __( 'Overline', 'maison-digitale' ), 'Free brand audit' ),
				array( 'area', 'headline', __( 'Headline', 'maison-digitale' ), 'Not sure where your brand stands? Find out, free.' ),
				array( 'area', 'text', __( 'Text', 'maison-digitale' ), 'Answer twelve questions and get your brand score in three minutes: identity, social presence, website and content.' ),
				array( 'text', 'btn', __( 'Button', 'maison-digitale' ), 'Get your free brand audit' ),
				array( 'url', 'btn_link', __( 'Button link', 'maison-digitale' ), '/free-brand-audit/' ),
			)
		);
		$this->end();
		$this->sec( 'panel', __( 'Panel', 'maison-digitale' ) );
		$this->dot_control( 'Free brand audit' );
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="pn gd pn-audit" <?php echo $this->panel_attrs( $s, false ); // phpcs:ignore ?> style="background: #c9a24f; color: #17120f; display: flex; flex-direction: column; justify-content: flex-end; gap: clamp(16px, 2cqw, 28px); padding: 130px clamp(24px, 5cqw, 80px) clamp(40px, 5cqw, 72px)">
			<p class="ov rv"><?php echo esc_html( $s['eyebrow'] ); ?></p>
			<div class="rv">
				<?php echo $this->q( 'font-size: clamp(56px, 7.4cqw, 106px); line-height: .62' ); // phpcs:ignore ?>
				<h2 class="fh" style="font-size: clamp(34px, 5.6cqw, 80px); max-width: 15em"><?php echo md_hl( $s['headline'] ); // phpcs:ignore ?></h2>
			</div>
			<p class="rv rv2 lines" style="margin: 0; font-size: clamp(18px, 1.6cqw, 22px); line-height: 1.5; max-width: 32em"><?php echo md_br( $s['text'] ); // phpcs:ignore ?></p>
			<?php if ( $s['btn'] ) : ?>
				<div class="rv rv3"><a class="bt b-ink" <?php echo md_href( $s['btn_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['btn'] ); ?></a></div>
			<?php endif; ?>
		</section>
		<?php
	}
}

/* =========================================================================
 * Panel 13 — Closing call to action with the footer attached
 * ====================================================================== */
class MD_W_Home_Closing extends MD_Panel {

	public function get_name() {
		return 'md-home-closing';
	}

	public function get_title() {
		return __( 'Home — Closing panel with footer', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'text', __( 'Text', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'area', 'headline', __( 'Headline', 'maison-digitale' ), 'Let’s work *together.*', array( 'description' => __( 'Wrap words in *asterisks* for gold.', 'maison-digitale' ) ) ),
				array( 'area', 'text', __( 'Line', 'maison-digitale' ), 'Like what you see? Tell us about your project.' ),
				array( 'text', 'btn', __( 'Button', 'maison-digitale' ), 'Start a project' ),
				array( 'url', 'btn_link', __( 'Button link', 'maison-digitale' ), '/lets-work-together/' ),
				array( 'switch', 'footer', __( 'Attach the site footer at the bottom of this panel', 'maison-digitale' ), 'yes', array( 'description' => __( 'The footer itself is edited in its own template (Templates → Saved Templates → Site Footer).', 'maison-digitale' ) ) ),
			)
		);
		$this->end();
		$this->sec( 'panel', __( 'Panel', 'maison-digitale' ) );
		$this->dot_control( "Let's work together" );
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="pn dk pn-close" <?php echo $this->panel_attrs( $s, true ); // phpcs:ignore ?> style="background: #17120f; color: #f6f1e9; display: flex; flex-direction: column">
			<div style="flex: 1 1 auto; display: flex; flex-direction: column; justify-content: flex-end; gap: clamp(12px, 1.4cqw, 20px); padding: 110px clamp(24px, 5cqw, 80px) clamp(24px, 3cqw, 44px)">
				<h2 class="fh rv" style="font-size: clamp(38px, 6.6cqw, 96px)"><?php echo md_hl( $s['headline'] ); // phpcs:ignore ?></h2>
				<div class="rv rv2" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px 32px">
					<p class="lines" style="margin: 0; font-size: clamp(18px, 1.6cqw, 22px); line-height: 1.5"><?php echo md_br( $s['text'] ); // phpcs:ignore ?></p>
					<?php if ( $s['btn'] ) : ?>
						<a class="bt b-gold" <?php echo md_href( $s['btn_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['btn'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( 'yes' === $s['footer'] ) : ?>
				<div style="border-top: 1px solid #3b322a">
					<?php md_full_footer(); ?>
				</div>
			<?php endif; ?>
		</section>
		<?php
	}
}
