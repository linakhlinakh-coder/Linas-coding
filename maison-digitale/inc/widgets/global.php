<?php
/**
 * Navigation, footer and the minimal header/footer of the brand audit page.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * Navigation — Nav.dc.html
 * ====================================================================== */
class MD_W_Nav extends MD_Widget {
	protected $md_icon = 'eicon-nav-menu';

	public function get_name() {
		return 'md-nav';
	}

	public function get_title() {
		return __( 'Site navigation', 'maison-digitale' );
	}

	protected function register_controls() {
		$svc_rows = array();
		foreach ( md_services() as $i => $s ) {
			$svc_rows[] = array(
				'name'  => $s['name'],
				'short' => $s['short'],
				'link'  => array( 'url' => md_service_path( $i ) ),
			);
		}

		$this->sec( 'logo', __( 'Logo', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'media', 'logo_light', __( 'Logo on light pages', 'maison-digitale' ), '', array( 'description' => __( 'Leave empty to use logo-horizontal.svg.', 'maison-digitale' ) ) ),
				array( 'media', 'logo_dark', __( 'Logo on dark pages and the mobile menu', 'maison-digitale' ), '', array( 'description' => __( 'Leave empty to use logo-horizontal-reversed.svg.', 'maison-digitale' ) ) ),
				array( 'text', 'logo_label', __( 'Logo accessible label', 'maison-digitale' ), 'Maison Digitale, home' ),
				array( 'url', 'home_link', __( 'Logo link', 'maison-digitale' ), '/' ),
			)
		);
		$this->end();

		$this->sec( 'links', __( 'Menu links', 'maison-digitale' ) );
		$this->fields(
			array(
				array(
					'rep',
					'items',
					__( 'Links', 'maison-digitale' ),
					array(
						array( 'label' => 'Home', 'link' => array( 'url' => '/' ), 'kind' => 'link' ),
						array( 'label' => 'About', 'link' => array( 'url' => '/about/' ), 'kind' => 'link' ),
						array( 'label' => 'Services', 'link' => array( 'url' => '/services/' ), 'kind' => 'services' ),
						array( 'label' => 'Portfolio', 'link' => array( 'url' => '/portfolio/' ), 'kind' => 'link' ),
						array( 'label' => 'Blog', 'link' => array( 'url' => '/blog/' ), 'kind' => 'link' ),
						array( 'label' => 'FAQ', 'link' => array( 'url' => '/faq/' ), 'kind' => 'link' ),
						array( 'label' => 'Contact', 'link' => array( 'url' => '/contact/' ), 'kind' => 'link' ),
					),
					array(
						'title'  => '{{{ label }}}',
						'fields' => array(
							array( 'text', 'label', __( 'Label', 'maison-digitale' ), '' ),
							array( 'url', 'link', __( 'Link', 'maison-digitale' ), '' ),
							array(
								'select',
								'kind',
								__( 'Type', 'maison-digitale' ),
								'link',
								array(
									'options' => array(
										'link'     => __( 'Plain link', 'maison-digitale' ),
										'services' => __( 'Opens the services dropdown', 'maison-digitale' ),
									),
								),
							),
						),
					),
				),
			)
		);
		$this->end();

