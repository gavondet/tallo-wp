<?php

/**
* Plugin Name:       Talleres Online
* Plugin URI:        https://talleresonline.net/plugins/
* Description:       El mejor plugin para Talleres Online
* Version:           1.0.0
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            El Despliegue
* Author URI:        https://www.eldespliegue.com.ar/
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain:       tallo-wp-plugin
* Domain Path:       /languages
*/


global $tallo_roles;

$tallo_roles= [
    'beta_tester' => array(
        'display_name' => 'Beta Tester',
        'capabilities' => array(
            'read' => true
        )
    )
];


// This enables debugging.
define( 'WP_DEBUG', true );
define( 'TALLERES_ONLINE_VERSION', '1.0.0' );

register_activation_hook( __FILE__, 'tallo_add_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_remove_custom_roles' );

add_action('plugins_loaded', 'tallo_check_version');


function tallo_add_custom_roles() {

    global $tallo_roles;

    foreach ($tallo_roles as $role => $values) {
        add_role( $role, $values['display_name'], $values['capabilities'] );
    };

}


function tallo_remove_custom_roles() {

    global $tallo_roles;

    foreach ($tallo_roles as $role => $values) {
        remove_role( $role, $values['display_name'], $values['capabilities'] );
    };

}


function tallo_check_version() {
    $version = get_option('talleres_online_version');

    if (TALLERES_ONLINE_VERSION !== get_option('talleres_online_version')) {
       tallo_add_custom_roles();
    }

    update_option('talleres_online_version', TALLERES_ONLINE_VERSION);
}
