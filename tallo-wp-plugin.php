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
    // TODO en todo caso estos tienen que tener el prefijo tallo
    // o los agregamos en la otra funcion y aca los que no sean automaticos
    'beta_tester' => array(
        'display_name' => 'Beta Tester',
        'capabilities' => array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            'upload_files' => true,
            'edit_tallo_proyectos' => true,
            'edit_published_tallo_proyectos' => true,
            'publish_tallo_proyectos' => true,
            'delete_tallo_proyectos' => true,
            'edit_tallo_anuncios' => true,
            'edit_published_tallo_anuncios' => true,
            'publish_tallo_anuncios' => true,
            'delete_tallo_anuncios' => true,
            'edit_tallo_link_pago' => true,
            'edit_tallo_published_link_pago' => true,
            'publish_tallo_link_pago' => true,
            'delete_tallo_link_pago' => true,
        )
    )
);

global $tallo_custom_post_types;
$tallo_custom_post_types = array(
    // TODO agregarles los prefijo tallo_ a los custom types
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
    'tallo_plantilla' => array(
        'name'          => 'Plantillas de Anuncios',
        'singular_name' => 'Plantilla de Anuncio',
        'slug'          => 'anuncios_plantilla',
        'menu_icon'     => 'dashicons-media-document',
    ),
    'tallo_link_pago' => array(
        'name'          => 'Links de Pago',
        'singular_name' => 'Link de Pago',
        'slug'          => 'link_pagos',
        'menu_icon'     => 'dashicons-tickets',
    ),
);

// Estos custom post types son los editables por los talleristas
// TODO que pasa si queremos poner otro plural? en ese caso usar un array en vez de string para ese caso y validarlo en la funcion que lo usa?
global $tallo_editable_custom_post_types;
$tallo_editable_custom_post_types = array(
    'tallo_proyecto',
    'tallo_anuncio',
    'tallo_link_pago'
);


define( 'TALLERES_ONLINE_VERSION', '1.0.2' );

register_activation_hook( __FILE__, 'tallo_add_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_remove_custom_roles' );
register_deactivation_hook( __FILE__, 'tallo_unregister_custom_post_types' );

add_action('plugins_loaded', 'tallo_check_version');
add_action('init', 'tallo_register_custom_post_types');
add_action('init', 'tallo_add_custom_posts_supports');
add_action('init', 'tallo_add_admin_capabilities');

add_action('restrict_manage_posts', 'tallo_restrict_manage_authors');

add_filter('wp_dropdown_users_args', 'tallo_add_custom_roles_to_dropdown', 10, 2 );




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
                'capability_type' => $custom_post_type,
                'map_meta_cap' => true,
            )
        );
    }
}


function tallo_add_custom_posts_supports() {
    global $tallo_editable_custom_post_types;

    $features = array('author', 'page-attributes');
    foreach ( $tallo_editable_custom_post_types as $custom_post ) {
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
    global $tallo_custom_post_types;

    $role = get_role( 'administrator' );

    $capabilities = array(
        'edit_',
        'delete_',
        'publish_',
        'edit_published_',
        'delete_published_',
    );

    foreach ($tallo_custom_post_types as $custom_post_type => $attributes) {
        foreach ($capabilities as $cap) {
            $role->add_cap( "{$cap}{$custom_post_type}" );
            $role->add_cap( "{$cap}{$custom_post_type}s" );
        }
    }

}


/*
 *   https://developer.wordpress.org/reference/hooks/wp_dropdown_users_args/
 */
function tallo_add_custom_roles_to_dropdown( $query_args, $r ) {
  
    global $post;
    global $tallo_roles, $tallo_editable_custom_post_types;

    $post_type = !is_null($post) ? $post->post_type : $_GET['post_type'];
    if (in_array($post_type, $tallo_editable_custom_post_types)) {

        $query_args['role__in'] = array_merge(
            ['administrator', 'editor'],
            array_keys($tallo_roles)
        );

        unset( $query_args['who'] );

    }
    return $query_args;
}


/*
 *    https://www.isitwp.com/wordpress-add-show-posts-by-author-filter-menu-to-admin-posts-list/
 */
function tallo_restrict_manage_authors() {

    global $tallo_editable_custom_post_types;

    if (
        isset($_GET['post_type']) 
        && in_array(strtolower($_GET['post_type']), $tallo_editable_custom_post_types)
    ) {
        wp_dropdown_users(array(
            'show_option_all'   => __('All Authors'),
            'show_option_none'  => false,
            'name'          => 'author',
            'selected'      => !empty($_GET['author']) ? $_GET['author'] : 0,
            'include_selected'  => false
        ));
    }
}


/**
 * Flushes rewrites if our project rule isn't yet added.
 */
function tallo_flush_rules() {
    $rules = get_option( 'rewrite_rules' );

    if ( ! isset( $rules['anuncio/([^/]+)/([^/]+)?$'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
}
add_action( 'wp_loaded','tallo_flush_rules' );

// Adding a new rule
/**
 * Adds a new rewrite rule.
 *
 * @param array $rules Existing rewrite rules.
 * @return array (Maybe) modified list of rewrites.
 */

function tallo_insert_rewrite_rules( $rules ) {
    $newrules = array();
    $newrules['anuncio/([^/]+)/([^/]+)?$'] = 'index.php?pagename=front_anuncio_view&username=$matches[1]&slug_anuncio=$matches[2]';

    return $newrules + $rules;
}
add_filter( 'rewrite_rules_array','tallo_insert_rewrite_rules' );

// Adding the username var so that WP recognizes it
function tallo_insert_query_vars( $vars ) {
    array_push( $vars, 'username' );
    array_push( $vars, 'slug_anuncio' );
    return $vars;
}
add_filter( 'query_vars','tallo_insert_query_vars' );
