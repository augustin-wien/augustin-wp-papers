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

/**
 * Register a custom post type called "Paper".
 *
 * @see get_post_type_labels() for label keys.
 */
function augustin_paper_init()
{
    register_post_type(
        'papers',
        // WordPress CPT Options Start
        array(
            'labels' => array(
                'name' => __('Papers'),
                'singular_name' => __('Paper')
            ),
            'has_archive' => true,
            'public' => true,
            'rewrite' => array('slug' => 'papers'),
            'show_in_rest' => true,
            'supports' => array('editor', 'excerpt',  'thumbnail', 'title', 'author', 'revisions', 'custom-fields', 'page-attributes', 'post-formats')
        )
    );
    add_filter('openid-connect-generic-login-button-text', function ($text) {
        $text = __('Login to my super cool IDP server');
        return $text;
    });
    
    add_action('openid-connect-generic-user-create', function ($user, $user_claim) {
        augustin_create_group_and_add_as_a_member($user_claim['groups'], $user);
    }, 10, 2);
    
    add_action('openid-connect-generic-update-user-using-current-claim', function ($user, $user_claim) {
        print_r($user_claim);
        augustin_create_group_and_add_as_a_member($user_claim['groups'], $user);
    }, 10, 2);
    
    
    
    
    
    add_action('after_setup_theme', 'augustin_remove_admin_bar');
}

add_action('init', 'augustin_paper_init');

function augustin_remove_admin_bar()
{
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}