		$this->sec( 'dropdown', __( 'Services dropdown', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'dd_title', __( 'Title', 'maison-digitale' ), 'Under one roof' ),
				array( 'text', 'dd_all', __( '"All services" label', 'maison-digitale' ), 'All services' ),
				array( 'url', 'dd_all_link', __( '"All services" link', 'maison-digitale' ), '/services/' ),
				array(
					'rep',
					'services',
					__( 'Services', 'maison-digitale' ),
					$svc_rows,
					array(
						'title'  => '{{{ name }}}',
						'fields' => array(
							array( 'text', 'name', __( 'Name', 'maison-digitale' ), '' ),
							array( 'text', 'short', __( 'Short name (mobile menu)', 'maison-digitale' ), '' ),
							array( 'url', 'link', __( 'Link', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->end();

		$this->sec( 'right', __( 'Language and buttons', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'switch', 'show_lang', __( 'Show EN / AR switch', 'maison-digitale' ), 'yes' ),
				array( 'text', 'en_label', __( 'English label', 'maison-digitale' ), 'EN' ),
				array( 'url', 'en_link', __( 'English link', 'maison-digitale' ), '/' ),
				array( 'text', 'ar_label', __( 'Arabic label (desktop)', 'maison-digitale' ), 'ع' ),
				array( 'text', 'ar_label_m', __( 'Arabic label (mobile menu)', 'maison-digitale' ), 'عربي' ),
				array( 'url', 'ar_link', __( 'Arabic link', 'maison-digitale' ), '/ar/' ),
				array( 'text', 'cta', __( 'Primary button', 'maison-digitale' ), 'Free Brand Audit' ),
				array( 'url', 'cta_link', __( 'Primary button link', 'maison-digitale' ), '/free-brand-audit/' ),
				array( 'text', 'work', __( 'Second button (mobile menu)', 'maison-digitale' ), "Let's Work Together" ),
				array( 'url', 'work_link', __( 'Second button link', 'maison-digitale' ), '/lets-work-together/' ),
			)
		);
		$this->end();

		$this->sec( 'mobile', __( 'Mobile menu', 'maison-digitale' ) );
		$this->socials_control(
			array(
				array( 'network' => 'instagram', 'link' => array( 'url' => 'https://instagram.com' ), 'label' => 'Instagram [link to be supplied]' ),
				array( 'network' => 'linkedin', 'link' => array( 'url' => 'https://linkedin.com' ), 'label' => 'LinkedIn [link to be supplied]' ),
			)
		);
		$this->fields(
			array(
				array( 'text', 'open_l', __( '"Open menu" label', 'maison-digitale' ), 'Open menu' ),
				array( 'text', 'close_l', __( '"Close menu" label', 'maison-digitale' ), 'Close menu' ),
				array( 'text', 'main_l', __( 'Navigation label', 'maison-digitale' ), 'Main' ),
			)
		);
		$this->end();
	}

	/**
	 * Is the link the current page?
	 *
	 * @param array $link Link.
	 * @return bool
	 */
	private function is_current( $link ) {
		$url = md_url( $link );
		if ( ! $url || '#' === $url[0] ) {
			return false;
		}
		$cur  = untrailingslashit( strtok( home_url( add_query_arg( array() ) ), '?' ) );
		$path = untrailingslashit( strtok( $url, '?' ) );
		if ( untrailingslashit( home_url( '/' ) ) === $path ) {
			return untrailingslashit( home_url( '/' ) ) === $cur;
		}
		return $path === $cur || 0 === strpos( $cur . '/', $path . '/' );
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$mode   = md_nav_mode();
		$dark   = in_array( $mode, array( 'dark', 'overlay', 'home' ), true );
		$uid    = 'md-dd-' . $this->get_id();
		$light  = md_logo( $s['logo_light'], 'logo-horizontal.svg' );
		$rev    = md_logo( $s['logo_dark'], 'logo-horizontal-reversed.svg' );
		$name   = get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Maison Digitale';
		$items  = (array) $s['items'];
		$svcs   = (array) $s['services'];
		$logo   = '<img class="lg-l" src="' . esc_url( $light ) . '" alt="' . esc_attr( $name ) . '"><img class="lg-d" src="' . esc_url( $rev ) . '" alt="' . esc_attr( $name ) . '">';
		?>
		<div class="nv<?php echo $dark ? ' dk' : ''; ?>" data-md-nav>
			<nav class="bar-d" aria-label="<?php echo esc_attr( $s['main_l'] ); ?>">
				<a class="logo" <?php echo md_href( $s['home_link'], '/' ); // phpcs:ignore ?> aria-label="<?php echo esc_attr( $s['logo_label'] ); ?>"><?php echo $logo; // phpcs:ignore ?></a>
				<div class="nv-links">
					<?php foreach ( $items as $it ) : ?>
						<?php if ( 'services' === $it['kind'] ) : ?>
							<button class="nl" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $uid ); ?>" data-md-dd><?php echo esc_html( $it['label'] ); ?><?php echo md_svg( 'chev' ); // phpcs:ignore ?></button>
						<?php else : ?>
							<a class="nl<?php echo $this->is_current( $it['link'] ) ? ' cur' : ''; ?>" <?php echo md_href( $it['link'] ); // phpcs:ignore ?><?php echo $this->is_current( $it['link'] ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $it['label'] ); ?></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<div class="nv-right">
					<?php if ( 'yes' === $s['show_lang'] ) : ?>
						<div class="nv-lang">
							<a class="nl" <?php echo md_href( $s['en_link'], '/' ); // phpcs:ignore ?> lang="en" style="letter-spacing: .14em; font-size: 12px"><?php echo esc_html( $s['en_label'] ); ?></a>
							<span aria-hidden="true" style="opacity: .5">/</span>
							<a class="nl ar-l" <?php echo md_href( $s['ar_link'] ); // phpcs:ignore ?> lang="ar" style="letter-spacing: 0; font-size: 14px; font-family: 'IBM Plex Sans Arabic', sans-serif"><?php echo esc_html( $s['ar_label'] ); ?></a>
						</div>
					<?php endif; ?>
					<?php if ( $s['cta'] ) : ?>
						<a class="cta" <?php echo md_href( $s['cta_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['cta'] ); ?></a>
					<?php endif; ?>
				</div>
			</nav>

			<div class="dd" id="<?php echo esc_attr( $uid ); ?>" hidden>
				<div style="display: flex; flex-direction: column; gap: 14px">
					<div class="dd-t"><?php echo esc_html( $s['dd_title'] ); ?></div>
					<a class="dd-all" <?php echo md_href( $s['dd_all_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['dd_all'] ); ?></a>
				</div>
				<div class="dd-g">
					<?php foreach ( $svcs as $i => $sv ) : ?>
						<a <?php echo md_href( $sv['link'] ); // phpcs:ignore ?>><span><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><?php echo esc_html( $sv['name'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="bar-m">
				<a class="logo" <?php echo md_href( $s['home_link'], '/' ); // phpcs:ignore ?> aria-label="<?php echo esc_attr( $s['logo_label'] ); ?>"><?php echo $logo; // phpcs:ignore ?></a>
				<button class="ic" type="button" aria-label="<?php echo esc_attr( $s['open_l'] ); ?>" aria-expanded="false" data-md-menu-open><?php echo md_svg( 'burger' ); // phpcs:ignore ?></button>
			</div>

			<div class="mm" hidden role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $s['main_l'] ); ?>" data-md-menu>
				<div class="mm-top">
					<img src="<?php echo esc_url( $rev ); ?>" alt="<?php echo esc_attr( $name ); ?>" style="height: 18px; width: auto; display: block">
					<button class="ic" type="button" aria-label="<?php echo esc_attr( $s['close_l'] ); ?>" data-md-menu-close><?php echo md_svg( 'close' ); // phpcs:ignore ?></button>
				</div>
				<div aria-hidden="true" class="mm-q md-q">“</div>
				<div style="display: flex; flex-direction: column; margin-top: 4px">
					<?php foreach ( $items as $it ) : ?>
						<a class="big" <?php echo md_href( $it['link'] ); // phpcs:ignore ?>><?php echo esc_html( $it['label'] ); ?></a>
						<?php if ( 'services' === $it['kind'] && $svcs ) : ?>
							<div class="mm-svc">
								<?php foreach ( $svcs as $sv ) : ?>
									<a <?php echo md_href( $sv['link'] ); // phpcs:ignore ?>><?php echo esc_html( $sv['short'] ? $sv['short'] : $sv['name'] ); ?></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<div class="mm-cta">
					<?php if ( $s['cta'] ) : ?>
						<a class="g" <?php echo md_href( $s['cta_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['cta'] ); ?></a>
					<?php endif; ?>
					<?php if ( $s['work'] ) : ?>
						<a class="l" <?php echo md_href( $s['work_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['work'] ); ?></a>
					<?php endif; ?>
				</div>
				<div class="mm-foot">
					<div style="display: flex; align-items: center; gap: 4px">
						<?php foreach ( (array) $s['socials'] as $so ) : ?>
							<?php if ( 'text' === $so['network'] ) : ?>
								<span style="font-size: 12px; color: #b9ad9d; padding: 0 8px"><?php echo esc_html( $so['label'] ); ?></span>
							<?php else : ?>
								<a class="ic" <?php echo md_href( $so['link'] ); // phpcs:ignore ?> aria-label="<?php echo esc_attr( $so['label'] ? $so['label'] : $so['network'] ); ?>"><?php echo md_social_svg( $so['network'], 22 ); // phpcs:ignore ?></a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<?php if ( 'yes' === $s['show_lang'] ) : ?>
						<div class="mm-lang">
							<a <?php echo md_href( $s['en_link'], '/' ); // phpcs:ignore ?> lang="en"><?php echo esc_html( $s['en_label'] ); ?></a>
							<span aria-hidden="true" style="opacity: .5">/</span>
							<a <?php echo md_href( $s['ar_link'] ); // phpcs:ignore ?> lang="ar" class="ar-l" style="font-size: 15px"><?php echo esc_html( $s['ar_label_m'] ); ?></a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

/* =========================================================================
 * Footer — Footer.dc.html
 * ====================================================================== */
class MD_W_Footer extends MD_Widget {
	protected $md_icon = 'eicon-footer';

	public function get_name() {
		return 'md-footer';
	}

	public function get_title() {
		return __( 'Site footer', 'maison-digitale' );
	}

	protected function register_controls() {
		$link_fields = array(
			'title'  => '{{{ label }}}',
			'fields' => array(
				array( 'text', 'label', __( 'Label', 'maison-digitale' ), '' ),
				array( 'url', 'link', __( 'Link', 'maison-digitale' ), '' ),
			),
		);
		$svc = array();
		foreach ( md_services() as $i => $s ) {
			$svc[] = array( 'label' => $s['name'], 'link' => array( 'url' => md_service_path( $i ) ) );
		}

		$this->sec( 'brand', __( 'Logo and descriptor', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'media', 'logo', __( 'Logo', 'maison-digitale' ), '', array( 'description' => __( 'Leave empty to use logo-reversed.svg.', 'maison-digitale' ) ) ),
				array( 'url', 'home_link', __( 'Logo link', 'maison-digitale' ), '/' ),
				array( 'area', 'desc', __( 'One-line descriptor', 'maison-digitale' ), 'A creative boutique agency in Lebanon. Storytelling, design and technology under one roof.' ),
			)
		);
		$this->end();

		$this->sec( 'cols', __( 'Columns', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 't_company', __( 'Column 1 title', 'maison-digitale' ), 'Company' ),
				array(
					'rep',
					'company',
					__( 'Column 1 links', 'maison-digitale' ),
					array(
						array( 'label' => 'About', 'link' => array( 'url' => '/about/' ) ),
						array( 'label' => 'Portfolio', 'link' => array( 'url' => '/portfolio/' ) ),
						array( 'label' => 'Blog', 'link' => array( 'url' => '/blog/' ) ),
						array( 'label' => 'FAQ', 'link' => array( 'url' => '/faq/' ) ),
					),
					$link_fields,
				),
				array( 'text', 't_services', __( 'Services column title', 'maison-digitale' ), 'Services' ),
				array( 'rep', 'services', __( 'Services links', 'maison-digitale' ), $svc, $link_fields ),
				array( 'text', 't_start', __( 'Column 3 title', 'maison-digitale' ), 'Get started' ),
				array(
					'rep',
					'start',
					__( 'Column 3 links', 'maison-digitale' ),
					array(
						array( 'label' => 'Free Brand Audit', 'link' => array( 'url' => '/free-brand-audit/' ) ),
						array( 'label' => "Let's Work Together", 'link' => array( 'url' => '/lets-work-together/' ) ),
						array( 'label' => 'Contact', 'link' => array( 'url' => '/contact/' ) ),
					),
					$link_fields,
				),
				array( 'text', 't_reach', __( 'Contact column title', 'maison-digitale' ), 'Contact' ),
				array(
					'rep',
					'reach',
					__( 'Contact details', 'maison-digitale' ),
					array(
						array( 'label' => '[email]', 'link' => array( 'url' => '' ) ),
						array( 'label' => '[phone / WhatsApp]', 'link' => array( 'url' => '' ) ),
						array( 'label' => '[location, Lebanon]', 'link' => array( 'url' => '' ) ),
					),
					array(
						'title'  => '{{{ label }}}',
						'fields' => array(
							array( 'text', 'label', __( 'Text', 'maison-digitale' ), '' ),
							array( 'url', 'link', __( 'Link (optional, e.g. mailto: or tel:)', 'maison-digitale' ), '' ),
						),
					),
				),
			)
		);
		$this->end();

		$this->sec( 'bottom', __( 'Bottom line', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'copy', __( 'Copyright', 'maison-digitale' ), '© {year} Maison Digitale. All rights reserved.', array( 'description' => __( '{year} becomes the current year.', 'maison-digitale' ) ) ),
			)
		);
		$this->socials_control(
			array(
				array( 'network' => 'instagram', 'link' => array( 'url' => 'https://instagram.com' ), 'label' => 'Instagram [link to be supplied]' ),
				array( 'network' => 'linkedin', 'link' => array( 'url' => 'https://linkedin.com' ), 'label' => 'LinkedIn [link to be supplied]' ),
				array( 'network' => 'text', 'link' => array( 'url' => '' ), 'label' => '[others]' ),
			)
		);
		$this->end();
	}

	/**
	 * @param array  $rows  Rows.
	 * @param string $class Extra class.
	 */
	private function links( $rows, $class = '' ) {
		foreach ( (array) $rows as $r ) {
			$url = md_url( isset( $r['link'] ) ? $r['link'] : '' );
			if ( $url ) {
				echo '<a class="fl ' . esc_attr( $class ) . '" ' . md_href( $r['link'] ) . '>' . esc_html( $r['label'] ) . '</a>'; // phpcs:ignore
			} else {
				echo '<span class="fl ' . esc_attr( $class ) . '">' . esc_html( $r['label'] ) . '</span>';
			}
		}
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$name = get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Maison Digitale';
		?>
		<footer class="ft">
			<div class="ft-in">
				<div class="ft-top">
					<div class="ft-brand">
						<a <?php echo md_href( $s['home_link'], '/' ); // phpcs:ignore ?> aria-label="<?php echo esc_attr( $name ); ?>" style="display: inline-flex"><img src="<?php echo esc_url( md_logo( $s['logo'], 'logo-reversed.svg' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>"></a>
						<p style="margin: 0; font-size: 15px; line-height: 24px; color: #b9ad9d"><?php echo md_br( $s['desc'] ); // phpcs:ignore ?></p>
					</div>
					<div class="ft-cols">
						<div>
							<p class="fo"><?php echo esc_html( $s['t_company'] ); ?></p>
							<?php $this->links( $s['company'] ); ?>
						</div>
						<div class="ft-svc">
							<p class="fo"><?php echo esc_html( $s['t_services'] ); ?></p>
							<div class="ft-svc-g"><?php $this->links( $s['services'] ); ?></div>
						</div>
						<div>
							<p class="fo"><?php echo esc_html( $s['t_start'] ); ?></p>
							<?php $this->links( $s['start'] ); ?>
						</div>
						<div>
							<p class="fo"><?php echo esc_html( $s['t_reach'] ); ?></p>
							<?php $this->links( $s['reach'], 'mu' ); ?>
						</div>
					</div>
				</div>
				<div class="ft-bot">
					<p style="margin: 0; font-size: 13px; line-height: 20px; color: #b9ad9d"><?php echo esc_html( str_replace( '{year}', gmdate( 'Y' ), $s['copy'] ) ); ?></p>
					<div style="display: flex; gap: 8px">
						<?php foreach ( (array) $s['socials'] as $so ) : ?>
							<?php if ( 'text' === $so['network'] ) : ?>
								<span class="ic tx"><?php echo esc_html( $so['label'] ); ?></span>
							<?php else : ?>
								<a class="ic" <?php echo md_href( $so['link'] ); // phpcs:ignore ?> aria-label="<?php echo esc_attr( $so['label'] ? $so['label'] : $so['network'] ); ?>"><?php echo md_social_svg( $so['network'], 20 ); // phpcs:ignore ?></a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</footer>
		<?php
	}
}

/* =========================================================================
 * Minimal header — BrandAudit.dc.html top bar
 * ====================================================================== */
class MD_W_Mini_Header extends MD_Widget {
	protected $md_icon = 'eicon-header';

	public function get_name() {
		return 'md-mini-header';
	}

	public function get_title() {
		return __( 'Minimal header (brand audit)', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'media', 'logo', __( 'Logo', 'maison-digitale' ), '', array( 'description' => __( 'Leave empty to use logo-horizontal.svg.', 'maison-digitale' ) ) ),
				array( 'url', 'home_link', __( 'Logo link', 'maison-digitale' ), '/' ),
				array( 'text', 'back', __( 'Back link label', 'maison-digitale' ), 'Back to the site' ),
				array( 'url', 'back_link', __( 'Back link', 'maison-digitale' ), '/' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$name = get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Maison Digitale';
		?>
		<div class="mh">
			<a class="logo" <?php echo md_href( $s['home_link'], '/' ); // phpcs:ignore ?> aria-label="<?php echo esc_attr( $name ); ?>"><img src="<?php echo esc_url( md_logo( $s['logo'], 'logo-horizontal.svg' ) ); ?>" alt="<?php echo esc_attr( $name ); ?>"></a>
			<a class="lk" <?php echo md_href( $s['back_link'], '/' ); // phpcs:ignore ?>><?php echo md_svg( 'arrow-back' ); // phpcs:ignore ?><?php echo esc_html( $s['back'] ); ?></a>
		</div>
		<?php
	}
}

/* =========================================================================
 * Minimal footer line — BrandAudit.dc.html bottom bar
 * ====================================================================== */
class MD_W_Mini_Footer extends MD_Widget {
	protected $md_icon = 'eicon-footer';

	public function get_name() {
		return 'md-mini-footer';
	}

	public function get_title() {
		return __( 'Minimal footer line', 'maison-digitale' );
	}

	protected function register_controls() {
		$this->sec( 'c', __( 'Content', 'maison-digitale' ) );
		$this->fields(
			array(
				array( 'text', 'left', __( 'Left', 'maison-digitale' ), '© {year} Maison Digitale. All rights reserved.' ),
				array( 'text', 'right', __( 'Right', 'maison-digitale' ), '[email] · [phone / WhatsApp]' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="mf">
			<span><?php echo esc_html( str_replace( '{year}', gmdate( 'Y' ), $s['left'] ) ); ?></span>
			<span><?php echo esc_html( $s['right'] ); ?></span>
		</div>
		<?php
	}
}
