<?php
/**
 * Form handling: contact, newsletter, brand audit and project enquiry.
 * Every entry is emailed and saved under "Form entries" in the dashboard.
 *
 * @package MaisonDigitale
 */

defined( 'ABSPATH' ) || exit;

function md_register_submissions() {
	register_post_type(
		'md_entry',
		array(
			'labels'              => array(
				'name'          => __( 'Form entries', 'maison-digitale' ),
				'singular_name' => __( 'Form entry', 'maison-digitale' ),
				'edit_item'     => __( 'Form entry', 'maison-digitale' ),
				'search_items'  => __( 'Search entries', 'maison-digitale' ),
				'not_found'     => __( 'No entries yet.', 'maison-digitale' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 26,
			'supports'            => array( 'title', 'editor' ),
			'capability_type'     => 'post',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'md_register_submissions' );

/**
 * AJAX endpoint for every form on the site.
 */
function md_handle_form() {
	check_ajax_referer( 'md_form', 'nonce' );

	// Honeypot: bots fill every field.
	if ( ! empty( $_POST['md_hp'] ) ) {
		wp_send_json_success();
	}

	$types = array(
		'contact'    => __( 'Contact message', 'maison-digitale' ),
		'newsletter' => __( 'Newsletter sign-up', 'maison-digitale' ),
		'audit'      => __( 'Brand audit', 'maison-digitale' ),
		'enquiry'    => __( 'Project enquiry', 'maison-digitale' ),
	);
	$type = isset( $_POST['form'] ) ? sanitize_key( wp_unslash( $_POST['form'] ) ) : 'contact';
	if ( ! isset( $types[ $type ] ) ) {
		$type = 'contact';
	}

	$raw    = isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$fields = array();
	$email  = '';
	$name   = '';
	foreach ( $raw as $label => $value ) {
		$label = sanitize_text_field( (string) $label );
		if ( '' === $label || count( $fields ) > 60 ) {
			continue;
		}
		$value = is_array( $value ) ? implode( ', ', array_map( 'sanitize_text_field', $value ) ) : sanitize_textarea_field( (string) $value );
		$value = mb_substr( $value, 0, 5000 );
		$fields[ $label ] = $value;
		if ( ! $email && is_email( $value ) ) {
			$email = $value;
		}
		if ( ! $name && preg_match( '/name|الاسم/i', $label ) && ! preg_match( '/business|company/i', $label ) ) {
			$name = $value;
		}
	}

	if ( 'newsletter' === $type && ! $email ) {
		wp_send_json_error( array( 'message' => __( 'Enter a complete email address.', 'maison-digitale' ) ), 400 );
	}
	if ( empty( $fields ) ) {
		wp_send_json_error( array( 'message' => __( 'The form is empty.', 'maison-digitale' ) ), 400 );
	}

	$page = isset( $_POST['page'] ) ? esc_url_raw( wp_unslash( $_POST['page'] ) ) : '';
	$body = '';
	foreach ( $fields as $label => $value ) {
		$body .= $label . ":\n" . $value . "\n\n";
	}
	if ( $page ) {
		$body .= __( 'Sent from', 'maison-digitale' ) . ': ' . $page . "\n";
	}

	$title = $types[ $type ] . ( $name ? ' — ' . $name : ( $email ? ' — ' . $email : '' ) );
	wp_insert_post(
		array(
			'post_type'    => 'md_entry',
			'post_status'  => 'private',
			'post_title'   => $title,
			'post_content' => $body,
		)
	);

	$to = md_opt( 'form_email', get_option( 'admin_email' ) );
	$headers = array();
	if ( $email ) {
		$headers[] = 'Reply-To: ' . ( $name ? $name . ' <' . $email . '>' : $email );
	}
	wp_mail( $to, '[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . $title, $body, $headers );

	wp_send_json_success();
}
add_action( 'wp_ajax_md_form', 'md_handle_form' );
add_action( 'wp_ajax_nopriv_md_form', 'md_handle_form' );
