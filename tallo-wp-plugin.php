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


function tallo_add_custom_roles() {
       add_role( 'custom_role', 'Custom Subscriber', array( 'read' => true, 'level_0' => true, 'edit_posts' => true ) );
}


function tallo_remove_custom_roles() {
       remove_role( 'custom_role', 'Custom Subscriber', array( 'read' => true, 'level_0' => true, 'edit_posts' => true ) );
}




register_activation_hook( __FILE__, 'tallo_add_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_remove_custom_roles' );