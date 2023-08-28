<?php

/**
 * Plugin Name: Augustin
 * Plugin URI: https://augustin.or.at/
 * Description: All things related to the augustin.
 * Version: 0.1
 * Author: Convive*
 * Author URI: https://convive.io/
 **/

//  Exit  if  accessed  directly.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently augustin plugin version.
 */
define('AUGUSTIN_VERSION', '0.0.1');


if (!defined('AUGUSTIN_DIR')) {
	define('AUGUSTIN_DIR', untrailingslashit(plugin_dir_path(__FILE__)));
}

if (!defined('AUGUSTIN_ADMIN')) {
	define('AUGUSTIN_ADMIN', AUGUSTIN_DIR . '/includes/admin/admin.php');
	require_once AUGUSTIN_ADMIN;
}

if (!defined('AUGUSTIN_KEYCLOAK')) {
	define('AUGUSTIN_KEYCLOAK', AUGUSTIN_DIR . '/includes/keycloak/keycloak.php');
	require_once AUGUSTIN_KEYCLOAK;
}
if (!defined('AUGUSTIN_PDF_PROCESSING')) {
	define('AUGUSTIN_PDF_PROCESSING', AUGUSTIN_DIR . '/includes/pdf_processing/pdf_processing.php');
	require_once AUGUSTIN_PDF_PROCESSING;
}
function prefix_editor_assets()
{
	wp_enqueue_script(
		'augustin-query-papers',
		plugins_url('/includes/blocks/query-papers.js', __FILE__),
		array('wp-blocks', 'wp-dom-ready', 'wp-edit-post'),
		filemtime(plugin_dir_path(__FILE__) . '/includes/blocks/query-papers.js')
	);
	wp_enqueue_script(
		'prefix-block-variations',
		plugin_dir_path(__FILE__) . 'blocks/query-papers.js',
		array('jquery'),
		'',
		true
	);
}
add_action('enqueue_block_editor_assets', 'prefix_editor_assets');
require_once(AUGUSTIN_DIR . '/includes/init.php');
add_action('init', 'augustin_paper_init');
