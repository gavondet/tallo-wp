<?php

/**
* Plugin Name:       Talleres Online
* Plugin URI:        https://talleresonline.net/plugins/
* Description:       El mejor plugin para Talleres Online
* Version:           1.1.1
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            El Despliegue
* Author URI:        https://www.eldespliegue.com.ar/
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain:       tallo-wp-plugin
* Domain Path:       /languages
*/

define( 'TALLERES_ONLINE_VERSION', '1.1.1' );

register_activation_hook( __FILE__, 'tallo_add_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_remove_custom_roles' );

add_action('plugins_loaded', 'tallo_check_version');


function tallo_add_custom_roles() {
    add_role( 'custom_role', 'Custom Subscriber', array( 'read' => true, 'level_0' => true, 'edit_posts' => true) );
//     add_role( 'custom_role2', 'Custom Subscriber2', array( 'read' => true, 'level_0' => true) );
}


function tallo_remove_custom_roles() {
    remove_role( 'custom_role', 'Custom Subscriber', array( 'read' => true, 'level_0' => true, 'edit_posts' => true ) );
//     remove_role( 'custom_role2', 'Custom Subscriber2', array( 'read' => true, 'level_0' => true) );
}


function tallo_check_version() {
    $version = get_option('talleres_online_version');

    if (TALLERES_ONLINE_VERSION !== get_option('talleres_online_version')) {
       tallo_add_custom_roles();
    }

    update_option('talleres_online_version', TALLERES_ONLINE_VERSION);
}


