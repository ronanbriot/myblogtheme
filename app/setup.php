<?php

/**
 * Theme setup.
 */

namespace App;

use function Roots\bundle;

/**
 * Register the theme assets.
 *
 * @return void
 */
add_action('wp_enqueue_scripts', function () {
    bundle('app')->enqueue();
}, 100);

/**
 * Register the theme assets with the block editor.
 *
 * @return void
 */
add_action('enqueue_block_editor_assets', function () {
    bundle('editor')->enqueue();
}, 100);

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
    ] + $config);
});

add_action('after_setup_theme', function () {
    load_theme_textdomain('sage', get_template_directory() . '/resources/lang');
});

// Replace Posts label as Articles in Admin Panel 
add_action('init', function () {
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'Évènements';
    $labels->singular_name = 'Évènement';
    $labels->add_new = 'Ajouter';
    $labels->add_new_item = 'Ajouter un nouvel évènement';
    $labels->edit_item = 'Editer évènement';
    $labels->new_item = 'Nouvel évènement';
    $labels->view_item = 'Voir évènement';
    $labels->search_items = 'Rechercher évènement';
    $labels->not_found = 'Aucun évènement trouvé';
    $labels->not_found_in_trash = 'Aucun évènement trouvé dans la corbeille';
    $labels->name_admin_bar = 'Ajouter un nouvel évènement';
});

add_action('admin_menu', function () {
    global $menu;
    global $submenu;
    $menu[5][0] = 'Évènements';
    $submenu['edit.php'][5][0] = 'Évènements';
    $submenu['edit.php'][10][0] = 'Ajouter un nouvel évènement';
    echo '';
});

add_action('init', function () {
    $post_type_object = get_post_type_object('post');
    $post_type_object->template = array(
        array('core/gallery'),
    );
});

// Activate WordPress Maintenance Mode
add_action('init', function () {
    if (
        $_SERVER['REQUEST_URI'] !== '/'
        && !str_contains($_SERVER['REQUEST_URI'], '/wp/wp-login.php')
        && !current_user_can('administrator') // rule to allow admins
        && WP_ENV === 'development'
        && 'PostmanRuntime/7.42.0' !== $_SERVER['HTTP_USER_AGENT'] // postman
        && 'Shotstack-Webhook/1.0' !== $_SERVER['HTTP_USER_AGENT'] // shotstack
        && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' // wp-cli
    ) {
        wp_die(
            __( 'Briefly unavailable for scheduled maintenance. Check back in a minute.', 'sage' ),
            __( 'Maintenance' ),
            503
        );
    }
});
