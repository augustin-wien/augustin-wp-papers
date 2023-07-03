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
    <form id="upload_form" action="/wp-content/plugins/augustin-wp-papers/includes/upload.php" enctype="multipart/form-data" method="post" target="messages">
        <p><input name="upload" id="upload" type="file" accept="pdf" /></p>
        <p><input id="btnSubmit" type="submit" value="Upload newspaper as pdf" /></p>
        <iframe name="messages" id="messages" width="100%" height="500px"></iframe>
      </form>
    </div>
<?php
}
