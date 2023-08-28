<?php
/**
 * Plugin Name: Augustin
 * Plugin URI: https://augustin.or.at/
 * Description: All things related to the augustin.
 * Version: 0.1
 * Author: Convive*
 * Author URI: https://convive.io/
 * */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
add_action('admin_menu', 'augustin_wp_papers_setup_menu');

function augustin_wp_papers_setup_menu()
{
    add_menu_page('Zeitung', 'Zeitung', 'manage_options', 'augustin-wp-papers', 'test_init');
}

function test_init()
{
    echo "<h1>Zeitungs Upload</h1>";
    echo "<p>Bitte laden Sie hier die Zeitung als PDF hoch.</p>";
?>
    <div class="wrap">
        <iframe src="http://localhost:8070/" width="100%" height="500px"></iframe>
    </div>
<?php
}
