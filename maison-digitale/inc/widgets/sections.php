<?php
/**
 * Inner page sections: About, Services, Service, Portfolio, Case study,
 * Blog, Article, FAQ and Contact designs.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * Page hero — the opening of every light inner page
 * ====================================================================== */
class MD_W_Page_Hero extends MD_Widget {
	protected $md_icon = 'eicon-header';

	public function get_name() {
		return 'md-page-hero';
	}

	public function get_title() {
		return __( 'Page hero', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'eyebrow', __( 'Overline', 'maison-digitale' ), 'About us' ),
				array( 'switch', 'quote', __( 'Gold quotation mark', 'maison-digitale' ), 'yes' ),
				array( 'area', 'headline', __( 'Headline', 'maison-digitale' ), 'The house behind the work.', array( 'description' => __( 'Wrap words in *asterisks* for gold.', 'maison-digitale' ) ) ),
				array( 'area', 'lead', __( 'Intro paragraph', 'maison-digitale' ), 'Maison Digitale is a creative boutique agency in Lebanon. A small senior team that tells brand stories and builds the systems that carry them.' ),
			)
		);
		$this->buttons_control( array() );
		$this->end();

		$this->sec( 'l', __( 'Layout', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'select',
					'size',
					__( 'Headline size', 'maison-digitale' ),
					'l',
					array(
						'options' => array(
							'm'   => 'M — 96px',
							'l'   => 'L — 104px',
							'xl'  => 'XL — 120px',
							'xxl' => 'XXL — 132px',
						),
					),
				),
				array( 'text', 'max', __( 'Headline max width (e.g. 12em)', 'maison-digitale' ), '' ),
				array(
					'select',
					'pbv',
					__( 'Space below', 'maison-digitale' ),
					'large',
					array(
						'options' => array(
							'normal' => __( 'Normal', 'maison-digitale' ),
							'large'  => __( 'Large', 'maison-digitale' ),
						),
					),
				),
			)
		);
		$this->tone_control( 'ground', array( 'ground', 'dark' ) );
		$this->end();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$dark  = 'dark' === $s['tone'];
		$sizes = array(
			'm'   => 'clamp(38px, 6.6cqw, 96px)',
			'l'   => 'clamp(40px, 7.2cqw, 104px)',
			'xl'  => 'clamp(44px, 8.4cqw, 120px)',
			'xxl' => 'clamp(48px, 9cqw, 132px)',
		);
		$size  = isset( $sizes[ $s['size'] ] ) ? $sizes[ $s['size'] ] : $sizes['l'];
		$pb    = 'large' === $s['pbv'] ? 'clamp(40px, 5cqw, 72px)' : 'clamp(32px, 4cqw, 56px)';
		$hstyle = 'font-size: ' . $size . ( $s['max'] ? '; max-width: ' . $s['max'] : '' );
		?>
		<header class="sec<?php echo $dark ? ' dk' : ''; ?>" style="padding-top: clamp(40px, 6cqw, 88px); padding-bottom: <?php echo esc_attr( $pb ); ?><?php echo $dark ? '; background: #17120f; color: #f6f1e9' : ''; ?>">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(16px, 2cqw, 28px)">
				<?php if ( $s['eyebrow'] ) : ?>
					<p class="ov" style="color: <?php echo $dark ? '#b9ad9d' : '#62574d'; ?>"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>
				<div>
					<?php
					if ( 'yes' === $s['quote'] ) {
						echo $this->q( 'font-size: clamp(60px, 8.4cqw, 120px); line-height: .62; color: ' . ( $dark ? '#c9a24f' : '#a67c2e' ) ); // phpcs:ignore
					}
					?>
					<h1 class="fh" style="<?php echo esc_attr( $hstyle ); ?>"><?php echo md_hl( $s['headline'] ); // phpcs:ignore ?></h1>
				</div>
				<?php if ( $s['lead'] ) : ?>
					<p style="margin: 0; font-size: clamp(19px, 1.7cqw, 24px); line-height: 1.5; max-width: <?php echo $dark ? '30em' : '34em'; ?>"><?php echo md_br( $s['lead'] ); // phpcs:ignore ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $s['buttons'] ) ) : ?>
					<div style="display: flex; flex-wrap: wrap; gap: 12px"><?php echo $this->buttons( $s['buttons'] ); // phpcs:ignore ?></div>
				<?php endif; ?>
			</div>
		</header>
		<?php
	}
}

/* =========================================================================
 * Dark hero — Service page and Case study header
 * ====================================================================== */
class MD_W_Dark_Hero extends MD_Widget {
	protected $md_icon = 'eicon-image-rollover';

	public function get_name() {
		return 'md-dark-hero';
	}

	public function get_title() {
		return __( 'Image hero (service, case study)', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), 'Service 01' ),
				array( 'area', 'title', __( 'Title', 'maison-digitale' ), 'Digital Marketing' ),
				array( 'area', 'promise', __( 'One-line promise', 'maison-digitale' ), 'Strategy and campaigns that turn attention into growth.' ),
				array( 'media', 'media', __( 'Background image or short loop', 'maison-digitale' ), '', array( 'media_types' => array( 'image', 'video' ) ) ),
				array( 'text', 'ph', __( 'Placeholder while no image is set', 'maison-digitale' ), '[Background image or short loop — to be supplied]' ),
				array(
					'select',
					'variant',
					__( 'Proportions', 'maison-digitale' ),
					'service',
					array(
						'options' => array(
							'service' => __( 'Service page', 'maison-digitale' ),
							'case'    => __( 'Case study', 'maison-digitale' ),
						),
					),
				),
				array( 'note', 'n', __( 'Set the page navigation to "Over a dark hero" in the page settings so the menu sits on this image.', 'maison-digitale' ) ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$case = 'case' === $s['variant'];
		$img  = md_media_url( $s['media'] );
		?>
		<header class="dk" style="background: #3d3324; color: #f6f1e9; position: relative; display: flex; flex-direction: column; min-height: <?php echo $case ? 'clamp(560px, 56cqw, 820px)' : 'clamp(520px, 52cqw, 760px)'; ?>; padding-top: 88px; box-sizing: border-box; overflow: hidden">
			<?php if ( $img ) : ?>
				<div class="bgm" aria-hidden="true" style="position: absolute; inset: 0">
					<?php if ( preg_match( '/\.(mp4|webm|mov)(\?.*)?$/i', $img ) ) : ?>
						<video src="<?php echo esc_url( $img ); ?>" autoplay muted loop playsinline style="width: 100%; height: 100%; object-fit: cover; display: block"></video>
					<?php else : ?>
						<img src="<?php echo esc_url( $img ); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block">
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div style="position: absolute; top: 108px; inset-inline-end: clamp(20px, 5cqw, 80px); font: 500 13px/20px 'Instrument Sans', sans-serif; border: 1px dashed #b9ad9d; color: #b9ad9d; padding: 8px 12px"><?php echo esc_html( $s['ph'] ); ?></div>
			<?php endif; ?>
			<div style="position: relative; margin-top: auto; background: #17120f; width: min(100%, <?php echo $case ? '900px' : '860px'; ?>); box-sizing: border-box; padding: clamp(28px, 4cqw, 56px) clamp(20px, 5cqw, 80px)">
				<p class="ov" style="color: #c9a24f"><?php echo esc_html( $s['label'] ); ?></p>
				<h1 class="fh" style="font-size: clamp(38px, 6cqw, 88px); margin-top: 14px"><?php echo md_hl( $s['title'] ); // phpcs:ignore ?></h1>
				<?php if ( $s['promise'] ) : ?>
					<p style="margin: 18px 0 0; font-size: clamp(19px, 1.7cqw, 24px); line-height: 1.45; max-width: 26em"><?php echo md_br( $s['promise'] ); // phpcs:ignore ?></p>
				<?php endif; ?>
			</div>
		</header>
		<?php
	}
}

/* =========================================================================
 * Wide image band — studio photo, map, article cover
 * ====================================================================== */
class MD_W_Image_Band extends MD_Widget {
	protected $md_icon = 'eicon-image';

	public function get_name() {
		return 'md-image-band';
	}

