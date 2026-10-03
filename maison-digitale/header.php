<?php
/**
 * Document head and site header.
 *
 * @package MaisonDigitale
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="md-skip" href="#md-main"><?php esc_html_e( 'Skip to content', 'maison-digitale' ); ?></a>
<div class="md-page">
<?php md_site_header(); ?>
<main id="md-main" class="md-main">
