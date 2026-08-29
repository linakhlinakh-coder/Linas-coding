<?php
/**
 * Imports the real blog archive from pierrekhoury.com, supplied as a
 * standard WordPress export (WXR) file bundled at inc/data/legacy-posts.xml.
 *
 * This intentionally does NOT use the core WordPress Importer plugin (which
 * would also try to pull in the 64 attachment/media items and rewrite
 * upload URLs) — the site owner is adding featured images directly in
 * wp-admin, so this only needs title, content, date, categories and tags
 * for each published post.
 *
 * Runs in small batches from Settings → Pierre Khoury (never automatically
 * on theme activation — 225 posts can easily exceed a shared host's PHP
 * execution time limit in one request) and is fully idempotent: every
 * import is de-duplicated against the post's original WXR <guid>, stored
 * as post meta, so clicking "Import" repeatedly just picks up where the
 * last batch left off.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PK_LEGACY_IMPORT_BATCH', 40 );

function pk_legacy_posts_file() {
	return get_template_directory() . '/inc/data/legacy-posts.xml';
}

/**
 * Parses the bundled WXR file into a plain array of published posts.
 * Cheap enough (a few hundred KB of DOM) to re-run on every batch rather
 * than caching, and keeps the importer stateless between requests.
 */
function pk_legacy_posts_data() {
	$file = pk_legacy_posts_file();
	if ( ! file_exists( $file ) ) {
		return array();
	}

	$xml = simplexml_load_file( $file );
	if ( ! $xml ) {
		return array();
	}

	$namespaces = $xml->getNamespaces( true );
	$posts      = array();

	foreach ( $xml->channel->item as $item ) {
		$wp = $item->children( $namespaces['wp'] );

		if ( 'post' !== (string) $wp->post_type || 'publish' !== (string) $wp->status ) {
			continue;
		}

		$content_ns = $item->children( $namespaces['content'] );
		$categories = array();
		$tags       = array();
		foreach ( $item->category as $cat ) {
			$domain = (string) $cat['domain'];
			$name   = trim( (string) $cat );
			if ( '' === $name ) {
				continue;
			}
			if ( 'category' === $domain ) {
				$categories[] = $name;
			} elseif ( 'post_tag' === $domain ) {
				$tags[] = $name;
			}
		}

		$posts[] = array(
			'guid'           => trim( (string) $item->guid ),
			'title'          => trim( (string) $item->title ),
			'link'           => trim( (string) $item->link ),
			'content'        => (string) $content_ns->encoded,
			'date'           => (string) $wp->post_date,
			'date_gmt'       => (string) $wp->post_date_gmt,
			'slug'           => (string) $wp->post_name,
			'comment_status' => (string) $wp->comment_status,
			'ping_status'    => (string) $wp->ping_status,
			'categories'     => $categories,
			'tags'           => $tags,
		);
	}

	return $posts;
}

function pk_legacy_import_counts() {
	$total    = get_option( 'pk_legacy_import_total' );
	if ( false === $total ) {
		$total = count( pk_legacy_posts_data() );
		update_option( 'pk_legacy_import_total', $total );
	}
	$imported = (int) get_option( 'pk_legacy_import_done', 0 );
	return array( 'total' => (int) $total, 'imported' => $imported );
}

/**
 * Finds an already-imported post by the original WXR guid, stored as
 * post meta on import.
 */
function pk_legacy_find_existing( $guid ) {
	$found = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_pk_legacy_guid',
			'meta_value'     => $guid,
		)
	);
	return ! empty( $found ) ? (int) $found[0] : 0;
}

/**
 * Imports up to PK_LEGACY_IMPORT_BATCH new posts and returns a summary.
 * Safe to call repeatedly — already-imported posts are skipped quickly.
 */
function pk_run_legacy_import_batch() {
	@set_time_limit( 90 );

	kses_remove_filters();

	$posts       = pk_legacy_posts_data();
	$inserted    = 0;
	$skipped     = 0;
	$category_ids = array();

	foreach ( $posts as $p ) {
		if ( $inserted >= PK_LEGACY_IMPORT_BATCH ) {
			break;
		}

		if ( pk_legacy_find_existing( $p['guid'] ) ) {
			$skipped++;
			continue;
		}

		$cat_ids = array();
		foreach ( $p['categories'] as $cat_name ) {
			if ( ! isset( $category_ids[ $cat_name ] ) ) {
				$term = term_exists( $cat_name, 'category' );
				if ( ! $term ) {
					$term = wp_insert_term( $cat_name, 'category' );
				}
				$category_ids[ $cat_name ] = is_wp_error( $term ) ? 0 : (int) $term['term_id'];
			}
			if ( $category_ids[ $cat_name ] ) {
				$cat_ids[] = $category_ids[ $cat_name ];
			}
		}

		$post_id = wp_insert_post(
			array(
				'post_title'     => $p['title'],
				'post_name'      => $p['slug'],
				'post_content'   => $p['content'],
				'post_status'    => 'publish',
				'post_type'      => 'post',
				'post_date'      => $p['date'] ? $p['date'] : current_time( 'mysql' ),
				'post_date_gmt'  => $p['date_gmt'] ? $p['date_gmt'] : current_time( 'mysql', 1 ),
				'comment_status' => 'closed' === $p['comment_status'] ? 'closed' : 'open',
				'ping_status'    => 'closed' === $p['ping_status'] ? 'closed' : 'open',
				'post_category'  => $cat_ids,
				'tags_input'     => $p['tags'],
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			$skipped++;
			continue;
		}

		update_post_meta( $post_id, '_pk_legacy_guid', $p['guid'] );
		if ( $p['link'] ) {
			update_post_meta( $post_id, '_pk_legacy_source_link', $p['link'] );
		}
		$inserted++;
	}

	kses_init_filters();

	if ( $inserted > 0 ) {
		update_option( 'pk_legacy_import_done', (int) get_option( 'pk_legacy_import_done', 0 ) + $inserted );
	}

	return array(
		'inserted' => $inserted,
		'skipped'  => $skipped,
		'total'    => count( $posts ),
	);
}

/**
 * Admin-triggered batch runner — see Settings → Pierre Khoury.
 */
function pk_maybe_run_legacy_import_from_admin() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! isset( $_GET['pk_import_legacy'] ) || ! check_admin_referer( 'pk_import_legacy' ) ) {
		return;
	}

	$result = pk_run_legacy_import_batch();

	$redirect = add_query_arg(
		array(
			'pk_legacy_imported' => $result['inserted'],
			'pk_legacy_skipped'  => $result['skipped'],
		),
		remove_query_arg( array( 'pk_import_legacy', '_wpnonce' ) )
	);
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_init', 'pk_maybe_run_legacy_import_from_admin' );
