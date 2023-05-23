<?php

/**
 * Plugin Name: Augustin
 * Plugin URI: https://augustin.or.at/
 * Description: All things related to the augustin.
 * Version: 0.1
 * Author: Convive*
 * Author URI: https://convive.io/
 **/

function console_log_message($message)
{

    $message = htmlspecialchars(stripslashes($message));
    //Replacing Quotes, so that it does not mess up the script
    $message = str_replace('"', "-", $message);
    $message = str_replace("'", "-", $message);

    return "<script>console.log('{$message}')</script>";
}

function create_group_and_add_as_a_member(
    $user_groups,
    $user = null
) {

    require_once(ABSPATH . 'wp-includes/pluggable.php');
    for ($i = 0; $i < count($user_groups); $i++) {
        $is_a_member = false;
        if ($group = Groups_Group::read_by_name($user_groups[$i])) {
            $is_a_member = Groups_User_Group::read($user->ID, $group->group_id);
            if (!$is_a_member) {
                print_r("add user " . $user->ID . " to group\n");
                Groups_User_Group::create(array('user_id' => $user->ID, 'group_id' => $group->group_id));
            }
        } else {
            print_r("create group\n");
            $group = Groups_Group::create([
                'name' => $user_groups[$i],
                'description' => 'An automatically created keycloak group' . $user_groups[$i],
            ]);
            $group->save();
            print_r("add user to group2\n");
            Groups_User_Group::create(array('user_id' => $user_id, 'group_id' => $group->group_id));
        }
    }
    if (in_array('admin', $user_groups)) {
        $user->set_role('administrator');
        $user->save();
    } else {
        $user->set_role('subscriber');
        $user->save();
    }
}
add_filter('openid-connect-generic-login-button-text', function ($text) {
    $text = __('Login to my super cool IDP server');
    return $text;
});

add_action('openid-connect-generic-user-create', function ($user, $user_claim) {
    create_group_and_add_as_a_member($user_claim['groups'], $user);
}, 10, 2);

add_action('openid-connect-generic-update-user-using-current-claim', function ($user, $user_claim) {
    print_r($user_claim);
    create_group_and_add_as_a_member($user_claim['groups'], $user);
}, 10, 2);


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
}

add_action('init', 'augustin_paper_init');


add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar()
{
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
