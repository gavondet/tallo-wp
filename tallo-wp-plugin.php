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
            'read' => true,
            'edit_tallo_proyectos' => true,
            'delete_tallo_proyectos' => true,
            'edit_tallo_tipo_proyectos' => true,
            'delete_tallo_tipo_proyectos' => true,
            'upload_files' => true,
        )
    )
];


// This enables debugging.
define( 'WP_DEBUG', true );
define( 'TALLERES_ONLINE_VERSION', '1.0.0' );

register_activation_hook( __FILE__, 'tallo_add_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_remove_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_unregister_proyectos_post_type' );
register_deactivation_hook( __FILE__, 'tallo_unregister_tipo_proyectos_post_type' );

add_action('plugins_loaded', 'tallo_check_version');
add_action('init', 'tallo_register_proyectos_post_type');
add_action('init', 'tallo_register_tipo_proyecto_post_type');


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

function tallo_register_proyectos_post_type() {
    register_post_type('tallo_proyecto',
        array(
            'labels'      => array(
                'name'          => __( 'Proyectos', 'textdomain' ),
                'singular_name' => __( 'Proyecto', 'textdomain' ),
            ),
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => array( 'slug' => 'proyectos' ),
            'menu_icon'   => 'dashicons-art',
            'map_meta_cap' => true
        )
    );
}

function tallo_register_tipo_proyecto_post_type() {
    register_post_type('tallo_tipo_proyecto',
        array(
            'labels'      => array(
                'name'          => __( 'Tipo de Proyectos', 'textdomain' ),
                'singular_name' => __( 'Tipo de Proyecto', 'textdomain' ),
            ),
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => array( 'slug' => 'tipo_proyectos' ),
            'menu_icon'   => 'dashicons-tag',
            'map_meta_cap' => true
        )
    );
}

function tallo_unregister_proyectos_post_type() {
    unregister_post_type('tallo_proyecto');
}

function tallo_unregister_tipo_proyectos_post_type() {
    unregister_post_type('tallo_tipo_proyecto');
}
