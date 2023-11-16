<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
/**
* This registers the categories as a menu when the menu is called top_menu
*/

function category_menu_category_menu_allowed_block_types_for_navigation($block_content, $block)
{

	if ($block["blockName"] == "core/navigation" && isset($block["attrs"]["className"]) && $block["attrs"]["className"] == "top_menu") {
		global $post;
		if (is_home())
			return $block_content;
		$terms = get_terms('category');
		
		$new_content ="";
		foreach ($terms as $term) {
			if ($term->name != "Uncategorized") {
				$new_content .= "<li><a href='/category/" . $term->slug . "'>" . $term->name . "</a></li>";
			}
		}
		$pos = strpos("$block_content", '</ul>');

		$block_content= substr_replace($block_content, $new_content, $pos, 0);
	}
	return $block_content;
}

function category_menu_category_menu_block_init()
{

	add_filter('render_block', 'category_menu_category_menu_allowed_block_types_for_navigation', 10, 3);
}
category_menu_category_menu_block_init();
