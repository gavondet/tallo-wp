<?php

/**
* Plugin Name:       Talleres Online
* Plugin URI:        https://talleresonline.net/plugins/
* Description:       El mejor plugin para Talleres Online
* Version:           1.0
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

$tallo_roles = array(
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
);

global $tallo_custom_post_types;

$tallo_custom_post_types = array(
    'tallo_proyecto' => array(
        'name'          => 'Proyectos',
        'singular_name' => 'Proyecto',
        'slug'          => 'proyectos',
        'menu_icon'     => 'dashicons-art',
    ),
    'tallo_tipo_proyecto' => array(
        'name'          => 'Tipo de Proyectos',
        'singular_name' => 'Tipo de Proyecto',
        'slug'          => 'tipo_proyectos',
        'menu_icon'     => 'dashicons-tag',
    ),
    'tallo_anuncio' => array(
	'name'          => 'Anuncios',
	'singular_name' => 'Anuncio',
	'slug'          => 'anuncios',
	'menu_icon'     => 'dashicons-megaphone',
    ),
);

define( 'TALLERES_ONLINE_VERSION', '1.0.2' );

register_activation_hook( __FILE__, 'tallo_add_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_remove_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_unregister_custom_post_types' );

add_action('plugins_loaded', 'tallo_check_version');
add_action('init', 'tallo_register_custom_post_types');



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


function tallo_register_custom_post_types() {
    global $tallo_custom_post_types;

    foreach ($tallo_custom_post_types as $custom_post_type => $attributes) {
        register_post_type($custom_post_type,
            array(
                'labels'      => array(
                    'name'          => __( $attributes['name'], 'textdomain' ),
                    'singular_name' => __( $attributes['singular_name'], 'textdomain' ),
                ),
                'public'      => true,
                'has_archive' => true,
                'rewrite'     => array( 'slug' => $attributes['slug'] ),
                'menu_icon'   => $attributes['menu_icon'],
                'map_meta_cap' => true
            )
        );
    };
}


function tallo_unregister_custom_post_types() {
    global $tallo_custom_post_types;

    foreach ($tallo_custom_post_types as $custom_post_type) {
        unregister_post_type($custom_post_type);
    };

}