	public function get_title() {
		return __( 'Wide image', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'media', 'media', __( 'Image or video', 'maison-digitale' ), '', array( 'media_types' => array( 'image', 'video' ) ) ),
				array( 'text', 'embed', __( 'Or embed URL (e.g. Google Maps embed link)', 'maison-digitale' ), '' ),
				array( 'text', 'alt', __( 'Alt text', 'maison-digitale' ), '' ),
				array( 'text', 'ph', __( 'Placeholder while nothing is set', 'maison-digitale' ), '[Wide photograph of the studio or the team at work — to be supplied]' ),
				array(
					'select',
					'h',
					__( 'Height', 'maison-digitale' ),
					'tall',
					array(
						'options' => array(
							'tall'  => __( 'Tall (about page)', 'maison-digitale' ),
							'map'   => __( 'Medium (contact map)', 'maison-digitale' ),
							'cover' => __( 'Cinema 21:9 (article cover)', 'maison-digitale' ),
						),
					),
				),
			)
		);
		$this->reveal_control();
		$this->end();
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$map = array(
			'tall'  => 'height: clamp(220px, 38cqw, 520px)',
			'map'   => 'height: clamp(220px, 30cqw, 420px)',
			'cover' => 'aspect-ratio: 21 / 9',
		);
		$st  = isset( $map[ $s['h'] ] ) ? $map[ $s['h'] ] : $map['tall'];
		echo '<div style="padding-inline: clamp(20px, 5cqw, 80px)">';
		if ( ! empty( $s['embed'] ) ) {
			echo '<div class="in ph has-media ' . esc_attr( $this->rv( $s ) ) . '" style="' . esc_attr( $st ) . '"><iframe src="' . esc_url( $s['embed'] ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="' . esc_attr( $s['alt'] ? $s['alt'] : 'Map' ) . '"></iframe></div>';
		} else {
			echo md_box( $s['media'], $s['ph'], 'in ' . $this->rv( $s ), $st, 'div', $s['alt'] ); // phpcs:ignore
		}
		echo '</div>';
	}
}

/* =========================================================================
 * Split text — label and heading on one side, text on the other
 * (About "Our story", Service "What it is", Case study "The brief")
 * ====================================================================== */
class MD_W_Split_Text extends MD_Widget {
	protected $md_icon = 'eicon-column';

	public function get_name() {
		return 'md-split-text';
	}

	public function get_title() {
		return __( 'Split text', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), '01' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'Our story' ),
				array( 'switch', 'quote', __( 'Gold quotation mark under the overline', 'maison-digitale' ), '' ),
				array(
					'rep',
					'paras',
					__( 'Paragraphs', 'maison-digitale' ),
					array(
						array( 'text' => 'Maison is the French word for house. In fashion and craft, a maison is a small workshop where a few experienced people make things properly, for clients they know by name.', 'style' => 'lead' ),
						array( 'text' => 'We built Maison Digitale on that idea: craft, care, and a senior team that stays close to the work. No account layers, no production line.', 'style' => 'body' ),
						array( 'text' => '[Story copy to be supplied: why the agency was founded, when, and what it set out to change.]', 'style' => 'muted' ),
					),
					array(
						'title'  => '{{{ text.substring(0, 50) }}}',
						'fields' => array(
							array( 'area', 'text', __( 'Text', 'maison-digitale' ), '', array( 'rows' => 5 ) ),
							array(
								'select',
								'style',
								__( 'Style', 'maison-digitale' ),
								'body',
								array(
									'options' => array(
										'xl'    => __( 'Statement (largest)', 'maison-digitale' ),
										'lead'  => __( 'Lead', 'maison-digitale' ),
										'body'  => __( 'Body', 'maison-digitale' ),
										'muted' => __( 'Body, muted', 'maison-digitale' ),
									),
								),
							),
						),
					),
				),
			)
		);
		$this->end();
		$this->sec( 'l', __( 'Layout', 'maison-digitale' ) );
		$this->tone_control( 'ground', array( 'ground', 'surface', 'dark' ) );
		$this->spacing_control();
		$this->end();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$tone  = $this->tone( $s['tone'] );
		$dark  = 'dark' === $s['tone'];
		$paras = (array) $s['paras'];
		$one   = 1 === count( $paras );
		$sty   = array(
			'xl'    => 'margin: 0; font-size: clamp(20px, 2.1cqw, 30px); line-height: 1.45',
			'lead'  => 'margin: 0; font-size: clamp(19px, 1.7cqw, 24px); line-height: 1.5',
			'body'  => 'margin: 0',
			'muted' => 'margin: 0; color: ' . ( $dark ? '#b9ad9d' : '#62574d' ),
		);
		$maxw  = array(
			'xl'   => '30em',
			'lead' => '34em',
		);
		?>
		<section class="sec <?php echo esc_attr( $tone[0] ); ?>" style="<?php echo esc_attr( $tone[1] . $this->pad( $s ) ); ?>">
			<div class="in" style="display: flex; flex-wrap: wrap; gap: clamp(24px, 5cqw, 80px)">
				<div style="flex: 1 1 280px">
					<?php if ( $s['label'] ) : ?>
						<p class="ov" style="color: <?php echo esc_attr( $this->muted( $s['tone'] ) ); ?>"><?php echo esc_html( $s['label'] ); ?></p>
					<?php endif; ?>
					<?php if ( 'yes' === $s['quote'] ) : ?>
						<?php echo $this->q( 'font-size: clamp(56px, 6cqw, 88px); line-height: .62; color: ' . ( $dark ? '#c9a24f' : '#a67c2e' ) . '; margin-top: 28px' ); // phpcs:ignore ?>
					<?php endif; ?>
					<?php if ( $s['title'] ) : ?>
						<h2 class="fh rv" style="font-size: clamp(32px, 4.4cqw, 64px); margin-top: 12px"><?php echo md_hl( $s['title'] ); // phpcs:ignore ?></h2>
					<?php endif; ?>
				</div>
				<?php
				if ( $one ) :
					$p  = $paras[0];
					$st = isset( $sty[ $p['style'] ] ) ? $sty[ $p['style'] ] : $sty['body'];
					$mw = isset( $maxw[ $p['style'] ] ) ? $maxw[ $p['style'] ] : '40em';
					?>
					<p class="rv" style="flex: 2 1 420px; <?php echo esc_attr( $st ); ?>; max-width: <?php echo esc_attr( $mw ); ?>"><?php echo md_br( $p['text'] ); // phpcs:ignore ?></p>
				<?php else : ?>
					<div class="rv" style="flex: 2 1 420px; display: flex; flex-direction: column; gap: 20px; max-width: 40em">
						<?php foreach ( $paras as $p ) : ?>
							<p style="<?php echo esc_attr( isset( $sty[ $p['style'] ] ) ? $sty[ $p['style'] ] : $sty['body'] ); ?>"><?php echo md_br( $p['text'] ); // phpcs:ignore ?></p>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Statements — About "What we believe"
 * ====================================================================== */
class MD_W_Statements extends MD_Widget {
	protected $md_icon = 'eicon-bullet-list';

	public function get_name() {
		return 'md-statements';
	}

	public function get_title() {
		return __( 'Numbered statements', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), '02' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'What we believe' ),
				array(
					'rep',
					'items',
					__( 'Statements', 'maison-digitale' ),
					$this->rows( array( 'text' ), array( 'Story first.', 'Craft over volume.', 'Technology in service of ideas.', 'Built for Lebanon and the region, in English and Arabic.' ) ),
					array(
						'title'  => '{{{ text }}}',
						'fields' => array( array( 'area', 'text', __( 'Statement', 'maison-digitale' ), '' ) ),
					),
				),
			)
		);
		$this->end();
		$this->sec( 'l', __( 'Layout', 'maison-digitale' ) );
		$this->tone_control( 'dark', array( 'dark', 'ground', 'surface' ) );
		$this->spacing_control();
		$this->end();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$tone  = $this->tone( $s['tone'] );
		$line  = 'dark' === $s['tone'] ? '#3b322a' : '#d8cdbc';
		$items = (array) $s['items'];
		$n     = count( $items );
		?>
		<section class="sec <?php echo esc_attr( $tone[0] ); ?>" style="<?php echo esc_attr( $tone[1] . $this->pad( $s ) ); ?>">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(28px, 4cqw, 56px)">
				<?php echo $this->head( $s['label'], $s['title'], $this->muted( $s['tone'] ) ); // phpcs:ignore ?>
				<div style="display: flex; flex-direction: column">
					<?php foreach ( $items as $i => $it ) : ?>
						<div class="rv" style="display: flex; flex-wrap: wrap; align-items: baseline; gap: 8px clamp(20px, 4cqw, 64px); border-top: 1px solid <?php echo esc_attr( $line ); ?>;<?php echo $i === $n - 1 ? ' border-bottom: 1px solid ' . esc_attr( $line ) . ';' : ''; ?> padding: clamp(18px, 2.4cqw, 32px) 0">
							<span class="ov" style="color: <?php echo esc_attr( $this->accent( $s['tone'] ) ); ?>; width: 40px"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<p class="fh" style="font-size: clamp(28px, 5cqw, 72px); flex: 1 1 300px"><?php echo md_hl( $it['text'] ); // phpcs:ignore ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Steps — "How we work", "Our process", "How it works", "What happens next"
 * ====================================================================== */
