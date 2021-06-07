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
            'upload_files' => true,
            'edit_proyectos' => true,
            'edit_published_proyectos' => true,
            'publish_proyectos' => true,
            'delete_proyectos' => true,
            'edit_anuncios' => true,
            'edit_published_anuncios' => true,
            'publish_anuncios' => true,
            'delete_anuncios' => true,
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
        'capability_type' => 'proyecto',
    ),
    'tallo_tipo_proyecto' => array(
        'name'          => 'Tipo de Proyectos',
        'singular_name' => 'Tipo de Proyecto',
        'slug'          => 'tipo_proyectos',
        'menu_icon'     => 'dashicons-tag',
        'capability_type' => 'tipo_proyecto',

    ),
    'tallo_anuncio' => array(
        'name'          => 'Anuncios',
        'singular_name' => 'Anuncio',
        'slug'          => 'anuncios',
        'menu_icon'     => 'dashicons-megaphone',
        'capability_type' => 'anuncio',
    ),
    'tallo_plantilla' => array(
        'name'          => 'Plantillas de Anuncios',
        'singular_name' => 'Plantilla de Anuncio',
        'slug'          => 'anuncios_plantilla',
        'menu_icon'     => 'dashicons-media-document',
        'capability_type' => 'plantilla',
    ),
);

define( 'TALLERES_ONLINE_VERSION', '1.0.2' );

register_activation_hook( __FILE__, 'tallo_add_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_remove_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_unregister_custom_post_types' );

add_action('plugins_loaded', 'tallo_check_version');
add_action('init', 'tallo_register_custom_post_types');
add_action('init', 'tallo_add_custom_posts_supports');
add_action('init', 'tallo_add_admin_capabilities');




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
                'capability_type' => $attributes['capability_type'],
                'map_meta_cap' => true,
            )
        );
    };
}


function tallo_add_custom_posts_supports() {
    $custom_posts = array(
        'tallo_tipo_proyecto',
        'tallo_plantilla'
    );
    $features = array('author', 'page-attributes');

    foreach ( $custom_posts as $custom_post ) {
        add_post_type_support( $custom_post, $features );
    }

}


function tallo_unregister_custom_post_types() {
    global $tallo_custom_post_types;

    foreach ($tallo_custom_post_types as $custom_post_type) {
        unregister_post_type($custom_post_type);
    };

}

function tallo_add_admin_capabilities() {
    $role = get_role( 'administrator' );

    $custom_post_types = array(
        'proyectos',
        'tipo_proyectos',
        'anuncios',
        'plantillas',
    );

    $capabilities = array(
        'edit_',
        'delete_',
        'publish_',
        'edit_published_',
        'delete_published_',
    );

    foreach ($custom_post_types as $custom_post_type) {
        foreach ($capabilities as $cap) {
            $role->add_cap( "{$cap}{$custom_post_type}" );
        }
    }

}
