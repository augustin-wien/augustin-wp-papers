<?php

/**
 * Plugin Name: Augustin
 * Plugin URI: https://augustin.or.at/
 * Description: All things related to the augustin.
 * Version: 0.1
 * Author: Convive*
 * Author URI: https://convive.io/
 * */
if (!defined('ABSPATH')) {
    exit;
}

function augustin_paper_init()
{

    $papers_labels = array(
        'name' => _x('Papers', 'post type general name'),
        'singular_name' => _x('Papers', 'post type singular name'),
        'add_new' => _x('Add New', 'Papers'),
        'add_new_item' => __('Add New Paper'),
        'edit_item' => __('Edit Papers'),
        'new_item' => __('New Papers'),
        'all_items' => __('All Papers'),
        'view_item' => __('View Papers'),
        'search_items' => __('Search Papers'),
        'not_found' => __('No Papers found'),
        'not_found_in_trash' => __('No Papers found in the Trash'),
        'menu_name' => 'Papers'
    );
    $papers_args = array(
        'labels' => $papers_labels,
        'description' => 'Papers and Papers Related information will be hold on this',
        'menu' => 5,
        'menu_icon' => 'dashicons-admin-post',
        'supports' => array('title', 'editor', 'thumbnail', 'post-format', 'excerpt'),
        'has_archive' => true,
        'taxonomies' => array('category_papers', 'category'),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'show_in_admin_bar'     => true,
        'menu_position'         => 5,
        'can_export'            => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => array('slug' => 'papers'),
        'capability_type'       => 'post',
        'query_var'             => true,
        'show_in_rest'          => true,
    );
    register_taxonomy(
        'category_papers',
        'results',
        array(
            'hierarchical' => true,
            'label' => 'Papers Category',
            'show_admin_column' => true,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => true
        )
    );

    register_post_type('papers', $papers_args);

    $articles_labels = array(
        'name' => _x('Articles', 'post type general name'),
        'singular_name' => _x('Articles', 'post type singular name'),
        'add_new' => _x('Add New', 'Articles'),
        'add_new_item' => __('Add New Paper'),
        'edit_item' => __('Edit Articles'),
        'new_item' => __('New Articles'),
        'all_items' => __('All Articles'),
        'view_item' => __('View Articles'),
        'search_items' => __('Search Articles'),
        'not_found' => __('No Articles found'),
        'not_found_in_trash' => __('No Articles found in the Trash'),
        'menu_name' => 'Articles'
    );
    $articles_args = array(
        'labels' => $articles_labels,
        'description' => 'Articles and Articles Related information will be hold on this',
        'menu' => 5,
        'menu_icon' => 'dashicons-admin-post',
        'supports' => array('title', 'editor', 'thumbnail', 'post-format', 'excerpt'),
        'has_archive' => true,
        'taxonomies' => array('category_papers', 'category'),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'show_in_admin_bar'     => true,
        'menu_position'         => 5,
        'can_export'            => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => array('slug' => 'articles'),
        'capability_type'       => 'post',
        'query_var'             => true,
        'show_in_rest'          => true,
    );
    register_post_type('articles', $articles_args);


    function namespace_share_category_with_pages()
    {
        register_taxonomy_for_object_type('category', 'papers');
        register_taxonomy_for_object_type('category', 'articles');
    }

    add_action('init', 'namespace_share_category_with_pages');
    add_filter('openid-connect-generic-login-button-text', function ($text) {
        $text = __('Login with augustin');
        return $text;
    });

    add_action('openid-connect-generic-user-create', function ($user, $user_claim) {
        augustin_create_group_and_add_as_a_member($user_claim['groups'], $user);
    }, 10, 2);

    add_action('openid-connect-generic-update-user-using-current-claim', function ($user, $user_claim) {
        augustin_create_group_and_add_as_a_member($user_claim['groups'], $user);
    }, 10, 2);


    function augustin_remove_admin_bar()
    {
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }
    }
}

add_filter('query_loop_block_query_vars', 'augustin_papers_filter_query');
function augustin_papers_filter_query($query)
{
    // ignore if the query block is not using this post type
    if ('papers' == $query['post_type']) {
        return papers_filter($query);
    }
    if ('articles' == $query['post_type']) {
        return articles_filter($query);
    }

    return $query;
}
function articles_filter($query)
{
    $tmp_cats = [];
    global $post;
    $paper_cat = get_the_terms($post->ID, 'category_papers');

    for ($i = 0; $i < count($paper_cat); $i++) {
        $name = $paper_cat[$i]->name;
        array_push(
            $tmp_cats,
            $name
        );
    }
    $query['tax_query'] = array(
        'relation' => 'AND',
        $query['tax_query'], // <-- this is the original tax_query
        array('taxonomy' => 'category_papers', 'field' => 'slug', 'terms' => $tmp_cats)
    );

    return $query;
}
function papers_filter($query)
{
    $user_id = get_current_user_id();
    $user = new Groups_User($user_id);
    $groups = $user->groups;
    $tmp_cats = [];

    for ($i = 0; $i < count($groups); $i++) {
        $name = $groups[$i]->name;
        if ($name  == "admin") {
            return $query;
        }
        $category_id = get_term_by('name', $name, 'category_papers');
        if ($category_id && $category_id->term_id != 0) {
            array_push(
                $tmp_cats,
                $name
            );
        }
    }
    $query['tax_query'] = array(
        'relation' => 'OR',

        array('taxonomy' => 'category_papers', 'field' => 'slug', 'terms' => $tmp_cats)
    );
    return $query;
}

add_filter('template_include', 'augustin_papers_permission_check', 99);

/**
 * @param $template
 * @return string
 */
function augustin_papers_permission_check($template)
{
    $user_id = get_current_user_id();
    $user = new Groups_User($user_id);
    $groups = $user->groups;

    global $post;
    if (empty($post)) {
        return $template;
    }
    $category_detail = get_the_terms($post->ID, 'category_papers');

    if (!is_single()) {
        return $template;
    }

    $is_magazin = "false";
    $magazin_category = "";
    if (empty($category_detail)) {
        return $template;
    }
    foreach ($category_detail as $cd) {
        $name = $cd->name;
        if (str_contains($name, "magazin")) {
            $is_magazin = "true";
            $magazin_category = $cd->name;
        }
    }
    if ($is_magazin == "true") {
        foreach ($groups as $group) {
            $name = $group->name;
            if ($name  == $magazin_category || $name  == "admin") {
                return $template;
            }
        }
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . get_bloginfo('url') . "/401");
        exit();
    }


    return $template;
}
