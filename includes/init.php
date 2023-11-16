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
$debug = False;
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
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'rewrite' => array('slug' => 'papers'),
        'capability_type' => 'post',
        'query_var' => true,
        'show_in_rest' => true,
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
    register_taxonomy(
        'category_articles',
        'results',
        array(
            'hierarchical' => true,
            'label' => 'Articles Category',
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
        'taxonomies' => array('category', 'category_articles', 'category_papers'),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'can_export' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'rewrite' => array('slug' => 'articles'),
        'capability_type' => 'post',
        'query_var' => true,
        'show_in_rest' => true,
    );
    register_post_type('articles', $articles_args);


    function namespace_share_category_with_pages()
    {
        register_taxonomy_for_object_type('category', 'papers');
        register_taxonomy_for_object_type('category', 'articles');
    }

    // set the name of the keycloak login button
    // todo: put it in the settings
    add_action('init', 'namespace_share_category_with_pages');
    add_filter('openid-connect-generic-login-button-text', function ($text) {
        $text = __('Login with augustin');
        return $text;
    });

    // hooks to create users and groups on login
    add_action('openid-connect-generic-user-create', function ($user, $user_claim) {
        augustin_create_group_and_add_as_a_member($user_claim['groups'], $user);
    }, 10, 2);

    add_action('openid-connect-generic-update-user-using-current-claim', function ($user, $user_claim) {
        augustin_create_group_and_add_as_a_member($user_claim['groups'], $user);
    }, 10, 2);


    // if we are on a newspaper page, sort the articles by the newspaper categories
    $terms = get_terms('category', array('fields' => 'ids'));
    $orderby_terms = function ($sql, $query) use ($terms, &$orderby_terms) {
        global $debug;
        if ($debug) {
            print_r("n");
        }
        if (isset($_GET['np']))
            return $sql;

        if ($debug) {
            print_r("n1");
        }
        // sort the posts by the terms array -> richtige reihenfolge der kategorien 
        $is_block_editor = false;

        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if (isset($screen)) {
                $is_block_editor = $screen->is_block_editor;
            }
        }
        // don't filter in the block editor
        if (strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false) {
            return $sql;
        }
        if (is_category() || is_tag() || is_single()) {
            return $sql;
        }
        if ($query->get("post_type") === "articles" && !$is_block_editor) {
            $field = $GLOBALS['wpdb']->term_relationships . '.term_taxonomy_id';
            $terms = array_map('intval', $terms);
            $sql = sprintf(
                'FIELD( %1$s, %2$s ) ',
                $field,
                implode(', ', $terms)
            );
            remove_filter(current_filter(), $orderby_terms);

        }

        return $sql;
    };
    add_filter('posts_orderby', $orderby_terms, 10, 2);

    // remove the admin bar for non admins
    function augustin_remove_admin_bar()
    {
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }
    }

    require plugin_dir_path( __FILE__ ) . 'blocks/category-menu/category-menu.php';
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

function get_current_newspaper()
{
    if (isset($_GET['np'])) {
        return $_GET['np'];
    }
    if (isset($_COOKIE["current-newspaper"])) {
        return $_COOKIE["current-newspaper"];
    }
    return null;
}

// only show articles of the selected newspaper
add_filter('pre_get_posts', 'query_post_type');
$category = "";
function query_post_type($query)
{
    global $category;
    if (is_category() || is_tag() || is_author()) {
        $post_type = $query->get("post_type");
        if (get_current_newspaper() && $post_type = "articles") {
            $post_type = $query->get("post_type");
            if ($post_type == "wp_global_styles" || $post_type == "wp_template_part" || $post_type == "wp_template")
                return $query;
            // print_r(is_author());

            // limit the query to the selected newspaper
            $newspaper_category = get_current_newspaper();
            // // get id of newspaper category
            $lCategory = $query->get("category_name");
            if ($lCategory != "") {
                $category = $lCategory;
            }
            $termIds = get_terms([
                'name__like' => $newspaper_category,
                'fields' => 'ids'
            ]);
            // Get the existing tax_query
            $query->set("post_type", "articles");
            if (!is_author()) {
                $query->set(
                    "tax_query",
                    array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'category_papers',
                            'field' => 'id',
                            'terms' => $termIds,
                        ),
                        array(
                            'taxonomy' => 'category',
                            'field' => 'slug',
                            'terms' => $category,
                        )
                    )
                );
            } else {
                $query->set(
                    "tax_query",
                    array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'category_papers',
                            'field' => 'id',
                            'terms' => $termIds,
                        )
                    )
                );
            }



            return $query;
        } else {
            // modify query for general category pages
            $post_type = get_query_var('post_type');
            if ($post_type)
                $post_type = $post_type;
            else
                $post_type = array('post', 'articles', 'nav_menu_item');

            $query->set('post_type', $post_type);
            // header("HTTP/1.1 301 Moved Permanently");
            // header("Location: " . get_bloginfo('url') . "/401");
            return $query;
        }

    }
}

function articles_filter($query)
{
    $tmp_cats = [];
    global $post;
    if (!is_null($post)) {
        $paper_cat = get_the_terms($post->ID, 'category_papers');

        if (is_array($paper_cat) && count($paper_cat) > 0) {
            for ($i = 0; $i < count($paper_cat); $i++) {
                $name = $paper_cat[$i]->name;
                array_push(
                    $tmp_cats,
                    $name
                );
            }
            $query['tax_query'] = array(
                'relation' => 'AND',
                array('taxonomy' => 'category_papers', 'field' => 'slug', 'terms' => $tmp_cats)
            );
        }
    }
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
        if ($name == "admin") {
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
 * This function checks if the user is allowed to see the newspaper and sets a cookie for the newspaper
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

    // set cookie for the newspaper
    if (!empty($category_detail)) {
        setcookie("current-newspaper", $category_detail[0]->slug, time() + 10600, "/");
    }
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
            if ($name == $magazin_category || $name == "admin") {
                return $template;
            }
        }
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . get_bloginfo('url') . "/401");
        exit();
    }


    return $template;
}


// Menu

// previous and next links

add_filter('next_post_link', 'augustin_adjacent_post_link', 10, 5);
add_filter('previous_post_link', 'augustin_adjacent_post_link', 10, 5);

function augustin_adjacent_post_link($output, $format, $link, $post, $adjacent)
{
    $previous = 'previous' === $adjacent;

    if (!($previous && is_attachment())) {
        $post = get_adjacent_post(true, '', $previous, 'category_papers');
    }

    if (!$post) {
        $output = '';
    } else {
        $title = $post->post_title;

        if (empty($post->post_title)) {
            $title = $previous ? __('Previous Post') : __('Next Post');
        }

        $title = apply_filters('the_title', $title, $post->ID);

        $date = mysql2date(get_option('date_format'), $post->post_date);
        $rel = $previous ? 'prev' : 'next';

        $string = '<a href="' . get_permalink($post) . '" rel="' . $rel . '">';
        $inlink = str_replace('%title', $title, $link);
        $inlink = str_replace('%date', $date, $inlink);
        $inlink = $string . $inlink . '</a>';

        $output = str_replace('%link', $inlink, $format);
    }

    return $output;
}

