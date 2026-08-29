<?php
/**
 * A minimal Settings page (Settings → Pierre Khoury) for the two values a
 * non-developer site owner needs to configure after launch: the WhatsApp
 * number used by every "Message on WhatsApp" CTA, and the Contact Form 7
 * form ID that powers the Contact page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pk_register_settings_page() {
	add_options_page(
		__( 'Pierre Khoury Theme', 'pierre-khoury' ),
		__( 'Pierre Khoury', 'pierre-khoury' ),
		'manage_options',
		'pierre-khoury-settings',
		'pk_render_settings_page'
	);
}
add_action( 'admin_menu', 'pk_register_settings_page' );

function pk_register_settings() {
	register_setting( 'pk_settings', 'pk_whatsapp_number', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'pk_settings', 'pk_contact_form_id', array( 'sanitize_callback' => 'absint' ) );
}
add_action( 'admin_init', 'pk_register_settings' );

function pk_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$reseed_url = wp_nonce_url( add_query_arg( 'pk_reseed', '1' ), 'pk_reseed' );
	$import_url = wp_nonce_url( add_query_arg( 'pk_import_legacy', '1' ), 'pk_import_legacy' );
	$counts     = pk_legacy_import_counts();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Pierre Khoury Theme Settings', 'pierre-khoury' ); ?></h1>

		<?php if ( isset( $_GET['pk_legacy_imported'] ) ) : ?>
			<div class="notice notice-success">
				<p>
					<?php
					printf(
						/* translators: 1: number of posts imported this batch, 2: number of posts skipped (already imported) */
						esc_html__( 'Imported %1$d post(s) this batch (%2$d already existed and were skipped).', 'pierre-khoury' ),
						(int) $_GET['pk_legacy_imported'],
						(int) ( $_GET['pk_legacy_skipped'] ?? 0 )
					);
					?>
				</p>
			</div>
		<?php endif; ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'pk_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="pk_whatsapp_number"><?php esc_html_e( 'WhatsApp number', 'pierre-khoury' ); ?></label></th>
					<td>
						<input name="pk_whatsapp_number" id="pk_whatsapp_number" type="text" class="regular-text" value="<?php echo esc_attr( get_option( 'pk_whatsapp_number', '' ) ); ?>" placeholder="96170000000" />
						<p class="description"><?php esc_html_e( 'Digits only, with country code (e.g. 96170000000 for a Lebanese +961 70 000 000 number). Used by every "Message on WhatsApp" button.', 'pierre-khoury' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="pk_contact_form_id"><?php esc_html_e( 'Contact Form 7 — Form ID', 'pierre-khoury' ); ?></label></th>
					<td>
						<input name="pk_contact_form_id" id="pk_contact_form_id" type="number" class="regular-text" value="<?php echo esc_attr( get_option( 'pk_contact_form_id', '' ) ); ?>" />
						<p class="description">
							<?php if ( class_exists( 'WPCF7' ) ) : ?>
								<?php esc_html_e( 'Create a form under Contact → Contact Forms with fields matching the brief (Full Name, Organization, Email, Phone/WhatsApp, Country/City, Service dropdown, Message), then paste its numeric ID here.', 'pierre-khoury' ); ?>
							<?php else : ?>
								<strong><?php esc_html_e( 'Contact Form 7 is not active yet.', 'pierre-khoury' ); ?></strong>
								<?php esc_html_e( 'Install & activate it first, then create your form and enter its ID here.', 'pierre-khoury' ); ?>
							<?php endif; ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>

		<hr />
		<h2><?php esc_html_e( 'Content', 'pierre-khoury' ); ?></h2>
		<p><?php esc_html_e( 'The theme creates the Home, About, Services, 5 service pages, Track Record, Blog and Contact pages automatically the first time it is activated, using the approved copy. If a page seems to be missing, use the button below to fill in anything that did not get created — pages you already edited will not be touched.', 'pierre-khoury' ); ?></p>
		<p><a href="<?php echo esc_url( $reseed_url ); ?>" class="button"><?php esc_html_e( 'Re-run content setup', 'pierre-khoury' ); ?></a></p>

		<hr />
		<h2><?php esc_html_e( 'Legacy blog archive', 'pierre-khoury' ); ?></h2>
		<p>
			<?php
			printf(
				/* translators: 1: number already imported, 2: total posts found in the bundled export */
				esc_html__( 'Imports the real posts from the pierrekhoury.com export bundled with this theme (titles, content, dates, categories & tags — no images, those are added directly in the Media Library). Imported so far: %1$d of %2$d.', 'pierre-khoury' ),
				(int) $counts['imported'],
				(int) $counts['total']
			);
			?>
		</p>
		<?php if ( $counts['imported'] < $counts['total'] ) : ?>
			<p>
				<a href="<?php echo esc_url( $import_url ); ?>" class="button button-primary">
					<?php esc_html_e( 'Import next batch of posts', 'pierre-khoury' ); ?>
				</a>
				<span class="description">
					<?php
					printf(
						/* translators: %d: batch size */
						esc_html__( ' Imports up to %d posts per click — click again to continue until all are imported. Safe to click repeatedly; already-imported posts are skipped.', 'pierre-khoury' ),
						(int) PK_LEGACY_IMPORT_BATCH
					);
					?>
				</span>
			</p>
		<?php else : ?>
			<p><strong><?php esc_html_e( 'All legacy posts have been imported.', 'pierre-khoury' ); ?></strong></p>
		<?php endif; ?>
	</div>
	<?php
}
