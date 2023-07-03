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
 if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently augustin plugin version.
 */
define( 'AUGUSTIN_VERSION', '0.0.1' );



if ( !defined( 'AUGUSTIN_DIR' ) ) {
	define( 'AUGUSTIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( !defined( 'AUGUSTIN_ADMIN' ) ) {
	define( 'AUGUSTIN_ADMIN', AUGUSTIN_DIR . '/includes/admin/admin.php' );
    require_once AUGUSTIN_ADMIN;
}

if ( !defined( 'AUGUSTIN_KEYCLOAK' ) ) {
	define( 'AUGUSTIN_KEYCLOAK', AUGUSTIN_DIR . '/includes/keycloak/keycloak.php' );
    require_once AUGUSTIN_KEYCLOAK;
}
if ( !defined( 'AUGUSTIN_PDF_PROCESSING' ) ) {
	define( 'AUGUSTIN_PDF_PROCESSING', AUGUSTIN_DIR . '/includes/pdf_processing/pdf_processing.php' );
    require_once AUGUSTIN_PDF_PROCESSING;
}

require_once AUGUSTIN_DIR . '/includes/init.php';