class MD_W_Steps extends MD_Widget {
	protected $md_icon = 'eicon-number-field';

	public function get_name() {
		return 'md-steps';
	}

	public function get_title() {
		return __( 'Steps', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), '03' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'How we work' ),
				array(
					'rep',
					'steps',
					__( 'Steps', 'maison-digitale' ),
					$this->rows(
						array( 'step', 'title', 'text' ),
						array(
							array( 'Step 1', 'Listen', 'We start with your brand, your audience and your numbers.' ),
							array( 'Step 2', 'Shape', 'We turn what we heard into a story and a plan.' ),
							array( 'Step 3', 'Create', 'We write, design, film and build it, in-house.' ),
							array( 'Step 4', 'Grow', 'We measure, learn and improve, month after month.' ),
						)
					),
					array(
						'title'  => '{{{ step }}} — {{{ title }}}',
						'fields' => array(
							array( 'text', 'step', __( 'Step label', 'maison-digitale' ), '' ),
							array( 'text', 'title', __( 'Title', 'maison-digitale' ), '' ),
							array( 'area', 'text', __( 'Text', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->buttons_control( array() );
		$this->end();
		$this->sec( 'l', __( 'Layout', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'select',
					'tsize',
					__( 'Step title size', 'maison-digitale' ),
					'l',
					array(
						'options' => array(
							'l' => '40px',
							'm' => '36px',
							's' => '32px',
						),
					),
				),
				array(
					'select',
					'col',
					__( 'Minimum column width', 'maison-digitale' ),
					'230',
					array(
						'options' => array(
							'230' => '230px (four across)',
							'260' => '260px (three across)',
						),
					),
				),
			)
		);
		$this->tone_control( 'ground', array( 'ground', 'surface' ) );
		$this->spacing_control();
		$this->end();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$tone  = $this->tone( $s['tone'] );
		$sizes = array(
			'l' => 'clamp(28px, 2.8cqw, 40px)',
			'm' => 'clamp(26px, 2.6cqw, 36px)',
			's' => 'clamp(24px, 2.4cqw, 32px)',
		);
		$ts    = isset( $sizes[ $s['tsize'] ] ) ? $sizes[ $s['tsize'] ] : $sizes['l'];
		?>
		<section class="sec <?php echo esc_attr( $tone[0] ); ?>" style="<?php echo esc_attr( $tone[1] . $this->pad( $s ) ); ?>">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(28px, 4cqw, 56px)">
				<?php echo $this->head( $s['label'], $s['title'] ); // phpcs:ignore ?>
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, <?php echo (int) $s['col']; ?>px), 1fr)); gap: 32px clamp(20px, 2.4cqw, 32px)">
					<?php foreach ( (array) $s['steps'] as $st ) : ?>
						<div class="step rv">
							<p class="ov" style="color: #a67c2e"><?php echo esc_html( $st['step'] ); ?></p>
							<h3 class="fh" style="font-size: <?php echo esc_attr( $ts ); ?>; margin: 14px 0 10px"><?php echo md_hl( $st['title'] ); // phpcs:ignore ?></h3>
							<p style="margin: 0"><?php echo md_br( $st['text'] ); // phpcs:ignore ?></p>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( ! empty( $s['buttons'] ) ) : ?>
					<div style="display: flex; flex-wrap: wrap; gap: 12px"><?php echo $this->buttons( $s['buttons'] ); // phpcs:ignore ?></div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Cards — Service "What's included", Brand audit "What we score"
 * ====================================================================== */
class MD_W_Cards extends MD_Widget {
	protected $md_icon = 'eicon-gallery-grid';

	public function get_name() {
		return 'md-cards';
	}

	public function get_title() {
		return __( 'Numbered cards', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), 'What’s included' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'Five parts, one plan' ),
				array(
					'rep',
					'cards',
					__( 'Cards', 'maison-digitale' ),
					$this->rows(
						array( 'title', 'text' ),
						array(
							array( 'Marketing strategy', 'Audience, positioning, channels and targets, written down and agreed.' ),
							array( 'Paid campaigns', 'Search and social advertising, planned, launched and managed.' ),
							array( 'Search engine optimisation', 'Technical fixes, content and structure that help people find you.' ),
							array( 'Email marketing', 'Newsletters and automated sequences that keep clients close.' ),
							array( 'Analytics and reporting', 'A clear monthly report on what moved, what did not, and what is next.' ),
						)
					),
					array(
						'fields' => array(
							array( 'text', 'title', __( 'Title', 'maison-digitale' ), '' ),
							array( 'area', 'text', __( 'Text', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->end();
		$this->sec( 'l', __( 'Layout', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'select',
					'col',
					__( 'Minimum card width', 'maison-digitale' ),
					'300',
					array(
						'options' => array(
							'300' => '300px',
							'260' => '260px',
						),
					),
				),
			)
		);
		$this->tone_control( 'surface', array( 'surface', 'ground' ) );
		$this->spacing_control();
		$this->end();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$surf = 'surface' === $s['tone'];
		?>
		<section class="sec" style="<?php echo esc_attr( ( $surf ? 'background: #ece4d6' : '' ) . $this->pad( $s ) ); ?>">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(28px, 4cqw, 56px)">
				<?php echo $this->head( $s['label'], $s['title'] ); // phpcs:ignore ?>
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, <?php echo (int) $s['col']; ?>px), 1fr)); gap: clamp(12px, 1.6cqw, 24px)">
					<?php foreach ( (array) $s['cards'] as $i => $c ) : ?>
						<div class="box rv"<?php echo $surf ? ' style="background: #f6f1e9"' : ''; ?>><p class="ov" style="color: #a67c2e"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></p><h3 class="fh" style="font-size: 26px; margin: 16px 0 8px"><?php echo md_hl( $c['title'] ); // phpcs:ignore ?></h3><p style="margin: 0; color: #62574d"><?php echo md_br( $c['text'] ); // phpcs:ignore ?></p></div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Team — About "The team"
 * ====================================================================== */
class MD_W_Team extends MD_Widget {
	protected $md_icon = 'eicon-person';

	public function get_name() {
		return 'md-team';
	}

	public function get_title() {
		return __( 'Team', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Founder', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), '04' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'The team' ),
				array( 'media', 'photo', __( 'Founder portrait', 'maison-digitale' ), '' ),
				array( 'text', 'photo_ph', __( 'Placeholder while no portrait is set', 'maison-digitale' ), '[Founder portrait]' ),
				array( 'text', 'role', __( 'Founder overline', 'maison-digitale' ), 'Founder' ),
				array( 'text', 'name', __( 'Founder name', 'maison-digitale' ), '[Founder name]' ),
				array( 'area', 'bio', __( 'Short bio', 'maison-digitale' ), '[Short founder bio to be supplied: background, experience, awards, and why she started the house.]', array( 'rows' => 5 ) ),
			)
		);
		$this->end();
		$this->sec( 't', __( 'Team members', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'rep',
					'members',
					__( 'Members', 'maison-digitale' ),
					$this->rows( array( 'name', 'role' ), array( array( '[Team member]', '[Role]' ), array( '[Team member]', '[Role]' ), array( '[Team member]', '[Role]' ), array( '[Team member]', '[Role]' ) ) ),
					array(
						'title'  => '{{{ name }}}',
						'fields' => array(
							array( 'media', 'photo', __( 'Photo', 'maison-digitale' ), '' ),
							array( 'text', 'name', __( 'Name', 'maison-digitale' ), '' ),
							array( 'text', 'role', __( 'Role', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->tone_control( 'surface', array( 'surface', 'ground' ) );
		$this->spacing_control();
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="sec" style="<?php echo esc_attr( ( 'surface' === $s['tone'] ? 'background: #ece4d6' : '' ) . $this->pad( $s ) ); ?>">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(28px, 4cqw, 56px)">
				<?php echo $this->head( $s['label'], $s['title'] ); // phpcs:ignore ?>
				<div class="rv" style="display: flex; flex-wrap: wrap; gap: clamp(24px, 4cqw, 64px); align-items: flex-end">
					<?php echo md_box( $s['photo'], $s['photo_ph'], '', 'flex: 1 1 280px; max-width: 460px; aspect-ratio: 4 / 5', 'div', $s['name'] ); // phpcs:ignore ?>
					<div style="flex: 1.4 1 320px; display: flex; flex-direction: column; gap: 14px; max-width: 36em">
						<p class="ov" style="color: #62574d"><?php echo esc_html( $s['role'] ); ?></p>
						<h3 class="fh" style="font-size: clamp(28px, 3.4cqw, 48px)"><?php echo md_hl( $s['name'] ); // phpcs:ignore ?></h3>
						<p style="margin: 0"><?php echo md_br( $s['bio'] ); // phpcs:ignore ?></p>
					</div>
				</div>
				<?php if ( ! empty( $s['members'] ) ) : ?>
					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 24px">
						<?php foreach ( $s['members'] as $m ) : ?>
							<div class="rv"><?php echo md_box( isset( $m['photo'] ) ? $m['photo'] : '', '[Photo]', '', 'aspect-ratio: 1 / 1', 'div', $m['name'] ); // phpcs:ignore ?><p style="margin: 12px 0 0; font-weight: 600"><?php echo esc_html( $m['name'] ); ?></p><p style="margin: 0; color: #62574d; font-size: 15px"><?php echo esc_html( $m['role'] ); ?></p></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Recognition — About figures, awards and client logos
 * ====================================================================== */
class MD_W_Recognition extends MD_Widget {
	protected $md_icon = 'eicon-trophy';

	public function get_name() {
		return 'md-recognition';
	}

	public function get_title() {
		return __( 'Recognition (figures, awards, logos)', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), '05' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'Recognition' ),
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
				array(
					'rep',
					'awards',
					__( 'Awards', 'maison-digitale' ),
					$this->rows( array( 'name', 'by' ), array( array( '[Award name]', '[Awarding body, year]' ), array( '[Award name]', '[Awarding body, year]' ), array( '[Award name]', '[Awarding body, year]' ) ) ),
					array(
						'title'  => '{{{ name }}}',
						'fields' => array(
							array( 'text', 'name', __( 'Award', 'maison-digitale' ), '' ),
							array( 'text', 'by', __( 'Awarding body, year', 'maison-digitale' ), '' ),
						),
					),
				),
				array(
					'rep',
					'logos',
					__( 'Client logos', 'maison-digitale' ),
					$this->rows( array( 'name' ), array( '[Client logo]', '[Client logo]', '[Client logo]', '[Client logo]', '[Client logo]', '[Client logo]' ) ),
					array(
						'title'  => '{{{ name }}}',
						'fields' => array(
							array( 'media', 'logo', __( 'Logo', 'maison-digitale' ), '' ),
							array( 'text', 'name', __( 'Client name / placeholder', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->spacing_control();
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="sec" style="<?php echo esc_attr( $this->pad( $s ) ); ?>">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(28px, 4cqw, 56px)">
				<?php echo $this->head( $s['label'], $s['title'] ); // phpcs:ignore ?>
				<?php if ( ! empty( $s['stats'] ) ) : ?>
					<div class="rv" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 24px clamp(20px, 3cqw, 48px); border-top: 1px solid #17120f; padding-top: 24px">
						<?php foreach ( $s['stats'] as $st ) : ?>
							<div><div class="fh" style="font-size: clamp(40px, 5cqw, 72px); color: #a67c2e"><?php echo esc_html( $st['value'] ); ?></div><p class="ov" style="color: #62574d; margin-top: 8px"><?php echo esc_html( $st['label'] ); ?></p></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $s['awards'] ) ) : ?>
					<div class="rv" style="display: flex; flex-direction: column">
						<?php foreach ( $s['awards'] as $a ) : ?>
							<div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 4px 24px; border-bottom: 1px solid #d8cdbc; padding: 14px 0"><span><?php echo esc_html( $a['name'] ); ?></span><span style="color: #62574d"><?php echo esc_html( $a['by'] ); ?></span></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $s['logos'] ) ) : ?>
					<div class="rv" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px">
						<?php foreach ( $s['logos'] as $l ) : ?>
							<?php echo md_box( isset( $l['logo'] ) ? $l['logo'] : '', $l['name'], 'o', 'height: 72px', 'div', $l['name'] ); // phpcs:ignore ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Call-to-action band — gold or black, with the quotation mark
 * ====================================================================== */
class MD_W_CTA_Band extends MD_Widget {
	protected $md_icon = 'eicon-call-to-action';

	public function get_name() {
		return 'md-cta-band';
	}

	public function get_title() {
		return __( 'Call-to-action band', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'switch', 'quote', __( 'Gold quotation mark', 'maison-digitale' ), 'yes' ),
				array( 'area', 'heading', __( 'Headline', 'maison-digitale' ), 'Let’s work together.' ),
				array( 'area', 'text', __( 'Line under the headline', 'maison-digitale' ), 'Like what you see? Tell us about your project.' ),
			)
		);
		$this->buttons_control(
			array(
				array( 'label' => 'Start a project', 'link' => array( 'url' => '/lets-work-together/' ), 'style' => 'b-ink' ),
				array( 'label' => 'Get your free brand audit', 'link' => array( 'url' => '/free-brand-audit/' ), 'style' => 'b-line' ),
			)
		);
		$this->end();
		$this->sec( 'l', __( 'Layout', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'select',
					'heading_size',
					__( 'Headline size', 'maison-digitale' ),
					'l',
					array(
						'options' => array(
							'l' => '88px',
							'm' => '76px',
						),
					),
				),
				array( 'text', 'heading_width', __( 'Headline max width (e.g. 13em)', 'maison-digitale' ), '' ),
			)
		);
		$this->tone_control( 'gold', array( 'gold', 'dark' ) );
		$this->spacing_control();
		$this->end();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$dark = 'dark' === $s['tone'];
		$size = 'm' === $s['heading_size'] ? 'clamp(34px, 5.2cqw, 76px)' : 'clamp(36px, 6cqw, 88px)';
		$hs   = 'font-size: ' . $size . ( $s['heading_width'] ? '; max-width: ' . $s['heading_width'] : '' );
		$st   = $dark ? 'background: #17120f; color: #f6f1e9; border-bottom: 1px solid #3b322a' : 'background: #c9a24f; color: #17120f';
		?>
		<section class="sec <?php echo $dark ? 'dk' : 'gd'; ?> md-cta" style="<?php echo esc_attr( $st . $this->pad( $s ) ); ?>">
			<div class="in rv" style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 24px 48px">
				<div>
					<?php
					if ( 'yes' === $s['quote'] ) {
						echo $this->q( 'font-size: clamp(56px, 7.4cqw, 106px); line-height: .62' . ( $dark ? '; color: #c9a24f' : '' ) ); // phpcs:ignore
					}
					?>
					<h2 class="fh" style="<?php echo esc_attr( $hs ); ?>"><?php echo md_hl( $s['heading'] ); // phpcs:ignore ?></h2>
					<?php if ( $s['text'] ) : ?>
						<p style="margin: 14px 0 0; font-size: clamp(18px, 1.6cqw, 22px)"><?php echo md_br( $s['text'] ); // phpcs:ignore ?></p>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $s['buttons'] ) ) : ?>
					<?php if ( 1 === count( $s['buttons'] ) ) : ?>
						<?php echo $this->buttons( $s['buttons'] ); // phpcs:ignore ?>
					<?php else : ?>
						<div style="display: flex; flex-wrap: wrap; gap: 12px"><?php echo $this->buttons( $s['buttons'] ); // phpcs:ignore ?></div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Service index — nine numbered rows with hover preview
 * ====================================================================== */
class MD_W_Service_Index extends MD_Widget {
	protected $md_icon = 'eicon-post-list';

	public function get_name() {
		return 'md-service-index';
	}

	public function get_title() {
		return __( 'Service index (numbered rows)', 'maison-digitale' );
	}

	protected function register_controls() {
		$rows = array();
		foreach ( md_services() as $i => $sv ) {
			$rows[] = array(
				'name' => $sv['name'],
				'line' => $sv['promise'],
				'link' => array( 'url' => md_service_path( $i ) ),
			);
		}
		$this->sec( 'c', __( 'Rows', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'rep',
					'rows',
					__( 'Services', 'maison-digitale' ),
					$rows,
					array(
						'title'  => '{{{ name }}}',
						'fields' => array(
							array( 'text', 'name', __( 'Name', 'maison-digitale' ), '' ),
							array( 'area', 'line', __( 'One line', 'maison-digitale' ), '' ),
							array( 'url', 'link', __( 'Link', 'maison-digitale' ), '' ),
							array( 'media', 'preview', __( 'Hover preview image', 'maison-digitale' ), '' ),
						),
					),
				),
				array( 'text', 'ph', __( 'Preview placeholder', 'maison-digitale' ), '[Image preview]' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section style="padding: 0 clamp(20px, 5cqw, 80px) clamp(56px, 8cqw, 120px)">
			<div class="in" style="border-bottom: 1px solid #17120f">
				<?php foreach ( (array) $s['rows'] as $i => $r ) : ?>
					<a class="row rv" <?php echo md_href( $r['link'] ); // phpcs:ignore ?>>
						<span class="ov" style="color: #a67c2e"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span style="display: flex; flex-wrap: wrap; align-items: baseline; gap: 6px 40px"><span class="fh nm" style="font-size: clamp(26px, 3.6cqw, 52px); flex: 1 1 420px"><?php echo esc_html( $r['name'] ); ?></span><span class="ln" style="flex: 1 1 260px; max-width: 26em"><?php echo esc_html( $r['line'] ); ?></span></span>
						<?php echo md_svg( 'arrow-row' ); // phpcs:ignore ?>
						<?php echo md_box( isset( $r['preview'] ) ? $r['preview'] : '', $s['ph'], 'pv d', '', 'span' ); // phpcs:ignore ?>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Service groups — "How the services connect"
 * ====================================================================== */
class MD_W_Service_Groups extends MD_Widget {
	protected $md_icon = 'eicon-flow';

	public function get_name() {
		return 'md-service-groups';
	}

	public function get_title() {
		return __( 'Service groups', 'maison-digitale' );
	}

	protected function register_controls() {
		$p = function ( $i ) {
			$sv = md_services();
			return $sv[ $i ]['name'] . ' | ' . md_service_path( $i );
		};
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), 'How the services connect' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'Grow. Create. *Systemise.*', array( 'description' => __( 'Wrap words in *asterisks* for gold.', 'maison-digitale' ) ) ),
				array(
					'rep',
					'groups',
					__( 'Groups', 'maison-digitale' ),
					array(
						array( 'title' => 'Grow', 'text' => 'Reach the right people and turn them into customers.', 'links' => implode( "\n", array( $p( 0 ), $p( 1 ) ) ) ),
						array( 'title' => 'Create', 'text' => 'Make the things your audience sees, reads and remembers.', 'links' => implode( "\n", array( $p( 2 ), $p( 3 ), $p( 4 ), $p( 5 ), $p( 6 ) ) ) ),
						array( 'title' => 'Systemise', 'text' => 'Keep every lead, client and task moving without the busywork.', 'links' => implode( "\n", array( $p( 7 ), $p( 8 ) ) ) ),
					),
					array(
						'fields' => array(
							array( 'text', 'title', __( 'Title', 'maison-digitale' ), '' ),
							array( 'area', 'text', __( 'Text', 'maison-digitale' ), '' ),
							array( 'area', 'links', __( 'Links, one per line: Label | /url/', 'maison-digitale' ), '', array( 'rows' => 6 ) ),
						),
					),
				),
			)
		);
		$this->spacing_control();
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="sec dk" style="background: #17120f; color: #f6f1e9<?php echo esc_attr( $this->pad( $s ) ); ?>">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(28px, 4cqw, 56px)">
				<?php echo $this->head( $s['label'], $s['title'], '#b9ad9d' ); // phpcs:ignore ?>
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 32px clamp(20px, 3cqw, 48px)">
					<?php foreach ( (array) $s['groups'] as $g ) : ?>
						<div class="rv grp" style="border-top: 1px solid #c9a24f; padding-top: 20px">
							<h3 class="fh" style="font-size: clamp(28px, 2.8cqw, 40px)"><?php echo md_hl( $g['title'] ); // phpcs:ignore ?></h3>
							<p style="margin: 10px 0 16px; color: #b9ad9d"><?php echo md_br( $g['text'] ); // phpcs:ignore ?></p>
							<?php foreach ( md_link_lines( $g['links'] ) as $l ) : ?>
								<a href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( $l['label'] ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Work cards — Service "Featured work" and Article "Related notes"
 * ====================================================================== */
class MD_W_Work_Cards extends MD_Widget {
	protected $md_icon = 'eicon-posts-grid';

	public function get_name() {
		return 'md-work-cards';
	}

	public function get_title() {
		return __( 'Project cards', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), 'Featured work' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'In practice' ),
				array( 'text', 'link_l', __( 'Link label', 'maison-digitale' ), 'All projects' ),
				array( 'url', 'link', __( 'Link', 'maison-digitale' ), '/portfolio/' ),
				array(
					'rep',
					'cards',
					__( 'Cards', 'maison-digitale' ),
					$this->rows( array( 'over', 'title', 'meta', 'link' ), array( array( '[Client]', '[Project name]', '', array( 'url' => '/portfolio/case-study/' ) ), array( '[Client]', '[Project name]', '', array( 'url' => '/portfolio/case-study/' ) ), array( '[Client]', '[Project name]', '', array( 'url' => '/portfolio/case-study/' ) ) ) ),
					array(
						'fields' => array(
							array( 'media', 'image', __( 'Image', 'maison-digitale' ), '', array( 'media_types' => array( 'image', 'video' ) ) ),
							array( 'text', 'over', __( 'Overline (client or category)', 'maison-digitale' ), '' ),
							array( 'text', 'title', __( 'Title', 'maison-digitale' ), '' ),
							array( 'text', 'meta', __( 'Meta line (optional)', 'maison-digitale' ), '' ),
							array( 'url', 'link', __( 'Link', 'maison-digitale' ), '' ),
						),
					),
				),
				array( 'text', 'ph', __( 'Image placeholder', 'maison-digitale' ), '[Project image]' ),
			)
		);
		$this->end();
		$this->sec( 'l', __( 'Layout', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'select',
					'style',
					__( 'Card style', 'maison-digitale' ),
					'project',
					array(
						'options' => array(
							'project' => __( 'Project (muted overline)', 'maison-digitale' ),
							'article' => __( 'Article (gold category, meta line)', 'maison-digitale' ),
						),
					),
				),
				array( 'switch', 'rule', __( 'Rule above the heading (service page)', 'maison-digitale' ), 'yes' ),
			)
		);
		$this->tone_control( 'ground', array( 'ground', 'surface' ) );
		$this->spacing_control( 'none' );
		$this->end();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$art  = 'article' === $s['style'];
		$rule = 'yes' === $s['rule'];
		?>
		<section class="sec" style="<?php echo esc_attr( ( 'surface' === $s['tone'] ? 'background: #ece4d6' : '' ) . $this->pad( $s ) ); ?>">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(24px, 3cqw, 40px)">
				<div style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 12px 32px;<?php echo $rule ? ' border-top: 1px solid #17120f; padding-top: clamp(28px, 4cqw, 56px)' : ''; ?>">
					<?php echo $this->head( $s['label'], $s['title'], '#62574d', $art ? 'clamp(30px, 4cqw, 56px)' : 'clamp(32px, 4.4cqw, 64px)' ); // phpcs:ignore ?>
					<?php if ( $s['link_l'] ) : ?>
						<a class="lk" <?php echo md_href( $s['link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['link_l'] ); ?><?php echo md_svg( 'arrow-s' ); // phpcs:ignore ?></a>
					<?php endif; ?>
				</div>
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr)); gap: <?php echo $art ? 'clamp(28px, 3cqw, 40px) clamp(16px, 2cqw, 28px)' : 'clamp(16px, 2cqw, 28px)'; ?>">
					<?php foreach ( (array) $s['cards'] as $c ) : ?>
						<a class="card rv" <?php echo md_href( $c['link'] ); // phpcs:ignore ?>>
							<div class="im" style="aspect-ratio: 4 / 3"><?php echo md_box( isset( $c['image'] ) ? $c['image'] : '', $art ? '[Article image]' : $s['ph'], '', '', 'div', $c['title'] ); // phpcs:ignore ?></div>
							<p class="ov" style="color: <?php echo $art ? '#a67c2e' : '#62574d'; ?>; margin-top: <?php echo $art ? '16px' : '14px'; ?>"><?php echo esc_html( $c['over'] ); ?></p>
							<p class="fh tt" style="font-size: 26px; margin-top: <?php echo $art ? '8px' : '6px'; ?>"><?php echo esc_html( $c['title'] ); ?></p>
							<?php if ( $c['meta'] ) : ?>
								<p class="ov" style="color: #62574d; margin-top: 12px"><?php echo esc_html( $c['meta'] ); ?></p>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Accordion — Service FAQ and the FAQ page groups (native <details>)
 * ====================================================================== */
class MD_W_Accordion extends MD_Widget {
	protected $md_icon = 'eicon-accordion';

	public function get_name() {
		return 'md-accordion';
	}

	public function get_title() {
		return __( 'Questions (accordion)', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), 'Service FAQ' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'Good to know' ),
				array(
					'rep',
					'items',
					__( 'Questions', 'maison-digitale' ),
					array(
						array( 'q' => 'How soon will I see results?', 'a' => '[Answer to be supplied.]', 'open' => 'yes' ),
						array( 'q' => 'What budget do I need for paid campaigns?', 'a' => '[Answer to be supplied.]', 'open' => '' ),
						array( 'q' => 'Which platforms do you advertise on?', 'a' => '[Answer to be supplied.]', 'open' => '' ),
						array( 'q' => 'How do you report on performance?', 'a' => '[Answer to be supplied.]', 'open' => '' ),
					),
					array(
						'title'  => '{{{ q }}}',
						'fields' => array(
							array( 'text', 'q', __( 'Question', 'maison-digitale' ), '' ),
							array( 'wys', 'a', __( 'Answer', 'maison-digitale' ), '' ),
							array( 'switch', 'open', __( 'Open on load', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->end();
		$this->sec( 'l', __( 'Layout', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'select',
					'variant',
					__( 'Style', 'maison-digitale' ),
					'service',
					array(
						'options' => array(
							'service' => __( 'Service page section', 'maison-digitale' ),
							'faq'     => __( 'FAQ page group (gold number, smaller heading)', 'maison-digitale' ),
						),
					),
				),
			)
		);
		$this->tone_control( 'surface', array( 'surface', 'ground' ) );
		$this->spacing_control();
		$this->end();
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$faq = 'faq' === $s['variant'];
		if ( $faq ) {
			$st = 'padding: 0 clamp(20px, 5cqw, 80px) clamp(40px, 5cqw, 72px)';
		} else {
			$st = 'surface' === $s['tone'] ? 'background: #ece4d6' : '';
		}
		$bg = $faq && 'surface' === $s['tone'] ? '; background: #ece4d6' : '';
		?>
		<section class="<?php echo $faq ? '' : 'sec'; ?>" style="<?php echo esc_attr( $st . $bg . $this->pad( $s ) ); ?>">
			<div class="in<?php echo $faq ? ' ' . esc_attr( $this->rv( $s ) ) : ''; ?>" style="display: flex; flex-wrap: wrap; gap: <?php echo $faq ? '16px clamp(24px, 5cqw, 80px)' : 'clamp(24px, 5cqw, 80px)'; ?>">
				<div style="flex: 1 1 <?php echo $faq ? '260px' : '280px'; ?>">
					<?php if ( $faq ) : ?>
						<p class="ov" style="color: #a67c2e"><?php echo esc_html( $s['label'] ); ?></p>
						<h2 class="fh" style="font-size: clamp(28px, 3.2cqw, 44px); margin-top: 10px"><?php echo md_hl( $s['title'] ); // phpcs:ignore ?></h2>
					<?php else : ?>
						<p class="ov" style="color: #62574d"><?php echo esc_html( $s['label'] ); ?></p>
						<h2 class="fh" style="font-size: clamp(32px, 4.4cqw, 64px); margin-top: 12px"><?php echo md_hl( $s['title'] ); // phpcs:ignore ?></h2>
					<?php endif; ?>
				</div>
				<div style="flex: <?php echo $faq ? '2.2' : '2'; ?> 1 420px">
					<?php foreach ( (array) $s['items'] as $it ) : ?>
						<details<?php echo 'yes' === $it['open'] ? ' open' : ''; ?>>
							<summary><?php echo esc_html( $it['q'] ); ?><span class="pm" aria-hidden="true"></span></summary>
							<div class="an"><?php echo wp_kses_post( wpautop( $it['a'] ) ); ?></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Next link — "Next service", "Next project"
 * ====================================================================== */
class MD_W_Next_Link extends MD_Widget {
	protected $md_icon = 'eicon-arrow-right';

	public function get_name() {
		return 'md-next-link';
	}

	public function get_title() {
		return __( 'Next page link', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), 'Next service — 02' ),
				array( 'area', 'title', __( 'Title', 'maison-digitale' ), 'Social Media Management' ),
				array( 'url', 'link', __( 'Link', 'maison-digitale' ), '/services/social-media-management/' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="sec dk" style="background: #17120f; color: #f6f1e9; padding-top: clamp(40px, 5cqw, 72px); padding-bottom: clamp(40px, 5cqw, 72px); border-bottom: 1px solid #3b322a">
			<div class="in">
				<a class="nx" <?php echo md_href( $s['link'] ); // phpcs:ignore ?>>
					<span>
						<span class="ov" style="color: #b9ad9d; display: block"><?php echo esc_html( $s['label'] ); ?></span>
						<span class="fh" style="font-size: clamp(32px, 5.6cqw, 80px); display: block; margin-top: 12px"><?php echo md_hl( $s['title'] ); // phpcs:ignore ?></span>
					</span>
					<?php echo md_svg( 'arrow-l' ); // phpcs:ignore ?>
				</a>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Meta row — Case study client, year, industry, services
 * ====================================================================== */
class MD_W_Meta_Row extends MD_Widget {
	protected $md_icon = 'eicon-info-box';

	public function get_name() {
		return 'md-meta-row';
	}

	public function get_title() {
		return __( 'Facts row (case study)', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'rep',
					'items',
					__( 'Facts', 'maison-digitale' ),
					$this->rows( array( 'label', 'value' ), array( array( 'Client', '[Client name]' ), array( 'Year', '[Year]' ), array( 'Industry', '[Industry]' ), array( 'Services delivered', '[Service] · [Service] · [Service]' ) ) ),
					array(
						'title'  => '{{{ label }}}',
						'fields' => array(
							array( 'text', 'label', __( 'Label', 'maison-digitale' ), '' ),
							array( 'text', 'value', __( 'Value', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section style="padding: clamp(24px, 3cqw, 40px) clamp(20px, 5cqw, 80px); border-bottom: 1px solid #d8cdbc">
			<div class="in" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 20px clamp(20px, 3cqw, 48px)">
				<?php foreach ( (array) $s['items'] as $it ) : ?>
					<div><p class="ov" style="color: #62574d"><?php echo esc_html( $it['label'] ); ?></p><p style="margin: 6px 0 0; font-weight: 500"><?php echo esc_html( $it['value'] ); ?></p></div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Gallery — Case study "The work"
 * ====================================================================== */
class MD_W_Gallery extends MD_Widget {
	protected $md_icon = 'eicon-gallery-masonry';

	public function get_name() {
		return 'md-gallery';
	}

	public function get_title() {
		return __( 'Work gallery', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), '03' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'The work' ),
				array(
					'rep',
					'items',
					__( 'Images and videos', 'maison-digitale' ),
					$this->rows(
						array( 'ph', 'width', 'ratio', 'dark' ),
						array(
							array( '[Full-width image]', 'full', '16 / 9', '' ),
							array( '[Image or mock-up]', 'half', '4 / 5', '' ),
							array( '[Image or mock-up]', 'half', '4 / 5', '' ),
							array( '[Video, with play control]', 'full', '16 / 9', 'yes' ),
							array( '[Image]', 'third', '1 / 1', '' ),
							array( '[Image]', 'third', '1 / 1', '' ),
							array( '[Image]', 'third', '1 / 1', '' ),
						)
					),
					array(
						'title'  => '{{{ width }}} · {{{ ph }}}',
						'fields' => array(
							array( 'media', 'media', __( 'Image or video file', 'maison-digitale' ), '', array( 'media_types' => array( 'image', 'video' ) ) ),
							array( 'text', 'video', __( 'Or YouTube / Vimeo URL', 'maison-digitale' ), '' ),
							array( 'text', 'ph', __( 'Placeholder / alt text', 'maison-digitale' ), '' ),
							array(
								'select',
								'width',
								__( 'Width', 'maison-digitale' ),
								'full',
								array(
									'options' => array(
										'full'  => __( 'Full width', 'maison-digitale' ),
										'half'  => __( 'Half (two side by side)', 'maison-digitale' ),
										'third' => __( 'Third (three side by side)', 'maison-digitale' ),
									),
								),
							),
							array(
								'select',
								'ratio',
								__( 'Proportions', 'maison-digitale' ),
								'16 / 9',
								array(
									'options' => array(
										'16 / 9' => '16:9',
										'4 / 5'  => '4:5',
										'1 / 1'  => '1:1',
										'4 / 3'  => '4:3',
										'3 / 2'  => '3:2',
									),
								),
							),
							array( 'switch', 'dark', __( 'Dark placeholder', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->end();
	}

	/**
	 * @param array $it Item.
	 * @return string
	 */
	private function item( $it ) {
		$style = 'aspect-ratio: ' . $it['ratio'];
		$video = trim( (string) $it['video'] );
		if ( $video ) {
			$embed = md_embed_url( $video );
			return '<div class="ph has-media rv" style="' . esc_attr( $style ) . '"><iframe src="' . esc_url( $embed ) . '" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy" title="' . esc_attr( $it['ph'] ) . '"></iframe></div>';
		}
		$url = md_media_url( $it['media'] );
		if ( $url && preg_match( '/\.(mp4|webm|mov)(\?.*)?$/i', $url ) ) {
			return '<div class="ph has-media rv" style="' . esc_attr( $style ) . '"><video src="' . esc_url( $url ) . '" controls playsinline preload="metadata"></video></div>';
		}
		return md_box( $it['media'], $it['ph'], ( 'yes' === $it['dark'] ? 'd ' : '' ) . 'rv', $style, 'div', $it['ph'] );
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = (array) $s['items'];
		$min   = array(
			'half'  => '320px',
			'third' => '240px',
		);
		?>
		<section style="padding: 0 clamp(20px, 5cqw, 80px) clamp(56px, 8cqw, 120px)">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(12px, 2cqw, 28px)">
				<div style="display: flex; align-items: baseline; gap: 24px; border-top: 1px solid #17120f; padding-top: 24px; margin-bottom: 12px">
					<p class="ov" style="color: #62574d"><?php echo esc_html( $s['label'] ); ?></p>
					<h2 class="fh" style="font-size: clamp(32px, 4.4cqw, 64px)"><?php echo md_hl( $s['title'] ); // phpcs:ignore ?></h2>
				</div>
				<?php
				$n = count( $items );
				for ( $i = 0; $i < $n; ) {
					$w = $items[ $i ]['width'];
					if ( 'full' === $w ) {
						echo $this->item( $items[ $i ] ); // phpcs:ignore
						$i++;
						continue;
					}
					echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, ' . esc_attr( $min[ $w ] ) . '), 1fr)); gap: clamp(12px, 2cqw, 28px)">';
					while ( $i < $n && $items[ $i ]['width'] === $w ) {
						echo $this->item( $items[ $i ] ); // phpcs:ignore
						$i++;
					}
					echo '</div>';
				}
				?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Results — Case study figures
 * ====================================================================== */
class MD_W_Results extends MD_Widget {
	protected $md_icon = 'eicon-counter';

	public function get_name() {
		return 'md-results';
	}

	public function get_title() {
		return __( 'Results (figures)', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), '04' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'Results' ),
				array(
					'rep',
					'items',
					__( 'Figures', 'maison-digitale' ),
					$this->rows( array( 'value', 'text' ), array( array( '[00%]', '[What this figure measures, over what period]' ), array( '[00]', '[What this figure measures, over what period]' ), array( '[00×]', '[What this figure measures, over what period]' ) ) ),
					array(
						'title'  => '{{{ value }}}',
						'fields' => array(
							array( 'text', 'value', __( 'Figure', 'maison-digitale' ), '' ),
							array( 'area', 'text', __( 'What it measures', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="sec dk" style="background: #17120f; color: #f6f1e9">
			<div class="in" style="display: flex; flex-direction: column; gap: clamp(28px, 4cqw, 56px)">
				<?php echo $this->head( $s['label'], $s['title'], '#b9ad9d' ); // phpcs:ignore ?>
				<div class="rv" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 220px), 1fr)); gap: 28px clamp(20px, 3cqw, 48px)">
					<?php foreach ( (array) $s['items'] as $it ) : ?>
						<div style="border-top: 1px solid #c9a24f; padding-top: 18px"><div class="fh" style="font-size: clamp(44px, 6cqw, 88px); color: #c9a24f"><?php echo esc_html( $it['value'] ); ?></div><p style="margin: 8px 0 0; color: #b9ad9d"><?php echo md_br( $it['text'] ); // phpcs:ignore ?></p></div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Client quote — Case study
 * ====================================================================== */
class MD_W_Quote extends MD_Widget {
	protected $md_icon = 'eicon-blockquote';

	public function get_name() {
		return 'md-quote';
	}

	public function get_title() {
		return __( 'Client quote', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'area', 'quote', __( 'Quote', 'maison-digitale' ), '[Client quote, one or two sentences, about working with Maison Digitale]' ),
				array( 'media', 'photo', __( 'Photo', 'maison-digitale' ), '' ),
				array( 'text', 'name', __( 'Name', 'maison-digitale' ), '[Client contact name]' ),
				array( 'text', 'role', __( 'Role, company', 'maison-digitale' ), '[Role, company]' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="sec">
			<figure class="in rv" style="margin: 0 auto; display: flex; flex-direction: column; gap: 20px; max-width: 980px">
				<?php echo $this->q( 'font-size: clamp(72px, 10cqw, 144px); line-height: .62; color: #a67c2e' ); // phpcs:ignore ?>
				<blockquote class="fh" style="font-size: clamp(26px, 3.8cqw, 54px); line-height: 1.12"><?php echo md_hl( $s['quote'] ); // phpcs:ignore ?></blockquote>
				<figcaption style="display: flex; align-items: center; gap: 14px; margin-top: 8px">
					<?php echo md_box( $s['photo'], '[Photo]', '', 'width: 56px; height: 56px; padding: 0; font-size: 10px; flex-shrink: 0', 'span', $s['name'] ); // phpcs:ignore ?>
					<span><span style="display: block; font-weight: 600"><?php echo esc_html( $s['name'] ); ?></span><span style="display: block; color: #62574d; font-size: 15px"><?php echo esc_html( $s['role'] ); ?></span></span>
				</figcaption>
			</figure>
		</section>
		<?php
	}
}

/* =========================================================================
 * Contact — details and the short form
 * ====================================================================== */
class MD_W_Contact extends MD_Widget {
	protected $md_icon = 'eicon-form-horizontal';

	public function get_name() {
		return 'md-contact';
	}

	public function get_title() {
		return __( 'Contact details and form', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'd', __( 'Details (left)', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'rep',
					'items',
					__( 'Details', 'maison-digitale' ),
					array(
						array( 'kind' => 'big', 'label' => 'Email', 'value' => '[email]', 'link' => array( 'url' => '' ) ),
						array( 'kind' => 'big', 'label' => 'Phone', 'value' => '[phone]', 'link' => array( 'url' => '' ) ),
						array( 'kind' => 'button', 'label' => '', 'value' => 'Message us on WhatsApp', 'link' => array( 'url' => 'https://wa.me' ) ),
						array( 'kind' => 'text', 'label' => 'Studio', 'value' => '[address, Lebanon]', 'link' => array( 'url' => '' ) ),
						array( 'kind' => 'text', 'label' => 'Working hours', 'value' => '[days and hours]', 'link' => array( 'url' => '' ) ),
						array( 'kind' => 'links', 'label' => 'Follow', 'value' => "[Instagram] | https://instagram.com\n[LinkedIn] | https://linkedin.com\n[others]", 'link' => array( 'url' => '' ) ),
					),
					array(
						'title'  => '{{{ label || value }}}',
						'fields' => array(
							array(
								'select',
								'kind',
								__( 'Type', 'maison-digitale' ),
								'text',
								array(
									'options' => array(
										'big'    => __( 'Label and large text', 'maison-digitale' ),
										'text'   => __( 'Label and text', 'maison-digitale' ),
										'button' => __( 'WhatsApp button', 'maison-digitale' ),
										'links'  => __( 'Label and links (one per line: Label | URL)', 'maison-digitale' ),
									),
								),
							),
							array( 'text', 'label', __( 'Label', 'maison-digitale' ), '' ),
							array( 'area', 'value', __( 'Text', 'maison-digitale' ), '' ),
							array( 'url', 'link', __( 'Link (optional; mailto:, tel:, https://wa.me/…)', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->end();

		$this->sec( 'f', __( 'Form (right)', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'f_name', __( 'Name label', 'maison-digitale' ), 'Name' ),
				array( 'text', 'f_email', __( 'Email label', 'maison-digitale' ), 'Email' ),
				array( 'text', 'f_phone', __( 'Phone label', 'maison-digitale' ), 'Phone' ),
				array( 'text', 'f_optional', __( '"Optional" note', 'maison-digitale' ), '(optional)' ),
				array( 'text', 'f_msg', __( 'Message label', 'maison-digitale' ), 'Message' ),
				array( 'text', 'f_send', __( 'Button', 'maison-digitale' ), 'Send' ),
				array( 'text', 'ok_label', __( 'Sent: overline', 'maison-digitale' ), 'Message sent' ),
				array( 'text', 'ok_title', __( 'Sent: title', 'maison-digitale' ), 'Thank you. We’ll be in touch soon.' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$uid = 'c' . $this->get_id();
		?>
		<section style="padding: 0 clamp(20px, 5cqw, 80px) clamp(56px, 7cqw, 104px)">
			<div class="in" style="display: flex; flex-wrap: wrap; gap: clamp(32px, 6cqw, 96px); border-top: 1px solid #17120f; padding-top: clamp(28px, 4cqw, 56px)">
				<div class="rv" style="flex: 1 1 300px; display: flex; flex-direction: column; gap: 24px">
					<?php foreach ( (array) $s['items'] as $it ) : ?>
						<?php
						$url = md_url( $it['link'] );
						switch ( $it['kind'] ) {
							case 'button':
								echo '<div><a class="bt b-line" ' . md_href( $it['link'], 'https://wa.me' ) . '>' . md_svg( 'whatsapp' ) . esc_html( $it['value'] ) . '</a></div>'; // phpcs:ignore
								break;
							case 'links':
								echo '<div><p class="ov" style="color: #62574d">' . esc_html( $it['label'] ) . '</p><p style="margin: 6px 0 0; display: flex; flex-wrap: wrap; gap: 4px 20px">';
								foreach ( md_lines( $it['value'] ) as $line ) {
									$parts = array_map( 'trim', explode( '|', $line, 2 ) );
									if ( isset( $parts[1] ) && '' !== $parts[1] ) {
										echo '<a href="' . esc_url( md_resolve_url( $parts[1] ) ) . '" style="display: inline-flex; align-items: center; min-height: 44px">' . esc_html( $parts[0] ) . '</a>';
									} else {
										echo '<span style="display: inline-flex; align-items: center; min-height: 44px; color: #62574d">' . esc_html( $parts[0] ) . '</span>';
									}
								}
								echo '</p></div>';
								break;
							default:
								$big   = 'big' === $it['kind'] ? ' font-size: 20px' : '';
								$value = md_br( $it['value'] );
								if ( $url ) {
									$value = '<a ' . md_href( $it['link'] ) . ' style="text-decoration: none">' . $value . '</a>';
								}
								echo '<div><p class="ov" style="color: #62574d">' . esc_html( $it['label'] ) . '</p><p style="margin: 6px 0 0;' . esc_attr( $big ) . '">' . $value . '</p></div>'; // phpcs:ignore
						}
						?>
					<?php endforeach; ?>
				</div>
				<div class="rv" style="flex: 1.3 1 360px">
					<form class="md-form" data-md-form="contact" novalidate style="display: flex; flex-direction: column; gap: 24px">
						<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 220px), 1fr)); gap: 24px">
							<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-name"><?php echo esc_html( $s['f_name'] ); ?></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-name" name="<?php echo esc_attr( $s['f_name'] ); ?>" type="text" autocomplete="name" required></div>
							<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-email"><?php echo esc_html( $s['f_email'] ); ?></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-email" name="<?php echo esc_attr( $s['f_email'] ); ?>" type="email" autocomplete="email" required></div>
						</div>
						<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-phone"><?php echo esc_html( $s['f_phone'] ); ?> <span class="opt-l"><?php echo esc_html( $s['f_optional'] ); ?></span></label><input class="fi" id="<?php echo esc_attr( $uid ); ?>-phone" name="<?php echo esc_attr( $s['f_phone'] ); ?>" type="tel" autocomplete="tel"></div>
						<div class="fd"><label for="<?php echo esc_attr( $uid ); ?>-msg"><?php echo esc_html( $s['f_msg'] ); ?></label><textarea class="fi" id="<?php echo esc_attr( $uid ); ?>-msg" name="<?php echo esc_attr( $s['f_msg'] ); ?>" rows="4" required></textarea></div>
						<input class="md-hp" type="text" name="md_hp" tabindex="-1" autocomplete="off" aria-hidden="true">
						<div class="md-sum" hidden role="alert"></div>
						<div><button type="submit" class="bt b-ink"><?php echo esc_html( $s['f_send'] ); ?></button></div>
					</form>
					<div class="md-sent" hidden tabindex="-1">
						<p class="ov"><?php echo esc_html( $s['ok_label'] ); ?></p>
						<p class="fh" style="font-size: 30px; margin-top: 10px"><?php echo md_hl( $s['ok_title'] ); // phpcs:ignore ?></p>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Routing cards — Contact "Have a project in mind?" / "Want a free review first?"
 * ====================================================================== */
class MD_W_Route_Cards extends MD_Widget {
	protected $md_icon = 'eicon-flip-box';

	public function get_name() {
		return 'md-route-cards';
	}

	public function get_title() {
		return __( 'Routing cards', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Cards', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'rep',
					'cards',
					__( 'Cards', 'maison-digitale' ),
					array(
						array( 'over' => 'Have a project in mind?', 'title' => 'Let’s work together', 'link' => array( 'url' => '/lets-work-together/' ), 'style' => 'a' ),
						array( 'over' => 'Want a free review first?', 'title' => 'Get your brand audit', 'link' => array( 'url' => '/free-brand-audit/' ), 'style' => 'b' ),
					),
					array(
						'fields' => array(
							array( 'text', 'over', __( 'Overline', 'maison-digitale' ), '' ),
							array( 'text', 'title', __( 'Title', 'maison-digitale' ), '' ),
							array( 'url', 'link', __( 'Link', 'maison-digitale' ), '' ),
							array(
								'select',
								'style',
								__( 'Colour', 'maison-digitale' ),
								'a',
								array(
									'options' => array(
										'a' => __( 'Black', 'maison-digitale' ),
										'b' => __( 'Gold', 'maison-digitale' ),
									),
								),
							),
						),
					),
				),
			)
		);
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="sec">
			<div class="in" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: clamp(12px, 2cqw, 28px)">
				<?php foreach ( (array) $s['cards'] as $c ) : ?>
					<a class="rt <?php echo 'b' === $c['style'] ? 'b' : 'a'; ?> rv" <?php echo md_href( $c['link'] ); // phpcs:ignore ?>>
						<span><span class="ov" style="display: block;<?php echo 'b' === $c['style'] ? '' : ' color: #c9a24f'; ?>"><?php echo esc_html( $c['over'] ); ?></span><span class="fh" style="display: block; font-size: clamp(30px, 4cqw, 56px); margin-top: 14px"><?php echo md_hl( $c['title'] ); // phpcs:ignore ?></span></span>
						<?php echo md_svg( 'arrow-m' ); // phpcs:ignore ?>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}

/* =========================================================================
 * Newsletter — Blog
 * ====================================================================== */
class MD_W_Newsletter extends MD_Widget {
	protected $md_icon = 'eicon-mail';

	public function get_name() {
		return 'md-newsletter';
	}

	public function get_title() {
		return __( 'Newsletter sign-up', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'label', __( 'Overline', 'maison-digitale' ), 'Newsletter' ),
				array( 'area', 'title', __( 'Heading', 'maison-digitale' ), 'New notes, once a month.' ),
				array( 'text', 'f_label', __( 'Field label', 'maison-digitale' ), 'Email' ),
				array( 'text', 'f_ph', __( 'Field placeholder', 'maison-digitale' ), 'you@company.com' ),
				array( 'text', 'btn', __( 'Button', 'maison-digitale' ), 'Subscribe' ),
				array( 'text', 'ok', __( 'Thank-you message', 'maison-digitale' ), 'Thank you. You are on the list.' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s   = $this->get_settings_for_display();
		$uid = 'nl-' . $this->get_id();
		?>
		<section class="sec dk" style="background: #17120f; color: #f6f1e9; border-bottom: 1px solid #3b322a">
			<div class="in rv" style="display: flex; flex-wrap: wrap; gap: 24px clamp(24px, 5cqw, 80px); align-items: flex-end">
				<div style="flex: 1.2 1 320px">
					<p class="ov" style="color: #b9ad9d"><?php echo esc_html( $s['label'] ); ?></p>
					<h2 class="fh" style="font-size: clamp(30px, 4.4cqw, 64px); margin-top: 12px"><?php echo md_hl( $s['title'] ); // phpcs:ignore ?></h2>
				</div>
				<div style="flex: 1 1 320px">
					<form class="md-form" data-md-form="newsletter" novalidate style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end">
						<div class="fd" style="flex: 1 1 220px">
							<label for="<?php echo esc_attr( $uid ); ?>"><?php echo esc_html( $s['f_label'] ); ?></label>
							<input class="fi" id="<?php echo esc_attr( $uid ); ?>" name="<?php echo esc_attr( $s['f_label'] ); ?>" type="email" placeholder="<?php echo esc_attr( $s['f_ph'] ); ?>" autocomplete="email" required>
						</div>
						<input class="md-hp" type="text" name="md_hp" tabindex="-1" autocomplete="off" aria-hidden="true">
						<button type="submit" class="bt b-gold"><?php echo esc_html( $s['btn'] ); ?></button>
						<div class="md-sum" hidden role="alert" style="flex: 1 1 100%"></div>
					</form>
					<p class="md-sent-line msg ok" hidden tabindex="-1" style="font-size: 17px"><?php echo md_svg( 'ok' ); // phpcs:ignore ?><?php echo esc_html( $s['ok'] ); ?></p>
				</div>
			</div>
		</section>
		<?php
	}
}
