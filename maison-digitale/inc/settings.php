<?php
/**
 * Theme settings: the admin page (Appearance → Maison Digitale) and the
 * "Maison Digitale page" panel inside Elementor's page settings.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

/**
 * Elementor templates available for header, footer and article call to action.
 *
 * @return array id => title
 */
function md_template_choices() {
	$choices = array( '' => __( '— Theme default —', 'maison-digitale' ) );
	if ( ! post_type_exists( 'elementor_library' ) ) {
		return $choices;
	}
	$posts = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	foreach ( $posts as $p ) {
		$choices[ (string) $p->ID ] = $p->post_title;
	}
	return $choices;
}

/**
 * Page settings inside Elementor (the cog icon, bottom-left of the editor).
 *
 * @param \Elementor\Core\Base\Document $document Document.
 */
function md_register_page_settings( $document ) {
	if ( ! method_exists( $document, 'get_name' ) ) {
		return;
	}
	$type = $document->get_name();
	if ( in_array( $type, array( 'section', 'header', 'footer', 'kit', 'popup', 'container', 'loop-item' ), true ) ) {
		return;
	}

	$document->start_controls_section(
		'md_page_section',
		array(
			'label' => __( 'Maison Digitale page', 'maison-digitale' ),
			'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
		)
	);

	$document->add_control(
		'md_nav',
		array(
			'label'       => __( 'Navigation', 'maison-digitale' ),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'default'     => 'light',
			'options'     => array(
				'light'   => __( 'Light page (ink logo and links)', 'maison-digitale' ),
				'dark'    => __( 'Dark page (ivory logo and links)', 'maison-digitale' ),
				'overlay' => __( 'Over a dark hero (ivory, sits on top of the first section)', 'maison-digitale' ),
				'home'    => __( 'Homepage: fixed, full-screen snap panels, switches light/dark per panel', 'maison-digitale' ),
				'minimal' => __( 'Minimal: logo and "Back to the site" only (brand audit)', 'maison-digitale' ),
			),
			'description' => __( 'Save and reload the preview to see the change.', 'maison-digitale' ),
		)
	);

	$document->add_control(
		'md_footer',
		array(
			'label'   => __( 'Footer', 'maison-digitale' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'full',
			'options' => array(
				'full'    => __( 'Full footer', 'maison-digitale' ),
				'minimal' => __( 'Minimal line (copyright and contact)', 'maison-digitale' ),
				'none'    => __( 'None (the homepage closing panel carries its own footer)', 'maison-digitale' ),
			),
		)
	);

	$document->add_control(
		'md_bg',
		array(
			'label'   => __( 'Page background', 'maison-digitale' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'ground',
			'options' => array(
				'ground' => __( 'Ivory', 'maison-digitale' ),
				'ink'    => __( 'Black', 'maison-digitale' ),
			),
		)
	);

	$document->add_control(
		'md_dir',
		array(
			'label'       => __( 'Text direction', 'maison-digitale' ),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'default'     => '',
			'options'     => array(
				''    => __( 'Site default', 'maison-digitale' ),
				'rtl' => __( 'Arabic, right to left', 'maison-digitale' ),
			),
			'description' => __( 'Use for Arabic pages when the site itself is in English.', 'maison-digitale' ),
		)
	);

	$choices = md_template_choices();
	$document->add_control(
		'md_header_tpl',
		array(
			'label'       => __( 'Header template', 'maison-digitale' ),
			'type'        => \Elementor\Controls_Manager::SELECT,
			'default'     => '',
			'options'     => $choices,
			'description' => __( 'Leave on default to use the site header. Pick "Site Header (Arabic)" for Arabic pages.', 'maison-digitale' ),
		)
	);
	$document->add_control(
		'md_footer_tpl',
		array(
			'label'   => __( 'Footer template', 'maison-digitale' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => $choices,
		)
	);

	$document->end_controls_section();
}
add_action( 'elementor/documents/register_controls', 'md_register_page_settings' );

/* -------------------------------------------------------------------------
 * Admin page
 * ---------------------------------------------------------------------- */

function md_admin_menu() {
	add_theme_page(
		__( 'Maison Digitale', 'maison-digitale' ),
		__( 'Maison Digitale', 'maison-digitale' ),
		'edit_theme_options',
		'maison-digitale',
		'md_admin_page'
	);
}
add_action( 'admin_menu', 'md_admin_menu' );

function md_register_settings() {
	register_setting(
		'md_options_group',
		'md_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'md_sanitize_options',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'md_register_settings' );

/**
 * @param array $in Raw input.
 * @return array
 */
function md_sanitize_options( $in ) {
	$in  = is_array( $in ) ? $in : array();
	$out = get_option( 'md_options', array() );
	$out = is_array( $out ) ? $out : array();
	foreach ( array( 'header_tpl', 'footer_tpl', 'footer_min_tpl', 'article_cta_tpl', 'header_min_tpl' ) as $k ) {
		if ( isset( $in[ $k ] ) ) {
			$out[ $k ] = absint( $in[ $k ] );
		}
	}
	if ( isset( $in['form_email'] ) ) {
		$emails = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', $in['form_email'] ) ) ) );
		$out['form_email'] = implode( ', ', $emails );
	}
	if ( isset( $in['loader_once'] ) || isset( $in['_submitted'] ) ) {
		$out['loader_once'] = empty( $in['loader_once'] ) ? 0 : 1;
	}
	return $out;
}

function md_admin_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$opts      = get_option( 'md_options', array() );
	$choices   = md_template_choices();
	$elementor = did_action( 'elementor/loaded' );
	$imported  = get_option( 'md_demo_imported' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Maison Digitale', 'maison-digitale' ); ?></h1>

		<?php if ( isset( $_GET['md_imported'] ) ) : // phpcs:ignore ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Demo pages imported. Your homepage is set. Open any page and click "Edit with Elementor" to change its content.', 'maison-digitale' ); ?></p></div>
		<?php endif; ?>

		<?php if ( ! $elementor ) : ?>
			<div class="notice notice-warning"><p><?php esc_html_e( 'Install and activate the free Elementor plugin first (Plugins → Add New → search "Elementor"). Every section of this theme is an Elementor widget.', 'maison-digitale' ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( '1. Demo pages', 'maison-digitale' ); ?></h2>
		<p><?php esc_html_e( 'Creates every page of the approved design (Home, Arabic home, About, Services and the nine service pages, Portfolio, a case study, Blog with sample notes, FAQ, Contact, Free Brand Audit, Let\'s Work Together), the site header and footer templates, and sets the homepage. Existing pages are never deleted.', 'maison-digitale' ); ?></p>
		<?php if ( $imported ) : ?>
			<p><strong><?php esc_html_e( 'Already imported.', 'maison-digitale' ); ?></strong> <?php esc_html_e( 'Importing again creates a second copy of each page.', 'maison-digitale' ); ?></p>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="md_import_demo">
			<?php wp_nonce_field( 'md_import_demo' ); ?>
			<?php submit_button( $imported ? __( 'Import demo pages again', 'maison-digitale' ) : __( 'Import demo pages', 'maison-digitale' ), $imported ? 'secondary' : 'primary', 'submit', false, $elementor ? array() : array( 'disabled' => 'disabled' ) ); ?>
		</form>

		<h2 style="margin-top:40px"><?php esc_html_e( '2. Site parts and forms', 'maison-digitale' ); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'md_options_group' ); ?>
			<input type="hidden" name="md_options[_submitted]" value="1">
			<table class="form-table" role="presentation">
				<?php
				$rows = array(
					'header_tpl'      => __( 'Site header (Elementor template)', 'maison-digitale' ),
					'footer_tpl'      => __( 'Site footer (Elementor template)', 'maison-digitale' ),
					'header_min_tpl'  => __( 'Minimal header, brand audit page', 'maison-digitale' ),
					'footer_min_tpl'  => __( 'Minimal footer line, brand audit page', 'maison-digitale' ),
					'article_cta_tpl' => __( 'Call to action under every blog note', 'maison-digitale' ),
				);
				foreach ( $rows as $key => $label ) :
					$val = isset( $opts[ $key ] ) ? (string) $opts[ $key ] : '';
					?>
					<tr>
						<th scope="row"><label for="md-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
						<td>
							<select id="md-<?php echo esc_attr( $key ); ?>" name="md_options[<?php echo esc_attr( $key ); ?>]">
								<?php foreach ( $choices as $id => $title ) : ?>
									<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $val, (string) $id ); ?>><?php echo esc_html( $title ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php if ( $val ) : ?>
								<a href="<?php echo esc_url( admin_url( 'post.php?post=' . (int) $val . '&action=elementor' ) ); ?>"><?php esc_html_e( 'Edit with Elementor', 'maison-digitale' ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<th scope="row"><label for="md-form-email"><?php esc_html_e( 'Send form entries to', 'maison-digitale' ); ?></label></th>
					<td>
						<input id="md-form-email" class="regular-text" type="text" name="md_options[form_email]" value="<?php echo esc_attr( isset( $opts['form_email'] ) ? $opts['form_email'] : '' ); ?>" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>">
						<p class="description"><?php esc_html_e( 'Contact, newsletter, brand audit and project enquiry forms. Separate several addresses with commas. Every entry is also saved under "Form entries" in the dashboard.', 'maison-digitale' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Homepage loader', 'maison-digitale' ); ?></th>
					<td><label><input type="checkbox" name="md_options[loader_once]" value="1" <?php checked( ! empty( $opts['loader_once'] ) ); ?>> <?php esc_html_e( 'Show the loading screen only on the first visit of a browsing session', 'maison-digitale' ); ?></label></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>

		<h2 style="margin-top:40px"><?php esc_html_e( 'How editing works', 'maison-digitale' ); ?></h2>
		<ul style="list-style:disc;padding-left:20px;max-width:780px">
			<li><?php esc_html_e( 'Each page is made of Maison Digitale widgets (find them in the Elementor panel under "Maison Digitale"). Click a section to edit its text, images, links and lists. Drag sections to reorder, duplicate or delete them.', 'maison-digitale' ); ?></li>
			<li><?php esc_html_e( 'Wrap words in *asterisks* inside a headline to set them in gold, as in the design.', 'maison-digitale' ); ?></li>
			<li><?php esc_html_e( 'Homepage panels: every "Home —" widget is one full-screen panel. The side dots are built automatically from the panels, in order. Add, remove or reorder panels freely.', 'maison-digitale' ); ?></li>
			<li><?php esc_html_e( 'Page look (navigation colour, homepage snapping, footer, Arabic direction) is set in Elementor\'s page settings: the cog icon at the bottom left of the editor → "Maison Digitale page".', 'maison-digitale' ); ?></li>
			<li><?php esc_html_e( 'Header and footer are Elementor templates under Templates → Saved Templates. Edit them once; they appear on every page.', 'maison-digitale' ); ?></li>
			<li><?php esc_html_e( 'Text in [square brackets] is a placeholder from the design for content still to be supplied.', 'maison-digitale' ); ?></li>
		</ul>
	</div>
	<?php
}

/**
 * Nudge after activation.
 */
function md_admin_notice() {
	if ( get_option( 'md_demo_imported' ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'appearance_page_maison-digitale' === $screen->id ) {
		return;
	}
	echo '<div class="notice notice-info"><p><strong>Maison Digitale:</strong> ' . esc_html__( 'activate Elementor, then import the demo pages to get the full website.', 'maison-digitale' ) . ' <a class="button button-primary" href="' . esc_url( admin_url( 'themes.php?page=maison-digitale' ) ) . '">' . esc_html__( 'Set up the site', 'maison-digitale' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'md_admin_notice' );
