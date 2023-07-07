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
function augustin_create_group_and_add_as_a_member(
    $user_groups,
    $user = null
) {

    require_once(ABSPATH . 'wp-includes/pluggable.php');
    for ($i = 0; $i < count($user_groups); $i++) {
        $is_a_member = false;
        if ($group = Groups_Group::read_by_name($user_groups[$i])) {
            $is_a_member = Groups_User_Group::read($user->ID, $group->group_id);
            if (!$is_a_member) {
                Groups_User_Group::create(array('user_id' => $user->ID, 'group_id' => $group->group_id));
            }
        } else {
            $group = Groups_Group::create([
                'name' => $user_groups[$i],
                'description' => 'An automatically created keycloak group' . $user_groups[$i],
            ]);
            if (!$group) {
                return;
            }
            Groups_User_Group::create(array('user_id' => $user_id, 'group_id' => $group));
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